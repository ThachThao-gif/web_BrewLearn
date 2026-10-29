<!DOCTYPE html>
<html lang="vi" class="h-full bg-[#FAF7F2]">
<head>
    <script>
        const accountTheme = @json(auth()->user()?->theme ?? 'light');
        const themeMedia = window.matchMedia('(prefers-color-scheme: dark)');
        function applyAccountTheme() {
            document.documentElement.dataset.theme = accountTheme === 'system' ? (themeMedia.matches ? 'dark' : 'light') : accountTheme;
        }
        applyAccountTheme();
        themeMedia.addEventListener('change', applyAccountTheme);
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'BrewLearn - Hệ Thống Đào Tạo & Quản Lý Năng Lực Barista')</title>

    <!-- Tailwind CSS qua CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coffee: {
                            50: '#FDFBF7', 100: '#FAF7F2', 200: '#F3ECE1', 300: '#E5D8C5',
                            400: '#CBB296', 500: '#A88764', 600: '#8C6743', 700: '#6E4E30',
                            800: '#4A2E1B', 900: '#2C190E',
                        },
                        honey: { 50: '#FBF4E6', 100: '#F6E7C8', 200: '#EBD3A4', 600: '#A8741F', 700: '#8A5E1A', 800: '#6E4A16', 900: '#523712' },
                        caramel: { 50: '#F8EEE6', 100: '#F0DCCB', 200: '#E2C2A6', 600: '#9A5B32', 700: '#7E4826', 800: '#62381E', 900: '#4A2A16' },
                        olive: { 50: '#F1F2E6', 100: '#E2E5CC', 200: '#CDD3A9', 600: '#6B7A3A', 700: '#56632E', 800: '#434D24', 900: '#33391B' },
                        terra: { 50: '#F9ECE6', 100: '#F2D6CB', 200: '#E6B9A7', 600: '#B0543A', 700: '#93432E', 800: '#753524', 900: '#57271B' },
                        amber_brand: '#D96B27',
                        sage_green: '#2E7D32',
                        amber_light: '#FFF5ED',
                    },
                    fontFamily: {
                        sans: ['Montserrat', '"Segoe UI"', 'Arial', 'sans-serif'],
                    },
                    fontWeight: {
                        semibold: '600',
                        bold: '600',
                        extrabold: '700',
                        black: '700',
                    }
                }
            }
        }
    </script>

    <!-- FontAwesome & Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        .page-back-button { position: fixed; bottom: 20px; left: 16px; z-index: 30; transition: left .25s ease; }
        @media (min-width: 768px) {
            .page-back-button { left: calc(var(--sidebar-width, 288px) + 24px); }
            body.sidebar-collapsed .page-back-button { left: 24px; }
        }
        #mainContent > main { padding-bottom: 88px; }
        #sidebar nav a[aria-current="page"], #sidebar nav a[data-modal-active="true"] {
            background: #70512b !important;
            color: #fff !important;
            border: 1px solid #70512b !important;
            font-weight: 500;
            box-shadow: 0 3px 8px #49321520;
        }
        #sidebar nav a[aria-current="page"] i, #sidebar nav a[data-modal-active="true"] i { color: #fff !important; }
        #sidebar nav a, #sidebar h3 { font-weight: 500; }
        #sidebar nav a:focus-visible { outline: 2px solid #b78540; outline-offset: 3px; }
        #sidebar:has(a[data-modal-active="true"]) nav a[aria-current="page"] {
            background: transparent !important; color: #70512b !important; border-color: transparent !important; box-shadow: none;
        }
        #sidebar:has(a[data-modal-active="true"]) nav a[aria-current="page"] i { color: #70512b !important; }
        .page-banner {
            background: #70512b !important;
            color: #fff !important;
            border: 1px solid #70512b !important;
            border-radius: 24px !important;
            padding: 24px 28px !important;
            box-shadow: 0 3px 6px #49321518 !important;
        }
        .page-banner h1, .page-banner h1 i, .page-banner h2:first-of-type { color: #fff !important; text-transform: uppercase; letter-spacing: .02em; }
        .page-banner h1 { font-size: 24px; line-height: 1.35; font-weight: 600; }
        .page-banner > p, .page-banner > div > p,
        .page-banner > div > div > p { color: #fef3c7 !important; }
        @media (max-width: 640px) {
            .page-banner { padding: 20px !important; }
            .page-banner h1 { font-size: 20px; }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #FAF7F2; }
        ::-webkit-scrollbar-thumb { background: #D5C5B3; border-radius: 9999px; }
        ::-webkit-scrollbar-thumb:hover { background: #A88764; }
        .smooth-transition { transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        html[data-theme="dark"] { color-scheme: dark; background: #171717; }
        html[data-theme="dark"] body, html[data-theme="dark"] #sidebar,
        html[data-theme="dark"] [class*="bg-[#FAF7F2]"], html[data-theme="dark"] [class*="bg-white"] { background-color: #242424; }
        html[data-theme="dark"] [class*="text-coffee-"], html[data-theme="dark"] [class*="text-slate-"],
        html[data-theme="dark"] [class*="text-gray-"] { color: #E8DFC8; }
        html[data-theme="dark"] input, html[data-theme="dark"] select, html[data-theme="dark"] textarea,
        html[data-theme="dark"] [class*="bg-slate-"], html[data-theme="dark"] [class*="bg-[#EFE7DA]"] { background-color: #333; color: #E8DFC8; }
        #sidebar { width: var(--sidebar-width, 288px); height: 100vh; height: 100dvh; overflow: hidden; transition: width .25s ease, transform .25s ease; }
        #sidebarResizeHandle { display: none; }
        body.sidebar-resizing { cursor: col-resize; user-select: none; }
        body.sidebar-resizing #sidebar { transition: none; }
        @media (min-width: 768px) {
            #sidebar { position: fixed; top: 0; transform: none; }
            #mainContent { margin-left: var(--sidebar-width, 288px); transition: margin-left .25s ease; }
            body.sidebar-collapsed #mainContent { margin-left: 0; }
            body.sidebar-resizing #mainContent { transition: none; }
            #sidebarResizeHandle { display: block; }
            #sidebar.sidebar-collapsed { width: 0; overflow: hidden; border-width: 0; visibility: hidden; }
        }
        @media (max-width: 767px) {
            #sidebar { width: min(var(--sidebar-width, 288px), 85vw); transform: translateX(-100%); }
            #sidebar.mobile-open { transform: translateX(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            #sidebar { transition: none; }
        }
    </style>
    <!-- Nhúng Alpine.js để xử lý Modal Bật Ra -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="h-full text-slate-800 font-sans antialiased bg-[#FAF7F2] flex flex-col md:flex-row overflow-x-hidden">

    @php
        // Lấy thông tin user hiện tại
        $currentUser = auth()->user() ?? $user ?? null;
        
        // Kiểm tra vai trò
        $role =$currentUser->role ?? '';
        $chucVu =$currentUser->ChucVu ?? '';

        $isLeader = ($role === 'truong_ca' || mb_strtolower($chucVu) === 'trưởng ca' || $role === 'leader');
        $isManager = in_array(mb_strtolower(trim($chucVu)), ['quản lý cửa hàng', 'quan ly cua hang', 'manager']);
    @endphp

    <!-- MOBILE SIDEBAR BACKDROP -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-30 hidden md:hidden transition-opacity"></div>

    <!-- SIDEBAR BÊN TRÁI -->
    <aside id="sidebar" class="w-72 bg-[#FAF7F2] border-r border-[#E8DFC8] flex flex-col justify-between shrink-0 fixed md:static inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out h-screen">
        
        <!-- PHẦN ĐỈNH + DANH SÁCH MENU -->
        <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain">
            <!-- LOGO AREA -->
            <div class="px-6 py-5 flex items-center justify-between border-b border-[#E8DFC8]/50 sticky top-0 bg-[#FAF7F2] z-10">
                <a href="{{ route($isManager ? 'manager.dashboard' : 'dashboard') }}" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#4A2E1B] to-[#6E4E30] flex items-center justify-center text-white shadow-md shadow-coffee-800/10">
                        <i class="fa-solid fa-mug-hot text-lg"></i>
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-coffee-900 block leading-none">BrewLearn</span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-coffee-600 block mt-1">Academy & SOP System</span>
                    </div>
                </a>
                <button type="button" onclick="toggleSidebar()" aria-label="Ẩn menu" title="Ẩn menu" class="text-slate-500 hover:text-slate-800 p-1 shrink-0">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- NAVIGATION LINKS -->
            <div class="p-4 space-y-6">
                
                <!-- 1. CHỈ HIỂN THỊ CHO TRƯỞNG CA -->
@if($isManager)
    <div>
        <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-coffee-500 mb-2">Quản lý cửa hàng</h3>
        <nav class="space-y-1">
            @foreach(['dashboard' => ['chart-pie', 'Tổng quan cửa hàng'], 'staff' => ['users', 'Hồ sơ nhân viên'], 'assessments' => ['clipboard-check', 'Duyệt đánh giá'], 'training' => ['arrows-rotate', 'Giao đào tạo lại'], 'notifications' => ['bell', 'Thông báo']] as $page => [$icon, $label])
                <a aria-current="{{ request()->routeIs('manager.'.$page) || ($page === 'staff' && request()->routeIs('manager.profile')) ? 'page' : 'false' }}" href="{{ route('manager.'.$page) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('manager.'.$page) || ($page === 'staff' && request()->routeIs('manager.profile')) ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:bg-[#F3ECE1]' }}"><i class="fa-solid fa-{{ $icon }} w-5 text-center text-coffee-600"></i><span>{{ $label }}</span></a>
            @endforeach
        </nav>
    </div>
@elseif($isLeader)
    <div>
        <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-coffee-500 mb-2">Quản lý nhân viên</h3>
        <nav class="space-y-1">
            <a aria-current="{{ request()->routeIs('leader.staffList') ? 'page' : 'false' }}" href="{{ route('leader.staffList') }}" 
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('leader.staffList') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm font-semibold' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }}">
                <i class="fa-solid fa-id-card w-5 text-center text-slate-500"></i>
                <span>Danh Sách Nhân Sự Ca</span>
            </a>

            <a aria-current="{{ request()->routeIs('leader.dashboard') ? 'page' : 'false' }}" href="{{ route('leader.dashboard') }}" 
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1] transition">
                <i class="fa-solid fa-clipboard-check w-5 text-center text-slate-500"></i>
                <span>Xếp Lịch Đánh Giá</span>
            </a>

            <a aria-current="{{ request()->routeIs('leader.scoring') ? 'page' : 'false' }}" href="{{ route('leader.scoring') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1] transition">
                <i class="fa-solid fa-pen-to-square w-5 text-center text-slate-500"></i>
                <span>Chấm Điểm Năng Lực</span>
            </a>

            <!-- 🔥 KHÔI PHỤC MỤC GỬI THÔNG BÁO CHO TRƯỞNG CA -->
            <a aria-current="{{ request()->routeIs('training.*') ? 'page' : 'false' }}" href="{{ route('training.assignments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-coffee-100"><i class="fa-solid fa-user-check w-5 text-center"></i>Đào tạo lại tôi phụ trách</a>
            <a href="javascript:void(0)" onclick="openSendNotificationModal()" 
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1] transition">
                <i class="fa-solid fa-paper-plane w-5 text-center text-amber_brand"></i>
                <span>Gửi Thông Báo Nhân Viên</span>
            </a>

            <!-- ⚠️ MỤC GHI NHẬN LỖI & SỰ CỐ TRONG CA -->
            <a href="javascript:void(0)" onclick="openIncidentModal()" 
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-red-700 hover:bg-red-50/80 transition border border-transparent hover:border-red-100">
                <i class="fa-solid fa-triangle-exclamation w-5 text-center text-red-500"></i>
                <span>Ghi Nhận Lỗi & Sự Cố</span>
            </a>
        </nav>
    </div>
    <div>
        <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-coffee-500 mb-2">Học tập & kiểm tra của tôi</h3>
        <nav class="space-y-1">
            <a aria-current="{{ request()->routeIs('courses.index', 'courses.show', 'courses.lesson', 'lessons.*') ? 'page' : 'false' }}" href="{{ route('courses.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('courses.*', 'lessons.*') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
                <i class="fa-solid fa-book-open w-5 text-center {{ request()->routeIs('courses.*', 'lessons.*') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
                <span>Khóa học & Bài giảng</span>
            </a>
            <a aria-current="{{ request()->routeIs('tests.*') ? 'page' : 'false' }}" href="{{ route('tests.index') }}"
               class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('tests.*') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
                <i class="fa-solid fa-clipboard-check w-5 text-center {{ request()->routeIs('tests.*') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
                <span>Kiểm tra của tôi</span>
            </a>
        </nav>
    </div>
@else
                 <!-- 2. CHỈ HIỂN THỊ CHO NHÂN VIÊN -->
<div>
    <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-coffee-500 mb-2">Học Tập & Đánh Giá</h3>
    <nav class="space-y-1">
        <!-- Tổng quan lộ trình -->
        <a aria-current="{{ request()->routeIs('dashboard', 'courses.roadmap') ? 'page' : 'false' }}" href="{{ route('dashboard', ['user_id' => auth()->id() ?? request()->query('user_id', 1)]) }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('dashboard') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
            <i class="fa-solid fa-chart-pie w-5 text-center {{ request()->routeIs('dashboard') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
            <span>Tổng quan lộ trình</span>
        </a>

        <!-- Khóa học & Bài giảng -->
        <a aria-current="{{ request()->routeIs('courses.index', 'courses.show', 'courses.lesson', 'lessons.*') ? 'page' : 'false' }}" href="{{ route('courses.index', ['user_id' => auth()->id() ?? request()->query('user_id', 1)]) }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('courses.*') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
            <i class="fa-solid fa-book-open w-5 text-center {{ request()->routeIs('courses.*') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
            <span>Khóa học & Bài giảng</span>
        </a>

        <!-- Kiểm tra & Đánh giá -->
        <a aria-current="{{ request()->routeIs('tests.*') ? 'page' : 'false' }}" href="{{ route('tests.index', ['user_id' => auth()->id() ?? request()->query('user_id', 1)]) }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('tests.*') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
            <i class="fa-solid fa-clipboard-check w-5 text-center {{ request()->routeIs('tests.*') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
            <span>Kiểm tra & Đánh giá</span>
        </a>

        <!-- Tiêu chuẩn năng lực -->
        <a aria-current="{{ request()->routeIs('assessments.*') ? 'page' : 'false' }}" href="{{ route('assessments.show') }}" 
           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium {{ request()->routeIs('assessments.show') ? 'text-coffee-900 bg-[#EFE7DA] border border-[#E2D4C1] shadow-sm' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }} transition">
            <i class="fa-solid fa-list-check w-5 text-center {{ request()->routeIs('assessments.show') ? 'text-coffee-800' : 'text-slate-500' }}"></i>
            <span>Tiêu chuẩn năng lực</span>
        </a>
    </nav>
</div>
@endif
               <!-- 3. TIÊU CHUẨN & HỖ TRỢ -->
<div class="mt-4">
    <h3 class="px-3 text-[11px] font-bold uppercase tracking-wider text-coffee-500 mb-2">TIÊU CHUẨN & HỖ TRỢ</h3>
    <nav class="space-y-1">
        <!-- 🔥 CHỈ ẨN SỔ TAY SOP VỚI TRƯỞNG CA (NHÂN VIÊN VẪN THẤY) -->
        @if(!$isLeader && !$isManager)
            <a href="javascript:void(0)" onclick="openSOPModal()" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1] transition">
                <i class="fa-solid fa-book-bookmark w-5 text-center text-slate-500"></i>
                <span>Sổ tay pha chế & SOP</span>
            </a>
        @endif

        <!-- DÙNG CHUNG CHO CẢ LEADER & NHÂN VIÊN -->
        @if(!$isLeader && \Illuminate\Support\Facades\Schema::hasTable('retraining_rounds') && \Illuminate\Support\Facades\DB::table('retraining_rounds')->where('owner_id', auth()->id())->exists())
            <a aria-current="{{ request()->routeIs('training.*') ? 'page' : 'false' }}" href="{{ route('training.assignments') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-coffee-100"><i class="fa-solid fa-user-check w-5 text-center"></i>Đào tạo lại tôi phụ trách</a>
        @endif
       

        <a aria-current="{{ request()->routeIs('users.*') ? 'page' : 'false' }}" href="{{ route('users.edit', ['id' => $currentUser->id ?? 1]) }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('users.edit') ? 'text-coffee-900 bg-[#F3ECE1] font-semibold' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }}">
            <i class="fa-solid fa-gear w-5 text-center text-slate-500"></i>
            <span>Cài đặt tài khoản</span>
        </a>

        <a aria-current="{{ request()->routeIs('policies.*') ? 'page' : 'false' }}" href="{{ route('policies.index', ['user_id' => $currentUser->id ?? 1]) }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('policies.index') ? 'text-coffee-900 bg-[#F3ECE1] font-semibold' : 'text-slate-600 hover:text-coffee-900 hover:bg-[#F3ECE1]' }}">
            <i class="fa-solid fa-file-contract w-5 text-center text-slate-500"></i>
            <span>Nội quy & Chính sách</span>
        </a>
         <!-- Nút Trợ giúp & Hỗ trợ trong Sidebar -->
<a aria-current="{{ request()->routeIs('help.*') ? 'page' : 'false' }}" href="{{ route('help.index') }}"
   class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('help.*') ? 'bg-amber-100 text-amber-900 border border-amber-300 shadow-sm' : 'text-slate-600 hover:bg-amber-50/50' }}">
    <i class="fa-solid fa-headset text-base text-amber-700"></i>
    <span>Trợ giúp & Hỗ trợ</span>
</a>

        <!-- Nút Đăng xuất -->
        <form action="{{ route('logout') }}" method="POST" class="mt-1">
            @csrf
            <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 transition text-left">
                <i class="fa-solid fa-right-from-bracket w-5 text-center text-red-500"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </nav>
</div>

            </div>
        </div>

       <!-- THẺ CÁ NHÂN CỦA USER Ở ĐÁY SIDEBAR -->
        <div class="shrink-0 p-4 border-t border-[#E8DFC8] bg-[#FAF7F2]">
            <div class="bg-[#EFE7DA]/80 border border-[#E2D4C1] rounded-2xl p-3 flex items-center justify-between gap-2 shadow-sm">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-amber_brand text-white flex items-center justify-center font-extrabold text-xs shrink-0 shadow">
                        {{ mb_substr($currentUser->name ?? 'M', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <h4 class="text-xs font-bold text-coffee-900 truncate">{{ $currentUser->name ?? 'Trần Minh Thư' }}</h4>
                        <div class="text-[10px] text-slate-500 truncate">
                            {{ $currentUser->ChucVu ?? 'Trưởng Ca' }} • {{ $currentUser->ChiNhanh ?? 'Flagship Q.1' }}
                        </div>
                        @if(!$isManager)
                        <div class="w-24 bg-white/80 h-1.5 rounded-full overflow-hidden mt-1 border border-slate-200">
                            <div class="bg-amber_brand h-full rounded-full" style="width: {{ $currentUser?->progress_percent ?? 0 }}%"></div>
                        </div>
                        @endif
                    </div>
                </div>
                <span class="bg-amber-900 text-amber-100 text-[10px] font-extrabold px-2 py-1 rounded-lg shrink-0">
                    {{ $isManager ? 'QL' : 'Cấp '.($currentUser->CapDo ?? 2) }}
                </span>
            </div>
        </div>
        <div id="sidebarResizeHandle" role="separator" tabindex="0" aria-label="Thay đổi độ rộng menu" aria-orientation="vertical" aria-valuemin="240" aria-valuemax="420" aria-valuenow="288" title="Kéo để thay đổi độ rộng menu; dùng phím mũi tên trái/phải" class="absolute right-0 top-0 bottom-0 w-2 cursor-col-resize touch-none hover:bg-coffee-300 focus:bg-coffee-300 focus:outline-none"></div>
    </aside>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            [['sopModal', 'openSOPModal()'], ['sendNotificationModal', 'openSendNotificationModal()'], ['incidentModal', 'openIncidentModal()']].forEach(([id, action]) => {
                const modal = document.getElementById(id);
                const link = document.querySelector(`#sidebar nav a[onclick="${action}"]`);
                if (!modal || !link) return;
                const update = () => {
                    const open = !modal.classList.contains('hidden');
                    link.dataset.modalActive = String(open);
                    link.setAttribute('aria-expanded', String(open));
                };
                link.setAttribute('aria-controls', id);
                new MutationObserver(update).observe(modal, { attributes: true, attributeFilter: ['class'] });
                update();
            });
        });
    </script>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="mainContent" class="flex-1 flex flex-col min-w-0 min-h-screen">
        <!-- TOPBAR HEADER -->
        <header class="bg-[#FAF7F2]/90 backdrop-blur-md sticky top-0 z-20 border-b border-[#E8DFC8] px-4 md:px-8 py-3.5 flex items-center justify-between gap-4 w-full">
            
            <!-- BÊN TRÁI: Nút Mobile + Chi nhánh + Ô tìm kiếm -->
            <div class="flex items-center gap-3 flex-1 max-w-2xl">
                <button id="sidebarToggle" type="button" onclick="toggleSidebar()" aria-controls="sidebar" aria-expanded="true" aria-label="Ẩn menu" title="Ẩn menu" class="p-2 rounded-lg bg-[#EFE7DA] text-coffee-800 hover:bg-[#E5D8C5] shrink-0">
                    <i class="fa-solid fa-bars"></i>
                </button>
                
                <!-- Chi nhánh làm việc của Nhân viên -->
                <div class="inline-flex items-center space-x-2 bg-[#EFE7DA] border border-[#E2D4C1] px-3.5 py-2 rounded-xl text-xs md:text-sm font-semibold text-coffee-900 shadow-sm shrink-0 select-none">
                    <i class="fa-solid fa-store text-amber_brand"></i>
                    <span>{{ $currentUser->ChiNhanh ?? 'BrewLearn Thảo Điền' }}</span>
                </div>

                <!-- Search Bar -->
                <form action="{{ route($isManager ? 'manager.staff' : 'search') }}" method="GET" class="hidden sm:flex flex-1 relative">
                    <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-coffee-800 text-sm transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                    <input type="text" 
                           name="q" 
                           value="{{ request('q') }}" 
                           placeholder="{{ $isManager ? 'Tìm nhân viên trong cửa hàng...' : 'Tìm bài học, công thức latte, quy trình SOP...' }}"
                           class="w-full bg-[#EFE7DA]/60 border border-[#E2D4C1] rounded-xl pl-10 pr-4 py-2 text-xs md:text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-coffee-600 focus:bg-white transition"
                           required>
                </form>
            </div> <!-- ĐÃ BỔ SUNG THẺ ĐÓNG DIV KHỐI BÊN TRÁI -->

           <!-- BÊN PHẢI: Tài khoản đang đăng nhập & Quả chuông Thông báo -->
<div class="flex items-center space-x-2 md:space-x-3 shrink-0">
    @if($currentUser)
        <a href="{{ route('users.edit', ['id' => $currentUser->id]) }}" title="Cài đặt tài khoản" class="flex items-center gap-2.5 rounded-xl border border-[#E2D4C1] bg-white py-1.5 pl-1.5 pr-3 shadow-sm transition hover:bg-[#FAF7F2]">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-coffee-800 text-xs font-bold text-amber-100" aria-hidden="true">{{ mb_strtoupper(mb_substr(collect(explode(' ', trim($currentUser->name)))->last(), 0, 1)) }}</span>
            <span class="hidden min-w-0 text-left sm:block">
                <span class="block max-w-40 truncate text-xs font-bold text-coffee-900">{{ $currentUser->name }}</span>
                <span class="block max-w-40 truncate text-[10px] text-slate-500">{{ $currentUser->ChucVu ?? 'Nhân viên' }}</span>
            </span>
        </a>
    @endif

    <!-- ================= QUẢ CHUÔNG & DROPDOWN THÔNG BÁO ================= -->
    <div class="relative inline-block text-left">
        <button type="button" onclick="toggleNotificationMenu()" class="relative p-2.5 rounded-xl bg-[#EFE7DA] hover:bg-[#E8DFC8] border border-[#E2D4C1] text-coffee-800 transition focus:outline-none cursor-pointer">
            <i class="fa-regular fa-bell text-base"></i>
            @if(isset($notifications) && $notifications->where('is_read', false)->count() > 0)
                <span id="bellUnreadBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-extrabold h-4 w-4 rounded-full flex items-center justify-center animate-pulse">
                    {{ $notifications->where('is_read', false)->count() }}
                </span>
            @endif
        </button>

        <!-- Menu xổ xuống khi bấm quả chuông -->
        <div id="notificationMenu" class="hidden absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 z-50 overflow-hidden border border-[#E8DFC8]">
            <div class="p-3.5 bg-[#FAF7F2] border-b border-[#E8DFC8] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bell text-amber_brand text-sm"></i>
                    <h3 class="font-bold text-coffee-900 text-xs">{{ $isManager ? 'Thông báo điều hành' : 'Thông báo từ Quản lý' }}</h3>
                </div>
                <span class="text-[10px] bg-amber-100 text-amber-900 font-bold px-2 py-0.5 rounded-full">
                    {{ isset($notifications) ? $notifications->count() : 0 }} tin
                </span>
            </div>

            <!-- Danh sách thông báo trong Dropdown -->
            <div class="max-h-80 overflow-y-auto divide-y divide-gray-100">
                @if(isset($notifications) && $notifications->count() > 0)
                    @foreach($notifications as $noti)
                        @php
                            $titleText     = $noti->title ?? $noti->tieu_de ?? 'Thông báo hệ thống';
                            $messageText   = $noti->message ?? $noti->noi_dung ?? $noti->content ?? '';
                            $typeText      = $noti->type ?? 'general';
                            $formattedTime = \Carbon\Carbon::parse($noti->created_at)->diffForHumans();
                            $fullTime      = \Carbon\Carbon::parse($noti->created_at)->format('H:i:s d/m/Y');
                        @endphp

                        <div onclick='handleBellClick({{ $noti->id }}, {
                                title: @json($titleText),
                                message: @json($messageText),
                                details: @json(app(\App\Models\NotificationDetails::class)->parse($messageText)),
                                type: @json($typeText),
                                created_at: @json($fullTime)
                            })' 
                             class="p-3.5 hover:bg-amber-50/60 transition cursor-pointer flex items-start justify-between gap-3 {{ !$noti->is_read ? 'bg-amber-50/20 border-l-2 border-red-500' : '' }}">
                            <div class="flex-1">
                                <div class="flex items-center justify-between gap-1.5 mb-1">
                                    <h4 class="text-xs font-bold text-coffee-900 line-clamp-1">{{ $titleText }}</h4>
                                    
                                    @if(!$noti->is_read)
                                        <span id="dot-{{ $noti->id }}" class="bg-red-500 h-2 w-2 rounded-full inline-block shrink-0"></span>
                                    @endif
                                </div>

                                <p class="text-[11px] text-gray-600 line-clamp-2 leading-relaxed">{{ $messageText }}</p>

                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[9px] text-gray-400">
                                        <i class="fa-regular fa-clock mr-1"></i>{{ $formattedTime }}
                                    </span>

                                    <!-- Badge nhỏ phân loại type -->
                                    @if(in_array($typeText, ['warning', 'incident']))
                                        <span class="text-[8px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded font-bold">Sự cố</span>
                                    @elseif($typeText === 'schedule')
                                        <span class="text-[8px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded font-bold">Lịch sát hạch</span>
                                    @else
                                        <span class="text-[8px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded font-bold">Hệ thống</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-6 text-center text-xs text-gray-400">
                        Không có thông báo nào.
                    </div>
                @endif
            </div>

            <!-- NÚT XEM TẤT CẢ -->
            <a href="{{ route($isManager ? 'manager.notifications' : 'notifications.index') }}"
               class="block text-center py-2.5 text-xs font-bold text-amber-900 bg-[#FAF7F2] hover:bg-[#EFE7DA] border-t border-[#E8DFC8] transition">
                Xem tất cả thông báo →
            </a>

        </div> <!-- Đóng #notificationMenu -->
    </div> <!-- Đóng .relative.inline-block -->
</div> <!-- Đóng div bên phải -->
</header>

<!-- Nút quay lại dùng chung cho nhân viên, trưởng ca và quản lý cửa hàng -->
    <div class="page-back-button">
        <button onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = this.dataset.fallback; }"
                data-fallback="{{ route($isManager ? 'manager.dashboard' : ($isLeader ? 'leader.dashboard' : 'dashboard')) }}"
                type="button"
                class="group inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-amber-950 bg-amber-100/90 backdrop-blur-md border border-amber-300/80 rounded-full shadow-lg hover:bg-amber-800 hover:text-white hover:border-amber-800 transition-all duration-200 active:scale-95 cursor-pointer">
            <svg class="w-4 h-4 text-amber-900 group-hover:text-white transition-transform group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Quay lại</span>
        </button>
    </div>

<!-- MAIN BODY CONTAINER -->
<main class="p-4 md:p-8 space-y-7 max-w-7xl w-full mx-auto flex-1">
    @yield('content')
</main>
</div>

<!-- GENERAL NOTIFICATION TOAST -->
<div id="toastNotification" class="fixed bottom-5 right-5 bg-coffee-900 text-white px-4 py-3 rounded-2xl shadow-2xl flex items-center space-x-3 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <i class="fa-solid fa-circle-info text-amber-400"></i>
    <span id="toastMessage" class="text-xs font-semibold">Thông báo từ hệ thống BrewLearn</span>
</div>

<!-- SCRIPT XỬ LÝ DROPDOWN VÀ MỞ DETAIL MODAL -->
<script>
    function toggleNotificationMenu() {
        const menu = document.getElementById('notificationMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function handleBellClick(id, item) {
        // 1. Tự động đánh dấu đã đọc qua API
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        }).catch(() => {
            fetch(`/notifications/mark-as-read/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            });
        });

        // 2. Ẩn chấm đỏ chưa đọc
        const dot = document.getElementById(`dot-${id}`);
        if (dot) dot.remove();

        // 3. Nếu đang có Modal mở chi tiết trên trang thì gọi mở Modal
        if (typeof openNotificationDetail === 'function') {
            openNotificationDetail(item);
        } else {
            // Nếu ở trang khác, chuyển hướng tới trang thông báo
            window.location.href = "{{ route('notifications.index') }}";
        }
    }

    // Đóng Dropdown khi click ra ngoài màn hình
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('notificationMenu');
        const bellBtn = event.target.closest('button[onclick="toggleNotificationMenu()"]');
        if (menu && !menu.contains(event.target) && !bellBtn && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
        }
    });
</script>

    <!-- MODAL POPUP TRA CỨU QUY CHUẨN & SOP BREWLEARN -->
<div id="sopModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-6 space-y-4 shadow-2xl relative border border-amber-100 max-h-[90vh] flex flex-col">
        
        <!-- Header -->
        <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-book-bookmark"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-coffee-900">Quy Chuẩn Vận Hành & SOP BrewLearn</h3>
                    <p class="text-xs text-slate-500">Tra cứu nhanh quy trình làm việc chuẩn hóa theo từng vị trí</p>
                </div>
            </div>
            <button type="button" onclick="closeSOPModal()" class="text-slate-400 hover:text-slate-800 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Tab Chuyển Vị Trí (Barista / Thu Ngân / Phục Vụ / Quản Lý Ca) -->
        <div class="flex items-center space-x-2 border-b border-slate-100 pb-2 flex-shrink-0 overflow-x-auto text-xs font-bold">
            <button type="button" onclick="switchSOPTab('barista')" id="tab-barista" class="sop-tab-btn px-3.5 py-2 rounded-xl bg-coffee-900 text-white transition flex items-center space-x-1.5 whitespace-nowrap">
                <i class="fa-solid fa-mug-hot"></i>
                <span>SOP Barista</span>
            </button>
            <button type="button" onclick="switchSOPTab('cashier')" id="tab-cashier" class="sop-tab-btn px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-coffee-900 transition flex items-center space-x-1.5 whitespace-nowrap">
                <i class="fa-solid fa-cash-register"></i>
                <span>SOP Thu Ngân</span>
            </button>
            <button type="button" onclick="switchSOPTab('service')" id="tab-service" class="sop-tab-btn px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-coffee-900 transition flex items-center space-x-1.5 whitespace-nowrap">
                <i class="fa-solid fa-concierge-bell"></i>
                <span>SOP Phục Vụ</span>
            </button>
            <button type="button" onclick="switchSOPTab('manager')" id="tab-manager" class="sop-tab-btn px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-coffee-900 transition flex items-center space-x-1.5 whitespace-nowrap">
                <i class="fa-solid fa-user-tie"></i>
                <span>Quản Lý Ca</span>
            </button>
        </div>

        <!-- Nội Dung Accordion xổ xuống (Cho phép Cuộn chuột) -->
        <div class="overflow-y-auto pr-1 space-y-2 flex-grow text-xs">
            
            <!-- 1. SOP BARISTA -->
            <div id="sop-content-barista" class="sop-tab-content space-y-2">
                
                <!-- Chuẩn bị trước ca -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('barista-1')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Chuẩn bị trước ca</span>
                        <i id="icon-barista-1" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="barista-1" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Có mặt trước giờ vào ca 15 phút, mặc đúng đồng phục và thẻ tên.</p>
                        <p>• Rửa tay tiệt trùng theo quy chuẩn 6 bước SOP.</p>
                        <p>• Kiểm tra bàn giao ca từ ca trước (nguyên liệu tồn, sự cố máy móc).</p>
                    </div>
                </div>

                <!-- Chuẩn bị nguyên liệu -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('barista-2')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Chuẩn bị nguyên liệu</span>
                        <i id="icon-barista-2" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="barista-2" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Kiểm tra hạn sử dụng (FIFO) nguyên liệu: Sữa tươi, mứt, syrup.</p>
                        <p>• Chuẩn bị đá viên, đong đầy hạt cà phê vào hộc máy xay.</p>
                    </div>
                </div>

                <!--  Pha Espresso & Đánh sữa -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('barista-3')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span> Pha Espresso & Đánh sữa</span>
                        <i id="icon-barista-3" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="barista-3" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• <strong>Chiết Espresso chuẩn:</strong> 25 - 30 giây lấy 36ml - 40ml dịch cà phê từ 18g bột.</p>
                        <p>• <strong>Nhiệt độ đánh sữa chuẩn:</strong> 60°C - 65°C (Không vượt quá 70°C làm cháy lactose).</p>
                        <p>• <strong>Vệ sinh vòi Steam:</strong> Xả hơi 1 giây & lau sạch bằng khăn ẩm chuyên dụng ngay sau mỗi lần đánh sữa.</p>
                    </div>
                </div>

                <!-- Vệ sinh & Bảo quản -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('barista-4')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Vệ sinh khu vực & Máy móc</span>
                        <i id="icon-barista-4" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="barista-4" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Backflush máy pha espresso bằng bột rửa chuyên dụng cuối ngày.</p>
                        <p>• Giặt và ngâm toàn bộ khăn lau quầy bar trong dung dịch khử trùng.</p>
                    </div>
                </div>

            </div>

            <!-- 2. SOP THU NGÂN -->
            <div id="sop-content-cashier" class="sop-tab-content space-y-2 hidden">
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('cashier-1')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Đăng nhập & Kiểm tra két tiền</span>
                        <i id="icon-cashier-1" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="cashier-1" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Đăng nhập tài khoản POS cá nhân.</p>
                        <p>• Kiểm tra và đếm tiền thối đầu ca (Tiền float) đúng định lượng chuẩn.</p>
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('cashier-2')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Tiếp nhận Order & Thanh toán</span>
                        <i id="icon-cashier-2" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="cashier-2" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Luôn tươi cười, chào khách bằng câu chuẩn: "BrewLearn xin chào [Tên khách]!".</p>
                        <p>• Xác nhận rõ: Tên món, Size, % Đường / Đá, Topping và hình thức Thanh toán (Tiền mặt/Chuyển khoản).</p>
                    </div>
                </div>
            </div>

            <!-- 3. SOP PHỤC VỤ -->
            <div id="sop-content-service" class="sop-tab-content space-y-2 hidden">
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('service-1')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span> Đón tiếp khách & Giao món</span>
                        <i id="icon-service-1" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="service-1" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Chủ động mở cửa và hướng dẫn khách vào bàn trống.</p>
                        <p>• Khi giao món: Đọc lại tên món nhẹ nhàng, chúc khách dùng ngon miệng.</p>
                    </div>
                </div>
            </div>

            <!-- 4. QUẢN LÝ CA -->
            <div id="sop-content-manager" class="sop-tab-content space-y-2 hidden">
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="toggleAccordion('manager-1')" class="w-full bg-slate-50 hover:bg-amber-50/50 p-3 text-left font-bold text-coffee-900 flex justify-between items-center transition">
                        <span>Giám sát & Ghi nhận lỗi trong ca</span>
                        <i id="icon-manager-1" class="fa-solid fa-chevron-down text-slate-400 text-xs transition-transform duration-200"></i>
                    </button>
                    <div id="manager-1" class="hidden p-3 bg-white text-slate-600 space-y-1.5 border-t border-slate-100">
                        <p>• Quan sát tác phong và việc tuân thủ SOP của các vị trí trong ca làm việc.</p>
                        <p>• Nếu phát hiện sai sót, thực hiện <strong>Ghi nhận sự cố & gửi Nhắc nhở nhẹ nhàng</strong> trên hệ thống để làm dữ liệu cho cảnh báo nguy cơ.</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Footer Modal -->
        <div class="pt-3 border-t border-slate-100 flex items-center justify-end flex-shrink-0">
            <button type="button" onclick="closeSOPModal()" class="px-5 py-2 bg-coffee-900 hover:bg-coffee-800 text-white font-bold rounded-xl transition">
                Đóng
            </button>
        </div>

    </div>
</div>

<!-- SCRIPT ĐIỀU KHIỂN TABS VÀ ACCORDION XỔ XUỐNG -->
<script>
    function openSOPModal() {
        document.getElementById('sopModal').classList.remove('hidden');
    }

    function closeSOPModal() {
        document.getElementById('sopModal').classList.add('hidden');
    }

    // Chuyển Tab Vị trí làm việc
    function switchSOPTab(tabName) {
        // Ẩn tất cả nội dung
        document.querySelectorAll('.sop-tab-content').forEach(el => el.classList.add('hidden'));
        
        // Reset style các nút tab
        document.querySelectorAll('.sop-tab-btn').forEach(btn => {
            btn.className = 'sop-tab-btn px-3.5 py-2 rounded-xl bg-slate-100 text-slate-600 hover:bg-amber-50 hover:text-coffee-900 transition flex items-center space-x-1.5 whitespace-nowrap';
        });

        // Hiển thị nội dung tab được chọn & highlight nút
        document.getElementById('sop-content-' + tabName).classList.remove('hidden');
        document.getElementById('tab-' + tabName).className = 'sop-tab-btn px-3.5 py-2 rounded-xl bg-coffee-900 text-white transition flex items-center space-x-1.5 whitespace-nowrap';
    }

    // Đóng/Mở Accordion xổ xuống
    function toggleAccordion(id) {
        const content = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>

    <script id="sidebarControls">
        (() => {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const toggle = document.getElementById('sidebarToggle');
            const handle = document.getElementById('sidebarResizeHandle');
            const desktop = window.matchMedia('(min-width: 768px)');
            let width = 288;
            let collapsed = false;
            let mobileOpen = false;
            let drag = null;

            try {
                const saved = JSON.parse(localStorage.getItem('brewlearn.sidebar') || 'null');
                if (saved && Number.isFinite(saved.width)) width = saved.width;
                collapsed = saved?.collapsed === true;
            } catch (_) {}

            function maxWidth() {
                return Math.max(240, Math.min(420, Math.floor(window.innerWidth * 0.45)));
            }

            function render() {
                const visibleWidth = Math.max(240, Math.min(maxWidth(), width));
                const open = desktop.matches ? !collapsed : mobileOpen;
                sidebar.style.setProperty('--sidebar-width', `${visibleWidth}px`);
                document.body.style.setProperty('--sidebar-width', `${visibleWidth}px`);
                document.body.classList.toggle('sidebar-collapsed', collapsed);
                sidebar.classList.toggle('sidebar-collapsed', collapsed);
                sidebar.classList.toggle('mobile-open', mobileOpen);
                sidebar.inert = !open;
                sidebar.setAttribute('aria-hidden', String(!open));
                backdrop.classList.toggle('hidden', desktop.matches || !mobileOpen);
                toggle.setAttribute('aria-expanded', String(open));
                toggle.setAttribute('aria-label', open ? 'Ẩn menu' : 'Hiện menu');
                toggle.title = open ? 'Ẩn menu' : 'Hiện menu';
                handle.setAttribute('aria-valuenow', String(visibleWidth));
                handle.setAttribute('aria-valuemax', String(maxWidth()));
            }

            function save() {
                try {
                    localStorage.setItem('brewlearn.sidebar', JSON.stringify({ width, collapsed }));
                } catch (_) {}
            }

            window.toggleSidebar = function () {
                if (desktop.matches) {
                    collapsed = !collapsed;
                    save();
                } else {
                    mobileOpen = !mobileOpen;
                }
                if (desktop.matches ? collapsed : !mobileOpen) toggle.focus();
                render();
            };

            handle.addEventListener('pointerdown', event => {
                if (!desktop.matches || event.button !== 0) return;
                event.preventDefault();
                drag = { x: event.clientX, width: sidebar.getBoundingClientRect().width };
                handle.setPointerCapture(event.pointerId);
                document.body.classList.add('sidebar-resizing');
            });

            handle.addEventListener('pointermove', event => {
                if (!drag) return;
                width = Math.max(240, Math.min(maxWidth(), drag.width + event.clientX - drag.x));
                render();
            });

            function finishDrag() {
                if (!drag) return;
                drag = null;
                document.body.classList.remove('sidebar-resizing');
                save();
            }

            handle.addEventListener('pointerup', finishDrag);
            handle.addEventListener('pointercancel', finishDrag);
            handle.addEventListener('lostpointercapture', finishDrag);
            handle.addEventListener('keydown', event => {
                if (!desktop.matches || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
                event.preventDefault();
                const currentWidth = sidebar.getBoundingClientRect().width;
                width = event.key === 'Home' ? 240 : event.key === 'End' ? maxWidth()
                    : Math.max(240, Math.min(maxWidth(), currentWidth + (event.key === 'ArrowRight' ? 16 : -16)));
                render();
                save();
            });

            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && !desktop.matches && mobileOpen) window.toggleSidebar();
            });
            window.addEventListener('resize', render);
            desktop.addEventListener('change', () => {
                finishDrag();
                mobileOpen = false;
                render();
            });
            render();
        })();
    </script>

    <!-- JAVASCRIPT LOGIC -->
    <script>
        function toggleStoreDropdown() {
            document.getElementById('storeDropdown').classList.toggle('hidden');
        }

        function selectStore(storeName) {
            document.getElementById('currentStore').innerText = storeName;
            document.getElementById('storeDropdown').classList.add('hidden');
            showNotification(`Đã chuyển sang dữ liệu cơ sở: ${storeName}`);
        }

        function showNotification(msg) {
            const toast = document.getElementById('toastNotification');
            document.getElementById('toastMessage').innerText = msg;
            toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            }, 3000);
        }

        function toggleNotificationMenu() {
            const menu = document.getElementById('notificationMenu');
            menu.classList.toggle('hidden');
        }

        function openSOPModal() { document.getElementById('sopModal').classList.remove('hidden'); }
        function closeSOPModal() { document.getElementById('sopModal').classList.add('hidden'); }

        window.addEventListener('click', function(e) {
            const storeBtn = document.getElementById('storeSelectBtn');
            const storeDropdown = document.getElementById('storeDropdown');
            if (storeBtn && !storeBtn.contains(e.target) && !storeDropdown.contains(e.target)) {
                storeDropdown.classList.add('hidden');
            }

            const notiMenu = document.getElementById('notificationMenu');
            const notiBtn = e.target.closest('button[onclick="toggleNotificationMenu()"]');
            if (notiMenu && !notiMenu.contains(e.target) && !notiBtn) {
                notiMenu.classList.add('hidden');
            }
        });
    </script>
 <!-- MODAL GỬI THÔNG BÁO DÀNH CHO TRƯỞNG CA -->
@if($isLeader)
<div id="sendNotificationModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 space-y-4 shadow-2xl relative border border-[#E8DFC8]">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-sm font-bold text-coffee-900 flex items-center">
                <i class="fa-solid fa-paper-plane text-amber_brand mr-2"></i>Gửi Thông Báo Cho Nhân Viên
            </h3>
            <button type="button" onclick="closeSendNotificationModal()" class="text-slate-400 hover:text-slate-800 p-1">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('leader.sendNotification') }}" method="POST" class="space-y-3 text-xs">
            @csrf
            
           <!-- Chọn nhân viên nhận thông báo -->
<div>
    <label class="font-bold text-coffee-800 block mb-1">Gửi đến nhân viên (Mã NV / User ID):</label>
    <select name="user_id" required class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl p-2.5 focus:outline-none focus:ring-1 focus:ring-coffee-600 font-bold">
        <option value="">-- Chọn nhân viên nhận thông báo --</option>
        <option value="all" class="font-bold text-blue-600">📢 Tất cả nhân viên tôi phụ trách</option>
        
        @php
            $staffsList = \App\Models\User::query()->ledBy($currentUser)->orderBy('name')->get();
        @endphp

        @foreach($staffsList as $staff)
            <option value="{{ $staff->id }}">
                {{ $staff->ma_nv ?? 'NV-' . str_pad($staff->id, 3, '0', STR_PAD_LEFT) }}: {{ $staff->name }}
            </option>
        @endforeach
    </select>
</div>

            <div>
                <label class="font-bold text-coffee-800 block mb-1">Tiêu đề thông báo:</label>
                <input type="text" name="title" required placeholder="VD: Nhắc nhở kiểm tra quy trình SOP..." class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl p-2.5 focus:outline-none focus:ring-1 focus:ring-coffee-600">
            </div>

            <div>
                <label class="font-bold text-coffee-800 block mb-1">Nội dung thông báo:</label>
                <textarea name="message" rows="3" required placeholder="Nhập nội dung chi tiết gửi nhân viên..." class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl p-2.5 focus:outline-none focus:ring-1 focus:ring-coffee-600"></textarea>
            </div>

            <div class="pt-2 flex justify-end space-x-2">
                <button type="button" onclick="closeSendNotificationModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold">Hủy</button>
                <button type="submit" class="px-4 py-2 bg-coffee-900 hover:bg-coffee-800 text-white rounded-xl font-bold transition">Gửi Ngay</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openSendNotificationModal() {
        document.getElementById('sendNotificationModal').classList.remove('hidden');
    }
    function closeSendNotificationModal() {
        document.getElementById('sendNotificationModal').classList.add('hidden');
    }
</script>

<!-- MODAL POPUP: GHI NHẬN LỖI & PHÁT NHẮC NHỞ IN-SHIFT -->
<div id="incidentModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-amber-100 flex flex-col max-h-[90vh]">

        <!-- Header Modal -->
        <div class="flex items-start justify-between gap-4 px-7 py-5 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-lg">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-coffee-900">Ghi nhận lỗi nghiệp vụ</h3>
                    <p class="text-sm text-slate-500">Lưu lỗi phát sinh trong ca và gửi nhắc nhở cho nhân viên</p>
                </div>
            </div>
            <button type="button" onclick="closeIncidentModal()" class="text-slate-400 hover:text-slate-800 p-1" aria-label="Đóng">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form id="incidentForm" action="{{ route('leader.recordIncident') }}" method="POST" class="flex flex-col min-h-0">
            @csrf
            <div class="px-7 py-6 flex flex-col gap-5 overflow-y-auto text-sm">
                <!-- Nhân viên -->
                <div>
                    <label for="incident_user_id" class="font-semibold text-coffee-900 block mb-1.5">1. Nhân viên <span class="text-red-500">*</span></label>
                    <select id="incident_user_id" name="user_id" required class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl px-3.5 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-coffee-600/40">
                        <option value="" selected disabled>-- Chọn nhân viên --</option>
                        @foreach($staffsList as $staff)
                            <option value="{{ $staff->id }}">
                                NV-{{ str_pad($staff->id, 3, '0', STR_PAD_LEFT) }}: {{ $staff->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Loại lỗi & Mức độ -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="incident_loai_loi" class="font-semibold text-coffee-900 block mb-1.5">2. Loại lỗi <span class="text-red-500">*</span></label>
                        <select id="incident_loai_loi" name="loai_loi" required class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl px-3.5 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-coffee-600/40">
                            <option value="" selected disabled>-- Chọn loại lỗi --</option>
                            <option value="Vệ sinh quầy bar">Vệ sinh quầy bar / Khu vực</option>
                            <option value="Quy trình pha chế (SOP)">Sai quy trình pha chế (SOP)</option>
                            <option value="Thái độ phục vụ">Thái độ phục vụ khách hàng</option>
                            <option value="An toàn lao động">Vi phạm an toàn lao động</option>
                            <option value="Đồng phục & Tác phong">Sai đồng phục & Tác phong</option>
                        </select>
                    </div>
                    <div>
                        <label for="incident_muc_do" class="font-semibold text-coffee-900 block mb-1.5">3. Mức độ <span class="text-red-500">*</span></label>
                        <select id="incident_muc_do" name="muc_do" required class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl px-3.5 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-coffee-600/40">
                            <option value="" selected disabled>-- Chọn mức độ --</option>
                            <option value="Nhe">Nhẹ</option>
                            <option value="TrungBinh">Trung bình</option>
                            <option value="NghiemTrong">Nghiêm trọng</option>
                        </select>
                    </div>
                </div>

                <!-- Thời điểm -->
                <div>
                    <label for="incident_thoi_diem" class="font-semibold text-coffee-900 block mb-1.5">4. Thời điểm xảy ra</label>
                    <input id="incident_thoi_diem" type="datetime-local" name="thoi_diem" class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl px-3.5 py-3 text-slate-800 focus:outline-none focus:ring-2 focus:ring-coffee-600/40">
                    <p class="text-xs text-slate-500 mt-1.5">Để trống nếu lỗi xảy ra ngay lúc ghi nhận.</p>
                </div>

                <!-- Nội dung -->
                <div>
                    <label for="incident_mo_ta" class="font-semibold text-coffee-900 block mb-1.5">5. Nội dung ghi nhận và nhắc nhở <span class="text-red-500">*</span></label>
                    <textarea id="incident_mo_ta" name="mo_ta" rows="4" required maxlength="2000" placeholder="Mô tả lỗi và nội dung nhắc nhở gửi đến nhân viên" class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl px-3.5 py-3 text-slate-800 resize-y focus:outline-none focus:ring-2 focus:ring-coffee-600/40"></textarea>
                </div>
            </div>

            <!-- Buttons -->
            <div class="px-7 py-4 flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 rounded-b-2xl">
                <button type="button" onclick="closeIncidentModal()" class="px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl font-semibold text-sm">Hủy</button>
                <button type="submit" class="px-6 py-2.5 bg-coffee-900 hover:bg-coffee-800 text-white rounded-xl font-semibold text-sm transition flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-paper-plane text-amber-300"></i>
                    <span>Lưu và gửi nhắc nhở</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openIncidentModal() {
        const form = document.getElementById('incidentForm');
        form.reset();
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('incident_thoi_diem').max = now.toISOString().slice(0, 16);
        document.getElementById('incidentModal').classList.remove('hidden');
    }
    function closeIncidentModal() {
        document.getElementById('incidentModal').classList.add('hidden');
    }
</script>
@endif
</body>
</html>
