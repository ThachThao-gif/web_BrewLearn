@extends('layouts.app')

@section('title', 'Tổng quan lộ trình - BrewLearn')

@section('content')

    <!-- ================= THÔNG BÁO TỪ TRƯỜNG CA / LỊCH SÁT HẠCH ================= -->
    @if(isset($notifications) && $notifications->count() > 0)
        <div class="mb-6 space-y-3">
            @foreach($notifications as $noti)
                @php
                    // Lấy loại thông báo (Mặc định là 'general' nếu null)
                    $type = $noti->type ?? 'general';

                    // Cấu hình giao diện động theo từng loại thông báo
                    $config = [
                        'warning' => [
                            'bg' => 'bg-red-50',
                            'border' => 'border-red-500',
                            'icon_bg' => 'bg-red-100 text-red-800',
                            'icon' => 'fa-triangle-exclamation',
                            'badge_bg' => 'bg-red-100 text-red-800 border-red-200',
                            'badge_text' => 'Nhắc nhở / Sự cố',
                        ],
                        'incident' => [
                            'bg' => 'bg-red-50',
                            'border' => 'border-red-500',
                            'icon_bg' => 'bg-red-100 text-red-800',
                            'icon' => 'fa-triangle-exclamation',
                            'badge_bg' => 'bg-red-100 text-red-800 border-red-200',
                            'badge_text' => 'Nhắc nhở / Sự cố',
                        ],
                        'schedule' => [
                            'bg' => 'bg-amber-50',
                            'border' => 'border-amber-500',
                            'icon_bg' => 'bg-amber-100 text-amber-800',
                            'icon' => 'fa-calendar-check',
                            'badge_bg' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'badge_text' => 'Lịch sát hạch',
                        ],
                        'general' => [
                            'bg' => 'bg-blue-50',
                            'border' => 'border-blue-500',
                            'icon_bg' => 'bg-blue-100 text-blue-800',
                            'icon' => 'fa-circle-info',
                            'badge_bg' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'badge_text' => 'Thông báo chung',
                        ],
                    ];

                    $style =$config[$type] ?? $config['general'];

                    // Fallbacks dữ liệu tiêu đề và nội dung
                    $notiTitle = $noti->title ?? $noti->tieu_de ?? 'Thông báo từ hệ thống';
                    $notiMsg   = $noti->message ?? $noti->noi_dung ?? $noti->content ?? '';
                @endphp

                <div class="{{ $style['bg'] }} border-l-4 {{$style['border'] }} p-4 rounded-r-2xl shadow-sm flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="p-2 {{ $style['icon_bg'] }} rounded-xl mt-0.5">
                            <i class="fa-solid {{ $style['icon'] }} text-base"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-bold text-coffee-900 text-sm">{{ $notiTitle }}</h4>
                                
                                <!-- Badge Phân loại -->
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md border {{ $style['badge_bg'] }}">
                                    {{ $style['badge_text'] }}
                                </span>

                                @if(!$noti->is_read)
                                    <span class="bg-red-500 text-white text-[9px] font-bold px-2 py-0.5 rounded-full uppercase">Mới</span>
                                @endif
                            </div>

                            <p class="text-xs text-coffee-800 mt-1 leading-relaxed whitespace-pre-line">{{ $notiMsg }}</p>
                            
                            <span class="text-[10px] text-slate-400 mt-2 block">
                                <i class="fa-regular fa-clock mr-1"></i>{{ \Carbon\Carbon::parse($noti->created_at)->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    
                    @if(!$noti->is_read)
                        <button onclick="markRead({{ $noti->id }})" class="text-xs text-slate-600 hover:text-coffee-900 font-semibold underline shrink-0 cursor-pointer">
                            Đánh dấu đã đọc
                        </button>
                    @endif
                </div>
            @endforeach
        </div>

        <script>
        function markRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            })
            .catch(() => {
                // Fallback route dự phòng
                fetch(`/notifications/mark-as-read/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => location.reload());
            });
        }
        </script>
    @endif
    <!-- ========================================================================= -->

    <!-- Banner Chào mừng -->
    <div class="bg-gradient-to-r from-coffee-900 via-coffee-800 to-coffee-700 rounded-3xl p-6 text-amber-50 shadow-xl relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="z-10">
            <span class="bg-amber_brand/80 text-white text-[11px] font-bold px-3 py-1 rounded-full border border-amber-300/30 uppercase tracking-wider">
                <i class="fa-solid fa-award mr-1"></i> Lộ trình: Barista Tiêu chuẩn Quốc tế
            </span>
            <h2 class="text-2xl md:text-3xl font-extrabold mt-3 mb-1">Chào buổi sáng, {{ auth()->user()->name ?? 'Minh Thu' }}! ☕</h2>
            <p class="text-coffee-200/90 text-xs md:text-sm max-w-xl">
                Bạn đã hoàn thành <span class="font-bold text-white">78%</span> lộ trình Cấp độ 2. Hoàn thành bài kiểm tra tuần này để xét duyệt lên Cấp độ 3!
            </p>
        </div>
        <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/15 flex items-center justify-between md:justify-end gap-4 w-full md:w-auto shrink-0 z-10">
            <div>
                <p class="text-[11px] text-coffee-200 font-semibold uppercase">Bài tiếp theo</p>
                <p class="font-bold text-xs md:text-sm text-white truncate max-w-[180px]">Bài 04: Kỹ thuật Steaming Sữa Microfoam</p>
            </div>
            <a href="{{ route('courses.show', ['id' => 4]) }}" class="bg-amber-100 hover:bg-white text-coffee-900 font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-sm shrink-0 flex items-center gap-1.5">
                <span>Tiếp tục</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    <!-- 4 Thẻ Thống Kê -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-coffee-200/60 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-coffee-700 bg-coffee-100 px-2.5 py-1 rounded-lg">Đang diễn ra</span>
                <i class="fa-solid fa-book-open text-coffee-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-coffee-900 mt-3">4 <span class="text-xs font-semibold text-slate-500">khóa</span></p>
            <p class="text-xs text-slate-500 mt-1">Khóa học đang theo học</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-coffee-200/60 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber_brand bg-amber_light px-2.5 py-1 rounded-lg">+4.5h tuần này</span>
                <i class="fa-solid fa-clock text-amber-400"></i>
            </div>
            <p class="text-3xl font-extrabold text-coffee-900 mt-3">32.5 <span class="text-xs font-semibold text-slate-500">giờ</span></p>
            <p class="text-xs text-slate-500 mt-1">Giờ thực hành tích lũy</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-coffee-200/60 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg">Xuất sắc</span>
                <i class="fa-solid fa-chart-line text-emerald-500"></i>
            </div>
            <p class="text-3xl font-extrabold text-coffee-900 mt-3">92<span class="text-xs font-semibold text-slate-500">/100</span></p>
            <p class="text-xs text-slate-500 mt-1">Điểm Quiz trung bình</p>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-coffee-200/60 shadow-sm hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-coffee-700 bg-coffee-100 px-2.5 py-1 rounded-lg">3 chứng chỉ</span>
                <i class="fa-solid fa-certificate text-coffee-500"></i>
            </div>
            <p class="text-3xl font-extrabold text-coffee-900 mt-3">3 <span class="text-xs font-semibold text-slate-500">hoàn tất</span></p>
            <p class="text-xs text-slate-500 mt-1">ATTP • CSKH • Espresso</p>
        </div>
    </div>

    <!-- KHU VỰC NỘI DUNG CHÍNH (2 CỘT) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Cột trái (Chiếm 2 phần): Khóa học đang học & Bài kiểm tra -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Danh sách Khóa Học Tiếp Tục -->
            <div class="bg-white rounded-3xl p-6 border border-coffee-200/60 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-coffee-900 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-amber_brand"></i> Khóa học đang tiến hành
                    </h3>
                    <a href="{{ route('courses.index') }}" class="text-xs font-bold text-coffee-700 hover:text-amber_brand transition">Xem tất cả →</a>
                </div>

                <div class="space-y-4">
                    <!-- Item 1 -->
                    <div class="p-4 rounded-2xl bg-coffee-50/50 border border-coffee-100 hover:border-coffee-300 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-blender text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-coffee-900">Kỹ thuật Chiết xuất & Steaming Sữa</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Bài 4/8 • 45 phút còn lại</p>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-4">
                            <div class="w-24 bg-slate-200 h-2 rounded-full overflow-hidden hidden sm:block">
                                <div class="bg-amber_brand h-[100%]" style="width: 60%"></div>
                            </div>
                            <a href="{{ route('courses.show', ['id' => 1]) }}" class="px-3.5 py-2 bg-coffee-800 hover:bg-coffee-900 text-white rounded-xl text-xs font-semibold transition">Học tiếp</a>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="p-4 rounded-2xl bg-coffee-50/50 border border-coffee-100 hover:border-coffee-300 transition flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-handshake text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-coffee-900">Quy chuẩn Phục vụ & CSKH tại Bar</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Bài 2/5 • 20 phút còn lại</p>
                            </div>
                        </div>
                        <div class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-4">
                            <div class="w-24 bg-slate-200 h-2 rounded-full overflow-hidden hidden sm:block">
                                <div class="bg-amber_brand h-[100%]" style="width: 40%"></div>
                            </div>
                            <a href="{{ route('courses.show', ['id' => 2]) }}" class="px-3.5 py-2 bg-coffee-800 hover:bg-coffee-900 text-white rounded-xl text-xs font-semibold transition">Học tiếp</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bài Kiểm Tra / Sát Hạch Sắp Tới -->
            <div class="bg-white rounded-3xl p-6 border border-coffee-200/60 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-coffee-900 flex items-center gap-2">
                        <i class="fa-solid fa-file-signature text-amber_brand"></i> Lịch kiểm tra & Sát hạch
                    </h3>
                    <button onclick="openRubricModal()" class="text-xs font-bold text-amber_brand hover:underline cursor-pointer">
                        <i class="fa-solid fa-circle-info mr-1"></i> Xem tiêu chí chấm điểm
                    </button>
                </div>

                <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-200/80 flex items-center justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber_brand text-white flex items-center justify-center shrink-0 font-bold">
                            <i class="fa-solid fa-stopwatch text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-coffee-900">Sát hạch kỹ năng Latte Art Cơ bản</h4>
                            <p class="text-xs text-amber-900/80 mt-0.5">Thời hạn: Trước 18:00 - Thứ Sáu tuần này</p>
                        </div>
                    </div>
                    <a href="{{ route('tests.show', ['id' => 101]) }}" class="px-4 py-2 bg-amber_brand hover:bg-[#C25A1A] text-white rounded-xl text-xs font-bold transition shrink-0 shadow-sm">
                        Làm bài test
                    </a>
                </div>
            </div>

        </div>

        <!-- Cột phải (Chiếm 1 phần): Nhiệm vụ hàng ngày & Tra cứu nhanh -->
        <div class="space-y-6">
            
            <!-- Thẻ Nhiệm Vụ Hôm Nay (Checklist) -->
            <div class="bg-white rounded-3xl p-6 border border-coffee-200/60 shadow-sm">
                <h3 class="text-base font-bold text-coffee-900 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-amber_brand"></i> Mục tiêu hôm nay
                </h3>
                <div class="space-y-3">
                    <label class="flex items-center space-x-3 p-3 rounded-xl bg-coffee-50/50 hover:bg-coffee-50 cursor-pointer border border-coffee-100 transition">
                        <input type="checkbox" checked class="w-4 h-4 accent-coffee-800 rounded">
                        <span class="text-xs font-semibold text-slate-500 line-through">Xem video chỉnh Grind Size</span>
                    </label>

                    <label class="flex items-center space-x-3 p-3 rounded-xl bg-coffee-50/50 hover:bg-coffee-50 cursor-pointer border border-coffee-100 transition">
                        <input type="checkbox" class="w-4 h-4 accent-coffee-800 rounded">
                        <span class="text-xs font-bold text-coffee-900">Thực hành 3 ly Latte hình Heart</span>
                    </label>

                    <label class="flex items-center space-x-3 p-3 rounded-xl bg-coffee-50/50 hover:bg-coffee-50 cursor-pointer border border-coffee-100 transition">
                        <input type="checkbox" class="w-4 h-4 accent-coffee-800 rounded">
                        <span class="text-xs font-bold text-coffee-900">Nộp bài test trắc nghiệm SOP ca tối</span>
                    </label>
                </div>
            </div>

            <!-- Thẻ Tra Cứu Công Thức SOP Nhanh -->
            <div class="bg-gradient-to-br from-coffee-800 to-coffee-900 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
                <h3 class="text-base font-bold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-book-bookmark text-amber-300"></i> Tra cứu SOP Nhanh
                </h3>
                <p class="text-xs text-coffee-200 mb-4">Cần kiểm tra định lượng công thức trong ca làm việc?</p>
                
                <div class="space-y-2">
                    <button onclick="showRecipe('Espresso chuẩn SOP', '18g bột -> 36ml dịch (25-30 giây)', 'Nhiệt độ nước 92°C - 94°C')" class="w-full text-left px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold transition flex justify-between items-center cursor-pointer">
                        <span>• Espresso Chuẩn</span>
                        <i class="fa-solid fa-angle-right text-[10px]"></i>
                    </button>
                    <button onclick="showRecipe('Cappuccino chuẩn SOP', '1 shot Espresso + 120ml Milk (Foam dày 1.5cm)', 'Góc nghiêng vòi steam 45 độ')" class="w-full text-left px-3.5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-xs font-semibold transition flex justify-between items-center cursor-pointer">
                        <span>• Cappuccino</span>
                        <i class="fa-solid fa-angle-right text-[10px]"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection