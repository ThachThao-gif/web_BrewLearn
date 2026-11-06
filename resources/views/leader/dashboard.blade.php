@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    @if($errors->any())
        <div role="alert" class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-sm">
            <p class="font-bold">Chưa gửi được đánh giá. Kiểm tra các nội dung sau:</p>
            <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- HIỂN THỊ THÔNG BÁO FLASH MESSAGES (Thành công / Lỗi) --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                <p class="text-sm font-medium text-rose-900">{{ session('error') }}</p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
        </div>
    @endif

    {{-- Header & Navigation --}}
    <div class="page-banner flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            @if($page === 'scoring')
                <h1 class="text-2xl font-bold text-slate-800">Chấm Điểm Đánh Giá Năng Lực</h1>
                <p class="text-sm text-slate-500 mt-1">Chấm năm tiêu chí theo lịch đã hẹn và gửi kết quả cho quản lý cửa hàng phê duyệt</p>
            @else
                <h1 class="text-2xl font-bold text-slate-800">Xếp Lịch Đánh Giá Năng Lực</h1>
                <p class="text-sm text-slate-500 mt-1">Theo dõi hạn đánh giá định kỳ 3 tháng và gửi lịch hẹn cho nhân viên đủ điều kiện</p>
            @endif
        </div>
        
        {{-- Nút chuyển sang trang Danh sách nhân sự --}}
       
    </div>

    {{-- Nhắc nhân viên đến kỳ đánh giá 3 tháng --}}
    @if($page === 'schedule' && isset($dueStaffs) && $dueStaffs->count() > 0)
    @php
        $today = now('Asia/Ho_Chi_Minh')->startOfDay();
        foreach ($dueStaffs as $due) {
            $pending = $due->scheduled_assessment;
            $due->stage = ! $pending ? ($due->eligibility['ready'] ? 'ready' : 'blocked') : ($pending->TrangThai === 'ChoDanhGia' ? 'scheduled' : ($pending->TrangThai === 'TraLai' ? 'returned' : 'reviewing'));
            $daysLeft = (int) $today->diffInDays($due->due_at->copy()->startOfDay(), false);
            $due->due_note = $daysLeft < 0 ? 'trễ '.abs($daysLeft).' ngày' : ($daysLeft === 0 ? 'đến hạn hôm nay' : 'còn '.$daysLeft.' ngày');
        }
        $stageLabels = ['scheduled' => 'Đã xếp lịch', 'reviewing' => 'Chờ quản lý duyệt', 'returned' => 'Cần bổ sung'];
        $reminderSummary = [
            'ready' => ['Cần xếp lịch', $dueStaffs->where('stage', 'ready')->count()],
            'blocked' => ['Chưa đủ điều kiện', $dueStaffs->where('stage', 'blocked')->count()],
            'scheduled' => ['Đã xếp lịch', $dueStaffs->where('stage', 'scheduled')->count()],
            'review' => ['Chờ duyệt / cần bổ sung', $dueStaffs->whereIn('stage', ['reviewing', 'returned'])->count()],
        ];
        $th = 'border border-slate-200 bg-slate-50 px-3 py-2 text-left text-xs font-semibold text-slate-600';
        $td = 'border border-slate-200 px-3 py-2 align-top';
    @endphp
    <section aria-labelledby="assessment-reminder-title" x-data="{ filter: null }" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h3 id="assessment-reminder-title" class="text-sm font-bold text-slate-800">Nhắc hạn đánh giá</h3>
            <div class="flex flex-wrap items-center gap-1 text-xs" role="group" aria-label="Lọc theo tình trạng">
                <button type="button" @click="filter = null" :aria-pressed="! filter" :class="! filter ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'" class="rounded-lg px-2.5 py-1 font-semibold">Tất cả ({{ $dueStaffs->count() }})</button>
                @foreach($reminderSummary as $group => [$label, $count])
                    <button type="button" @click="filter = filter === '{{ $group }}' ? null : '{{ $group }}'" :aria-pressed="filter === '{{ $group }}'"
                            :class="filter === '{{ $group }}' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-100'" class="rounded-lg px-2.5 py-1 font-semibold">{{ $label }} ({{ $count }})</button>
                @endforeach
            </div>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr><th class="{{ $th }} w-12 text-center">STT</th><th class="{{ $th }}">Nhân viên</th><th class="{{ $th }}">Chức danh</th><th class="{{ $th }}">Hạn đánh giá</th><th class="{{ $th }}">Tình trạng</th><th class="{{ $th }}">Ghi chú</th></tr>
                </thead>
                <tbody>
                    @foreach($dueStaffs as $due)
                        <tr x-show="! filter || filter === '{{ in_array($due->stage, ['reviewing', 'returned']) ? 'review' : $due->stage }}'">
                            <td class="{{ $td }} text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="{{ $td }} font-semibold text-slate-800">{{ $due->name }}</td>
                            <td class="{{ $td }} text-slate-600">{{ $due->ChucVu }}</td>
                            <td class="{{ $td }}">{{ $due->due_at->format('d/m/Y') }} <span class="text-slate-500">({{ $due->due_note }})</span></td>
                            <td class="{{ $td }}">{{ $stageLabels[$due->stage] ?? $due->due_status }}</td>
                            <td class="{{ $td }} text-slate-600">
                                @if($due->stage === 'scheduled')
                                    Hẹn {{ \Carbon\Carbon::parse($due->scheduled_assessment->NgayHen)->format('H:i d/m/Y') }}
                                @elseif($due->stage === 'returned')
                                    Quản lý trả lại, cần chấm bổ sung
                                @elseif($due->stage === 'reviewing')
                                    Đã chấm, chờ quản lý duyệt
                                @elseif(! $due->eligibility['ready'])
                                    Chưa đủ điều kiện đánh giá:
                                    <button type="button" onclick="document.getElementById('due-{{ $due->id }}').showModal()" aria-haspopup="dialog" class="font-semibold text-slate-800 underline underline-offset-2">còn thiếu {{ count($due->eligibility['missing']) }} yêu cầu</button>
                                @else
                                    Đủ điều kiện đánh giá.
                                    <button type="button" onclick="document.querySelector('select[name=user_id]').value='{{ $due->id }}'; document.querySelector('select[name=user_id]').focus();" class="font-semibold text-slate-800 underline underline-offset-2">Xếp lịch</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @foreach($reminderSummary as $group => [$label, $count])
                        @if($count === 0)
                            <tr x-show="filter === '{{ $group }}'" x-cloak><td colspan="6" class="{{ $td }} py-5 text-center text-slate-500">Không có nhân viên ở mục "{{ $label }}".</td></tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        @foreach($dueStaffs as $due)
            @if(! $due->scheduled_assessment && ! $due->eligibility['ready'])
                <dialog id="due-{{ $due->id }}" aria-labelledby="due-{{ $due->id }}-title" class="w-[min(640px,calc(100vw-2rem))] max-h-[85vh] rounded-2xl p-0 shadow-2xl backdrop:bg-slate-900/40">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                        <h2 id="due-{{ $due->id }}-title" class="text-lg font-bold text-slate-800">{{ $due->name }} – yêu cầu cần hoàn thành</h2>
                        <form method="dialog"><button aria-label="Đóng" class="rounded-lg px-2 py-1 text-slate-400 hover:bg-slate-50 hover:text-slate-700"><i class="fa-solid fa-xmark"></i></button></form>
                    </div>
                    <div class="space-y-3 px-6 py-5 text-sm">
                        <table class="w-full border-collapse">
                            <tr><th class="{{ $th }} w-36">Hạn đánh giá</th><td class="{{ $td }}">{{ $due->due_at->format('d/m/Y') }} · {{ $due->due_status }}</td></tr>
                            <tr><th class="{{ $th }}">Tình trạng</th><td class="{{ $td }}">Chưa đủ điều kiện đánh giá — cần hoàn thành {{ count($due->eligibility['missing']) }} yêu cầu học tập và kiểm tra trước khi xếp lịch.</td></tr>
                        </table>
                        <ul class="list-disc space-y-1 pl-5 text-slate-700">
                            @foreach($due->eligibility['missing'] as $missing)<li>{{ $missing }}</li>@endforeach
                        </ul>
                    </div>
                </dialog>
            @endif
        @endforeach
    </section>
    @endif

    <div class="grid grid-cols-1 gap-6">
        @if($page === 'schedule')
        {{-- Block 1: Phát thông báo lịch đánh giá định kỳ --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center"><i class="fa-regular fa-calendar-plus" aria-hidden="true"></i></div>
                <h2 class="text-base font-bold text-slate-800">Phát Thông Báo Lịch Đánh Giá</h2>
            </div>

            <form action="{{ route('leader.createSchedule') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                @csrf
                <div>
    <label class="block text-xs font-semibold text-slate-600 mb-1">Chọn Nhân Viên <span class="text-rose-500">*</span></label>
    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
    <option value="">-- Chọn nhân viên --</option>
    @if(isset($staffs) && count($staffs) > 0)
        @foreach($staffs as $staff)
            <option value="{{ $staff->id }}" @disabled(!$staff->eligibility['ready'])>{{ $staff->name }} (NV-{{ str_pad($staff->id, 3, '0', STR_PAD_LEFT) }}){{ !$staff->eligibility['ready'] ? ' — Chưa đủ điều kiện' : ' — Đủ điều kiện' }}</option>
        @endforeach
    @endif
</select>
<p class="text-xs text-slate-500 mt-2">Chỉ chọn được người hoàn thành toàn bộ bài học và đạt các bài kiểm tra được phân công. Nhắc hạn vẫn giữ người chưa đủ điều kiện.</p>
</div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tiêu Chuẩn Năng Lực <span class="text-rose-500">*</span></label>
                    <select name="MaCTDT" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Chọn tiêu chuẩn năng lực --</option>
                        @if(isset($standards) && count($standards) > 0)
                            @foreach($standards as $std)
                                <option value="{{ $std->MaCTDT }}">{{ $std->TenCTDT }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Ngày Đánh Giá <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="NgayDanhGia" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                </div>

                {{-- Ô nhập Ghi chú / Nội dung dặn dò --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Ghi chú / Nội dung dặn dò <span class="text-slate-400 font-normal">(Không bắt buộc)</span>
                    </label>
                    
                    <textarea name="note" 
                              rows="2"
                              placeholder="Nhập nội dung dặn dò cho nhân viên (ví dụ: Ôn tập quy trình POS, mang đồng phục chuẩn)..." 
                              class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl p-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-transparent focus:bg-white transition resize-y shadow-sm"></textarea>
                </div>

                <div class="md:col-span-2 flex justify-end border-t border-slate-100 pt-4">
                    <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm rounded-xl transition shadow-sm">
                        Tạo lịch & gửi thông báo
                    </button>
                </div>
            </form>
        </div>
        @else

        {{-- Block 2: Danh sách lịch đánh giá cần chấm điểm --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-clipboard-check" aria-hidden="true"></i></div>
                    <h2 class="text-base font-bold text-slate-800">Lịch Đánh Giá Cần Chấm Điểm</h2>
                </div>
                {{-- Ô Lọc tìm kiếm nhanh --}}
                <div class="relative sm:w-64">
                    <input type="text" id="tableSearchInput" onkeyup="filterScheduleTable()" placeholder="Tìm tên nhân viên..." class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2 text-slate-400 text-xs"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-slate-500" id="scheduleTable">
                    <thead class="text-xs text-slate-700 uppercase bg-slate-50 border-b border-slate-100">
                        <tr>
                            <th scope="col" class="py-3 px-4">Nhân viên</th>
                            <th scope="col" class="py-3 px-4">Ngày đánh giá</th>
                            <th scope="col" class="py-3 px-4">Trạng thái</th>
                            <th scope="col" class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @if(isset($schedules) && count($schedules) > 0)
                            @foreach($schedules as $item)
                                <tr class="hover:bg-slate-50/50 transition schedule-row">
                                    <td class="py-3 px-4">
                                        <span class="block font-semibold text-slate-700 staff-name-col">{{ $item->staff_name }}</span>
                                        <span class="text-xs text-slate-500">{{ \Illuminate\Support\Str::after($item->TieuChuan ?? 'Tiêu chuẩn năng lực', 'Tiêu chuẩn năng lực ') }}</span>
                                        @if($item->ChiTietDeXuat)
                                            @include('components.assessment-improvement-summary', ['savedProposal' => json_decode($item->ChiTietDeXuat, true)])
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($item->NgayHen)->format('d/m/Y H:i') }}
                                    </td>
                                    
                                    {{-- Cột Trạng thái --}}
                                    @php
                                        [$statusLabel, $statusTone, $statusDot] = match ($item->TrangThai) {
                                            'DaDuyet', 'HoanThanh' => ['Đã duyệt', 'border-emerald-200 bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
                                            'ChoPheDuyet' => ['Chờ duyệt', 'border-violet-200 bg-violet-50 text-violet-700', 'bg-violet-500'],
                                            'TraLai' => ['Cần bổ sung', 'border-amber-300 bg-amber-50 text-amber-800', 'bg-amber-500'],
                                            default => ['Chờ đánh giá', 'border-blue-200 bg-blue-50 text-blue-700', 'bg-blue-500'],
                                        };
                                    @endphp
                                    <td class="py-3 px-4">
                                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusTone }}">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $statusDot }}" aria-hidden="true"></span>{{ $statusLabel }}
                                        </span>
                                        @if($item->TrangThai === 'TraLai' && ($item->ly_do_tra_lai ?? null))
                                            <p class="mt-1 max-w-48 text-xs text-amber-800">{{ $item->ly_do_tra_lai }}</p>
                                        @endif
                                    </td>

                                    {{-- Cột Thao tác --}}
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        @if(in_array($item->TrangThai, ['ChoDanhGia', 'TraLai']) && ! $item->can_score)
                                            <span class="text-xs text-slate-500">Trưởng ca khác được phân công chấm</span>
                                        @elseif(in_array($item->TrangThai, ['ChoDanhGia', 'TraLai']))
                                            <button type="button"
                                                    class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                                                    onclick="openScoringModal('{{ $item->id }}', '{{ e($item->staff_name) }}', '{{ e($item->TieuChuan ?? '') }}')">
                                                <i class="fa-solid fa-pen-to-square text-[10px]" aria-hidden="true"></i>{{ $item->TrangThai === 'TraLai' ? 'Chấm lại' : 'Chấm điểm' }}
                                            </button>
                                        @else
                                            <span class="text-sm font-bold text-slate-800">{{ number_format((float) $item->Diem, 1, ',', '') }}</span>
                                            <span class="text-xs text-slate-500">điểm</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400 text-sm">
                                    Hiện chưa có lịch đánh giá nào cần chấm điểm.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- MODAL CHẤM ĐIỂM & XỬ LÝ NĂNG LỰC --}}
    <div id="scoringModal" onclick="handleBackdropClick(event)" class="fixed inset-0 bg-slate-900/50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-5 overflow-y-auto max-h-[90vh]" onclick="event.stopPropagation()">
            
            {{-- Header Modal --}}
            <div class="flex items-center justify-between border-b pb-3 border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Bảng Chấm Điểm Năng Lực</h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Nhân viên: <span id="modalStaffName" class="font-bold text-slate-700"></span> | 
                        Tiêu chuẩn: <span id="modalStandard" class="text-amber-600 font-medium"></span>
                    </p>
                </div>
                <button type="button" onclick="closeScoringModal()" class="text-slate-400 hover:text-slate-600 text-xl font-bold">&times;</button>
            </div>

            <form id="scoringForm" action="{{ route('leader.saveScore') }}" method="POST" class="space-y-4">
                @csrf
                @if($errors->any() && old('assessment_id'))
                    <div role="alert" class="bg-rose-50 text-rose-800 rounded-lg p-3 text-sm">
                        <p class="font-bold">Vui lòng bổ sung nội dung trước khi gửi:</p>
                        <ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <input type="hidden" name="assessment_id" id="modalAssessmentId">

                {{-- Các tiêu chí chấm điểm --}}
                <div class="space-y-3">
                    <div class="grid grid-cols-12 gap-2 text-xs font-bold text-slate-500 uppercase bg-slate-50 p-2 rounded-lg">
                        <div class="col-span-7">Tiêu chí đánh giá</div>
                        <div class="col-span-5 text-right">Thang điểm (0 - 100)</div>
                    </div>

                    {{-- 1. Kiến thức nghiệp vụ (20%) --}}
                    <div class="grid grid-cols-12 gap-2 items-center text-sm border-b pb-2 border-slate-50">
                        <div class="col-span-7">
                            <p class="font-semibold text-slate-700">1. Kiến thức nghiệp vụ <span class="text-amber-600">(20%)</span></p>
                            <p class="text-xs text-slate-400">Hiểu quy trình pha chế, menu (Ngưỡng ≥ 70)</p>
                        </div>
                        <div class="col-span-5">
                            <input type="number" min="0" max="100" name="diem_nghiep_vu" id="score_nv"
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-right font-semibold focus:ring-2 focus:ring-amber-500 outline-none" 
                                   value="80" oninput="calculateTotalScore()" required>
                        </div>
                    </div>

                    {{-- 2. Thao tác công việc (30%) --}}
                    <div class="grid grid-cols-12 gap-2 items-center text-sm border-b pb-2 border-slate-50">
                        <div class="col-span-7">
                            <p class="font-semibold text-slate-700">2. Thao tác công việc <span class="text-amber-600">(30%)</span></p>
                            <p class="text-xs text-slate-400">Thao tác pha chế, chất lượng đồ uống (Ngưỡng ≥ 70)</p>
                        </div>
                        <div class="col-span-5">
                            <input type="number" min="0" max="100" name="diem_ky_nang" id="score_kn"
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-right font-semibold focus:ring-2 focus:ring-amber-500 outline-none" 
                                   value="80" oninput="calculateTotalScore()" required>
                        </div>
                    </div>

                    {{-- 3. Tuân thủ quy trình (20%) --}}
                    <div class="grid grid-cols-12 gap-2 items-center text-sm border-b pb-2 border-slate-50">
                        <div class="col-span-7">
                            <p class="font-semibold text-slate-700">3. Tuân thủ quy trình <span class="text-amber-600">(20%)</span></p>
                            <p class="text-xs text-slate-400">Quy trình mở/đóng ca, vệ sinh (Ngưỡng ≥ 70)</p>
                        </div>
                        <div class="col-span-5">
                            <input type="number" min="0" max="100" name="diem_quy_trinh" id="score_qt"
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-right font-semibold focus:ring-2 focus:ring-amber-500 outline-none" 
                                   value="90" oninput="calculateTotalScore()" required>
                        </div>
                    </div>

                    {{-- 4. Thái độ phục vụ (15%) --}}
                    <div class="grid grid-cols-12 gap-2 items-center text-sm border-b pb-2 border-slate-50">
                        <div class="col-span-7">
                            <p class="font-semibold text-slate-700">4. Thái độ phục vụ <span class="text-amber-600">(15%)</span></p>
                            <p class="text-xs text-slate-400">Giao tiếp, thái độ với khách (Ngưỡng ≥ 70)</p>
                        </div>
                        <div class="col-span-5">
                            <input type="number" min="0" max="100" name="diem_phuc_vu" id="score_pv"
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-right font-semibold focus:ring-2 focus:ring-amber-500 outline-none" 
                                   value="82" oninput="calculateTotalScore()" required>
                        </div>
                    </div>

                    {{-- 5. Tác phong (15%) --}}
                    <div class="grid grid-cols-12 gap-2 items-center text-sm border-b pb-2 border-slate-50">
                        <div class="col-span-7">
                            <p class="font-semibold text-slate-700">5. Tác phong & phối hợp <span class="text-amber-600">(15%)</span></p>
                            <p class="text-xs text-slate-400">Đồng phục, đúng giờ, đồng đội (Ngưỡng ≥ 70)</p>
                        </div>
                        <div class="col-span-5">
                            <input type="number" min="0" max="100" name="diem_tac_phong" id="score_tp"
                                   class="w-full px-3 py-1.5 border border-slate-200 rounded-lg text-right font-semibold focus:ring-2 focus:ring-amber-500 outline-none" 
                                   value="85" oninput="calculateTotalScore()" required>
                        </div>
                    </div>

                    {{-- Nhận xét của Trưởng ca --}}
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nhận xét của Trưởng ca</label>
                        <textarea name="comment" rows="2" class="w-full p-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-amber-500" placeholder="Nhập nhận xét chi tiết..."></textarea>
                    </div>
                </div>

                {{-- TỔNG ĐIỂM KẾT QUẢ --}}
                <div class="bg-amber-50/60 p-4 rounded-xl border border-amber-100 flex items-center justify-between mt-4">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Điểm tổng hợp</p>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span id="totalScore" class="text-2xl font-bold text-amber-700">0/100</span>
                            <span id="passBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">ĐẠT</span>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500 space-y-0.5">
                        <p>Đạt chuẩn: Điểm tổng <span class="font-bold text-slate-700">≥ 80</span></p>
                        <p>Và mọi tiêu chí <span class="font-bold text-slate-700">≥ 70</span></p>
                    </div>
                </div>

                {{-- PHẦN ĐỀ XUẤT XỬ LÝ (TỰ ĐỘNG HIỆN KHI CHƯA ĐẠT) --}}
                <div id="improvementSection" class="hidden bg-rose-50 p-4 rounded-xl border border-rose-100 space-y-3">
                    <p class="text-xs font-bold text-rose-800 uppercase">Đề xuất xử lý của Trưởng ca (Nhân viên chưa đạt):</p>
                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-700">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="daotaobosung" onchange="updateProposalSections()" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Đào tạo bổ sung</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="hoclaibaihoc" onchange="updateProposalSections()" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Học lại bài học liên quan</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="theodoica" onchange="updateProposalSections()" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Theo dõi thêm trong ca làm việc</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="danhgialai" onchange="updateProposalSections()" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Đánh giá lại</span>
                        </label>
                    </div>
                    @include('components.assessment-improvement-details')
                </div>

                {{-- Nút bấm gửi --}}
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeScoringModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-sm font-medium transition">Hủy</button>
                    <button type="submit" id="submitBtn" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm">
                        Gửi kết quả đánh giá
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openScoringModal(id, staffName, standardName) {
        document.getElementById('scoringForm').reset();
        document.getElementById('modalAssessmentId').value = id;
        document.getElementById('modalStaffName').innerText = staffName;
        document.getElementById('modalStandard').innerText = standardName || 'Tiêu chuẩn năng lực';
        document.getElementById('scoringModal').classList.remove('hidden');
        calculateTotalScore();
    }

    function closeScoringModal() {
        document.getElementById('scoringModal').classList.add('hidden');
    }

    function handleBackdropClick(event) {
        closeScoringModal();
    }

    function calculateTotalScore() {
        // Ép kiểu & Giới hạn điểm từ 0 đến 100
        const getValidScore = (id) => {
            let el = document.getElementById(id);
            let val = parseFloat(el.value) || 0;
            if (val > 100) { val = 100; el.value = 100; }
            if (val < 0) { val = 0; el.value = 0; }
            return val;
        };

        const nv = getValidScore('score_nv');
        const kn = getValidScore('score_kn');
        const qt = getValidScore('score_qt');
        const pv = getValidScore('score_pv');
        const tp = getValidScore('score_tp');

        const total = (nv * 0.20) + (kn * 0.30) + (qt * 0.20) + (pv * 0.15) + (tp * 0.15);
        const finalScore = Math.round(total * 10) / 10;

        document.getElementById('totalScore').innerText = finalScore + '/100';

        const allCriteriaPassed = (nv >= 70 && kn >= 70 && qt >= 70 && pv >= 70 && tp >= 70);
        const isPassed = (finalScore >= 80) && allCriteriaPassed;

        const badge = document.getElementById('passBadge');
        const improvementSection = document.getElementById('improvementSection');
        const submitBtn = document.getElementById('submitBtn');
        improvementSection.dataset.passed = String(isPassed);

        if (isPassed) {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
            badge.innerText = 'ĐẠT (≥80)';
            
            improvementSection.classList.add('hidden');
            submitBtn.innerText = 'Gửi kết quả đánh giá';
            submitBtn.className = 'px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
            badge.innerText = !allCriteriaPassed && finalScore >= 80 ? 'CHƯA ĐẠT (Có tiêu chí < 70)' : 'CHƯA ĐẠT (<80)';
            
            improvementSection.classList.remove('hidden');
            submitBtn.innerText = 'Gửi đề xuất xử lý';
            submitBtn.className = 'px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-medium transition shadow-sm';
        }
        updateProposalSections();
    }

    function updateProposalSections() {
        const failed = document.getElementById('improvementSection').dataset.passed === 'false';
        const checkboxes = [...document.querySelectorAll('#scoringForm input[name="proposals[]"]')];
        checkboxes.forEach(input => { input.disabled = !failed; });
        checkboxes[0].setCustomValidity(failed && !checkboxes.some(input => input.checked) ? 'Vui lòng chọn ít nhất một đề xuất xử lý.' : '');
        document.querySelectorAll('[data-proposal]').forEach(section => {
            const enabled = failed && checkboxes.some(input => input.checked && input.value === section.dataset.proposal);
            section.disabled = !enabled;
            section.classList.toggle('hidden', !enabled);
            section.querySelectorAll('[data-required]').forEach(input => { input.required = enabled; });
        });
        filterProposalLessons();
        updateObservationRequirement();
    }

    function filterProposalLessons() {
        const courseId = document.getElementById('proposalCourse').value;
        [...document.getElementById('proposalLessons').options].forEach(option => {
            const matches = option.dataset.course === courseId;
            option.hidden = !matches;
            option.disabled = !matches;
            if (!matches) option.selected = false;
        });
    }

    function updateObservationRequirement() {
        const section = document.querySelector('[data-proposal="theodoica"]');
        const shifts = document.getElementById('observationShifts');
        const date = document.getElementById('observationDate');
        shifts.required = !section.disabled && !date.value;
        date.required = !section.disabled && !shifts.value;
    }

    @if($errors->any() && old('assessment_id'))
        @php
            $retryAssessment = $schedules->firstWhere('id', old('assessment_id'));
        @endphp
        openScoringModal(@json(old('assessment_id')), @json($retryAssessment->staff_name ?? ''), @json($retryAssessment->TieuChuan ?? ''));
        const previousInput = @json(session()->getOldInput());
        function restoreScoringInput(name, value) {
            if (value && typeof value === 'object' && !Array.isArray(value)) {
                Object.entries(value).forEach(([key, item]) => restoreScoringInput(name ? `${name}[${key}]` : key, item));
                return;
            }
            const controls = [...document.getElementById('scoringForm').elements].filter(input => input.name === name + (Array.isArray(value) ? '[]' : ''));
            controls.forEach(input => {
                if (input.type === 'checkbox') input.checked = (value || []).map(String).includes(input.value);
                else if (input.multiple) [...input.options].forEach(option => { option.selected = (value || []).map(String).includes(option.value); });
                else input.value = value ?? '';
            });
        }
        restoreScoringInput('', previousInput);
        calculateTotalScore();
    @endif

    function filterScheduleTable() {
        const input = document.getElementById('tableSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('.schedule-row');

        rows.forEach(row => {
            const staffName = row.querySelector('.staff-name-col').textContent.toLowerCase();
            if (staffName.includes(input)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
</script>
@endsection
