@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6 space-y-6">

    {{-- HEADER --}}
    <div class="page-banner bg-[#6F4E37] text-white rounded-3xl p-6 shadow-md flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-black flex items-center gap-2">
            <i></i> TRỢ GIÚP & HỖ TRỢ
            </h1>
            <p class="text-xs text-amber-100 mt-1">Giải đáp thắc mắc, hướng dẫn thao tác & tiếp nhận báo lỗi hệ thống</p>
        </div>
    </div>

    {{-- THANH TAB ĐIỀU HƯỚNG (5 MỤC) --}}
    <div class="bg-white rounded-2xl p-2 border border-[#E8DFC8] shadow-sm flex items-center gap-2 overflow-x-auto">
        <button type="button" onclick="openTab(event, 'tab-1')" class="help-tab-btn active px-4 py-2.5 rounded-xl text-xs font-bold bg-[#6F4E37] text-white transition-all whitespace-nowrap">
            <i class="fa-solid fa-circle-question mr-1"></i> 1. Câu hỏi thường gặp (FAQ)
        </button>
        <button type="button" onclick="openTab(event, 'tab-2')" class="help-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-[#FAF7F2] transition-all whitespace-nowrap">
            <i class="fa-solid fa-book-open mr-1"></i> 2. Hướng dẫn sử dụng
        </button>
        <button type="button" onclick="openTab(event, 'tab-3')" class="help-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-[#FAF7F2] transition-all whitespace-nowrap">
            <i class="fa-solid fa-bug text-red-500 mr-1"></i> 3. Báo lỗi hệ thống
        </button>
        <button type="button" onclick="openTab(event, 'tab-4')" class="help-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-[#FAF7F2] transition-all whitespace-nowrap">
            <i class="fa-solid fa-paper-plane text-amber-600 mr-1"></i> 4. Gửi yêu cầu hỗ trợ
        </button>
       <button type="button" id="btn-tab-5" onclick="openTab(event, 'tab-5')" class="help-tab-btn px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600">
    5. Theo dõi yêu cầu
</button>
    </div>

    {{-- NỘI DUNG CÁC TAB --}}
    
    {{-- TAB 1: FAQ --}}
    <div id="tab-1" class="help-tab-content space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-2">
                <span class="text-[10px] font-bold text-amber-800 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200 uppercase">1.1 Tài khoản & Đăng nhập</span>
                <h4 class="font-bold text-slate-800 text-sm">Quên mật khẩu hoặc tài khoản bị khóa?</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Liên hệ Quản lý cửa hàng để được cấp lại mật khẩu nội bộ.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-2">
                <span class="text-[10px] font-bold text-blue-800 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200 uppercase">1.2 Học tập & Bài kiểm tra</span>
                <h4 class="font-bold text-slate-800 text-sm">Mất kết nối mạng khi đang làm bài?</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Tải lại trang sau khi có mạng, hệ thống tự động lưu đáp án đã chọn.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-2">
                <span class="text-[10px] font-bold text-purple-800 bg-purple-50 px-2.5 py-1 rounded-md border border-purple-200 uppercase">1.3 Đánh giá năng lực</span>
                <h4 class="font-bold text-slate-800 text-sm">Khi nào được đánh giá năng lực?</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Lần đầu sau 3 tháng làm việc, sau đó định kỳ 3 tháng một lần, khi đã hoàn thành bài học và đạt các bài kiểm tra bắt buộc. Trưởng ca cùng ca xếp lịch và chấm trực tiếp.</p>
            </div>
            <div class="bg-white p-5 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-2">
                <span class="text-[10px] font-bold text-red-800 bg-red-50 px-2.5 py-1 rounded-md border border-red-200 uppercase">1.4 Cảnh báo nguy cơ</span>
                <h4 class="font-bold text-slate-800 text-sm">Điểm cảnh báo nguy cơ bị tăng cao?</h4>
                <p class="text-xs text-slate-600 leading-relaxed">Điểm tăng khi điểm kiểm tra trung bình dưới 80, có bài kiểm tra chưa đạt, tiến độ đào tạo dưới 80%, kết quả đánh giá năng lực gần nhất được duyệt là Chưa đạt, hoặc có lỗi nghiệp vụ trong 30 ngày gần nhất. Để giảm điểm: hoàn thành bài học, làm lại bài chưa đạt, đạt lần đánh giá năng lực tiếp theo và hạn chế lỗi nghiệp vụ. Điểm được tính lại tự động lúc 01:00 hằng ngày và chỉ dùng để tham khảo, không thay thế kết quả đánh giá năng lực.</p>
            </div>
        </div>
    </div>

    {{-- TAB 2: HƯỚNG DẪN SỬ DỤNG --}}
    <div id="tab-2" class="help-tab-content hidden space-y-4">
        <div class="bg-white p-6 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b pb-2">📖 HƯỚNG DẪN THAO TÁC</h3>
            <ul class="space-y-3 text-xs text-slate-600">
                <li class="p-3 bg-[#FAF7F2] rounded-xl"><b>2.1 Bắt đầu:</b> Đăng nhập tài khoản, kiểm tra thông tin Ca làm & Cửa hàng công tác.</li>
                <li class="p-3 bg-[#FAF7F2] rounded-xl"><b>2.2 Học bài & Tài liệu:</b> Vào Chương trình đào tạo, xem tài liệu SOP/Video pha chế.</li>
                <li class="p-3 bg-[#FAF7F2] rounded-xl"><b>2.3 Làm bài kiểm tra:</b> Chọn bài thi trắc nghiệm -> Đọc kỹ câu hỏi -> Bấm Nộp bài.</li>
                <li class="p-3 bg-[#FAF7F2] rounded-xl"><b>2.4 Xem kết quả:</b> Kiểm tra % tiến độ học tập và điểm thi tại trang Hồ sơ cá nhân.</li>
            </ul>
        </div>
    </div>

    {{-- TAB 3: BÁO LỖI HỆ THỐNG & TAB 4: GỬI YÊU CẦU --}}
    <div id="tab-3" class="help-tab-content hidden">
        <form action="{{ route('help.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl border border-[#E8DFC8] shadow-sm space-y-4">
            @csrf
            <h3 class="font-bold text-slate-800 text-sm border-b pb-2">🚨 BÁO LỖI HỆ THỐNG / YÊU CẦU TRỢ GIÚP</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Loại sự cố / Yêu cầu <span class="text-red-500">*</span></label>
                    <select name="loai_yeu_cau" class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="Không đăng nhập được">Không đăng nhập được</option>
                        <option value="Không mở được bài học">Không mở được bài học</option>
                        <option value="Không làm được bài kiểm tra">Không làm được bài kiểm tra</option>
                        <option value="Lỗi hiển thị dữ liệu">Lỗi hiển thị dữ liệu</option>
                        <option value="Yêu cầu hỗ trợ khác">Lỗi khác / Yêu cầu hỗ trợ</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Mức độ ưu tiên <span class="text-red-500">*</span></label>
                    <select name="muc_do_uu_tien" class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3 py-2 text-xs font-bold">
                        <option value="Thap">Thấp (Giải đáp thông tin)</option>
                        <option value="TrungBinh" selected>Trung bình (Ảnh hưởng cá nhân)</option>
                        <option value="Cao">Cao (Không thi/học được)</option>
                        <option value="Khancap">Khẩn cấp (Lỗi toàn hệ thống)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Chủ đề <span class="text-red-500">*</span></label>
                <input type="text" name="chu_de" required placeholder="Nhập chủ đề ngắn gọn..." class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3 py-2 text-xs font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nội dung chi tiết <span class="text-red-500">*</span></label>
                <textarea name="noi_dung" rows="3" required placeholder="Mô tả chi tiết bước gặp lỗi..." class="w-full bg-[#FAF7F2] border border-[#E2D4C1] rounded-xl px-3 py-2 text-xs font-bold"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Đính kèm ảnh minh họa</label>
                <input type="file" name="hinh_anh" accept="image/*" class="w-full text-xs text-slate-500">
            </div>

            <button type="submit" class="bg-[#6F4E37] text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-sm">
                <i class="fa-solid fa-paper-plane mr-1"></i> Gửi Yêu Cầu Hỗ Trợ
            </button>
        </form>
    </div>

    {{-- TAB 4 dùng chung giao diện với TAB 3 --}}
    <div id="tab-4" class="help-tab-content hidden"></div>

    {{-- TAB 5: THEO DÕI YÊU CẦU --}}
    <div id="tab-5" class="help-tab-content hidden">
        <div class="bg-white rounded-2xl border border-[#E8DFC8] p-5 shadow-sm space-y-4">
            <h3 class="font-bold text-slate-800 text-sm border-b pb-2">📋 LỊCH SỬ YÊU CẦU HỖ TRỢ DÃ GỬI</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-[#FAF7F2] text-slate-700 font-bold border-b border-[#E8DFC8]">
                        <tr>
                            <th class="p-2.5">Mã YC</th>
                            <th class="p-2.5">Chủ đề</th>
                            <th class="p-2.5">Mức độ</th>
                            <th class="p-2.5">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($myTickets ?? [] as $ticket)
                        <tr>
                            <td class="p-2.5 font-bold text-[#6F4E37]">TK-{{ $ticket->id }}</td>
                            <td class="p-2.5 font-bold text-slate-800">{{ $ticket->chu_de }}</td>
                            <td class="p-2.5 font-bold">{{ $ticket->muc_do_uu_tien }}</td>
                            <td class="p-2.5 font-bold text-blue-600">{{ $ticket->trang_thai }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-5 text-center text-slate-400">Chưa có yêu cầu hỗ trợ nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPT CHÍNH XÁC XỬ LÝ SỰ KIỆN CLICK TAB --}}
<script>
    function openTab(evt, tabId) {
        // 1. Nếu click vào Tab 4 thì chuyển hướng hiển thị Tab 3 (Dùng chung Form)
        if (tabId === 'tab-4') {
            tabId = 'tab-3';
        }

        // 2. Ẩn tất cả nội dung Tab
        const contents = document.querySelectorAll('.help-tab-content');
        contents.forEach(content => content.classList.add('hidden'));

        // 3. Reset giao diện tất cả nút bấm
        const buttons = document.querySelectorAll('.help-tab-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-[#6F4E37]', 'text-white');
            btn.classList.add('text-slate-600', 'hover:bg-[#FAF7F2]');
        });

        // 4. Hiển thị Tab được chọn
        const targetTab = document.getElementById(tabId);
        if (targetTab) {
            targetTab.classList.remove('hidden');
        }

        // 5. Active nút vừa click
        if (evt && evt.currentTarget) {
            evt.currentTarget.classList.add('bg-[#6F4E37]', 'text-white');
            evt.currentTarget.classList.remove('text-slate-600', 'hover:bg-[#FAF7F2]');
        }
    }
</script>
@endsection
