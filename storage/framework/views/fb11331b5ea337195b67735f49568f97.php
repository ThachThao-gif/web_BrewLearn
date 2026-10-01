

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- HIỂN THỊ THÔNG BÁO FLASH MESSAGES (Thành công / Lỗi) -->
    <?php if(session('success')): ?>
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <p class="text-sm font-medium text-emerald-900"><?php echo e(session('success')); ?></p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold text-lg">&times;</button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm flex items-center justify-between">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                <p class="text-sm font-medium text-rose-900"><?php echo e(session('error')); ?></p>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold text-lg">&times;</button>
        </div>
    <?php endif; ?>

    <!-- Header & Navigation -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản Lý Đánh Giá Năng Lực</h1>
            <p class="text-sm text-slate-500 mt-1">Phân hệ điều hành và chấm điểm dành cho Trưởng Ca (Leader)</p>
        </div>
        
        <!-- Nút chuyển sang trang Danh sách nhân sự -->
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('leader.staffList')); ?>" class="inline-flex items-center gap-2 bg-[#4A2C11] text-white px-4 py-2 rounded-xl hover:bg-opacity-90 transition-all font-medium text-sm shadow-sm">
                <i class="fa-solid fa-users text-xs"></i>
                <span>Xem Danh Sách Nhân Sự</span>
            </a>
        </div>
    </div>

    <!-- Cảnh báo nhân viên đến kỳ sát hạch 3 tháng -->
    <?php if(isset($dueStaffs) && $dueStaffs->count() > 0): ?>
    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-xl shadow-sm mb-6">
        <div class="flex items-center justify-between mb-2">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bell text-amber-600 animate-bounce"></i>
                <h3 class="font-bold text-amber-900 text-sm md:text-base">
                    Nhắc nhở: Có <?php echo e($dueStaffs->count()); ?> nhân viên đến kỳ đánh giá năng lực 3 tháng!
                </h3>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
            <?php $__currentLoopData = $dueStaffs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $due): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white p-3 rounded-lg border border-amber-200 flex items-center justify-between shadow-xs">
                    <div>
                        <p class="font-bold text-xs text-slate-800"><?php echo e($due->name); ?></p>
                        <p class="text-[11px] text-slate-500">Vào làm: <?php echo e($due->joined_date_formatted); ?> (<?php echo e($due->days_worked); ?> ngày)</p>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Block 1: Phát thông báo lịch đánh giá định kỳ -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">1</div>
                <h2 class="text-base font-bold text-slate-800">Phát Thông Báo Lịch Đánh Giá</h2>
            </div>

            <form action="<?php echo e(route('leader.createSchedule')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
    <label class="block text-xs font-semibold text-slate-600 mb-1">Chọn Nhân Viên <span class="text-rose-500">*</span></label>
    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
    <option value="">-- Chọn nhân viên --</option>
    <?php if(isset($staffs) && count($staffs) > 0): ?>
        <?php $__currentLoopData = $staffs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($staff->id); ?>"><?php echo e($staff->name ?? $staff->HoTen); ?> (NV-<?php echo e(str_pad($staff->id, 3, '0', STR_PAD_LEFT)); ?>)</option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</select>
</div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tiêu Chuẩn Năng Lực <span class="text-rose-500">*</span></label>
                    <select name="MaCTDT" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Chọn tiêu chuẩn năng lực --</option>
                        <?php if(isset($standards) && count($standards) > 0): ?>
                            <?php $__currentLoopData = $standards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $std): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($std->MaCTDT); ?>"><?php echo e($std->TenCTDT); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Ngày Đánh Giá Sát Hạch <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="NgayDanhGia" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                </div>

                <!-- Ô nhập Ghi chú / Nội dung dặn dò -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Ghi chú / Nội dung dặn dò <span class="text-slate-400 font-normal">(Không bắt buộc)</span>
                    </label>
                    
                    <textarea name="note" 
                              rows="4" 
                              placeholder="Nhập nội dung dặn dò cho nhân viên (ví dụ: Ôn tập quy trình POS, mang đồng phục chuẩn)..." 
                              class="w-full bg-[#FAF7F2] border border-[#E8DFC8] rounded-xl p-3 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:border-transparent focus:bg-white transition resize-y shadow-sm"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium text-sm rounded-xl transition shadow-sm">
                    Tạo Lịch Đánh Giá
                </button>
            </form>
        </div>

        <!-- Block 2: Danh sách lịch sát hạch cần chấm điểm -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">2</div>
                    <h2 class="text-base font-bold text-slate-800">Lịch Sát Hạch Cần Chấm Điểm</h2>
                </div>
                <!-- Ô Lọc tìm kiếm nhanh -->
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
                            <th scope="col" class="py-3 px-4">Tiêu chuẩn</th>
                            <th scope="col" class="py-3 px-4">Ngày đánh giá</th>
                            <th scope="col" class="py-3 px-4">Trạng thái</th>
                            <th scope="col" class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if(isset($schedules) && count($schedules) > 0): ?>
                            <?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-50/50 transition schedule-row">
                                    <td class="py-3 px-4 font-semibold text-slate-700 staff-name-col">
                                        <?php echo e($item->staff_name); ?>

                                    </td>
                                    <td class="py-3 px-4 text-slate-600">
                                        <?php echo e($item->tiêu_chuẩn ?? $item->TieuChuan ?? 'Tiêu chuẩn năng lực'); ?>

                                    </td>
                                    <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                                        <?php echo e(\Carbon\Carbon::parse($item->NgayHen)->format('d/m/Y H:i')); ?>

                                    </td>
                                    
                                    <!-- Cột Trạng thái -->
                                    <td class="py-3 px-4">
                                        <?php if($item->TrangThai === 'ChoPheDuyet'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                                Chờ phê duyệt
                                            </span>
                                        <?php elseif($item->TrangThai === 'DaDuyet' || $item->TrangThai === 'HoanThanh'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                ✅ Đã duyệt
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                Chờ đánh giá
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Cột Thao tác -->
                                    <td class="py-3 px-4 text-right">
                                        <?php if($item->TrangThai === 'ChoDanhGia'): ?>
                                            <button type="button" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition shadow-sm"
                                                    onclick="openScoringModal('<?php echo e($item->id); ?>', '<?php echo e(e($item->staff_name)); ?>', '<?php echo e(e($item->tiêu_chuẩn ?? $item->TieuChuan ?? '')); ?>')">
                                                Chấm Điểm
                                            </button>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 font-medium italic">
                                                Đã gửi (<?php echo e($item->Diem ?? 0); ?>đ)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-400 text-sm">
                                    Hiện chưa có lịch sát hạch nào cần chấm điểm.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL CHẤM ĐIỂM & XỬ LÝ NĂNG LỰC -->
    <div id="scoringModal" onclick="handleBackdropClick(event)" class="fixed inset-0 bg-slate-900/50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full p-6 space-y-5 overflow-y-auto max-h-[90vh]" onclick="event.stopPropagation()">
            
            <!-- Header Modal -->
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

            <form action="<?php echo e(route('leader.saveScore')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="assessment_id" id="modalAssessmentId">

                <!-- Các tiêu chí chấm điểm -->
                <div class="space-y-3">
                    <div class="grid grid-cols-12 gap-2 text-xs font-bold text-slate-500 uppercase bg-slate-50 p-2 rounded-lg">
                        <div class="col-span-7">Tiêu chí đánh giá</div>
                        <div class="col-span-5 text-right">Thang điểm (0 - 100)</div>
                    </div>

                    <!-- 1. Kiến thức nghiệp vụ (20%) -->
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

                    <!-- 2. Thao tác công việc (30%) -->
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

                    <!-- 3. Tuân thủ quy trình (20%) -->
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

                    <!-- 4. Thái độ phục vụ (15%) -->
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

                    <!-- 5. Tác phong (15%) -->
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

                    <!-- Nhận xét của Trưởng ca -->
                    <div class="pt-2">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nhận xét của Trưởng ca</label>
                        <textarea name="comment" rows="2" class="w-full p-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-amber-500" placeholder="Nhập nhận xét chi tiết..."></textarea>
                    </div>
                </div>

                <!-- TỔNG ĐIỂM KẾT QUẢ -->
                <div class="bg-amber-50/60 p-4 rounded-xl border border-amber-100 flex items-center justify-between mt-4">
                    <div>
                        <p class="text-xs text-slate-500 font-medium uppercase">Điểm tổng hợp</p>
                        <div class="flex items-baseline gap-2 mt-0.5">
                            <span id="totalScore" class="text-2xl font-bold text-amber-700">0%</span>
                            <span id="passBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">ĐẠT</span>
                        </div>
                    </div>
                    <div class="text-right text-xs text-slate-500 space-y-0.5">
                        <p>Đạt chuẩn: Điểm tổng <span class="font-bold text-slate-700">≥ 80</span></p>
                        <p>Và mọi tiêu chí <span class="font-bold text-slate-700">≥ 70</span></p>
                    </div>
                </div>

                <!-- PHẦN ĐỀ XUẤT XỬ LÝ (TỰ ĐỘNG HIỆN KHI CHƯA ĐẠT) -->
                <div id="improvementSection" class="hidden bg-rose-50 p-4 rounded-xl border border-rose-100 space-y-3">
                    <p class="text-xs font-bold text-rose-800 uppercase">Đề xuất xử lý của Trưởng ca (Nhân viên chưa đạt):</p>
                    <div class="grid grid-cols-2 gap-2 text-xs text-slate-700">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="daotaobosung" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Đào tạo bổ sung</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="hoclaibaihoc" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Học lại bài học liên quan</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="theodoica" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Theo dõi thêm trong ca làm việc</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="proposals[]" value="danhgialai" class="rounded text-amber-600 focus:ring-amber-500">
                            <span>Đánh giá lại</span>
                        </label>
                    </div>
                </div>

                <!-- Nút bấm gửi -->
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

        document.getElementById('totalScore').innerText = finalScore + '%';

        const allCriteriaPassed = (nv >= 70 && kn >= 70 && qt >= 70 && pv >= 70 && tp >= 70);
        const isPassed = (finalScore >= 80) && allCriteriaPassed;

        const badge = document.getElementById('passBadge');
        const improvementSection = document.getElementById('improvementSection');
        const submitBtn = document.getElementById('submitBtn');

        if (isPassed) {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800';
            badge.innerText = 'ĐẠT (≥80%)';
            
            improvementSection.classList.add('hidden');
            submitBtn.innerText = 'Gửi kết quả đánh giá';
            submitBtn.className = 'px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition shadow-sm';
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800';
            badge.innerText = !allCriteriaPassed && finalScore >= 80 ? 'CHƯA ĐẠT (Có tiêu chí < 70)' : 'CHƯA ĐẠT (<80%)';
            
            improvementSection.classList.remove('hidden');
            submitBtn.innerText = 'Gửi đề xuất xử lý';
            submitBtn.className = 'px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-medium transition shadow-sm';
        }
    }

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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\leader\dashboard.blade.php ENDPATH**/ ?>