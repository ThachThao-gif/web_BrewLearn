

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header trang -->
    <div class="page-banner mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Sổ Tay Nội Quy & Quy Chuẩn Hoạt Động BrewLearn</h1>
        <p class="text-sm text-slate-500 mt-1">Hướng dẫn đầy đủ về phong cách làm việc, văn hóa ứng xử và quy định làm việc.</p>
    </div>

    <!-- Layout chính: Sidebar Mục lục bên trái & Nội dung bên phải -->
    <div class="grid grid-cols-12 gap-8 items-start">
        
        <!-- HÌNH 2: MỤC LỤC SỔ TAY (Cố định bên trái khi cuộn) -->
        <div class="col-span-12 lg:col-span-4 sticky top-6">
            <div class="bg-white p-5 rounded-2xl border border-amber-100/60 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-book-open text-amber-600"></i> Mục lục sổ tay
                    </h2>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full">06 Phân hệ</span>
                </div>

                <!-- Danh sách 6 mục -->
                <div class="space-y-2">
                    <button onclick="scrollToSection('section-1')" class="toc-btn active w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-amber-100 text-amber-800 font-bold text-xs rounded-lg">01</span>
                            <span class="text-slate-700">Diện mạo & Tác phong (Grooming)</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>

                    <button onclick="scrollToSection('section-2')" class="toc-btn w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs rounded-lg">02</span>
                            <span class="text-slate-700">Kỷ luật Ca làm & Chấm công</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>

                    <button onclick="scrollToSection('section-3')" class="toc-btn w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs rounded-lg">03</span>
                            <span class="text-slate-700">Giao tiếp & Văn hóa Phục vụ</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>

                    <button onclick="scrollToSection('section-4')" class="toc-btn w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs rounded-lg">04</span>
                            <span class="text-slate-700">Vệ sinh ATTP & Quy tắc FIFO</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>

                    <button onclick="scrollToSection('section-5')" class="toc-btn w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs rounded-lg">05</span>
                            <span class="text-slate-700">Bảo quản Thiết bị & Khẩu hao</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>

                    <button onclick="scrollToSection('section-6')" class="toc-btn w-full flex items-center justify-between p-3 rounded-xl border transition text-left text-sm font-medium hover:bg-amber-50/50">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 flex items-center justify-center bg-slate-100 text-slate-700 font-bold text-xs rounded-lg">06</span>
                            <span class="text-slate-700">Khen thưởng & Xử lý Vi phạm</span>
                        </div>
                        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- HÌNH 3: NỘI DUNG CHI TIẾT CÁC MỤC -->
        <div class="col-span-12 lg:col-span-8 space-y-8">
            
            <!-- Mục 01: Diện mạo & Tác phong -->
            <div id="section-1" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Quy chuẩn diện mạo</span>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Điều 1 - 8</span>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">01. Chuẩn Mực Tác Phong Barista (Grooming & Uniform)</h2>
                <p class="text-sm text-slate-600 mb-6">Hình ảnh của mỗi Barista là đại diện trực tiếp cho chất lượng tách cà phê và tiêu chuẩn vệ sinh khắt khe của BrewLearn.</p>

                <!-- 2 Ảnh minh họa tác phong -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-100 p-2">
                        <img src="https://images.unsplash.com/photo-1559526324-4b87b5e36e44?w=500" class="w-full h-40 object-cover rounded-lg mb-2" alt="Tác phong">
                        <p class="text-xs font-bold text-slate-700">Tác phong quầy Bar chuẩn hóa</p>
                        <p class="text-[11px] text-slate-500">Tạp dề da-canvas nâu, áo sơ mi màu kem, bảng tên ghim ngực trái thẳng hàng.</p>
                    </div>
                    <div class="rounded-xl overflow-hidden bg-slate-50 border border-slate-100 p-2">
                        <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500" class="w-full h-40 object-cover rounded-lg mb-2" alt="Trang sức">
                        <p class="text-xs font-bold text-slate-700">Tiêu chuẩn bàn tay & Trang sức ATTP</p>
                        <p class="text-[11px] text-slate-500">Móng tay cắt ngắn dưới 2mm, tuyệt đối không sơn màu/gắn đá; chỉ đeo 01 nhẫn trơn.</p>
                    </div>
                </div>

                <!-- 3 Thẻ Quy định chi tiết -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100/60">
                        <p class="text-xs font-bold text-amber-900 mb-1"><i class="fa-solid fa-user-ninja mr-1"></i> Tóc & Khuôn mặt</p>
                        <ul class="text-[11px] text-slate-600 space-y-1 list-disc list-inside">
                            <li>Tóc dài qua vai bắt buộc búi gọn củ tỏi.</li>
                            <li>Nam giới cạo râu sạch sẽ đầu ca.</li>
                        </ul>
                    </div>
                    <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100/60">
                        <p class="text-xs font-bold text-amber-900 mb-1"><i class="fa-solid fa-shirt mr-1"></i> Đồng phục & Thẻ tên</p>
                        <ul class="text-[11px] text-slate-600 space-y-1 list-disc list-inside">
                            <li>Áo sơ mi linen trung tính luôn giặt sạch, ủi phẳng.</li>
                            <li>Thẻ tên đeo chính xác ngực trái.</li>
                        </ul>
                    </div>
                    <div class="bg-amber-50/50 p-3 rounded-xl border border-amber-100/60">
                        <p class="text-xs font-bold text-amber-900 mb-1"><i class="fa-solid fa-shoe-prints mr-1"></i> Giày & An toàn sàn</p>
                        <ul class="text-[11px] text-slate-600 space-y-1 list-disc list-inside">
                            <li>Giày thể thao bít mũi màu tối.</li>
                            <li>Đế cao su chống trượt trên bề mặt ướt.</li>
                        </ul>
                    </div>
                </div>

                <!-- Đạt chuẩn vs Không đạt chuẩn -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-100">
                        <p class="text-xs font-bold text-emerald-800 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-thumbs-up"></i> ĐẠT CHUẨN (DO'S)</p>
                        <ul class="text-xs text-emerald-900 space-y-1.5">
                            <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Mang đầy đủ đồng phục, bảng tên và bút thử syrup trước khi vào trạm.</li>
                            <li class="flex items-start gap-2"><i class="fa-solid fa-check text-emerald-600 mt-0.5"></i> Tháo tạp dề khi ra khỏi quầy bar (đi vệ sinh, nghỉ giải lao).</li>
                        </ul>
                    </div>
                    <div class="bg-rose-50/60 p-4 rounded-xl border border-rose-100">
                        <p class="text-xs font-bold text-rose-800 mb-2 flex items-center gap-1.5"><i class="fa-solid fa-thumbs-down"></i> KHÔNG ĐẠT CHUẨN (DON'TS)</p>
                        <ul class="text-xs text-rose-900 space-y-1.5">
                            <li class="flex items-start gap-2"><i class="fa-solid fa-xmark text-rose-600 mt-0.5"></i> Sơn móng tay màu nổi, móng giả gắn đá có nguy cơ rơi vào thức uống.</li>
                            <li class="flex items-start gap-2"><i class="fa-solid fa-xmark text-rose-600 mt-0.5"></i> Mặc áo đồng phục nhăn nhúm, dính vết bẩn cà phê của ca trước.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Mục 02: Kỷ luật Ca làm -->
            <div id="section-2" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Kỷ luật thời gian</span>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">Điều 9 - 18</span>
                </div>
                <h2 class="text-xl font-bold text-slate-800 mb-2">02. Quy Định Giờ Giấc, Chấm Công & Hoán Đổi Ca</h2>
                <p class="text-sm text-slate-600 mb-4">Đảm bảo vận hành quầy Bar diễn ra trơn tru và công bằng giữa các thành viên.</p>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-600 space-y-2">
                    <p>• Có mặt trước 15 phút ca làm việc để nhận bàn giao quầy bar.</p>
                    <p>• Việc đổi ca phải thực hiện qua ứng dụng và được Cửa hàng trưởng phê duyệt trước 24h.</p>
                </div>
            </div>

            <!-- Các section 03, 04, 05, 06 bổ sung tương tự -->
            <div id="section-3" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6">
                <h2 class="text-xl font-bold text-slate-800 mb-2">03. Giao tiếp & Văn hóa Phục vụ</h2>
                <p class="text-sm text-slate-600">Luôn giữ thái độ thân thiện và tôn trọng khách hàng cũng như đồng nghiệp.</p>
            </div>

        </div>
    </div>
</div>
<!-- MỤC 04: VỆ SINH ATTP, HACCP & PCCC QUẦY BAR -->
<div id="section-4" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6 mt-8">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">AN TOÀN SỨC KHỎE</span>
        <span class="text-xs bg-amber-100/60 text-amber-900 px-2.5 py-1 rounded-full font-medium">Điều 29 - 36</span>
    </div>
    <h2 class="text-xl font-bold text-slate-800 mb-2">04. Vệ Sinh ATTP, Tiêu Chuẩn HACCP & PCCC Quẩy Bar</h2>
    <p class="text-sm text-slate-600 mb-6">Chất lượng một ly thức uống không chỉ đo bằng hương vị specialty mà quan trọng hơn cả là sự an toàn tuyệt đối cho sức khỏe thực khách.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <!-- Quy trình rửa tay -->
        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-hands-bubbles text-amber-700"></i> Quy Trình Rửa Tay 6 Bước
            </p>
            <p class="text-[11px] text-slate-600 mb-2">Rửa tay tối thiểu 30 giây bằng xà phòng sát khuẩn tại bồn rửa chuyên dụng Handwash Station:</p>
            <ol class="text-[11px] text-slate-600 space-y-1.5 list-decimal list-inside">
                <li>Ngay khi bắt đầu bước vào khu vực pha chế.</li>
                <li>Sau mỗi lần tiếp xúc với tiền mặt hoặc thẻ khách hàng.</li>
                <li>Sau khi xử lý thùng rác hoặc chạm vào khăn lau dơ.</li>
                <li>Sau khi ho, hắt hơi hoặc chạm tay lên mặt/tóc.</li>
            </ol>
        </div>

        <!-- Nguyên tắc FIFO -->
        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-box-archive text-amber-700"></i> Nguyên Tắc FIFO & Tem Nhãn (Day-Dot)
            </p>
            <p class="text-[11px] text-slate-600 mb-2">Áp dụng <strong>First In - First Out</strong> (Vào trước - Ra trước) cho 100% nguyên vật liệu:</p>
            <ul class="text-[11px] text-slate-600 space-y-1.5 list-disc list-inside">
                <li>Sữa tươi thanh trùng: Sử dụng tối đa 48h sau mở nắp, bảo quản 2°C - 4°C.</li>
                <li>Syrup hoa quả/Puree mở nắp: Dán tem nhãn Day-Dot ghi rõ Ngày mở & Giờ hết hạn.</li>
                <li>Hạt cà phê: Hạt sau mở van xả khí tối ưu sử dụng trong vòng 14 ngày.</li>
                <li><strong>Tuyệt đối cấm sử dụng bất kỳ nguyên liệu nào quá hạn (dù chỉ 01 giờ).</strong></li>
            </ul>
        </div>
    </div>

    <!-- Cảnh báo vùng cấm -->
    <div class="p-4 bg-rose-50/70 rounded-xl border border-rose-100 flex items-start gap-3">
        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg shrink-0 mt-0.5"></i>
        <div>
            <p class="text-xs font-bold text-rose-900 mb-0.5">Vùng Cấm Nghiêm Ngặt Tại Quẩy Pha Chế Mở (Front Bar)</p>
            <p class="text-xs text-rose-800">Cấm ăn uống thức ăn cá nhân, hút thuốc (kể cả thuốc lá điện tử), nhai kẹo cao su trong toàn bộ không gian quẩy pha chế và kho nguyên liệu. Nước uống của nhân viên phải được đựng trong bình giữ nhiệt có nắp đậy kín và cất tại kệ đáy Back-bar.</p>
        </div>
    </div>
</div>

<!-- MỤC 05: QUẢN LÝ THẤT THOÁT, HƯ HỎNG & SỬ DỤNG THIẾT BỊ QUÁN -->
<div id="section-5" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6 mt-8">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">TÀI SẢN & THIẾT BỊ</span>
        <span class="text-xs bg-amber-100/60 text-amber-900 px-2.5 py-1 rounded-full font-medium">Điều 37 - 42</span>
    </div>
    <h2 class="text-xl font-bold text-slate-800 mb-4">05. Quản Lý Thất Thoát, Hư Hỏng & Sử Dụng Thiết Bị Quán</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                <i class="fa-solid fa-mug-hot text-amber-700"></i> Máy Espresso La Marzocco
            </p>
            <p class="text-[11px] text-slate-600">Backflush bột Cafiza cuối mỗi ngày. Tuyệt đối không dùng vật nhọn cạo lưới shower screen gây xước gioăng cao su.</p>
        </div>

        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                <i class="fa-solid fa-gear text-amber-700"></i> Máy Xay Mahlkönig EK43
            </p>
            <p class="text-[11px] text-slate-600">Khởi động motor trước khi thả hạt để chống kẹt đĩa xay. Vệ sinh họng xả bằng chổi lông mềm mỗi 2 giờ phục vụ.</p>
        </div>

        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-2 flex items-center gap-1.5">
                <i class="fa-solid fa-wine-glass text-amber-700"></i> Khấu Trừ Vỡ Hỏng (Breakage)
            </p>
            <p class="text-[11px] text-slate-600">Định mức hao hụt ly gốm/thủy tinh cho phép: 0.2% doanh số. Vỡ do sơ xuất không báo cáo sẽ khấu trừ 50% giá vốn vào quỹ ca.</p>
        </div>
    </div>
</div>

<!-- MỤC 06: CHẾ ĐỘ PHÚC LỢI, THƯỞNG CA & XỬ LÝ VI PHẠM -->
<div id="section-6" class="policy-section bg-white p-6 rounded-2xl border border-slate-100 shadow-sm scroll-mt-6 mt-8">
    <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-amber-700">CHẾ ĐỘ & KỶ LUẬT</span>
        <span class="text-xs bg-amber-100/60 text-amber-900 px-2.5 py-1 rounded-full font-medium">Điều 43 - 49</span>
    </div>
    <h2 class="text-xl font-bold text-slate-800 mb-4">06. Chế Độ Phúc Lợi, Thưởng Ca & Xử Lý Vi Phạm</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Cơ chế khen thưởng -->
        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-star text-amber-600"></i> Cơ Chế Khen Thưởng & Vinh Danh
            </p>
            <div class="space-y-3">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-award text-amber-700 text-sm mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Ngôi sao Barista của tháng</p>
                        <p class="text-[11px] text-slate-600">Thưởng nóng 1.500.000đ + Huy hiệu vàng danh dự trên tạp dề.</p>
                    </div>
                </div>
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-trophy text-amber-700 text-sm mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Thưởng điểm CSAT xuất sắc</p>
                        <p class="text-[11px] text-slate-600">Tặng 500.000đ khi đạt 100% đánh giá 5 sao từ khách hàng trong tháng.</p>
                    </div>
                </div>
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-lightbulb text-amber-700 text-sm mt-0.5"></i>
                    <div>
                        <p class="text-xs font-bold text-slate-800">Sáng kiến SOP & Công thức mới</p>
                        <p class="text-[11px] text-slate-600">Thưởng lên tới 3.000.000đ cho công thức thức uống mùa vụ được chuỗi đưa vào menu.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 Cấp độ chế tài -->
        <div class="bg-amber-50/40 p-4 rounded-xl border border-amber-100/60">
            <p class="text-xs font-bold text-amber-900 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-gavel text-amber-700"></i> 3 Cấp Độ Chế Tài Kỷ Luật
            </p>
            <div class="space-y-2.5">
                <div class="p-2.5 bg-white rounded-lg border border-amber-200/60">
                    <p class="text-xs font-bold text-amber-900">Mức 1: Nhắc nhở bằng miệng & Huấn luyện lại</p>
                    <p class="text-[11px] text-slate-600 mt-0.5">Áp dụng cho: Đi trễ dưới 10 phút lần đầu, quên ghim bảng tên, chưa vuốt nếp tạp dề.</p>
                </div>
                <div class="p-2.5 bg-white rounded-lg border border-amber-200/60">
                    <p class="text-xs font-bold text-amber-900">Mức 2: Biên bản cảnh cáo (Warning Letter)</p>
                    <p class="text-[11px] text-slate-600 mt-0.5">Áp dụng cho: Vi phạm vệ sinh, gian lận quẹt thẻ, tự ý đổi ca không báo trước.</p>
                </div>
                <div class="p-2.5 bg-rose-50 rounded-lg border border-rose-200">
                    <p class="text-xs font-bold text-rose-800">Mức 3: Đình chỉ ca làm việc / Đơn phương chấm dứt HĐLĐ</p>
                    <p class="text-[11px] text-rose-700 mt-0.5">Áp dụng cho: Gian lận tài chính, cố tình gây gổ, sử dụng nguyên liệu quá hạn nghiêm trọng.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Script hỗ trợ chuyển đổi active state & cuộn mượt -->
<script>
    function scrollToSection(sectionId) {
        // Cuộn tới mục tương ứng
        const element = document.getElementById(sectionId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }

        // Cập nhật giao diện nút Active trên Mục lục
        document.querySelectorAll('.toc-btn').forEach(btn => {
            btn.classList.remove('bg-amber-100/70', 'border-amber-300', 'font-bold');
            btn.classList.add('border-transparent');
        });

        event.currentTarget.classList.add('bg-amber-100/70', 'border-amber-300', 'font-bold');
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\policies\index.blade.php ENDPATH**/ ?>