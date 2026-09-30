@extends('layouts.app')

@section('title', 'Cài Đặt Tài Khoản - BrewLearn')

@section('content')
<div class="space-y-6 pb-20">

    <!-- BREADCRUMB & HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-coffee-800 transition">Trang chủ</a>
                <span>&rsaquo;</span>
                <span class="text-coffee-900 font-semibold"> Cài đặt tài khoản </span>
            </div>
            <h1 class="text-xl md:text-2xl font-extrabold text-coffee-900 tracking-tight">
                Cài Đặt Tài Khoản
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Quản lý thông tin cá nhân, liên hệ người thân, bảo mật, thông báo và giao diện.
            </p>
        </div>

        <div class="flex items-center shrink-0">
            <a href="{{ route('users.edit', $user->id ?? auth()->id()) }}" class="px-4 py-2.5 bg-white hover:bg-[#FAF7F2] border border-[#E8DFC8] text-coffee-900 rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                <i class="fa-regular fa-id-card text-amber_brand"></i>
                Xem Hồ Sơ Nhân Sự & Năng Lực
                <i class="fa-solid fa-arrow-right text-[10px] text-slate-400"></i>
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold rounded-2xl flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 text-xs font-semibold rounded-2xl space-y-1">
            <div class="font-bold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation text-red-600"></i> Có lỗi xảy ra, vui lòng kiểm tra lại:
            </div>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- DANH SÁCH TAB ĐIỀU HƯỚNG -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-[#E8DFC8]">
        <button type="button" onclick="switchTab('contact')" id="btn-tab-contact" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap bg-white border border-[#E2D4C1] text-coffee-900 shadow-sm">
            <i class="fa-regular fa-address-book text-amber_brand"></i> Thông tin cá nhân & Liên hệ
        </button>
        <button type="button" onclick="switchTab('security')" id="btn-tab-security" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition flex items-center gap-2 whitespace-nowrap bg-[#EFE7DA]/50 text-slate-600 hover:bg-[#EFE7DA]">
            <i class="fa-solid fa-shield-halved text-slate-500"></i> Bảo mật & Đăng nhập
        </button>
        <button type="button" onclick="switchTab('notifications')" id="btn-tab-notifications" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition flex items-center gap-2 whitespace-nowrap bg-[#EFE7DA]/50 text-slate-600 hover:bg-[#EFE7DA]">
            <i class="fa-regular fa-bell text-slate-500"></i> Cài đặt thông báo
        </button>
        <button type="button" onclick="switchTab('preferences')" id="btn-tab-preferences" class="tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition flex items-center gap-2 whitespace-nowrap bg-[#EFE7DA]/50 text-slate-600 hover:bg-[#EFE7DA]">
            <i class="fa-solid fa-sliders text-slate-500"></i> Giao diện & Ngôn ngữ
        </button>
    </div>

    <!-- KHU VỰC NỘI DUNG TỪNG TAB -->
    <div class="max-w-4xl space-y-6">

        <!-- TAB 1: THÔNG TIN CÁ NHÂN & LIÊN HỆ -->
        <div id="tab-contact" class="tab-panel space-y-6">

            <!-- BẢNG 1.1: THÔNG TIN NHÂN SỰ & ĐỊNH DANH (READ-ONLY) -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                        <i class="fa-solid fa-user-gear text-amber_brand"></i> Thông tin định danh & Nhân sự
                    </h2>
                    <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                        <i class="fa-solid fa-lock text-[9px] mr-0.5"></i> Do HR quản lý
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <!-- Họ và Tên -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Họ và tên đầy đủ</label>
                        <input type="text" value="{{ $user->name }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>

                    <!-- Name Tag / Tên gọi -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Name Tag / Biển tên</label>
                        <input type="text" value="{{ $user->name_tag ?? $user->name }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>

                    <!-- Ngày sinh -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Ngày sinh</label>
                        <input type="text" value="{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : 'Chưa cập nhật' }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>

                    <!-- CCCD / CMND -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Số CCCD / CMND</label>
                        <input type="text" value="{{ $user->id_card ?? 'Chưa cập nhật' }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>

                    <!-- Chức vụ & Cấp độ -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Chức vụ / Cấp độ</label>
                        <input type="text" value="{{ $user->ChucVu ?? 'Nhân viên' }} {{ $user->CapDo ? '(Cấp '.$user->CapDo.')' : '' }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>

                    <!-- Chi nhánh & Trạm làm việc -->
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Chi nhánh / Trạm làm việc</label>
                        <input type="text" value="{{ $user->ChiNhanh ?? 'Cửa hàng' }} {{ $user->tram_lam_viec ? '• '.$user->tram_lam_viec : '' }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2 font-semibold text-slate-700 cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- BẢNG 1.2: CẬP NHẬT THÔNG TIN LIÊN HỆ & NGƯỜI THÂN (SỬA ĐƯỢC) -->
            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                        <i class="fa-regular fa-address-book text-amber_brand"></i> Thông tin liên hệ cá nhân & Khẩn cấp
                    </h2>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Có thể tự cập nhật</span>
                </div>

                <form action="{{ route('account.update-contact') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Email nội bộ (Read-only) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Email nội bộ hệ thống <span class="text-slate-400 font-normal">(Do HR cấp • Chỉ xem)</span>
                        </label>
                        <div class="relative">
                            <input type="email" value="{{ $user->email }}" disabled readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-3.5 pr-28 py-2 text-xs md:text-sm font-semibold text-slate-500 cursor-not-allowed">
                            <span class="absolute right-2 top-1/2 -translate-y-1/2 text-[10px] font-extrabold bg-emerald-100 text-emerald-800 px-2 py-1 rounded-md flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> {{ $user->email_verified_at ? 'Đã xác thực' : 'Chưa xác thực' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email cá nhân (Sửa được) -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Email cá nhân (Nhận dự phòng)</label>
                            <input type="email" name="secondary_email" value="{{ old('secondary_email', $user->secondary_email) }}" placeholder="example@gmail.com" required class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                        </div>

                        <!-- Số điện thoại cá nhân -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Số điện thoại di động</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="0901234567" required class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                        </div>
                    </div>

                    <hr class="border-slate-100 my-2">

                    <!-- THÔNG TIN NGƯỜI THÂN / LIÊN HỆ KHẨN CẤP -->
                    <div class="space-y-3">
                        <div class="text-xs font-extrabold text-coffee-900 uppercase tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-heart-pulse text-red-500"></i> Người thân / Liên hệ khẩn cấp
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tên người thân -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Họ & Tên người thân</label>
                                <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $user->emergency_contact) }}" placeholder="VD: Nguyễn Văn A (Bố/Mẹ/Vợ)" class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                            </div>

                            <!-- SĐT người thân -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Số điện thoại người thân</label>
                                <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $user->emergency_phone) }}" placeholder="0987654321" class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-coffee-900 hover:bg-coffee-800 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thông tin liên hệ
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 2: BẢO MẬT & ĐĂNG NHẬP -->
        <div id="tab-security" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-amber_brand"></i> Bảo mật tài khoản & Đổi mật khẩu
                    </h2>
                </div>

                <form action="{{ route('account.update-password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Mật khẩu hiện tại <span class="text-red-500">*</span></label>
                        <input type="password" name="current_password" required class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Mật khẩu mới <span class="text-red-500">*</span></label>
                            <input type="password" name="password" required class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Xác nhận mật khẩu mới <span class="text-red-500">*</span></label>
                            <input type="password" name="password_confirmation" required class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-coffee-900 hover:bg-coffee-800 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                            <i class="fa-solid fa-key"></i> Đổi mật khẩu
                        </button>
                    </div>
                </form>

                <hr class="border-slate-100">

                <div class="space-y-3">
                    <h3 class="text-xs font-extrabold text-coffee-900 uppercase tracking-wider">Thiết bị đang đăng nhập</h3>

                    <div class="p-3 bg-[#FAF7F2] rounded-2xl border border-[#E8DFC8] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center text-xs font-bold">
                                <i class="fa-solid fa-laptop"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-coffee-900 flex items-center gap-2">
                                    Chrome trên Windows
                                    <span class="text-[9px] font-extrabold bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded">Thiết bị này</span>
                                </div>
                                <div class="text-[10px] text-slate-400">IP: {{ request()->ip() }} • Vừa truy cập</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('account.logout-other-devices') }}" method="POST" class="pt-1">
                        @csrf
                        <button type="submit" class="w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-right-from-bracket text-red-500"></i> Đăng xuất khỏi tất cả các thiết bị khác
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TAB 3: THÔNG BÁO -->
        <div id="tab-notifications" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                        <i class="fa-regular fa-bell text-amber_brand"></i> Tùy chọn nhận thông báo
                    </h2>
                </div>

                <form action="{{ route('account.update-notifications') }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200/80 space-y-2">
                        <div class="text-[10px] font-extrabold uppercase text-amber-900 flex items-center gap-1">
                            <i class="fa-solid fa-lock text-[9px]"></i> Thông báo hệ thống bắt buộc
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                            <span>Cảnh báo tiến độ đào tạo & Nhắc sát hạch</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Luôn bật</span>
                        </div>
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-700">
                            <span>Kết quả đánh giá năng lực & Điểm thi</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Luôn bật</span>
                        </div>
                    </div>

                    <div class="space-y-3 pt-1">
                        @php
                            $notifications = $user->notification_settings ?? [];
                        @endphp
                        <label class="flex items-center justify-between cursor-pointer p-2 rounded-xl hover:bg-slate-50 transition">
                            <div>
                                <div class="text-xs font-bold text-coffee-900">Bài học & Khóa đào tạo mới</div>
                                <div class="text-[10px] text-slate-400">Thông báo khi có bài học SOP/Công thức mới</div>
                            </div>
                            <input type="checkbox" name="notify_new_lessons" value="1" @checked(data_get($notifications, 'notify_new_lessons', true)) class="w-4 h-4 rounded text-coffee-900 focus:ring-coffee-700">
                        </label>

                        <label class="flex items-center justify-between cursor-pointer p-2 rounded-xl hover:bg-slate-50 transition">
                            <div>
                                <div class="text-xs font-bold text-coffee-900">Nhắc nhở làm bài kiểm tra</div>
                                <div class="text-[10px] text-slate-400">Nhắc nhở trước 24h khi bài Quiz sắp hết hạn</div>
                            </div>
                            <input type="checkbox" name="notify_quiz_reminders" value="1" @checked(data_get($notifications, 'notify_quiz_reminders', true)) class="w-4 h-4 rounded text-coffee-900 focus:ring-coffee-700">
                        </label>

                        <label class="flex items-center justify-between cursor-pointer p-2 rounded-xl hover:bg-slate-50 transition">
                            <div>
                                <div class="text-xs font-bold text-coffee-900">Nhắc hoàn thành chương trình đào tạo</div>
                                <div class="text-[10px] text-slate-400">Cập nhật phần trăm tiến độ học tập tuần</div>
                            </div>
                            <input type="checkbox" name="notify_progress" value="1" @checked(data_get($notifications, 'notify_progress', true)) class="w-4 h-4 rounded text-coffee-900 focus:ring-coffee-700">
                        </label>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-5 py-2.5 bg-coffee-900 hover:bg-coffee-800 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu cấu hình thông báo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- TAB 4: GIAO DIỆN & NGÔN NGỮ -->
        <div id="tab-preferences" class="tab-panel hidden space-y-6">
            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-amber_brand"></i> Giao diện & Ngôn ngữ
                    </h2>
                </div>

                <form action="{{ route('account.update-preferences') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Chế độ giao diện</label>
                        <div class="grid grid-cols-3 gap-2">
                            <label class="border border-[#E8DFC8] bg-[#FAF7F2] p-2.5 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:bg-white text-center transition">
                                <input type="radio" name="theme" value="light" @checked(old('theme', $user->theme ?? 'light') == 'light') class="mb-1 text-coffee-900 focus:ring-coffee-700">
                                <i class="fa-regular fa-sun text-amber-600 text-sm mb-1"></i>
                                <span class="text-[11px] font-bold text-coffee-900">Sáng</span>
                            </label>

                            <label class="border border-slate-200 bg-slate-50 p-2.5 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:bg-white text-center transition">
                                <input type="radio" name="theme" value="dark" @checked(old('theme', $user->theme) == 'dark') class="mb-1 text-coffee-900 focus:ring-coffee-700">
                                <i class="fa-regular fa-moon text-indigo-600 text-sm mb-1"></i>
                                <span class="text-[11px] font-bold text-slate-700">Tối</span>
                            </label>

                            <label class="border border-slate-200 bg-slate-50 p-2.5 rounded-2xl flex flex-col items-center justify-center cursor-pointer hover:bg-white text-center transition">
                                <input type="radio" name="theme" value="system" @checked(old('theme', $user->theme) == 'system') class="mb-1 text-coffee-900 focus:ring-coffee-700">
                                <i class="fa-solid fa-desktop text-slate-500 text-sm mb-1"></i>
                                <span class="text-[11px] font-bold text-slate-700">Hệ thống</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Ngôn ngữ hiển thị</label>
                        <select name="language" class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3.5 py-2 text-xs md:text-sm font-semibold text-coffee-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-700 transition">
                            <option value="vi" @selected(old('language', $user->language ?? 'vi') == 'vi')>🇻🇳 Tiếng Việt (Mặc định)</option>
                            <option value="en" @selected(old('language', $user->language) == 'en') disabled>🇺🇸 English (Sắp hỗ trợ)</option>
                        </select>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="px-5 py-2.5 bg-coffee-900 hover:bg-coffee-800 text-white text-xs font-bold rounded-xl transition shadow flex items-center gap-1.5">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu giao diện
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- KHU VỰC THÔNG TIN HR & YÊU CẦU CHỈNH SỬA KHỐI CHUNG -->
    <div class="max-w-4xl pt-4 border-t border-[#E8DFC8]">
        <div class="bg-white rounded-3xl p-5 border border-[#E8DFC8] shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="text-xs font-extrabold text-coffee-900 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-amber_brand"></i> Yêu cầu thay đổi thông tin nhân sự?
                </div>
                <p class="text-[11px] text-slate-500">
                    Các thông tin định danh như Họ tên, Ngày sinh, Số CCCD, Cửa hàng do Phòng Nhân sự (HR) quản lý.
                </p>
            </div>
            <a href="{{ route('support.hr-request') }}" class="px-4 py-2.5 bg-[#FAF7F2] hover:bg-[#EFE7DA] border border-[#E8DFC8] text-coffee-900 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shrink-0 shadow-sm">
                Gửi yêu cầu HR <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>
    </div>

</div>

<!-- JAVASCRIPT CHUYỂN TAB -->
<script>
    function switchTab(tabName) {
        // 1. Ẩn tất cả các tab panel
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });

        // 2. Reset style tất cả các nút tab về dạng inactive
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = "tab-btn px-4 py-2.5 rounded-xl text-xs font-semibold transition flex items-center gap-2 whitespace-nowrap bg-[#EFE7DA]/50 text-slate-600 hover:bg-[#EFE7DA]";
            const icon = btn.querySelector('i');
            if (icon) {
                icon.className = icon.className.replace(/text-amber_brand/, 'text-slate-500');
            }
        });

        // 3. Hiện tab panel được chọn
        const targetPanel = document.getElementById('tab-' + tabName);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }

        // 4. Highlight nút tab đang được chọn
        const activeBtn = document.getElementById('btn-tab-' + tabName);
        if (activeBtn) {
            activeBtn.className = "tab-btn px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap bg-white border border-[#E2D4C1] text-coffee-900 shadow-sm";
            const icon = activeBtn.querySelector('i');
            if (icon) {
                icon.className = icon.className.replace(/text-slate-500/, 'text-amber_brand');
            }
        }

        // 5. Cập nhật URL hash
        window.location.hash = tabName;
    }

    // Tự động mở tab từ URL Hash
    document.addEventListener('DOMContentLoaded', function () {
        const hash = window.location.hash.replace('#', '');
        if (hash && ['contact', 'security', 'notifications', 'preferences'].includes(hash)) {
            switchTab(hash);
        }
    });
</script>
@endsection