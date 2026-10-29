import json
from pathlib import Path
import sqlite3
import sys
import unittest

sys.path.insert(0, str(Path(__file__).resolve().parents[2] / 'app' / 'ml'))
from predict import predict_risk
from ket_noi_db import lay_danh_sach_nhan_vien, lay_du_lieu_du_doan, du_doan_nhan_vien, ghi_ket_qua_du_doan


class Cursor:
    def __init__(self, db):
        self.cursor = db.cursor()

    def __enter__(self):
        return self

    def __exit__(self, *args):
        self.cursor.close()

    def execute(self, sql, args=()):
        self.cursor.execute(sql.replace('%s', '?'), args)

    def fetchall(self):
        return [dict(row) for row in self.cursor.fetchall()]

    def fetchone(self):
        row = self.cursor.fetchone()
        return dict(row) if row else None


class Connection:
    def __init__(self):
        self.db = sqlite3.connect(':memory:')
        self.db.row_factory = sqlite3.Row

    def cursor(self):
        return Cursor(self.db)


class RiskRulesTest(unittest.TestCase):
    def test_score_cases_and_boundaries(self):
        cases = [(55, 1, 40, 8, 'Cao'), (75, .5, 90, 5, 'Cao'),
                 (70, 1/3, 60, 4, 'Trung bình'), (85, 0, 30, 2, 'Thấp'),
                 (90, 0, 100, 0, 'Thấp'), (60, .25, 50, 4, 'Trung bình'),
                 (80, .5, 80, 3, 'Trung bình'), (85, 1/5, 100, 1, 'Thấp'),
                 (90, 1/7, 100, 1, 'Thấp'), (59.96, 0, 100, 3, 'Trung bình')]
        for average, failed, progress, total, level in cases:
            with self.subTest(average=average, failed=failed, progress=progress):
                result = predict_risk(progress, average, 2, failed, 0)
                self.assertEqual(total, result['details']['tong_diem'])
                self.assertEqual(level, result['risk_level'])
                self.assertEqual(total / 13, result['normalized_score'])
                self.assertEqual('rules-v3', result['rules_version'])

    def test_invalid_scoring_inputs(self):
        for value in [None, float('nan'), float('inf'), -1, 101, True]:
            with self.subTest(value=value), self.assertRaises(ValueError):
                predict_risk(100, value, 2, 0, 0)
        with self.assertRaises(ValueError):
            predict_risk(100, 80, 2, 1.1, 0)

    def setUp(self):
        self.conn = Connection()
        self.conn.db.executescript('''
            CREATE TABLE users (id INTEGER, ChucVu TEXT, employment_status TEXT DEFAULT 'DangLam', cua_hang_id INTEGER DEFAULT 1);
            CREATE TABLE chuong_trinh_dao_tao (MaCTDT INTEGER, ChucVuApDung TEXT);
            CREATE TABLE nhan_vien_khoa_hoc (user_id INTEGER, MaCTDT INTEGER, TienDo REAL);
            CREATE TABLE bai_hoc (MaBaiHoc INTEGER, FK_MaCTDT INTEGER);
            CREATE TABLE bai_kiem_tra (MaBaiKiemTra INTEGER, MaBaiHoc INTEGER, DiemChuan REAL);
            CREATE TABLE ket_qua_kiem_tra (id INTEGER, user_id INTEGER, MaBaiKiemTra INTEGER, Diem REAL, created_at TEXT);
            CREATE TABLE user_lesson_progresses (user_id INTEGER, lesson_id INTEGER, visit_count INTEGER);
            CREATE TABLE du_doan_nguy_co (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, cua_hang_id INTEGER,
                diem_rui_ro REAL, muc_canh_bao TEXT, chi_tiet_yeu_to TEXT, phien_ban_mo_hinh TEXT, ngay_du_doan TEXT);
            CREATE TABLE danh_gia_nang_luc (id INTEGER, user_id INTEGER, TrangThai TEXT, IsPassed INTEGER, ngay_duyet TEXT);
            CREATE TABLE loi_nghiep_vu (id INTEGER, user_id INTEGER, thoi_diem_xay_ra TEXT, muc_do TEXT DEFAULT 'Nhanh');
            INSERT INTO users (id, ChucVu) VALUES (1, 'Barista');
            INSERT INTO chuong_trinh_dao_tao VALUES (1, 'ALL STAFF'), (2, 'Barista'), (3, 'Thu ngân');
            INSERT INTO nhan_vien_khoa_hoc VALUES (1, 1, 100);
            INSERT INTO bai_hoc VALUES (1, 1), (2, 2), (3, 3);
            INSERT INTO bai_kiem_tra VALUES (1, 1, 85), (2, 2, 90), (3, 3, 80);
        ''')
        self.at = '2026-10-14 12:00:00'

    def tearDown(self):
        self.conn.db.close()

    def result(self, id, test, score, time='2026-10-14 11:00:00'):
        self.conn.db.execute('INSERT INTO ket_qua_kiem_tra VALUES (?, 1, ?, ?, ?)', (id, test, score, time))

    def test_latest_tie_cutoff_standards_scope_and_unstarted_progress(self):
        self.result(1, 1, 20, '2026-10-13 12:00:00')
        self.result(2, 1, 90)
        self.result(3, 1, 82)  # tied time: larger id wins, fails standard 85
        self.result(4, 2, 90)
        self.result(5, 2, 10, '2026-10-14 13:00:00')  # future excluded
        self.result(6, 3, 0)  # foreign course excluded
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual(86, data['avg_quiz_score'])
        self.assertEqual(.5, data['failed_rate'])
        self.assertEqual(50, data['progress_percent'])
        self.assertEqual(4, data['quiz_attempts'])
        self.assertEqual(2, data['completed_quiz_count'])
        self.assertEqual(2, data['applicable_quiz_count'])
        self.assertTrue(data['du_dieu_kien_du_doan'])  # zero visits allowed
        self.assertEqual(4, du_doan_nhan_vien(data)['chi_tiet_yeu_to']['details']['tong_diem'])

    def test_retries_do_not_accumulate_failures(self):
        self.result(1, 1, 20, '2026-10-12 12:00:00')
        self.result(2, 1, 40, '2026-10-13 12:00:00')
        self.result(3, 1, 85)
        self.result(4, 2, 90)
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual(0, data['failed_rate'])
        self.assertEqual(87.5, data['avg_quiz_score'])

    def test_one_distinct_quiz_is_insufficient_and_null_standard_defaults_to_80(self):
        self.conn.db.execute('UPDATE bai_kiem_tra SET DiemChuan = NULL WHERE MaBaiKiemTra = 1')
        self.result(1, 1, 70)
        self.result(2, 1, 80)
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual(0, data['failed_rate'])
        result = du_doan_nhan_vien(data)
        self.assertEqual('ChuaDuDuLieu', result['muc_canh_bao'])
        self.assertIsNone(result['diem_rui_ro'])
        self.assertEqual('rules-v3', result['phien_ban_mo_hinh'])

    def test_no_courses_and_no_quizzes(self):
        self.conn.db.execute("UPDATE users SET ChucVu = 'Unknown'")
        self.conn.db.execute("DELETE FROM chuong_trinh_dao_tao WHERE ChucVuApDung = 'ALL STAFF'")
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual(0, data['applicable_course_count'])
        self.assertFalse(data['du_dieu_kien_du_doan'])

    def test_invalid_latest_cannot_fall_back_to_old_pass(self):
        self.result(1, 1, 90, '2026-10-13 12:00:00')
        self.result(2, 1, 101)
        with self.assertRaises(ValueError):
            lay_du_lieu_du_doan(self.conn, 1, self.at)

    def test_missing_timestamp_is_not_silently_ignored(self):
        self.result(1, 1, 90, None)
        with self.assertRaises(ValueError):
            lay_du_lieu_du_doan(self.conn, 1, self.at)

    def test_insert_keeps_history_and_exact_calculation_time(self):
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        result = du_doan_nhan_vien(data)
        for _ in range(2):
            ghi_ket_qua_du_doan(self.conn, 1, 1, result, self.at)
        rows = self.conn.db.execute('SELECT * FROM du_doan_nguy_co').fetchall()
        self.assertEqual(2, len(rows))
        self.assertEqual(self.at, rows[0]['ngay_du_doan'])
        self.assertEqual(self.at, json.loads(rows[0]['chi_tiet_yeu_to'])['calculated_at'])

    def test_allowlist_rejects_unknown_null_manager_and_inactive_accounts(self):
        self.conn.db.executemany('INSERT INTO users (id, ChucVu, employment_status, cua_hang_id) VALUES (?, ?, ?, ?)', [
            (2, 'Thu ngân', 'DangLam', 1), (3, 'Phục vụ', 'DangLam', 1),
            (4, None, 'DangLam', 1), (5, 'Admin', 'DangLam', 1),
            (6, 'Trưởng ca', 'DangLam', 1), (7, 'Thực Tập Sinh', 'DangLam', 1),
            (8, 'Barista', 'NghiViec', 1), (9, 'Barista', 'TamNgung', 1),
            (10, 'Barista', 'DangLam', None), (11, ' BARISTA ', 'DangLam', 1)])
        self.assertEqual([1, 2, 3, 11], [row['id'] for row in lay_danh_sach_nhan_vien(self.conn)])

    def test_null_standard_79_fails_and_invalid_standard_rejects(self):
        self.conn.db.execute('UPDATE bai_kiem_tra SET DiemChuan = NULL WHERE MaBaiKiemTra = 1')
        self.result(1, 1, 79)
        self.result(2, 2, 90)
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual(.5, data['failed_rate'])
        self.conn.db.execute('UPDATE bai_kiem_tra SET DiemChuan = 101 WHERE MaBaiKiemTra = 1')
        with self.assertRaises(ValueError):
            lay_du_lieu_du_doan(self.conn, 1, self.at)

    def test_exact_scoring_boundaries(self):
        cases = [(59.999, 0, 80, 3), (60, 0, 80, 2), (79.999, 0, 80, 2),
                 (80, 0, 80, 0), (80, 0.000001, 80, 1),
                 (80, 0.499999, 80, 1), (80, .5, 80, 3),
                 (80, 0, 49.999, 2), (80, 0, 50, 1), (80, 0, 79.999, 1)]
        for average, failed, progress, total in cases:
            with self.subTest(case=(average, failed, progress)):
                self.assertEqual(total, predict_risk(progress, average, 2, failed, 0)['details']['tong_diem'])

    def test_sufficient_result_insert_preserves_auditable_details(self):
        self.result(1, 1, 82)
        self.result(2, 2, 90)
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        result = du_doan_nhan_vien(data)
        ghi_ket_qua_du_doan(self.conn, 1, 1, result, self.at)
        row = self.conn.db.execute('SELECT * FROM du_doan_nguy_co').fetchone()
        self.assertEqual(4 / 13, row['diem_rui_ro'])
        self.assertEqual('TrungBinh', row['muc_canh_bao'])
        details = json.loads(row['chi_tiet_yeu_to'])
        self.assertEqual([2, 1], [r['result_id'] for r in details['selected_results']])
        self.assertEqual(1, details['failed_quiz_count'])
        self.assertEqual(4, details['details']['tong_diem'])

    def assessment(self, id, passed, approved_at='2026-10-10 09:00:00', status='DaDuyet'):
        self.conn.db.execute('INSERT INTO danh_gia_nang_luc VALUES (?, 1, ?, ?, ?)', (id, status, passed, approved_at))

    def error(self, id, time, level='Nhanh'):
        self.conn.db.execute('INSERT INTO loi_nghiep_vu VALUES (?, 1, ?, ?)', (id, time, level))

    def test_assessment_and_error_scoring(self):
        cases = [(False, 0, 0), (True, 0, 3), (False, 1, 1), (False, 2, 2), (False, 5, 2), (True, 2, 5)]
        for failed, errors, total in cases:
            with self.subTest(failed=failed, errors=errors):
                result = predict_risk(100, 90, 2, 0, 0, latest_assessment_failed=failed, recent_error_count=errors)
                self.assertEqual(total, result['details']['tong_diem'])
        self.assertEqual('Cao', predict_risk(100, 90, 2, 0, 0, True, 2)['risk_level'])
        self.assertEqual('Trung bình', predict_risk(100, 90, 2, 0, 0, True, 0)['risk_level'])

    def test_one_serious_error_scores_like_repeated_errors(self):
        cases = [(1, 0, 1), (1, 1, 2), (2, 0, 2), (3, 1, 2)]
        for errors, serious, total in cases:
            with self.subTest(errors=errors, serious=serious):
                result = predict_risk(100, 90, 2, 0, 0, recent_error_count=errors, recent_serious_error_count=serious)
                self.assertEqual(total, result['details']['diem_thanh_phan']['loi_nghiep_vu'])
        reason = predict_risk(100, 90, 2, 0, 0, recent_error_count=1, recent_serious_error_count=1)['details']['ly_do_canh_bao']
        self.assertEqual(['Có 1 lỗi nghiệp vụ trong 30 ngày gần nhất, trong đó 1 lỗi nghiêm trọng'], reason)
        with self.assertRaises(ValueError):
            predict_risk(100, 90, 2, 0, 0, recent_error_count=1, recent_serious_error_count=2)

    def test_latest_approved_failed_assessment_raises_warning_even_with_good_quizzes(self):
        self.result(1, 1, 95)
        self.result(2, 2, 95)
        self.assessment(1, 1, '2026-09-01 09:00:00')
        self.assessment(2, 0, '2026-10-10 09:00:00')
        self.assessment(3, 1, '2026-10-12 09:00:00', 'ChoPheDuyet')  # not approved yet
        self.assessment(4, 1, '2026-10-15 09:00:00')  # approved after calculation time
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertTrue(data['latest_assessment_failed'])
        self.assertEqual(2, data['latest_assessment_id'])
        result = du_doan_nhan_vien(data)
        self.assertEqual(3, result['chi_tiet_yeu_to']['details']['diem_thanh_phan']['danh_gia_gan_nhat'])

    def test_later_passed_assessment_clears_assessment_factor(self):
        self.result(1, 1, 95)
        self.result(2, 2, 95)
        self.assessment(1, 0, '2026-09-01 09:00:00')
        self.assessment(2, 1, '2026-10-10 09:00:00')
        self.assertFalse(lay_du_lieu_du_doan(self.conn, 1, self.at)['latest_assessment_failed'])

    def test_approved_assessment_without_result_is_rejected(self):
        self.assessment(1, None)
        with self.assertRaises(ValueError):
            lay_du_lieu_du_doan(self.conn, 1, self.at)

    def test_only_errors_within_30_days_before_calculation_count(self):
        self.error(1, '2026-09-14 12:00:00')  # exactly 30 days earlier: excluded
        self.error(2, '2026-09-14 12:00:01')
        self.error(3, '2026-10-14 12:00:00')
        self.error(4, '2026-10-14 12:00:01')  # after calculation time
        self.assertEqual(2, lay_du_lieu_du_doan(self.conn, 1, self.at)['recent_error_count'])

    def test_serious_errors_are_counted_within_the_same_window(self):
        self.error(1, '2026-09-10 12:00:00', 'NghiemTrong')  # outside 30 days
        self.error(2, '2026-10-10 12:00:00', 'NghiemTrong')
        self.error(3, '2026-10-11 12:00:00', 'TrungBinh')
        data = lay_du_lieu_du_doan(self.conn, 1, self.at)
        self.assertEqual((2, 1), (data['recent_error_count'], data['recent_serious_error_count']))

    def test_duplicate_progress_is_reported_instead_of_double_counting(self):
        self.conn.db.execute('INSERT INTO nhan_vien_khoa_hoc VALUES (1, 1, 50)')
        with self.assertRaisesRegex(ValueError, 'Duplicate course progress'):
            lay_du_lieu_du_doan(self.conn, 1, self.at)


if __name__ == '__main__':
    unittest.main()
