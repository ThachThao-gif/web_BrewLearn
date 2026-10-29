<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-[#FAF8F5] py-8 px-4 md:px-8 font-sans text-slate-800">
    <div class="max-w-6xl mx-auto space-y-6">

        <!-- BREADCRUMB -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="#" class="hover:text-amber-800 transition">Trang chủ</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
            <a href="#" class="hover:text-amber-800 transition">Đào tạo & Đánh giá</a>
            <i class="fa-solid fa-chevron-right text-[9px] text-slate-400"></i>
            <span class="font-semibold text-slate-800">Tiêu chuẩn đánh giá năng lực</span>
        </div>

        <!-- HEADER TỔNG QUAN -->
        <div class="page-banner bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-900 border border-amber-200/60 text-xs font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-clipboard-check text-amber-700"></i> Quy định đánh giá nhân sự
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Tiêu Chuẩn Đánh Giá Năng Lực Nhân Viên
                    </h1>
                </div>

                <div class="flex items-center gap-3 bg-slate-50 px-4 py-3 rounded-2xl border border-slate-200/80 shrink-0">
                    <div class="w-10 h-10 rounded-xl bg-amber-800 text-white flex items-center justify-center font-bold">
                        <i class="fa-solid fa-calendar-day text-lg"></i>
                    </div>
                    <div class="text-xs">
                        <p class="text-slate-400 uppercase font-semibold text-[10px]">Kỳ đánh giá</p>
                        <p class="font-bold text-slate-800 text-sm">Sau 3 tháng làm việc</p>
                    </div>
                </div>
            </div>

            <!-- THÔNG TIN CHUNG THỂ HỆN BẰNG CARDS THÔNG TIN -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div class="flex items-center gap-3 p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <div class="text-xs">
                        <span class="text-slate-400 block text-[10px]">Người đánh giá trực tiếp</span>
                        <strong class="text-slate-800 font-semibold">Ca trưởng / Manager</strong>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <div class="text-xs">
                        <span class="text-slate-400 block text-[10px]">Mục tiêu chung</span>
                        <strong class="text-slate-800 font-semibold">Đạt tất cả tiêu chuẩn áp dụng</strong>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3.5 bg-slate-50/80 rounded-2xl border border-slate-100">
                    <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center text-xs shrink-0">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="text-xs">
                        <span class="text-slate-400 block text-[10px]">Quy trình nhận thông báo</span>
                        <strong class="text-slate-800 font-semibold">Quản lý ca gửi kết quả & bài bổ sung</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- BẢNG CHI TIẾT TIÊU CHUẨN NĂNG LỰC -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-2 h-5 bg-amber-800 rounded-full"></span> Khung Tiêu Chuẩn Năng Lực Bắt Buộc
                </h2>
                <span class="text-xs text-amber-900 font-semibold bg-amber-50 px-3 py-1 rounded-full border border-amber-200/50">
                    Đạt khi điểm tổng ≥ 80 và mỗi tiêu chí ≥ 70
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-4 px-6 w-1/4">Năng Lực</th>
                            <th class="py-4 px-4 text-center w-1/6">Trọng Số / Tối Thiểu</th>
                            <th class="py-4 px-6 w-7/12">Ý Nghĩa & Yêu Cầu Chi Tiết</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        <!-- Hàng 1 -->
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-bold text-slate-800 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-amber-100/70 text-amber-900 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-book"></i>
                                </span>
                                Nghiệp vụ
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs inline-block">
                                    20% · ≥ 70
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 leading-relaxed">
                                Nắm vững đầy đủ kiến thức về danh mục sản phẩm, thành phần nguyên liệu, quy trình bảo quản và tiêu chuẩn vệ sinh ATTP tại quầy.
                            </td>
                        </tr>

                        <!-- Hàng 2 -->
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-bold text-slate-800 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-amber-100/70 text-amber-900 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-mug-hot"></i>
                                </span>
                                Kỹ năng
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs inline-block">
                                    30% · ≥ 70
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 leading-relaxed">
                                Thực hiện chuẩn xác thao tác của vị trí (định lượng và công thức pha chế với Barista, thanh toán với Thu ngân, phục vụ bàn với Phục vụ) và xử lý tình huống công việc.
                            </td>
                        </tr>

                        <!-- Hàng 3 -->
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-bold text-slate-800 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-amber-100/70 text-amber-900 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-rotate"></i>
                                </span>
                                Quy trình
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs inline-block">
                                    20% · ≥ 70
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 leading-relaxed">
                                Nghiêm túc tuân thủ quy trình chuẩn bị ca làm (Opening/Closing), nguyên tắc FIFO khi quản lý nguyên liệu và duy trì tốc độ làm việc.
                            </td>
                        </tr>

                        <!-- Hàng 4 -->
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-bold text-slate-800 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-amber-100/70 text-amber-900 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-heart-pulse"></i>
                                </span>
                                Phục vụ
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs inline-block">
                                    15% · ≥ 70
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 leading-relaxed">
                                Giao tiếp lịch sự, thái độ niềm nở, chủ động hỗ trợ và phục vụ đúng tiêu chuẩn trải nghiệm dịch vụ khách hàng của thương hiệu.
                            </td>
                        </tr>

                        <!-- Hàng 5 -->
                        <tr class="hover:bg-amber-50/30 transition">
                            <td class="py-4 px-6 font-bold text-slate-800 flex items-center gap-3">
                                <span class="w-8 h-8 rounded-xl bg-amber-100/70 text-amber-900 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </span>
                                Tác phong
                            </td>
                            <td class="py-4 px-4 text-center">
                                <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-xs inline-block">
                                    15% · ≥ 70
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 leading-relaxed">
                                Thực hiện đúng nội quy lao động, quy định về đồng phục/tác phong, đảm bảo quy chuẩn vệ sinh cá nhân và an toàn lao động.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- GHI CHÚ BỔ SUNG DÀNH CHO NHÂN VIÊN -->
            <div class="p-5 bg-amber-50/50 border-t border-slate-100 flex items-start gap-3 text-xs text-amber-950">
                <i class="fa-solid fa-circle-info text-amber-700 text-sm mt-0.5 shrink-0"></i>
                <p class="leading-relaxed">
                    <strong>Lưu ý:</strong> Sau khi ca trưởng hoàn thành kỳ sát hạch đánh giá thực tế, kết quả và thông báo phân công bài học bổ sung (nếu có) sẽ được gửi đến tài khoản của bạn.
                </p>
            </div>
        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/assessments/show.blade.php ENDPATH**/ ?>