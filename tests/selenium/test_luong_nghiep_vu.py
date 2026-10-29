"""Kiểm thử luồng nghiệp vụ trên giao diện bằng Selenium (KB01–KB07).

Cách chạy (cần bật MySQL của WAMP và có Chrome):
    venv\\Scripts\\python.exe -m unittest tests.selenium.test_luong_nghiep_vu -v

Mỗi lần chạy, chương trình:
1. Sao chép cơ sở dữ liệu minh họa `brewlearn` sang `brewlearn_selenium`,
   nên dữ liệu minh họa không bị thay đổi.
2. Đặt mật khẩu kiểm thử cho các tài khoản dùng trong kịch bản (chỉ trên bản sao).
3. Bật máy chủ riêng tại http://127.0.0.1:8001 nối với bản sao.
4. Mở Chrome và thực hiện lần lượt KB01 → KB07 như người dùng thật.

Đặt biến môi trường HEADLESS=1 để chạy Chrome ẩn.
KB03 nối tiếp kết quả của KB02 nên các kịch bản chạy theo thứ tự tên.
"""
import os
import subprocess
import tempfile
import time
import unittest
import urllib.request
from datetime import date, timedelta
from pathlib import Path

import pymysql
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import Select, WebDriverWait

GOC_DU_AN = Path(__file__).resolve().parents[2]
MYSQL_BIN = Path(os.getenv("MYSQL_BIN", r"D:\wamp64\bin\mysql\mysql9.1.0\bin"))
CSDL_GOC = "brewlearn"
CSDL_KIEM_THU = "brewlearn_selenium"
DIA_CHI = "http://127.0.0.1:8001"
MAT_KHAU_KIEM_THU = "Selenium@2026"

QUAN_LY = "ngocbich@brewlearn.com"
TRUONG_CA = "minhthu@brewlearn.com"
NHAN_VIEN = "vannam@brewlearn.com"
NHAN_VIEN_DUOC_DANH_GIA = "operations1.demo@brewlearn.com"
NHAN_VIEN_VONG_3 = "hoanganh@brewlearn.com"
NHAN_VIEN_CANH_BAO = "tuan.demo@brewlearn.com"


def ket_noi():
    return pymysql.connect(host="127.0.0.1", user="root", password="", database=CSDL_KIEM_THU,
                           charset="utf8mb4", cursorclass=pymysql.cursors.DictCursor, autocommit=True)


def truy_van(sql, *tham_so):
    with ket_noi() as conn, conn.cursor() as cur:
        cur.execute(sql, tham_so)
        return cur.fetchall()


def sao_chep_csdl():
    mysql = [str(MYSQL_BIN / "mysql"), "--default-character-set=utf8mb4", "-uroot"]
    subprocess.run(mysql + ["-e", f"DROP DATABASE IF EXISTS {CSDL_KIEM_THU}; "
                   f"CREATE DATABASE {CSDL_KIEM_THU} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"], check=True)
    dump = subprocess.Popen([str(MYSQL_BIN / "mysqldump"), "--default-character-set=utf8mb4", "-uroot",
                             "--single-transaction", CSDL_GOC], stdout=subprocess.PIPE)
    subprocess.run(mysql + [CSDL_KIEM_THU], stdin=dump.stdout, check=True)
    dump.stdout.close()
    if dump.wait() != 0:
        raise RuntimeError("Không sao chép được cơ sở dữ liệu")


def chuan_bi_du_lieu():
    """Đặt mật khẩu kiểm thử và tạo tiền đề cho KB04: yêu cầu đào tạo lại đang ở vòng 3."""
    mat_khau = subprocess.run(["php", "-r", f"echo password_hash('{MAT_KHAU_KIEM_THU}', PASSWORD_BCRYPT);"],
                              capture_output=True, text=True, check=True).stdout
    emails = (QUAN_LY, TRUONG_CA, NHAN_VIEN, NHAN_VIEN_DUOC_DANH_GIA, NHAN_VIEN_VONG_3)
    truy_van(f"UPDATE users SET password = %s WHERE email IN ({','.join(['%s'] * len(emails))})", mat_khau, *emails)

    nhan_vien = truy_van("SELECT id FROM users WHERE email = %s", NHAN_VIEN_VONG_3)[0]["id"]
    quan_ly = truy_van("SELECT id FROM users WHERE email = %s", QUAN_LY)[0]["id"]
    truong_ca = truy_van("SELECT id FROM users WHERE email = %s", TRUONG_CA)[0]["id"]
    yeu_cau = truy_van("SELECT id FROM yeu_cau_dao_tao_lai WHERE user_id = %s", nhan_vien)[0]["id"]
    de_xuat = '{"details": {"theodoica": {"behavior": "Theo dõi thái độ phục vụ", "shift_count": 3}}}'
    with ket_noi() as conn, conn.cursor() as cur:
        cur.execute("""INSERT INTO danh_gia_nang_luc (user_id, NguoiDanhGia_id, TieuChuan, NgayDanhGia, NgayHen, Diem,
                       NhanXet, TrangThai, Diem_NghiepVu, Diem_KyNang, Diem_QuyTrinh, Diem_PhucVu, Diem_TacPhong,
                       DeXuatXuLy, ChiTietDeXuat, IsPassed, created_at, updated_at)
                       VALUES (%s, %s, 'Tiêu chuẩn năng lực Barista', NOW(), NOW(), 62, 'Đánh giá lại vòng 3',
                       'ChoPheDuyet', 60, 60, 65, 60, 65, '["theodoica"]', %s, 0, NOW(), NOW())""",
                    (nhan_vien, truong_ca, de_xuat))
        danh_gia = cur.lastrowid
        for so_vong in (1, 2, 3):
            cur.execute("""INSERT INTO retraining_rounds (request_id, round_number, assigned_by, owner_id, instructions,
                           deadline, assessment_id, status, result, confirmed_by, confirmed_at, created_at, updated_at)
                           VALUES (%s, %s, %s, %s, %s, CURDATE(), %s, %s, %s, %s, NOW(), NOW(), NOW())""",
                        (yeu_cau, so_vong, quan_ly, truong_ca, f"Nội dung vòng {so_vong}",
                         danh_gia if so_vong == 3 else None, "ChoDanhGiaLai" if so_vong == 3 else "DaDuyet",
                         "ChuaCoKetQua" if so_vong == 3 else "ChuaDat", truong_ca))
        cur.execute("""UPDATE yeu_cau_dao_tao_lai SET tien_trinh = 'ChoDanhGiaLai', danh_gia_lai_id = %s,
                       nguoi_phu_trach_id = %s, han_hoan_thanh = CURDATE(), ngay_danh_gia_lai = CURDATE() WHERE id = %s""",
                    (danh_gia, truong_ca, yeu_cau))
    return {"danh_gia_vong_3": danh_gia, "yeu_cau_vong_3": yeu_cau,
            "nhan_vien_canh_bao": chuan_bi_du_lieu_canh_bao()}


def chuan_bi_du_lieu_canh_bao():
    """Tiền đề cho KB07, tính tay trước theo bộ quy tắc ở mục 4.5:
    tiến độ 60% → S3 = 1; ba bài 90, 80, 40 → trung bình 70 → S1 = 2, 1/3 bài chưa đạt → S2 = 1,
    yếu tố kiểm tra = max(S1, S2) = 2;
    chưa có đánh giá được duyệt → S4 = 0; lỗi nghiêm trọng do KB07 ghi qua giao diện → S5 = 2.
    Tổng 2 + 1 + 0 + 2 = 5/10 → 50,0/100, mức Cao.
    """
    nhan_vien = truy_van("SELECT id FROM users WHERE email = %s", NHAN_VIEN_CANH_BAO)[0]["id"]
    chuong_trinh = [row["MaCTDT"] for row in truy_van(
        """SELECT c.MaCTDT FROM chuong_trinh_dao_tao c JOIN users u ON u.id = %s
           WHERE UPPER(TRIM(c.ChucVuApDung)) = UPPER(TRIM(u.ChucVu))
           OR UPPER(TRIM(c.ChucVuApDung)) IN ('ALL STAFF', 'ALL', 'TẤT CẢ')""", nhan_vien)]
    o_chuong_trinh = ",".join(["%s"] * len(chuong_trinh))
    bai_kiem_tra = [row["MaBaiKiemTra"] for row in truy_van(
        f"""SELECT q.MaBaiKiemTra FROM bai_kiem_tra q JOIN bai_hoc l ON l.MaBaiHoc = q.MaBaiHoc
            WHERE l.FK_MaCTDT IN ({o_chuong_trinh}) AND COALESCE(q.DiemChuan, 80) <= 80
            ORDER BY q.MaBaiKiemTra LIMIT 3""", *chuong_trinh)]
    with ket_noi() as conn, conn.cursor() as cur:
        cur.execute("DELETE FROM nhan_vien_khoa_hoc WHERE user_id = %s", (nhan_vien,))
        cur.executemany("INSERT INTO nhan_vien_khoa_hoc (user_id, MaCTDT, TienDo) VALUES (%s, %s, 60)",
                        [(nhan_vien, ma) for ma in chuong_trinh])
        cur.execute("DELETE FROM ket_qua_kiem_tra WHERE user_id = %s", (nhan_vien,))
        cur.executemany("""INSERT INTO ket_qua_kiem_tra (user_id, MaBaiKiemTra, Diem, TrangThai, created_at, updated_at)
                           VALUES (%s, %s, %s, %s, UTC_TIMESTAMP() - INTERVAL 1 HOUR, UTC_TIMESTAMP() - INTERVAL 1 HOUR)""",
                        [(nhan_vien, ma, diem, "Dat" if diem >= 80 else "KhongDat")
                         for ma, diem in zip(bai_kiem_tra, (90, 80, 40))])
        cur.execute("DELETE FROM loi_nghiep_vu WHERE user_id = %s", (nhan_vien,))
    return nhan_vien


class LuongNghiepVuTest(unittest.TestCase):

    @classmethod
    def setUpClass(cls):
        sao_chep_csdl()
        cls.tien_de = chuan_bi_du_lieu()
        server = GOC_DU_AN / "vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"
        # Tắt dời ngày demo: tiền đề có mốc thời gian cố định, và tệp mốc ngày dùng chung với bản demo thật
        moi_truong = dict(os.environ, DB_DATABASE=CSDL_KIEM_THU, APP_URL=DIA_CHI, BREWLEARN_DEMO_AUTO_SHIFT="false")
        cls.may_chu = subprocess.Popen(["php", "-S", "127.0.0.1:8001", str(server)], cwd=GOC_DU_AN / "public",
                                       env=moi_truong, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL)
        cls.addClassCleanup(cls.may_chu.terminate)
        for _ in range(50):
            try:
                urllib.request.urlopen(DIA_CHI + "/login", timeout=1)
                break
            except OSError:
                time.sleep(0.2)
        # Hồ sơ Chrome riêng, tránh xung đột khi Chrome của người dùng đang mở
        ho_so_chrome = tempfile.TemporaryDirectory(prefix="brewlearn-selenium-")
        cls.addClassCleanup(ho_so_chrome.cleanup)
        tuy_chon = webdriver.ChromeOptions()
        tuy_chon.add_argument(f"--user-data-dir={ho_so_chrome.name}")
        tuy_chon.add_argument("--no-first-run")
        tuy_chon.add_argument("--no-default-browser-check")
        tuy_chon.add_argument("--window-size=1400,1000")
        if os.getenv("HEADLESS") == "1":
            tuy_chon.add_argument("--headless=new")
        cls.trinh_duyet = webdriver.Chrome(options=tuy_chon)
        cls.addClassCleanup(cls.trinh_duyet.quit)

    # ---------- Các thao tác dùng chung ----------

    def mo(self, duong_dan):
        self.trinh_duyet.get(DIA_CHI + duong_dan)

    def cho(self, dieu_kien, giay=10):
        return WebDriverWait(self.trinh_duyet, giay).until(dieu_kien)

    def tim(self, xpath):
        return self.cho(EC.presence_of_element_located((By.XPATH, xpath)))

    def bam(self, xpath):
        self.cho(EC.element_to_be_clickable((By.XPATH, xpath))).click()

    def noi_dung_trang(self):
        return self.trinh_duyet.find_element(By.TAG_NAME, "body").text

    def cho_dong_chu(self, chu):
        """Chờ trang mới tải xong và hiện dòng chữ cần kiểm tra."""
        self.cho(EC.text_to_be_present_in_element((By.TAG_NAME, "body"), chu))

    def dang_nhap(self, email):
        self.trinh_duyet.delete_all_cookies()
        self.mo("/login")
        self.tim("//input[@name='email']").send_keys(email)
        self.trinh_duyet.find_element(By.NAME, "password").send_keys(MAT_KHAU_KIEM_THU)
        self.bam("//button[@type='submit']")
        self.cho(lambda d: "/login" not in d.current_url)

    def chap_nhan_hop_thoai(self):
        hop_thoai = self.cho(EC.alert_is_present())
        noi_dung = hop_thoai.text
        hop_thoai.accept()
        return noi_dung

    def dat_gia_tri(self, xpath, gia_tri):
        """Điền ô ngày tháng theo định dạng chuẩn, không phụ thuộc ngôn ngữ của Chrome."""
        self.trinh_duyet.execute_script("arguments[0].value = arguments[1]", self.tim(xpath), gia_tri)

    # ---------- KB01. Học tập và kiểm tra ----------

    def test_kb01_nhan_vien_lam_bai_truot_roi_lam_lai_dat(self):
        nhan_vien = truy_van("SELECT id, ChucVu FROM users WHERE email = %s", NHAN_VIEN)[0]
        bai = truy_van("""SELECT q.MaBaiKiemTra FROM bai_kiem_tra q JOIN bai_hoc l ON l.MaBaiHoc = q.MaBaiHoc
                          JOIN chuong_trinh_dao_tao c ON c.MaCTDT = l.FK_MaCTDT
                          WHERE c.ChucVuApDung = %s AND EXISTS (SELECT 1 FROM cau_hoi_kiem_tra h
                          WHERE h.MaBaiKiemTra = q.MaBaiKiemTra) ORDER BY q.MaBaiKiemTra LIMIT 1""",
                       nhan_vien["ChucVu"])[0]["MaBaiKiemTra"]
        dap_an = [row["DapAnDung"].strip().upper() for row in
                  truy_van("SELECT DapAnDung FROM cau_hoi_kiem_tra WHERE MaBaiKiemTra = %s ORDER BY id", bai)]
        so_luot_truoc = truy_van("SELECT COUNT(*) AS n FROM ket_qua_kiem_tra WHERE user_id = %s AND MaBaiKiemTra = %s",
                                 nhan_vien["id"], bai)[0]["n"]
        self.dang_nhap(NHAN_VIEN)

        def lam_bai(chon_dung):
            self.mo(f"/tests/{bai}")
            for i, dung in enumerate(dap_an):
                chon = dung if chon_dung else next(k for k in "ABCD" if k != dung)
                self.bam(f"//label[contains(@class,'option-label-{i}')][.//input[@value='{chon}']]")
                if i < len(dap_an) - 1:
                    self.bam("//button[@id='btnNext']")
            self.bam("//button[contains(@onclick,'submitExam(false)')]")
            self.chap_nhan_hop_thoai()
            return self.chap_nhan_hop_thoai()

        # Bước 1: chọn toàn đáp án sai → hệ thống báo chưa đạt, 0 điểm
        ket_qua = lam_bai(chon_dung=False)
        self.assertIn("Rất tiếc", ket_qua)
        self.assertIn("0/100", ket_qua)
        # Bước 2: làm lại, chọn đúng hết → hệ thống báo đạt, 100 điểm
        ket_qua = lam_bai(chon_dung=True)
        self.assertIn("Chúc mừng", ket_qua)
        self.assertIn("100/100", ket_qua)
        # Bước 3: cả hai lượt đều được lưu thành lịch sử
        so_luot_sau = truy_van("SELECT COUNT(*) AS n FROM ket_qua_kiem_tra WHERE user_id = %s AND MaBaiKiemTra = %s",
                               nhan_vien["id"], bai)[0]["n"]
        self.assertEqual(so_luot_truoc + 2, so_luot_sau)

    # ---------- KB02. Đánh giá năng lực và phê duyệt ----------

    def cham_diem_cho_khang(self):
        self.mo("/leader/scoring")
        self.bam("//tr[.//span[normalize-space()='Nguyễn Minh Khang']]//button")
        for o, diem in zip(("score_nv", "score_kn", "score_qt", "score_pv", "score_tp"), (85, 85, 85, 85, 69)):
            o_diem = self.tim(f"//input[@id='{o}']")
            o_diem.clear()
            o_diem.send_keys(str(diem))
        self.trinh_duyet.find_element(By.NAME, "comment").send_keys("Tác phong chưa đạt yêu cầu")
        self.cho(lambda d: d.find_element(By.ID, "totalScore").text == "82.6%")

    def test_kb02_cham_diem_tra_lai_va_phe_duyet(self):
        danh_gia = truy_van("""SELECT d.id FROM danh_gia_nang_luc d JOIN users u ON u.id = d.user_id
                               WHERE u.email = %s AND d.TrangThai = 'ChoDanhGia'""", NHAN_VIEN_DUOC_DANH_GIA)[0]["id"]

        # Bước 1: trưởng ca nhập 85, 85, 85, 85, 69 → D = 82,6, chưa đạt vì tác phong dưới 70
        self.dang_nhap(TRUONG_CA)
        self.cham_diem_cho_khang()
        self.assertIn("CHƯA ĐẠT", self.tim("//span[@id='passBadge']").text)

        # Bước 2: gửi khi chưa chọn đề xuất → biểu mẫu không cho gửi
        bieu_mau = self.tim("//form[@id='scoringForm']")
        self.assertFalse(self.trinh_duyet.execute_script("return arguments[0].checkValidity()", bieu_mau))

        # Bước 3: chọn đề xuất học lại một bài rồi gửi → hồ sơ chuyển Chờ phê duyệt
        self.bam("//input[@value='hoclaibaihoc']")
        Select(self.tim("//select[@id='proposalCourse']")).select_by_index(1)
        bai_hoc = self.tim("//select[@id='proposalLessons']/option[not(@disabled)]")
        Select(self.trinh_duyet.find_element(By.ID, "proposalLessons")).select_by_value(bai_hoc.get_attribute("value"))
        self.bam("//button[@id='submitBtn']")
        self.tim("//tr[.//span[normalize-space()='Nguyễn Minh Khang']]//span[contains(.,'Chờ duyệt')]")
        ho_so = truy_van("SELECT TrangThai, Diem, IsPassed FROM danh_gia_nang_luc WHERE id = %s", danh_gia)[0]
        self.assertEqual(("ChoPheDuyet", 82.6, 0), (ho_so["TrangThai"], float(ho_so["Diem"]), ho_so["IsPassed"]))

        # Bước 4: quản lý bấm Trả lại khi bỏ trống lý do → không gửi được
        self.dang_nhap(QUAN_LY)
        self.mo("/manager/assessments")
        self.bam(f"//button[@aria-label='Xem chi tiết đánh giá {danh_gia}']")
        o_ly_do = self.tim(f"//dialog[@id='assessment-{danh_gia}']//input[@name='reason']")
        self.assertFalse(self.trinh_duyet.execute_script("return arguments[0].form.checkValidity()", o_ly_do))

        # Bước 5: trả lại có lý do → hồ sơ Trả lại, điểm giữ nguyên
        o_ly_do.send_keys("Bổ sung nhận xét về tác phong")
        self.bam(f"//dialog[@id='assessment-{danh_gia}']//button[normalize-space()='Trả lại']")
        self.cho(lambda d: truy_van("SELECT TrangThai FROM danh_gia_nang_luc WHERE id = %s",
                                    danh_gia)[0]["TrangThai"] == "TraLai")
        self.assertEqual(82.6, float(truy_van("SELECT Diem FROM danh_gia_nang_luc WHERE id = %s", danh_gia)[0]["Diem"]))

        # Bước 6: trưởng ca thấy lý do, chấm lại và gửi lại
        self.dang_nhap(TRUONG_CA)
        self.mo("/leader/scoring")
        self.assertIn("Bổ sung nhận xét về tác phong", self.noi_dung_trang())
        self.cham_diem_cho_khang()
        self.bam("//input[@value='hoclaibaihoc']")
        Select(self.tim("//select[@id='proposalCourse']")).select_by_index(1)
        bai_hoc = self.tim("//select[@id='proposalLessons']/option[not(@disabled)]")
        Select(self.trinh_duyet.find_element(By.ID, "proposalLessons")).select_by_value(bai_hoc.get_attribute("value"))
        self.bam("//button[@id='submitBtn']")
        self.tim("//tr[.//span[normalize-space()='Nguyễn Minh Khang']]//span[contains(.,'Chờ duyệt')]")

        # Bước 7: quản lý phê duyệt → kết quả chính thức, hệ thống tạo yêu cầu đào tạo lại
        self.dang_nhap(QUAN_LY)
        self.mo("/manager/assessments")
        self.bam(f"//button[@aria-label='Xem chi tiết đánh giá {danh_gia}']")
        self.bam(f"//dialog[@id='assessment-{danh_gia}']//button[contains(.,'Phê duyệt')]")
        self.cho(lambda d: truy_van("SELECT TrangThai FROM danh_gia_nang_luc WHERE id = %s",
                                    danh_gia)[0]["TrangThai"] == "DaDuyet")
        yeu_cau = truy_van("""SELECT y.tien_trinh FROM yeu_cau_dao_tao_lai y JOIN users u ON u.id = y.user_id
                              WHERE u.email = %s""", NHAN_VIEN_DUOC_DANH_GIA)
        self.assertEqual(["ChoGiao"], [row["tien_trinh"] for row in yeu_cau])

    # ---------- KB03. Giao và theo dõi đào tạo lại ----------

    def test_kb03_giao_vong_dao_tao_lai_va_ba_vai_tro_cung_theo_doi(self):
        yeu_cau = truy_van("""SELECT y.id FROM yeu_cau_dao_tao_lai y JOIN users u ON u.id = y.user_id
                              WHERE u.email = %s AND y.tien_trinh = 'ChoGiao'""", NHAN_VIEN_DUOC_DANH_GIA)
        self.assertTrue(yeu_cau, "Cần chạy KB02 trước để có yêu cầu đào tạo lại")
        yeu_cau = yeu_cau[0]["id"]

        # Bước 1: quản lý giao vòng 1 cho trưởng ca Minh Thư phụ trách
        self.dang_nhap(QUAN_LY)
        self.mo("/manager/training")
        self.bam(f"//button[@aria-label='Xem chi tiết yêu cầu {yeu_cau}']")
        hop_thoai = f"//dialog[@id='training-{yeu_cau}']"
        Select(self.tim(f"{hop_thoai}//select[@name='nguoi_phu_trach_id']")).select_by_visible_text("Trần Minh Thư")
        self.dat_gia_tri(f"{hop_thoai}//input[@name='han_hoan_thanh']", (date.today() + timedelta(days=7)).isoformat())
        self.dat_gia_tri(f"{hop_thoai}//input[@name='ngay_danh_gia_lai']", (date.today() + timedelta(days=14)).isoformat())
        o_noi_dung = self.tim(f"{hop_thoai}//textarea[@name='ghi_chu_thuc_hien']")
        o_noi_dung.clear()
        o_noi_dung.send_keys("Học lại bài được đề xuất và làm lại bài kiểm tra")
        self.bam(f"{hop_thoai}//button[contains(.,'Lưu & giao đào tạo lại')]")
        self.cho(lambda d: truy_van("SELECT tien_trinh FROM yeu_cau_dao_tao_lai WHERE id = %s",
                                    yeu_cau)[0]["tien_trinh"] == "DangThucHien")
        self.assertEqual(1, truy_van("SELECT COUNT(*) AS n FROM retraining_rounds WHERE request_id = %s",
                                     yeu_cau)[0]["n"])

        # Bước 2: nhân viên nhận thông báo và thấy tiến độ ở mục Học lại
        self.dang_nhap(NHAN_VIEN_DUOC_DANH_GIA)
        self.mo("/notifications")
        self.assertIn("Kế hoạch đào tạo lại", self.noi_dung_trang())
        self.mo("/courses#retraining-progress")
        tien_do = self.cho(EC.visibility_of_element_located((By.ID, "retraining-progress"))).text
        self.assertIn("Đang học lại", tien_do)
        self.assertIn("Trần Minh Thư", tien_do)
        self.assertIn("đã hoàn thành 0/1 bài", tien_do)

        # Bước 3: trưởng ca thấy vòng được giao nhưng chưa được xác nhận khi nhân viên chưa học xong
        self.dang_nhap(TRUONG_CA)
        self.mo("/training/my-assignments")
        vong_cua_khang = self.tim("//article[.//caption[contains(.,'Nguyễn Minh Khang')]]")
        self.assertIn("Thực hiện sau khi nhân viên hoàn thành học lại và kiểm tra", vong_cua_khang.text)
        self.assertFalse(vong_cua_khang.find_elements(
            By.XPATH, ".//button[contains(.,'Xác nhận đã hoàn thành nội dung được giao')]"))

    # ---------- KB04. Giới hạn ba vòng đào tạo lại ----------

    def test_kb04_chua_dat_sau_vong_3_thi_dung_dao_tao_lai(self):
        danh_gia, yeu_cau = self.tien_de["danh_gia_vong_3"], self.tien_de["yeu_cau_vong_3"]

        # Bước 1: quản lý phê duyệt kết quả đánh giá lại vòng 3 là Chưa đạt
        self.dang_nhap(QUAN_LY)
        self.mo("/manager/assessments")
        self.bam(f"//button[@aria-label='Xem chi tiết đánh giá {danh_gia}']")
        self.bam(f"//dialog[@id='assessment-{danh_gia}']//button[contains(.,'Phê duyệt')]")
        self.cho(lambda d: truy_van("SELECT tien_trinh FROM yeu_cau_dao_tao_lai WHERE id = %s",
                                    yeu_cau)[0]["tien_trinh"] == "DungDaoTao")

        # Bước 2: màn hình đào tạo lại báo đã đạt giới hạn, không còn nút giao vòng 4
        self.mo("/manager/training")
        self.bam(f"//button[@aria-label='Xem chi tiết yêu cầu {yeu_cau}']")
        hop_thoai = self.tim(f"//dialog[@id='training-{yeu_cau}']")
        self.assertIn("Đã đạt giới hạn 3 vòng đào tạo lại", hop_thoai.text)
        self.assertFalse(hop_thoai.find_elements(By.XPATH, ".//button[contains(.,'Lưu & giao đào tạo lại')]"))

        # Bước 3: quản lý nhận thông báo, hệ thống không tạo thêm yêu cầu hoặc vòng mới
        self.mo("/notifications")
        self.assertIn("Đào tạo lại đã đạt giới hạn", self.noi_dung_trang())
        self.assertEqual(3, truy_van("SELECT COUNT(*) AS n FROM retraining_rounds WHERE request_id = %s",
                                     yeu_cau)[0]["n"])

    # ---------- KB05. Phân quyền ----------

    def test_kb05_moi_vai_tro_chi_mo_duoc_chuc_nang_cua_minh(self):
        # Bước 1: nhân viên mở trang của trưởng ca và quản lý → bị chặn
        self.dang_nhap(NHAN_VIEN)
        self.mo("/leader/dashboard")
        self.assertIn("Không thể truy cập nội dung này", self.noi_dung_trang())
        self.mo("/manager/dashboard")
        self.assertIn("Trang này cần tài khoản quản lý cửa hàng", self.noi_dung_trang())

        # Bước 2: trưởng ca mở trang quản lý và hồ sơ nhân viên cửa hàng khác → bị chặn
        self.dang_nhap(TRUONG_CA)
        self.mo("/manager/staff")
        self.assertIn("Trang này cần tài khoản quản lý cửa hàng", self.noi_dung_trang())
        cua_hang_khac = truy_van("""SELECT id, name FROM users WHERE cua_hang_id <> (SELECT cua_hang_id FROM users
                                    WHERE email = %s) AND ChucVu IN ('Barista', 'Thu ngân', 'Phục vụ') LIMIT 1""",
                                 TRUONG_CA)[0]
        self.mo(f"/leader/staff/{cua_hang_khac['id']}")
        noi_dung = self.noi_dung_trang()
        self.assertIn("Not Found", noi_dung)
        self.assertNotIn(cua_hang_khac["name"], noi_dung)

        # Bước 3: người chưa đăng nhập bị chuyển về trang đăng nhập
        self.trinh_duyet.delete_all_cookies()
        self.mo("/manager/dashboard")
        self.cho(EC.url_contains("/login"))

    # ---------- KB06. Điều kiện tạo lịch đánh giá ----------

    def tao_lich(self, lua_chon):
        """Chọn nhân viên, tiêu chuẩn, thời gian ngày mai rồi gửi biểu mẫu tạo lịch."""
        bieu_mau = "//form[contains(@action,'create-schedule')]"
        self.trinh_duyet.execute_script("arguments[0].selected = true", lua_chon)
        Select(self.tim(f"{bieu_mau}//select[@name='MaCTDT']")).select_by_index(1)
        self.dat_gia_tri(f"{bieu_mau}//input[@name='NgayDanhGia']", f"{date.today() + timedelta(days=1)}T09:00")
        self.bam(f"{bieu_mau}//button[@type='submit']")

    def test_kb06_chi_tao_lich_khi_nhan_vien_du_dieu_kien(self):
        danh_sach = "//form[contains(@action,'create-schedule')]//select[@name='user_id']/option"
        self.dang_nhap(TRUONG_CA)
        self.mo("/leader/dashboard")

        # Bước 1: nhân viên chưa học xong hoặc chưa đạt bài kiểm tra bị khóa trong danh sách chọn
        chua_du = self.tim(f"{danh_sach}[@disabled and contains(.,'Chưa đủ điều kiện')]")
        ma_chua_du = int(chua_du.get_attribute("value"))
        so_lich_truoc = truy_van("SELECT COUNT(*) AS n FROM danh_gia_nang_luc WHERE user_id = %s", ma_chua_du)[0]["n"]

        # Bước 2: cố gửi yêu cầu cho nhân viên này (bỏ khóa trên trình duyệt) → máy chủ vẫn từ chối
        self.trinh_duyet.execute_script("arguments[0].disabled = false", chua_du)
        self.tao_lich(chua_du)
        self.cho_dong_chu("Chưa đủ điều kiện đánh giá:")
        self.assertEqual(so_lich_truoc, truy_van("SELECT COUNT(*) AS n FROM danh_gia_nang_luc WHERE user_id = %s",
                                                 ma_chua_du)[0]["n"])

        # Bước 3: nhân viên đủ điều kiện → tạo lịch thành công, hồ sơ ở trạng thái Chờ đánh giá
        du_dieu_kien = self.tim(f"{danh_sach}[not(@disabled) and @value!='']")
        ma_du = int(du_dieu_kien.get_attribute("value"))
        self.tao_lich(du_dieu_kien)
        self.cho_dong_chu("Đã xếp lịch và phát thông báo tới nhân viên thành công")
        self.assertEqual(1, truy_van("""SELECT COUNT(*) AS n FROM danh_gia_nang_luc
                                        WHERE user_id = %s AND TrangThai = 'ChoDanhGia'""", ma_du)[0]["n"])

    # ---------- KB07. Ghi nhận lỗi nghiệp vụ, tính và hiển thị cảnh báo ----------

    def test_kb07_tinh_va_hien_thi_canh_bao_nguy_co(self):
        nhan_vien = self.tien_de["nhan_vien_canh_bao"]

        # Bước 1: trưởng ca ghi nhận một lỗi nghiêm trọng qua giao diện
        self.dang_nhap(TRUONG_CA)
        self.mo("/leader/dashboard")
        self.bam("//a[contains(@onclick,'openIncidentModal')]")
        bieu_mau = "//form[contains(@action,'record-incident')]"
        Select(self.tim(f"{bieu_mau}//select[@name='user_id']")).select_by_value(str(nhan_vien))
        Select(self.tim(f"{bieu_mau}//select[@name='loai_loi']")).select_by_value("Thái độ phục vụ")
        Select(self.tim(f"{bieu_mau}//select[@name='muc_do']")).select_by_value("NghiemTrong")
        self.tim(f"{bieu_mau}//textarea[@name='mo_ta']").send_keys("Lớn tiếng với khách khi đông khách")
        self.bam(f"{bieu_mau}//button[@type='submit']")
        self.cho_dong_chu("Đã ghi nhận lỗi nghiệp vụ")

        # Bước 2: chạy chương trình tính cảnh báo (bình thường chạy tự động hằng đêm)
        lenh = subprocess.run(["php", "artisan", "brewlearn:calculate-risk"], cwd=GOC_DU_AN,
                              env=dict(os.environ, DB_DATABASE=CSDL_KIEM_THU), capture_output=True)
        self.assertEqual(0, lenh.returncode)

        # Bước 3: quản lý mở hồ sơ, kết quả trùng với kết quả tính tay: 5/10 điểm → 50,0/100, mức Cao
        self.dang_nhap(QUAN_LY)
        self.mo(f"/manager/staff/{nhan_vien}")
        self.bam("//*[@id='tab-risk']")
        noi_dung = self.cho(EC.visibility_of_element_located((By.ID, "panel-risk"))).text
        for mong_doi in ("Cao", "Điểm cảnh báo 50.0/100", "Đã làm 3/",
                         "Điểm quiz trung bình từ 60 đến dưới 80",
                         "Tiến độ đào tạo từ 50% đến dưới 80%",
                         "Có 1 lỗi nghiệp vụ trong 30 ngày gần nhất, trong đó 1 lỗi nghiêm trọng"):
            self.assertIn(mong_doi, noi_dung)
        self.assertNotIn("Kết quả đánh giá năng lực gần nhất được duyệt là Chưa đạt", noi_dung)
        # S2 = 1 thấp hơn S1 = 2 nên không được tính thêm và không hiện thành lý do riêng
        self.assertNotIn("Có bài kiểm tra chưa đạt, chiếm dưới 50% số bài đã làm", noi_dung)


if __name__ == "__main__":
    unittest.main(verbosity=2)
