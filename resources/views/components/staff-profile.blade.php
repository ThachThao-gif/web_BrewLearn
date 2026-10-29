{{-- Khung hồ sơ nhân viên dùng chung cho trưởng ca và quản lý cửa hàng.
     Trang gọi tự mở thẻ bao có x-data="{ tab: 'info' }" và có thể gắn thêm panel riêng sau khung này. --}}
@php
    $extraTabs = $extraTabs ?? [];
    $showRisk = $showRisk ?? false;
    $isWorking = ($staff->employment_status ?? 'DangLam') === 'DangLam';
    $employmentLabel = $isWorking ? 'Đang làm việc' : (\App\Models\AssessmentReminder::EMPLOYMENT_LABELS[$staff->employment_status] ?? $staff->employment_status);
@endphp
@php
    $joinedAt = $staff->joined_at ? \Carbon\Carbon::parse($staff->joined_at) : null;
    $tenureMonths = $joinedAt ? (int) $joinedAt->diffInMonths(now()) : null;
    $shiftLabel = $shift ? $shift->ten_ca.' ('.substr($shift->bat_dau, 0, 5).' – '.substr($shift->ket_thuc, 0, 5).')' : null;
    $idDigits = preg_replace('/\D/', '', (string) $staff->id_card);
    $idCard = strlen($idDigits) >= 4 ? str_repeat('•', strlen($idDigits) - 4).substr($idDigits, -4) : ($staff->id_card ? 'Đã lưu (ẩn)' : null);
    $quizFailed = $quizzes->filter(fn ($quiz) => $quiz->Diem < ($quiz->DiemChuan ?? 80))->count();
    if (! $latestAssessment) {
        $assessmentResult = null;
    } elseif ($latestAssessment->TrangThai === 'ChoPheDuyet') {
        $assessmentResult = 'Chờ duyệt';
    } elseif ($latestAssessment->IsPassed !== null) {
        $assessmentResult = $latestAssessment->IsPassed ? 'Đạt' : 'Chưa đạt';
    } else {
        $assessmentResult = 'Đã duyệt';
    }
    $resultTone = $assessmentResult === 'Đạt' ? 'text-emerald-700' : ($assessmentResult === 'Chưa đạt' ? 'text-rose-700' : 'text-amber-700');
    $dueTones = ['Quá hạn' => 'text-rose-700', 'Đến hạn' => 'text-rose-700', 'Sắp đến hạn' => 'text-amber-700'];
    $statusStyles = [
        'ChoDanhGia' => ['Chờ đánh giá', 'border-blue-200 bg-blue-50 text-blue-700'],
        'ChoPheDuyet' => ['Chờ quản lý phê duyệt', 'border-amber-300 bg-amber-50 text-amber-800'],
        'TraLai' => ['Bị trả lại', 'border-rose-300 bg-rose-50 text-rose-700'],
        'DaDuyet' => ['Đã phê duyệt', 'border-emerald-300 bg-emerald-50 text-emerald-700'],
        'HoanThanh' => ['Đã phê duyệt', 'border-emerald-300 bg-emerald-50 text-emerald-700'],
    ];

    $workInfo = [
        'Mã nhân viên' => 'NV-'.str_pad($staff->id, 3, '0', STR_PAD_LEFT),
        'Vị trí công việc' => $staff->ChucVu,
        'Cấp độ' => $staff->CapDo ? 'Cấp '.$staff->CapDo : null,
        'Nơi làm việc' => $store->ten_cua_hang ?? $staff->ChiNhanh,
        'Ca làm việc' => $shiftLabel,
        'Trạm làm việc' => $staff->tram_lam_viec,
        'Ngày vào làm' => $joinedAt?->format('d/m/Y'),
        'Thâm niên' => $tenureMonths === null ? null : ($tenureMonths < 1 ? 'Dưới 1 tháng' : $tenureMonths.' tháng'),
    ];
    $personalInfo = [
        'Họ và tên' => $staff->name,
        'Tên hiển thị' => $staff->name_tag,
        'Ngày sinh' => $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('d/m/Y') : null,
        'Điện thoại' => $staff->phone,
        'Email' => $staff->email,
        'Email phụ' => $staff->secondary_email,
        'Số CCCD' => $idCard,
        'Liên hệ khẩn cấp' => $staff->emergency_contact ? $staff->emergency_contact.($staff->emergency_phone ? ' · '.$staff->emergency_phone : '') : $staff->emergency_phone,
    ];
    $tabs = [
        'info' => ['Thông tin', 'fa-id-card'],
        'courses' => ['Khóa học', 'fa-book-open'],
        'quizzes' => ['Lượt quiz', 'fa-list-check'],
        'assessments' => ['Đánh giá', 'fa-clipboard-check'],
    ];
    $tabCounts = ['courses' => $courses->count(), 'quizzes' => $quizzes->count(), 'assessments' => $assessments->count()];
    foreach ($extraTabs as $key => [$label, $icon, $count]) {
        $tabs[$key] = [$label, $icon];
        if ($count !== null) {
            $tabCounts[$key] = $count;
        }
    }
@endphp
    <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-coffee-700 hover:text-coffee-900"><i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>{{ $backLabel }}</a>

    {{-- Thẻ tóm tắt nhân viên --}}
    <section class="overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm">
        <div class="flex flex-col gap-5 p-6 md:flex-row md:items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->name) }}&background=6E4E30&color=fff&size=192" alt="{{ $staff->name }}" class="h-20 w-20 shrink-0 rounded-full ring-4 ring-coffee-100">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-coffee-900">{{ $staff->name }}</h1>
                    <span class="rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $isWorking ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-slate-300 bg-slate-50 text-slate-600' }}">{{ $employmentLabel }}</span>
                </div>
                <p class="mt-1 text-sm text-slate-500">{{ $workInfo['Mã nhân viên'] }} · {{ $staff->ChucVu ?? 'Nhân viên' }}{{ $shift ? ' · '.$shift->ten_ca : '' }}</p>
                <ul class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-sm text-slate-600">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-envelope text-coffee-600" aria-hidden="true"></i>{{ $staff->email }}</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-phone text-coffee-600" aria-hidden="true"></i>{{ $staff->phone ?: 'Chưa cập nhật' }}</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-store text-coffee-600" aria-hidden="true"></i>{{ $store->ten_cua_hang ?? $staff->ChiNhanh ?? 'Chưa cập nhật' }}</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-mug-hot text-coffee-600" aria-hidden="true"></i>{{ $staff->tram_lam_viec ?: 'Chưa phân trạm' }}</li>
                </ul>
            </div>
        </div>
        <dl class="grid grid-cols-2 border-t border-coffee-100 bg-coffee-50/50 {{ $showRisk ? 'md:grid-cols-3 xl:grid-cols-6' : 'md:grid-cols-5' }} md:divide-x divide-coffee-100">
            <div class="px-5 py-4">
                <dt class="text-xs text-slate-500">Tiến độ đào tạo</dt>
                <dd class="mt-1 text-xl font-extrabold text-coffee-900">{{ $learning['progress'] }}%</dd>
                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-coffee-100"><div class="h-full rounded-full bg-amber_brand" style="width: {{ min($learning['progress'], 100) }}%"></div></div>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs text-slate-500">Khóa đã hoàn thành</dt>
                <dd class="mt-1 text-xl font-extrabold text-coffee-900">{{ $learning['completed'] }}/{{ $learning['total'] }}</dd>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs text-slate-500">Quiz gần nhất</dt>
                <dd class="mt-1 text-xl font-extrabold text-coffee-900">{{ $quizzes->first() ? round($quizzes->first()->Diem, 1) : '—' }}</dd>
                <p class="text-xs text-slate-500">{{ $quizzes->count() }} lượt · <span class="{{ $quizFailed ? 'text-rose-600' : '' }}">{{ $quizFailed }} không đạt</span></p>
            </div>
            <div class="px-5 py-4">
                <dt class="text-xs text-slate-500">Đánh giá gần nhất</dt>
                @if($latestAssessment)
                    <dd class="mt-1 text-xl font-extrabold text-coffee-900">{{ round($latestAssessment->Diem, 1) }}</dd>
                    <p class="text-xs"><span class="font-semibold {{ $resultTone }}">{{ $assessmentResult }}</span> <span class="text-slate-500">· {{ \Carbon\Carbon::parse($latestAssessment->NgayDanhGia ?? $latestAssessment->updated_at)->format('d/m/Y') }}</span></p>
                @else
                    <dd class="mt-1 text-sm font-semibold text-slate-400">Chưa đánh giá</dd>
                @endif
            </div>
            <div class="{{ $showRisk ? '' : 'col-span-2 md:col-span-1' }} px-5 py-4">
                <dt class="text-xs text-slate-500">Hạn đánh giá 3 tháng</dt>
                <dd class="mt-1 text-xl font-extrabold text-coffee-900">{{ $staff->due_at?->format('d/m/Y') ?? '—' }}</dd>
                <p class="text-xs font-semibold {{ $dueTones[$staff->due_status] ?? 'text-slate-500' }}">{{ $staff->due_status }}</p>
            </div>
            @if($showRisk)
                <div class="px-5 py-4">
                    <dt class="text-xs text-slate-500">Cảnh báo theo luật</dt>
                    <dd class="mt-1.5">@include('manager.risk', ['risk' => $staff->risk])</dd>
                    @if($staff->prediction?->diem_rui_ro !== null)
                        <p class="mt-1 text-xs text-slate-500">Điểm {{ number_format($staff->prediction->diem_rui_ro * 100, 1) }}/100 · chỉ để tham khảo</p>
                    @endif
                </div>
            @endif
        </dl>
    </section>

    {{-- Thanh chuyển mục --}}
    <div role="tablist" aria-label="Nội dung hồ sơ" class="flex gap-1 overflow-x-auto md:flex-wrap rounded-2xl border border-coffee-200 bg-white p-1.5 shadow-sm">
        @foreach($tabs as $key => [$label, $icon])
            <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}" :aria-selected="tab === '{{ $key }}'" @click="tab = '{{ $key }}'"
                    :class="tab === '{{ $key }}' ? 'bg-coffee-800 text-white shadow-sm' : 'text-slate-600 hover:bg-coffee-50 hover:text-coffee-900'"
                    class="flex shrink-0 items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition">
                <i class="fa-solid {{ $icon }}" aria-hidden="true"></i>{{ $label }}
                @isset($tabCounts[$key])<span class="rounded-full px-1.5 text-xs" :class="tab === '{{ $key }}' ? 'bg-white/20' : 'bg-coffee-100 text-coffee-800'">{{ $tabCounts[$key] }}</span>@endisset
            </button>
        @endforeach
    </div>

    {{-- Thông tin --}}
    <div id="panel-info" role="tabpanel" aria-labelledby="tab-info" x-show="tab === 'info'" class="grid gap-5 lg:grid-cols-2">
        @foreach(['Thông tin công việc' => $workInfo, 'Thông tin cá nhân' => $personalInfo] as $title => $items)
            <section class="rounded-2xl border border-coffee-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-base font-bold text-coffee-900">{{ $title }}</h2>
                <dl class="grid gap-x-6 gap-y-4 sm:grid-cols-2">
                    @foreach($items as $label => $value)
                        <div class="min-w-0">
                            <dt class="text-xs text-slate-500">{{ $label }}</dt>
                            <dd class="mt-0.5 break-words text-sm font-semibold {{ $value ? 'text-coffee-900' : 'font-normal text-slate-400' }}">{{ $value ?: 'Chưa cập nhật' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endforeach
    </div>

    {{-- Tiến độ theo khóa học --}}
    <section id="panel-courses" role="tabpanel" aria-labelledby="tab-courses" x-show="tab === 'courses'" x-cloak class="rounded-2xl border border-coffee-200 bg-white p-6 shadow-sm">
        <h2 class="mb-4 text-base font-bold text-coffee-900">Tiến độ theo khóa học</h2>
        <div class="grid gap-3 md:grid-cols-2">
            @forelse($courses as $course)
                <div class="rounded-xl border border-coffee-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <h3 class="text-sm font-bold text-coffee-900">{{ $course->TenCTDT }}</h3>
                        <strong class="shrink-0 text-sm {{ $course->progress >= 100 ? 'text-emerald-700' : 'text-coffee-900' }}">{{ $course->progress }}%</strong>
                    </div>
                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-coffee-100"><div class="h-full rounded-full {{ $course->progress >= 100 ? 'bg-emerald-500' : 'bg-amber_brand' }}" style="width: {{ min($course->progress, 100) }}%"></div></div>
                    <p class="mt-2 text-xs text-slate-500">{{ $course->completed }}/{{ $course->total }} bài học đã hoàn thành</p>
                </div>
            @empty
                <p class="text-sm text-slate-500">Chưa có khóa học áp dụng cho chức vụ này.</p>
            @endforelse
        </div>
    </section>

    {{-- Từng lượt làm quiz --}}
    <section id="panel-quizzes" role="tabpanel" aria-labelledby="tab-quizzes" x-show="tab === 'quizzes'" x-cloak class="overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 px-6 pt-6 pb-4">
            <h2 class="text-base font-bold text-coffee-900">Từng lượt làm quiz</h2>
            <span class="text-xs text-slate-500">{{ $quizzes->count() }} lượt · {{ $quizFailed }} lượt không đạt</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[560px] text-sm">
                <thead class="bg-coffee-50 text-left text-xs text-slate-600">
                    <tr><th class="px-6 py-2.5 font-semibold">Bài kiểm tra</th><th class="px-3 py-2.5 font-semibold">Lượt</th><th class="px-3 py-2.5 font-semibold">Điểm</th><th class="px-3 py-2.5 font-semibold">Kết quả</th><th class="px-6 py-2.5 font-semibold">Ngày làm</th></tr>
                </thead>
                <tbody>
                    @forelse($quizzes as $quiz)
                        @php
                            $quizPassed = $quiz->Diem >= ($quiz->DiemChuan ?? 80);
                        @endphp
                        <tr class="border-t border-coffee-100">
                            <td class="px-6 py-2.5">{{ $quiz->TenBaiKiemTra ?? 'Bài '.$quiz->MaBaiKiemTra }}</td>
                            <td class="px-3 py-2.5 text-slate-600">Lần {{ $quiz->attempt_number }}</td>
                            <td class="px-3 py-2.5 font-semibold">{{ round($quiz->Diem, 1) }}/100</td>
                            <td class="px-3 py-2.5"><span class="font-semibold {{ $quizPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $quizPassed ? 'Đạt' : 'Chưa đạt' }}</span> <span class="text-xs text-slate-400">(chuẩn {{ $quiz->DiemChuan ?? 80 }})</span></td>
                            <td class="px-6 py-2.5 text-xs text-slate-600">{{ \Carbon\Carbon::parse($quiz->NgayLam ?? $quiz->created_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-5 text-slate-500">Chưa có lượt làm quiz.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Lịch sử đánh giá --}}
    <section id="panel-assessments" role="tabpanel" aria-labelledby="tab-assessments" x-show="tab === 'assessments'" x-cloak class="rounded-2xl border border-coffee-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-base font-bold text-coffee-900">Lịch sử đánh giá năng lực</h2>
            <a href="{{ $assessmentLink[0] }}" class="text-sm font-bold text-amber-700 hover:text-amber-800">{{ $assessmentLink[1] }} →</a>
        </div>
        <p class="mb-3 mt-1 text-xs text-slate-500">Kết quả chỉ là chính thức khi quản lý đã phê duyệt.</p>
        @forelse($assessments as $assessment)
            @php
                [$statusLabel, $statusTone] = $statusStyles[$assessment->TrangThai] ?? [$assessment->TrangThai, 'border-slate-300 bg-slate-50 text-slate-600'];
                $assessedAt = $assessment->NgayDanhGia ?? $assessment->NgayHen ?? $assessment->created_at;
            @endphp
            <article class="border-t border-coffee-100 py-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-coffee-900">{{ $assessment->TieuChuan ?? 'Đánh giá năng lực' }}</h3>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $assessedAt ? \Carbon\Carbon::parse($assessedAt)->format('d/m/Y H:i') : 'Chưa có ngày' }} · Người chấm: {{ $assessment->assessor_name ?? 'Chưa phân công' }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        @if($assessment->Diem !== null)
                            <strong class="text-sm text-coffee-900">{{ round($assessment->Diem, 1) }}/100</strong>
                            @if($assessment->IsPassed !== null)
                                <span class="font-semibold {{ $assessment->IsPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $assessment->IsPassed ? 'Đạt' : 'Chưa đạt' }}</span>
                            @endif
                        @endif
                        <span class="rounded-full border px-2.5 py-0.5 font-semibold {{ $statusTone }}">{{ $statusLabel }}</span>
                    </div>
                </div>
                @if($assessment->NhanXet)
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $assessment->NhanXet }}</p>
                @endif
                @if(isset($proposalOwners) && $assessment->ChiTietDeXuat)
                    @include('components.assessment-improvement-summary', ['savedProposal' => json_decode($assessment->ChiTietDeXuat, true)])
                @endif
                @if($assessment->TrangThai === 'TraLai' && ($assessment->ly_do_tra_lai ?? null))
                    <p class="mt-2 rounded-lg bg-rose-50 p-3 text-xs text-rose-800"><span class="font-semibold">Lý do trả lại:</span> {{ $assessment->ly_do_tra_lai }}</p>
                @endif
            </article>
        @empty
            <p class="border-t border-coffee-100 pt-4 text-sm text-slate-500">Chưa có đánh giá năng lực.</p>
        @endforelse
    </section>
