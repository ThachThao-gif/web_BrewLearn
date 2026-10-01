<?php

namespace App\Http\Controllers;

use App\Models\AssessmentReminder;
use App\Models\CourseProgress;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ManagerController extends Controller
{
    private function storeId(): int
    {
        abort_unless(in_array(mb_strtolower(trim(auth()->user()->ChucVu ?? '')), ['quản lý cửa hàng', 'quan ly cua hang', 'manager']) && auth()->user()->cua_hang_id, 403);

        return (int) auth()->user()->cua_hang_id;
    }

    private function members(): Builder
    {
        return User::query()->where('cua_hang_id', $this->storeId())->where('id', '!=', auth()->id())
            ->whereRaw("LOWER(TRIM(COALESCE(ChucVu, ''))) NOT IN (?, ?, ?, ?, ?)", ['quản lý cửa hàng', 'quan ly cua hang', 'quản lý vùng', 'hr', 'hr đào tạo']);
    }

    private function context(Request $request): array
    {
        $shifts = DB::table('ca_lam_viec')->where('cua_hang_id', $this->storeId())->orderBy('ten_ca')->get();
        $query = $this->members();
        if ($request->filled('shift')) {
            $request->shift === 'none' ? $query->whereNull('ca_lam_viec_id') : $query->where('ca_lam_viec_id', $request->shift);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->string('q').'%');
        }
        $staff = $query->get();
        $predictions = DB::table('du_doan_nguy_co')->whereIn('user_id', $staff->modelKeys())
            ->where(fn ($q) => $q->where('cua_hang_id', $this->storeId())->orWhereNull('cua_hang_id'))
            ->orderByDesc('ngay_du_doan')->orderByDesc('id')->get()->unique('user_id')->keyBy('user_id');
        $evaluations = DB::table('danh_gia_nang_luc')->whereIn('user_id', $staff->modelKeys())->whereIn('TrangThai', ['ChoPheDuyet', 'DaDuyet', 'HoanThanh', 'TraLai'])->whereNotNull('Diem')->orderByDesc('NgayDanhGia')->orderByDesc('id')->get();
        $progress = app(CourseProgress::class);
        foreach ($staff as $person) {
            $courses = $progress->assignedCourses($person->ChucVu ?? '');
            $person->training_progress = $courses->isEmpty() ? 0 : round($courses->avg(fn ($course) => $progress->sync($person->id, $course->MaCTDT)), 1);
            $person->prediction = $predictions->get($person->id);
            $person->risk = $person->prediction->muc_canh_bao ?? 'ChuaDuDuLieu';
            $person->last_evaluation = $evaluations->firstWhere('user_id', $person->id);
            $person->shift_name = $shifts->firstWhere('id', $person->ca_lam_viec_id)?->ten_ca ?? 'Chưa phân ca';
        }
        app(AssessmentReminder::class)->decorate($staff);
        $staff = $staff->sortBy(fn ($p) => ['Cao' => 0, 'TrungBinh' => 1, 'Thap' => 2, 'ChuaDuDuLieu' => 3][$p->risk] ?? 3)->values();
        $store = DB::table('cua_hang')->find($this->storeId());

        return compact('staff', 'shifts', 'store');
    }

    private function proposalData(): array
    {
        return ['proposalOwners' => $this->members()->get(), 'proposalCourses' => DB::table('chuong_trinh_dao_tao')->get(), 'proposalLessons' => DB::table('bai_hoc')->get()];
    }

    public function dashboard(Request $request): View
    {
        Artisan::call('brewlearn:remind-assessments', ['--store' => $this->storeId()]);
        $data = $this->context($request);
        $ids = $this->members()->assessmentEligible()->whereIn('id', $data['staff']->modelKeys())->pluck('id');
        $data['pendingCount'] = DB::table('danh_gia_nang_luc')->whereIn('user_id', $ids)->where('TrangThai', 'ChoPheDuyet')->count();
        $data['trainingCount'] = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $ids)->where('trang_thai', 'DaDuyet')->count();

        return view('manager.dashboard', $data);
    }

    public function staff(Request $request): View
    {
        return view('manager.staff', $this->context($request));
    }

    public function profile(Request $request, int $id): View
    {
        $person = $this->members()->findOrFail($id);
        $data = $this->context(new Request);
        $person = $data['staff']->firstWhere('id', $id) ?? $person;
        $progress = app(CourseProgress::class);
        $courses = $progress->assignedCourses($person->ChucVu ?? '')->map(function ($course) use ($person, $progress) {
            $course->progress = $progress->sync($person->id, $course->MaCTDT);
            $lessons = DB::table('bai_hoc')->where('FK_MaCTDT', $course->MaCTDT)->pluck('MaBaiHoc');
            $records = DB::table('user_lesson_progresses')->where('user_id', $person->id)->whereIn('lesson_id', $lessons)->get();
            $course->total = $lessons->count();
            $course->completed = $records->filter(fn ($p) => $p->is_completed || $p->progress_percent >= 95)->count();
            $course->hasDetails = $records->isNotEmpty();

            return $course;
        });
        $quizzes = DB::table('ket_qua_kiem_tra as k')->leftJoin('bai_kiem_tra as b', 'k.MaBaiKiemTra', '=', 'b.MaBaiKiemTra')->where('k.user_id', $id)->select('k.*', 'b.TenBaiKiemTra', 'b.DiemChuan')->orderByDesc('k.id')->get();
        $assessments = DB::table('danh_gia_nang_luc')->where('user_id', $id)->orderByDesc('id')->get();
        $training = DB::table('yeu_cau_dao_tao_lai')->where('user_id', $id)->orderByDesc('id')->get();

        return view('manager.profile', array_merge($data, $this->proposalData(), compact('person', 'courses', 'quizzes', 'assessments', 'training')));
    }

    public function createShift(Request $request): RedirectResponse
    {
        $storeId = $this->storeId();
        $data = $request->validate(['ten_ca' => ['required', 'string', 'max:100', Rule::unique('ca_lam_viec')->where('cua_hang_id', $storeId)], 'bat_dau' => ['required', 'date_format:H:i'], 'ket_thuc' => ['required', 'date_format:H:i']]);
        DB::table('ca_lam_viec')->insert($data + ['cua_hang_id' => $storeId, 'created_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'Đã tạo ca làm việc.');
    }

    public function assignShift(Request $request, int $id): RedirectResponse
    {
        $person = $this->members()->findOrFail($id);
        $data = $request->validate(['ca_lam_viec_id' => ['nullable', Rule::exists('ca_lam_viec', 'id')->where('cua_hang_id', $this->storeId())]]);
        DB::table('users')->where('id', $person->id)->update(['ca_lam_viec_id' => $data['ca_lam_viec_id'] ?? null, 'updated_at' => now()]);

        return back()->with('success', 'Đã cập nhật ca làm việc.');
    }

    public function updateEmployment(Request $request, int $id): RedirectResponse
    {
        $person = $this->members()->findOrFail($id);
        $data = $request->validate(['employment_status' => ['required', Rule::in(array_keys(AssessmentReminder::EMPLOYMENT_LABELS))]]);
        DB::table('users')->where('id', $person->id)->update($data + ['updated_at' => now()]);

        return back()->with('success', 'Đã cập nhật trạng thái làm việc. Nhân viên tạm ngưng hoặc nghỉ việc không được tính vào nhắc hạn.');
    }

    public function assessments(Request $request): View
    {
        $data = $this->context($request);
        $statusTab = $request->query('status') === 'approved' ? 'approved' : 'pending';
        $query = DB::table('danh_gia_nang_luc')->whereIn('user_id', $this->members()->assessmentEligible()->whereIn('id', $data['staff']->modelKeys())->pluck('id'));
        $statusCounts = (clone $query)->selectRaw('TrangThai, COUNT(*) as total')->groupBy('TrangThai')->pluck('total', 'TrangThai');
        $tabCounts = ['pending' => ($statusCounts['ChoPheDuyet'] ?? 0) + ($statusCounts['TraLai'] ?? 0), 'approved' => ($statusCounts['DaDuyet'] ?? 0) + ($statusCounts['HoanThanh'] ?? 0)];
        $assessments = $query->whereIn('TrangThai', $statusTab === 'approved' ? ['DaDuyet', 'HoanThanh'] : ['ChoPheDuyet', 'TraLai'])
            ->orderByRaw('COALESCE(NgayDanhGia, created_at, updated_at) ASC')->orderBy('id')->get();

        return view('manager.assessments', array_merge($data, $this->proposalData(), compact('assessments', 'statusTab', 'tabCounts')));
    }

    public function review(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['action' => ['required', Rule::in(['approve', 'return'])], 'reason' => ['required_if:action,return', 'nullable', 'string', 'max:2000']]);
        DB::transaction(function () use ($id, $data) {
            $assessment = DB::table('danh_gia_nang_luc')->whereIn('user_id', $this->members()->assessmentEligible()->pluck('id'))->lockForUpdate()->find($id);
            abort_unless($assessment, 404);
            abort_unless($assessment->TrangThai === 'ChoPheDuyet', 409, 'Đánh giá đã được xử lý.');
            $approve = $data['action'] === 'approve';
            if ($approve && ($assessment->IsPassed === null || (! $assessment->IsPassed && ! $assessment->ChiTietDeXuat))) {
                throw ValidationException::withMessages(['assessment' => 'Đánh giá chưa có đủ kết quả theo 5 tiêu chí hoặc nội dung đề xuất. Vui lòng trả lại để trưởng ca bổ sung.']);
            }
            DB::table('danh_gia_nang_luc')->where('id', $id)->update(['TrangThai' => $approve ? 'DaDuyet' : 'TraLai', 'nguoi_duyet_id' => auth()->id(), 'ngay_duyet' => now(), 'ly_do_tra_lai' => $approve ? null : $data['reason'], 'updated_at' => now()]);
            $priorPlan = DB::table('yeu_cau_dao_tao_lai')->where('danh_gia_lai_id', $id)->first();
            if ($approve && $priorPlan) {
                DB::table('yeu_cau_dao_tao_lai')->where('id', $priorPlan->id)->update(['trang_thai' => $assessment->IsPassed ? 'HoanThanh' : 'DaDuyet', 'tien_trinh' => $assessment->IsPassed ? 'HoanTat' : 'ChoGiao', 'ket_qua_sau_dao_tao_lai' => $assessment->IsPassed ? 'Dat' : 'ChuaDat', 'ke_hoach' => $assessment->IsPassed ? $priorPlan->ke_hoach : $assessment->ChiTietDeXuat, 'updated_at' => now()]);
            } elseif ($approve && ! $assessment->IsPassed && $assessment->ChiTietDeXuat) {
                DB::table('yeu_cau_dao_tao_lai')->insert(['user_id' => $assessment->user_id, 'assessment_id' => $id, 'nguoi_de_xuat_id' => $assessment->NguoiDanhGia_id, 'nguoi_duyet_id' => auth()->id(), 'nguon_de_xuat' => 'Trưởng ca đề xuất', 'ly_do' => 'Đánh giá chưa đạt: '.($assessment->NhanXet ?? ''), 'ke_hoach' => $assessment->ChiTietDeXuat, 'trang_thai' => 'DaDuyet', 'tien_trinh' => 'ChoGiao', 'ket_qua_sau_dao_tao_lai' => 'ChuaCoKetQua', 'ngay_de_xuat' => now(), 'ngay_duyet' => now(), 'created_at' => now(), 'updated_at' => now()]);
            }
            $message = $approve ? 'Quản lý đã duyệt đánh giá đào tạo của bạn.' : 'Quản lý trả lại đánh giá để bổ sung: '.$data['reason'];
            $this->notify($assessment->user_id, 'Kết quả duyệt đánh giá', $message, 'assessment-review-'.$id.'-'.$data['action'].'-employee');
            if ($assessment->NguoiDanhGia_id) {
                $this->notify($assessment->NguoiDanhGia_id, 'Kết quả duyệt đánh giá', $message, 'assessment-review-'.$id.'-'.$data['action'].'-leader');
            }
        });

        return back()->with('success', $data['action'] === 'approve' ? 'Đã duyệt đánh giá. Đề xuất chưa đạt được chuyển sang mục Đào tạo lại.' : 'Đã trả lại đánh giá kèm lý do.');
    }

    public function training(Request $request): View
    {
        $data = $this->context($request);
        $training = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $data['staff']->modelKeys())->orderByDesc('id')->get();

        return view('manager.training', array_merge($data, $this->proposalData(), compact('training')));
    }

    private function plan(int $id): object
    {
        return DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $this->members()->pluck('id'))->find($id) ?? abort(404);
    }

    public function reviewTraining(Request $request, int $id): RedirectResponse
    {
        $plan = $this->plan($id);
        $data = $request->validate(['action' => ['required', Rule::in(['approve', 'reject'])], 'reason' => ['required_if:action,reject', 'nullable', 'string', 'max:2000']]);
        abort_unless($plan->trang_thai === 'ChoDuyet', 409);
        DB::table('yeu_cau_dao_tao_lai')->where('id', $id)->where('trang_thai', 'ChoDuyet')->update(['trang_thai' => $data['action'] === 'approve' ? 'DaDuyet' : 'TuChoi', 'nguoi_duyet_id' => auth()->id(), 'ngay_duyet' => now(), 'ghi_chu_thuc_hien' => $data['reason'] ?? null, 'updated_at' => now()]);

        return back()->with('success', 'Đã xử lý đề xuất đào tạo lại.');
    }

    public function assignTraining(Request $request, int $id): RedirectResponse
    {
        $plan = $this->plan($id);
        abort_unless($plan->trang_thai === 'DaDuyet' && in_array($plan->tien_trinh, ['ChoGiao', 'DangThucHien']), 409);
        $data = $request->validate(['nguoi_phu_trach_id' => ['required', Rule::in($this->members()->where('id', '!=', $plan->user_id)->pluck('id')->all())], 'han_hoan_thanh' => ['required', 'date', 'after_or_equal:today'], 'ngay_danh_gia_lai' => ['required', 'date', 'after_or_equal:han_hoan_thanh'], 'ghi_chu_thuc_hien' => ['required', 'string', 'max:5000']]);
        DB::table('yeu_cau_dao_tao_lai')->where('id', $id)->update($data + ['tien_trinh' => 'DangThucHien', 'updated_at' => now()]);
        foreach (array_unique([$plan->user_id, (int) $data['nguoi_phu_trach_id']]) as $userId) {
            $this->notify($userId, 'Kế hoạch đào tạo lại', 'Hạn hoàn thành: '.$data['han_hoan_thanh'].'. Nội dung: '.$data['ghi_chu_thuc_hien'], 'training-assigned-'.$id.'-'.$userId.'-'.now()->timestamp);
        }

        return back()->with('success', 'Đã giao đào tạo lại và gửi thông báo cho nhân viên, người phụ trách.');
    }

    public function reassess(Request $request, int $id): RedirectResponse
    {
        $leaders = $this->members()->whereRaw('LOWER(TRIM(ChucVu)) IN (?, ?, ?)', ['trưởng ca', 'truong_ca', 'leader'])->pluck('id');
        $data = $request->validate(['assessor_id' => ['required', Rule::in($leaders->all())], 'assessment_date' => ['required', 'date', 'after_or_equal:today'], 'assessment_time' => ['required', 'date_format:H:i'], 'criteria' => ['required', 'string', 'max:2000']]);
        DB::transaction(function () use ($id, $data) {
            $plan = DB::table('yeu_cau_dao_tao_lai')->whereIn('user_id', $this->members()->assessmentEligible()->pluck('id'))->lockForUpdate()->find($id);
            abort_unless($plan, 404);
            abort_unless($plan->trang_thai === 'DaDuyet' && $plan->tien_trinh === 'DangThucHien', 409);
            $assessmentId = DB::table('danh_gia_nang_luc')->insertGetId(['user_id' => $plan->user_id, 'NguoiDanhGia_id' => $data['assessor_id'], 'TieuChuan' => 'Đánh giá lại sau đào tạo #'.$id.': '.$data['criteria'], 'NgayHen' => $data['assessment_date'].' '.$data['assessment_time'].':00', 'TrangThai' => 'ChoDanhGia', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('yeu_cau_dao_tao_lai')->where('id', $id)->update(['danh_gia_lai_id' => $assessmentId, 'ngay_danh_gia_lai' => $data['assessment_date'], 'tien_trinh' => 'ChoDanhGiaLai', 'updated_at' => now()]);
            foreach ([$plan->user_id, (int) $data['assessor_id']] as $userId) {
                $this->notify($userId, 'Lịch đánh giá lại', 'Đánh giá lại ngày '.$data['assessment_date'].' lúc '.$data['assessment_time'].'. '.$data['criteria'], 'training-reassess-'.$assessmentId.'-'.$userId);
            }
        });

        return back()->with('success', 'Đã tạo lịch cho trưởng ca. Kế hoạch chỉ hoàn tất khi kết quả đánh giá lại được duyệt và đạt.');
    }

    public function notifications(Request $request): View
    {
        Artisan::call('brewlearn:remind-assessments', ['--store' => $this->storeId()]);
        $data = $this->context($request);
        $data['managerNotifications'] = DB::table('notifications')->where('user_id', auth()->id())->whereNotIn('type', ['risk', 'prediction'])->orderByDesc('id')->paginate(20);

        return view('manager.notifications', $data);
    }

    private function notify(int $userId, string $title, string $message, string $key): void
    {
        DB::table('notifications')->insertOrIgnore(['user_id' => $userId, 'title' => $title, 'message' => $message, 'type' => 'training', 'is_read' => false, 'event_key' => $key, 'created_at' => now(), 'updated_at' => now()]);
    }
}
