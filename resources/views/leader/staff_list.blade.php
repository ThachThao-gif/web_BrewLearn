@extends('layouts.app')

@section('title', 'Danh Sách Nhân Sự Ca & Theo Dõi Năng Lực')

@section('content')
<!-- Khởi tạo trạng thái Modal & Bộ lọc trong Alpine.js -->
<div class="space-y-6 font-sans" x-data="{ searchQuery: '', activeTab: 'all', selectedStaff: null }">

    <!-- BREADCRUMB & HEADER CONTAINER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium mb-1">
                <span>QUẢN TRỊ CA & ĐÀO TẠO</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-coffee-800 font-semibold">Danh Sách Nhân Sự Ca & Theo Dõi Năng Lực</span>
            </div>
            <h1 class="text-2xl font-bold text-coffee-900 tracking-tight">Danh Sách Nhân Sự Ca & Theo Dõi Năng Lực</h1>
            <div class="flex items-center space-x-3 text-xs text-slate-600 mt-1">
                <span class="flex items-center"><i class="fa-solid fa-sun text-amber-500 mr-1.5"></i> Ca Trực Hiện Tại</span>
                <span>•</span>
                <span>{{ $truongCa->ChiNhanh ?? 'Flagship Quận 1, TP.HCM' }}</span>
                <span>•</span>
                <span>Trưởng ca: <strong class="text-coffee-900 font-semibold">{{ $truongCa->name ?? 'Chưa phân công' }}</strong></span>
            </div>
        </div>

        <div class="flex items-center space-x-2.5">
            <button class="px-3.5 py-2 bg-white border border-[#E2D4C1] text-slate-700 hover:bg-coffee-50 rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid fa-sliders text-[#6E4E30]"></i>
                <span>Tùy chỉnh cột</span>
            </button>
            <button class="px-4 py-2 bg-coffee-900 hover:bg-coffee-800 text-white rounded-xl text-xs font-semibold shadow-sm transition flex items-center gap-1.5">
                <i class="fa-solid fa-download"></i>
                <span>Xuất báo cáo ca</span>
            </button>
        </div>
    </div>

    <!-- BANNER SECURITY TIER 2 -->
    <div class="bg-[#FFFDF9] border border-[#E8DFC8] rounded-2xl p-3.5 flex items-center justify-between shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-sm font-bold shrink-0">
                <i class="fa-solid fa-shield-halved text-[#D96B27]"></i>
            </div>
            <div class="text-xs">
                <span class="font-bold text-coffee-900 uppercase mr-1">SECURITY TIER 2</span>
                <span class="font-bold text-coffee-900">Chế độ xem chuẩn hóa Trưởng Ca:</span>
                <span class="text-slate-600"> Giám sát vận hành ca, Tiến độ đào tạo & Theo dõi thời hạn sát hạch nhân sự.</span>
            </div>
        </div>
    </div>

    <!-- PHẦN THỐNG KÊ NHANH -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">TỔNG NHÂN SỰ CA HÔM NAY</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-2xl font-black text-coffee-900">{{ str_pad($totalStaffs ?? count($staffs), 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-xs font-bold text-slate-600">nhân viên</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">{{ $totalStaffs ?? count($staffs) }} Đang trong ca</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-coffee-50 text-coffee-800 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">TIẾN ĐỘ HỌC TRUNG BÌNH CA</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-2xl font-black text-coffee-900">{{ $avgProgress ?? 0 }}%</span>
                </div>
                <div class="w-28 bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" style="width: {{ $avgProgress ?? 0 }}%"></div>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <div class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1">ĐẾN KỲ SÁT HẠCH (3 THÁNG)</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-2xl font-black text-coffee-900">{{ str_pad($dueCount ?? 0, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-xs font-bold text-slate-600">nhân sự</span>
                </div>
                <p class="text-[11px] text-amber-700 font-medium mt-1">Cần thực hiện sát hạch định kỳ</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>
    </div>

    <!-- BỘ LỌC TÌM KIẾM & TAB -->
    <div class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm space-y-3">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchQuery" placeholder="Tìm theo Tên nhân viên, Mã NV..." class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl pl-9 pr-4 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-coffee-600 focus:bg-white transition">
            </div>
            <div class="flex items-center space-x-3 overflow-x-auto pb-1 md:pb-0">
                <div class="flex items-center bg-[#FAF7F2] p-1 rounded-xl border border-[#E8DFC8] text-xs font-semibold shrink-0">
                    <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-coffee-900 shadow-sm' : 'text-slate-600 hover:text-coffee-900'" class="px-3 py-1.5 rounded-lg transition">Tất cả ({{ $totalStaffs ?? count($staffs) }})</button>
                    <button type="button" @click="activeTab = 'shift'" :class="activeTab === 'shift' ? 'bg-white text-coffee-900 shadow-sm' : 'text-slate-600 hover:text-coffee-900'" class="px-3 py-1.5 rounded-lg transition">Đang trong ca ({{ $totalStaffs ?? count($staffs) }})</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH NHÂN VIÊN -->
    <div class="space-y-4">
        @forelse($staffs as $staff)
            @php
                // 1. TÍNH TOÁN CÁC BIẾN TIẾN ĐỘ ĐỒNG BỘ
                $coursesTotal     = $staff->total_courses_count ?? 0;
                $coursesCompleted = $staff->completed_courses_count ?? 0;
                $progress         = $staff->overall_progress ?? 0;

                // 2. TÍNH TOÁN QUIZ
                $quizScore        = $staff->quiz_latest_score ?? 0;
                $quizAttempts     = $staff->quiz_attempts_count ?? 1;
                $quizFailed       = $staff->quiz_failed_count ?? 0;

                // 3. THÔNG TIN CƠ BẢN
                $staffCode        = 'NV-' . str_pad($staff->id ?? 0, 3, '0', STR_PAD_LEFT);
                $staffName        = $staff->name ?? 'N/A';
                $staffChucVu      = $staff->ChucVu ?? $staff->chuc_vu ?? 'Thu ngân';
                $staffPhone       = $staff->phone ?? $staff->SoDienThoai ?? 'Chưa cập nhật';
                $staffEmail       = $staff->email ?? 'N/A';
                $staffTram        = $staff->tram_lam_viec ?? $staff->Tram ?? 'Quầy Bar';
                $rubricScore      = round((float) ($staff->RubricScore ?? $staff->rubric_score ?? 0), 1);

                // 4. LOGIC SÁT HẠCH
                $startDate        = $staff->joined_at ?? $staff->created_at ?? null;
                $daysWorked       = $startDate ? \Carbon\Carbon::parse($startDate)->diffInDays(now()) : 0;
                $isAssessmentDue  = ($daysWorked >= 80 && $daysWorked <= 105);
                $inShift          = $staff->in_shift ?? true;
            @endphp
            
            <div x-show="(searchQuery === '' || '{{ strtolower(addslashes($staffName)) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($staffCode) }}'.includes(searchQuery.toLowerCase())) && (activeTab === 'all' || (activeTab === 'shift' && {{ $inShift ? 'true' : 'false' }}))" 
                 class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm relative overflow-hidden transition hover:shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    
                    <!-- Thông tin NV -->
                    <div class="lg:col-span-4 flex items-start space-x-3">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($staffName) }}&background=6E4E30&color=fff" class="w-12 h-12 rounded-full object-cover" alt="{{ $staffName }}">
                            <span class="w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full absolute bottom-0 right-0"></span>
                        </div>
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-bold text-coffee-900">{{ $staffName }}</h4>
                                <span class="text-[10px] font-extrabold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded">{{ $staffCode }}</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">{{ $staffChucVu }}</p>
                            <div class="text-[10px] text-slate-400 flex items-center space-x-2 pt-1">
                                <span>Trạm: {{ $staffTram }}</span>
                                <span>•</span>
                                <span class="text-emerald-700 font-semibold">Đang trong ca</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tiến độ -->
                    <div class="lg:col-span-3 bg-[#FAF7F2] p-3 rounded-xl border border-[#E8DFC8]/60 space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-coffee-900 truncate">Tiến độ đào tạo</span>
                            <span class="font-black text-emerald-700">{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: {{ $progress }}%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-500 pt-0.5">
                            <span>Cấp độ: <strong>Cấp {{ $staff->CapDo ?? 1 }}</strong></span>
                            <span class="text-slate-400">({{ $coursesCompleted }}/{{ $coursesTotal }} khóa)</span>
                        </div>
                    </div>

                    <!-- Điểm số -->
                    <div class="lg:col-span-3 grid grid-cols-2 gap-2 text-center">
                        <div class="bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-500 block">Lý thuyết Quiz</span>
                            <span class="text-base font-black text-coffee-900">{{ $quizScore }}/100</span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-500 block">Đánh Giá Rubric</span>
                            <span class="text-base font-black text-coffee-900">{{ $rubricScore }}/10</span>
                        </div>
                    </div>

                    <!-- Trạng thái & Nút Mở Modal -->
                    <div class="lg:col-span-2 flex flex-col justify-center items-end space-y-1.5">
                        @if($isAssessmentDue)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                <i class="fa-solid fa-clock-rotate-left mr-1.5"></i> Đến kỳ sát hạch
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                <i class="fa-solid fa-circle-check mr-1.5 text-slate-400"></i> Chưa đến kỳ
                            </span>
                        @endif

                        <button type="button" 
                                onclick="showStaffDetail({
                                    id: '{{ $staff->id }}',
                                    code: '{{ $staffCode }}',
                                    name: '{{ addslashes($staffName) }}',
                                    chucVu: '{{ addslashes($staffChucVu) }}',
                                    phone: '{{ $staffPhone }}',
                                    email: '{{ $staffEmail }}',
                                    tram: '{{ addslashes($staffTram) }}',
                                    
                                    // Dữ liệu khóa học thực tế
                                    lessonsTotal: {{ $coursesTotal }},
                                    lessonsCompleted: {{ $coursesCompleted }},
                                    progress: '{{ $progress }}',
                                    
                                    // Dữ liệu Quiz
                                    quiz: '{{ $quizScore }}',
                                    quizLatest: '{{ $quizScore }}',
                                    quizAttempts: {{ $quizAttempts }},
                                    quizFailed: {{ $quizFailed }},

                                    capDo: '{{ $staff->CapDo ?? $staff->cap_do ?? 1 }}',
                                    editUrl: '{{ Route::has('users.edit') ? route('users.edit', $staff->id) : '#' }}'
                                })" 
                                class="font-bold text-amber-800 hover:text-coffee-900 transition flex items-center text-xs mt-1 cursor-pointer">
                            <i class="fa-solid fa-address-card mr-1.5"></i>Xem Hồ sơ <i class="fa-solid fa-angle-right text-[10px] ml-1"></i>
                        </button>
                    </div>

                </div>
            </div>
        @empty
            <div class="bg-white p-8 text-center rounded-2xl border border-dashed border-slate-300">
                <p class="text-sm font-semibold text-slate-600">Hiện chưa có dữ liệu nhân viên trong cơ sở dữ liệu.</p>
            </div>
        @endforelse
    </div>

</div>

<!-- POPUP CHI TIẾT HỒ SƠ NHÂN VIÊN -->
<div id="staffDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" onclick="if(event.target === this) closeStaffDetail()">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto border border-[#E8DFC8] space-y-4">
        
        <!-- Header Popup -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-3">
            <div class="flex items-center gap-3">
                <div id="modalAvatar" class="w-11 h-11 rounded-2xl bg-coffee-900 text-amber-300 font-extrabold flex items-center justify-center text-sm shadow">
                    NV
                </div>
                <div>
                    <h3 id="modalName" class="font-bold text-coffee-900 text-base leading-tight">Nguyễn Văn A</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <span id="modalCode" class="text-[10px] font-extrabold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md">NV-000</span>
                        <span id="modalChucVu" class="text-xs font-medium text-slate-500">Chức vụ</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="closeStaffDetail()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-xl hover:bg-slate-100 transition cursor-pointer">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Thông tin hành chính -->
        <div class="grid grid-cols-2 gap-3 text-xs">
            <div class="p-3 bg-[#FAF7F2] rounded-2xl border border-[#E8DFC8]/60 space-y-1">
                <span class="text-slate-400 block font-semibold text-[10px] uppercase">Số điện thoại</span>
                <span id="modalPhone" class="font-bold text-coffee-900 block">-</span>
            </div>
            <div class="p-3 bg-[#FAF7F2] rounded-2xl border border-[#E8DFC8]/60 space-y-1">
                <span class="text-slate-400 block font-semibold text-[10px] uppercase">Trạm phân công</span>
                <span id="modalTram" class="font-bold text-coffee-900 truncate block">-</span>
            </div>
        </div>

        <!-- KHUNG THÔNG TIN TIẾN ĐỘ KHÓA HỌC -->
        <div class="bg-amber-50/60 p-4 rounded-2xl border border-amber-100 space-y-3">
            <div class="flex justify-between items-center">
                <h4 class="font-bold text-amber-900 text-xs uppercase tracking-wide flex items-center gap-1.5">
                    <i class="fa-solid fa-book-open text-amber-600"></i> Tiến Độ Học Tập Khóa Học
                </h4>
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 font-bold mr-1">Tiến độ tổng:</span>
                    <span id="modalProgress" class="font-extrabold text-amber-800 text-sm">0%</span>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div class="w-full bg-amber-100 h-2 rounded-full overflow-hidden">
                <div id="modalProgressBar" class="bg-amber-600 h-full rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>

            <!-- 2 Ô chi tiết khóa học -->
            <div class="grid grid-cols-2 gap-2 text-center text-xs pt-1">
                <div class="bg-white p-2.5 rounded-xl border border-amber-100 shadow-sm">
                    <span class="text-[10px] text-slate-400 block font-semibold">Tổng khóa phân công</span>
                    <span id="modalLessonsTotal" class="font-extrabold text-slate-800 text-sm">0</span>
                </div>
                <div class="bg-white p-2.5 rounded-xl border border-emerald-100 shadow-sm">
                    <span class="text-[10px] text-emerald-600 block font-semibold">Đã hoàn thành 100%</span>
                    <span id="modalLessonsCompleted" class="font-extrabold text-emerald-700 text-sm">0</span>
                </div>
            </div>
        </div>

        <!-- KHUNG THÔNG TIN KẾT QUẢ THI QUIZ -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-3">
            <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wide flex items-center gap-1.5">
                <i class="fa-solid fa-graduation-cap text-amber-700"></i> Kết Quả Thi Quiz
            </h4>
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400 block font-semibold mb-0.5">Điểm Quiz gần nhất</span>
                    <span id="modalQuizLatest" class="font-extrabold text-amber-800 text-base">0/100</span>
                </div>
                <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-sm">
                    <span class="text-[10px] text-slate-400 block font-semibold mb-0.5">Tổng lượt thi</span>
                    <span id="modalQuizAttempts" class="font-extrabold text-slate-700 text-base">0</span>
                </div>
                <div class="bg-white p-2.5 rounded-xl border border-rose-100 shadow-sm">
                    <span class="text-[10px] text-rose-500 block font-semibold mb-0.5">Số lần không đạt</span>
                    <span id="modalQuizFailed" class="font-extrabold text-rose-600 text-base">0</span>
                </div>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="flex gap-3 pt-2 border-t border-slate-100">
            <button type="button" onclick="closeStaffDetail()" class="flex-1 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition cursor-pointer">
                Đóng cửa sổ
            </button>
            <a id="modalEditBtn" href="#" class="flex-1 py-2.5 bg-coffee-900 text-white font-bold text-xs rounded-xl hover:bg-coffee-800 transition text-center flex items-center justify-center gap-1.5 shadow">
                <i class="fa-solid fa-user-pen text-amber-300"></i> Chỉnh sửa hồ sơ
            </a>
        </div>
    </div>
</div>

<!-- JAVASCRIPT ĐỒNG BỘ HIỂN THỊ DỮ LIỆU -->
<script>
function showStaffDetail(data) {
    if (!data) return;

    // 1. Thông tin cá nhân
    document.getElementById('modalName').innerText = data.name || 'N/A';
    document.getElementById('modalCode').innerText = data.code || 'NV-000';
    document.getElementById('modalChucVu').innerText = data.chucVu || '';
    document.getElementById('modalPhone').innerText = data.phone || 'N/A';
    document.getElementById('modalTram').innerText = data.tram || 'N/A';

    // Avatar
    const elAvatar = document.getElementById('modalAvatar');
    if (elAvatar) {
        if (data.name) {
            const words = data.name.trim().split(' ');
            const initials = words.length > 1 ? (words[0][0] + words[words.length - 1][0]) : data.name.substring(0, 2);
            elAvatar.innerText = initials.toUpperCase();
        } else {
            elAvatar.innerText = 'NV';
        }
    }

    // 2. Dữ liệu khóa học
    document.getElementById('modalLessonsTotal').innerText = data.lessonsTotal ?? 0;
    document.getElementById('modalLessonsCompleted').innerText = data.lessonsCompleted ?? 0;
    
    const progressVal = data.progress ?? 0;
    document.getElementById('modalProgress').innerText = progressVal + '%';
    document.getElementById('modalProgressBar').style.width = progressVal + '%';

    // 3. Dữ liệu Quiz
    const latestScore = data.quizLatest ?? data.quiz ?? 0;
    document.getElementById('modalQuizLatest').innerText = latestScore + '/100';
    document.getElementById('modalQuizAttempts').innerText = data.quizAttempts ?? 0;
    document.getElementById('modalQuizFailed').innerText = data.quizFailed ?? 0;

    // Link chỉnh sửa
    const elEditBtn = document.getElementById('modalEditBtn');
    if (elEditBtn) elEditBtn.href = data.editUrl || '#';

    // Hiển thị Popup
    const modal = document.getElementById('staffDetailModal');
    if (modal) modal.classList.remove('hidden');
}

function closeStaffDetail() {
    const modal = document.getElementById('staffDetailModal');
    if (modal) modal.classList.add('hidden');
}
</script>
@endsection