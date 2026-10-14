

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">
    <?php if($errors->any()): ?>
        <div role="alert" class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-sm">
            <p class="font-bold">Chưa gửi được đánh giá. Kiểm tra các nội dung sau:</p>
            <ul class="list-disc pl-5"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
        </div>
    <?php endif; ?>

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
    <div class="page-banner flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản Lý Đánh Giá Năng Lực</h1>
            <p class="text-sm text-slate-500 mt-1">Tiến hành đánh giá năng lực sau 3 tháng </p>
        </div>
        
        <!-- Nút chuyển sang trang Danh sách nhân sự -->
       
    </div>

    <!-- Cảnh báo nhân viên đến kỳ sát hạch 3 tháng -->
    <?php if(isset($dueStaffs) && $dueStaffs->count() > 0): ?>
    <section aria-labelledby="assessment-reminder-title" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fa-regular fa-calendar-check" aria-hidden="true"></i></span>
                <div>
                    <h3 id="assessment-reminder-title" class="text-base font-bold text-slate-800">Nhắc hạn đánh giá</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Theo dõi thời hạn và điều kiện sát hạch của nhân viên.</p>
                </div>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600"><?php echo e($dueStaffs->count()); ?> nhân viên cần theo dõi</span>
        </div>

        <div class="divide-y divide-slate-100">
            <?php $__currentLoopData = $dueStaffs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $due): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="px-5 py-4 sm:px-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex min-w-0 items-center gap-3">
                            <span aria-hidden="true" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-sm font-bold text-slate-600"><?php echo e(mb_substr($due->name, 0, 1)); ?></span>
                            <div class="min-w-0">
                                <p class="break-words text-sm font-semibold text-slate-800"><?php echo e($due->name); ?></p>
                                <p class="mt-1 text-xs text-slate-500">Hạn đánh giá <span class="font-medium text-slate-700"><?php echo e($due->due_at->format('d/m/Y')); ?></span></p>
                            </div>
                        </div>
                        <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['inline-flex w-fit shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-rose-50 text-rose-700' => $due->due_status === 'Quá hạn', 'bg-amber-50 text-amber-700' => $due->due_status !== 'Quá hạn']); ?>">
                            <i class="fa-regular fa-clock" aria-hidden="true"></i><?php echo e($due->due_status); ?>

                        </span>
                    </div>
                    <div class="mt-3 sm:ml-[52px]">
                        <?php if($due->scheduled_assessment): ?>
                            <p class="inline-flex items-center gap-2 text-xs font-medium text-blue-700"><i class="fa-regular fa-calendar" aria-hidden="true"></i><?php echo e($due->scheduled_assessment->TrangThai === 'ChoDanhGia' ? 'Đã xếp lịch' : 'Đang xử lý kết quả'); ?> · #<?php echo e($due->scheduled_assessment->id); ?></p>
                        <?php elseif(!$due->eligibility['ready']): ?>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <p class="text-xs font-semibold text-slate-700">Chưa đủ điều kiện đánh giá</p>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">Cần hoàn thành <?php echo e(count($due->eligibility['missing'])); ?> yêu cầu học tập và kiểm tra trước khi xếp lịch.</p>
                                <details class="group mt-2">
                                    <summary class="w-fit cursor-pointer rounded text-xs font-semibold text-amber-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-amber-600">Xem yêu cầu cần hoàn thành</summary>
                                    <ul class="mt-3 max-h-64 space-y-2 overflow-y-auto border-t border-slate-200 pt-3 text-xs leading-relaxed text-slate-600">
                                        <?php $__currentLoopData = $due->eligibility['missing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="flex items-start gap-2"><i class="fa-regular fa-circle mt-1 shrink-0 text-[8px] text-slate-400" aria-hidden="true"></i><span><?php echo e($missing); ?></span></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </details>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <p class="text-xs font-medium text-emerald-700"><i class="fa-solid fa-check mr-1" aria-hidden="true"></i>Đủ điều kiện đánh giá</p>
                                <button type="button" onclick="document.querySelector('select[name=user_id]').value='<?php echo e($due->id); ?>'; document.querySelector('select[name=user_id]').focus();" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800 transition hover:bg-amber-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">Xếp lịch đánh giá <i class="fa-solid fa-arrow-right ml-1" aria-hidden="true"></i></button>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
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
            <option value="<?php echo e($staff->id); ?>" <?php if(!$staff->eligibility['ready']): echo 'disabled'; endif; ?>><?php echo e($staff->name ?? $staff->HoTen); ?> (NV-<?php echo e(str_pad($staff->id, 3, '0', STR_PAD_LEFT)); ?>)<?php echo e(!$staff->eligibility['ready'] ? ' — Chưa đủ điều kiện' : ' — Đủ điều kiện'); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</select>
<p class="text-xs text-slate-500 mt-2">Chỉ chọn được người hoàn thành toàn bộ bài học và đạt các quiz được phân công. Nhắc hạn vẫn giữ người chưa đủ điều kiện.</p>
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
                    Tạo lịch & gửi thông báo
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

                                        <?php if($item->ChiTietDeXuat): ?>
                                            <?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($item->ChiTietDeXuat, true)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                        <?php endif; ?>
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
                                        <?php elseif($item->TrangThai === 'TraLai'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Cần bổ sung</span>
                                            <p class="text-xs text-rose-700 mt-1"><?php echo e($item->ly_do_tra_lai ?? ''); ?></p>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                                Chờ đánh giá
                                            </span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Cột Thao tác -->
                                    <td class="py-3 px-4 text-right">
                                        <?php if(in_array($item->TrangThai, ['ChoDanhGia', 'TraLai'])): ?>
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

            <form id="scoringForm" action="<?php echo e(route('leader.saveScore')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php if($errors->any() && old('assessment_id')): ?>
                    <div role="alert" class="bg-rose-50 text-rose-800 rounded-lg p-3 text-sm">
                        <p class="font-bold">Vui lòng bổ sung nội dung trước khi gửi:</p>
                        <ul class="list-disc pl-5"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>
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
                    <?php echo $__env->make('components.assessment-improvement-details', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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

        document.getElementById('totalScore').innerText = finalScore + '%';

        const allCriteriaPassed = (nv >= 70 && kn >= 70 && qt >= 70 && pv >= 70 && tp >= 70);
        const isPassed = (finalScore >= 80) && allCriteriaPassed;

        const badge = document.getElementById('passBadge');
        const improvementSection = document.getElementById('improvementSection');
        const submitBtn = document.getElementById('submitBtn');
        improvementSection.dataset.passed = String(isPassed);

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

    <?php if($errors->any() && old('assessment_id')): ?>
        <?php ($retryAssessment = $schedules->firstWhere('id', old('assessment_id'))); ?>
        openScoringModal(<?php echo json_encode(old('assessment_id'), 15, 512) ?>, <?php echo json_encode($retryAssessment->staff_name ?? '', 15, 512) ?>, <?php echo json_encode($retryAssessment->tiêu_chuẩn ?? '', 15, 512) ?>);
        const previousInput = <?php echo json_encode(session()->getOldInput(), 15, 512) ?>;
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
    <?php endif; ?>

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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/leader/dashboard.blade.php ENDPATH**/ ?>