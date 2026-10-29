<?php $__env->startSection('title', 'Danh Sách Nhân Sự Ca & Theo Dõi Năng Lực'); ?>

<?php $__env->startSection('content'); ?>
<!-- Khởi tạo trạng thái Modal & Bộ lọc trong Alpine.js -->
<div class="space-y-6 font-sans" x-data="{ searchQuery: '', activeTab: 'all', selectedStaff: null }">

    <!-- BREADCRUMB & HEADER CONTAINER -->
    <div class="page-banner bg-white border border-coffee-200 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-coffee-900 tracking-tight">Danh Sách Nhân Sự Ca & Theo Dõi Năng Lực</h1>
            <p class="text-sm text-slate-500 mt-2">Theo dõi nhân viên trong ca, tiến độ đào tạo và các hạn đánh giá cần xử lý.</p>
        </div>

        <div class="flex items-center space-x-2.5">
        </div>
    </div>

    <!-- PHẦN THỐNG KÊ NHANH -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white border border-coffee-200 border-t-4 border-t-coffee-700 rounded-2xl p-5 shadow-sm flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-coffee-700 mb-3">Nhân sự trong ca</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-coffee-900"><?php echo e($totalStaffs ?? count($staffs)); ?></span>
                    <span class="text-xs font-bold text-slate-600">nhân viên</span>
                </div>
                <p class="text-xs text-slate-500 mt-3">Nhân viên đang làm thuộc phạm vi ca phụ trách.</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-coffee-50 text-coffee-800 flex items-center justify-center text-lg">
                <i class="fa-solid fa-users"></i>
            </div>
        </div>

        <div class="bg-white border border-emerald-100 border-t-4 border-t-emerald-500 rounded-2xl p-5 shadow-sm flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 mb-3">Tiến độ đào tạo</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-coffee-900"><?php echo e($avgProgress ?? 0); ?>%</span>
                </div>
                <p class="text-xs text-slate-500 mt-3">Tiến độ học trung bình của nhân viên trong ca.</p>
                <div class="w-28 bg-slate-100 h-1.5 rounded-full overflow-hidden mt-2">
                    <div class="bg-emerald-500 h-full rounded-full transition-all duration-300" style="width: <?php echo e($avgProgress ?? 0); ?>%"></div>
                </div>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>

        <a href="<?php echo e(route('leader.dashboard')); ?>" class="bg-amber-50 border border-amber-200 border-t-4 border-t-amber-500 rounded-2xl p-5 shadow-sm flex items-start justify-between gap-3 hover:shadow-md transition focus:outline-none focus:ring-2 focus:ring-amber-500">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-3">Nhắc hạn sát hạch</p>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-coffee-900"><?php echo e($dueCount ?? 0); ?></span>
                    <span class="text-xs font-bold text-slate-600">nhân sự</span>
                </div>
                <p class="text-xs text-amber-800 mt-3">Sắp đến hạn trong 7 ngày, đến hạn hoặc quá hạn.</p>
                <p class="text-xs font-bold text-amber-800 mt-2">Xem lịch & xếp sát hạch <i class="fa-solid fa-arrow-right ml-1"></i></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-lg">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </a>
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
                    <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-coffee-900 shadow-sm' : 'text-slate-600 hover:text-coffee-900'" class="px-3 py-1.5 rounded-lg transition">Tất cả (<?php echo e($totalStaffs ?? count($staffs)); ?>)</button>
                    <button type="button" @click="activeTab = 'shift'" :class="activeTab === 'shift' ? 'bg-white text-coffee-900 shadow-sm' : 'text-slate-600 hover:text-coffee-900'" class="px-3 py-1.5 rounded-lg transition">Đang trong ca (<?php echo e($totalStaffs ?? count($staffs)); ?>)</button>
                </div>
            </div>
        </div>
    </div>

    <!-- DANH SÁCH NHÂN VIÊN -->
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $staffs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                // 1. TÍNH TOÁN CÁC BIẾN TIẾN ĐỘ ĐỒNG BỘ
                $coursesTotal     = $staff->total_courses_count ?? 0;
                $coursesCompleted = $staff->completed_courses_count ?? 0;
                $progress         = $staff->overall_progress ?? 0;

                // 2. TÍNH TOÁN QUIZ
                $quizScore        = $staff->theory_score;

                // 3. THÔNG TIN CƠ BẢN
                $staffCode        = 'NV-' . str_pad($staff->id ?? 0, 3, '0', STR_PAD_LEFT);
                $staffName        = $staff->name ?? 'N/A';
                $staffChucVu      = $staff->ChucVu ?? $staff->chuc_vu ?? 'Thu ngân';
                $staffTram        = $staff->tram_lam_viec ?? $staff->Tram ?? 'Quầy Bar';
                $assessmentScore  = $staff->assessment_score;
                $assessmentStatus = $staff->assessment_status;

                $inShift          = $staff->in_shift ?? true;
            ?>
            
            <div x-show="(searchQuery === '' || '<?php echo e(strtolower(addslashes($staffName))); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($staffCode)); ?>'.includes(searchQuery.toLowerCase())) && (activeTab === 'all' || (activeTab === 'shift' && <?php echo e($inShift ? 'true' : 'false'); ?>))" 
                 class="bg-white border border-[#E8DFC8] rounded-2xl p-4 shadow-sm relative overflow-hidden transition hover:shadow-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center">
                    
                    <!-- Thông tin NV -->
                    <div class="lg:col-span-4 flex items-start space-x-3">
                        <div class="relative">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($staffName)); ?>&background=6E4E30&color=fff" class="w-12 h-12 rounded-full object-cover" alt="<?php echo e($staffName); ?>">
                            <span class="w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full absolute bottom-0 right-0"></span>
                        </div>
                        <div class="space-y-0.5">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-sm font-bold text-coffee-900"><?php echo e($staffName); ?></h4>
                                <span class="text-[10px] font-extrabold bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded"><?php echo e($staffCode); ?></span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium"><?php echo e($staffChucVu); ?></p>
                            <div class="text-[10px] text-slate-400 flex items-center space-x-2 pt-1">
                                <span>Trạm: <?php echo e($staffTram); ?></span>
                                <span>•</span>
                                <span class="text-emerald-700 font-semibold">Đang trong ca</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tiến độ -->
                    <div class="lg:col-span-3 bg-[#FAF7F2] p-3 rounded-xl border border-[#E8DFC8]/60 space-y-1.5">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-coffee-900 truncate">Tiến độ đào tạo</span>
                            <span class="font-black text-emerald-700"><?php echo e($progress); ?>%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-emerald-600 h-full rounded-full transition-all duration-300" style="width: <?php echo e($progress); ?>%"></div>
                        </div>
                        <div class="flex justify-between text-[10px] text-slate-500 pt-0.5">
                            <span>Cấp độ: <strong>Cấp <?php echo e($staff->CapDo ?? 1); ?></strong></span>
                            <span class="text-slate-400">(<?php echo e($coursesCompleted); ?>/<?php echo e($coursesTotal); ?> khóa)</span>
                        </div>
                    </div>

                    <!-- Điểm số -->
                    <div class="lg:col-span-3 grid grid-cols-2 gap-2 text-center">
                        <div class="bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-500 block">Kiểm tra lý thuyết</span>
                            <?php if($quizScore !== null): ?>
                                <span class="text-base font-black text-coffee-900"><?php echo e($quizScore); ?>/100</span>
                                <span class="text-[10px] text-slate-500 block">Lần gần nhất</span>
                            <?php else: ?>
                                <span class="text-xs font-semibold text-slate-500">Chưa làm bài</span>
                            <?php endif; ?>
                        </div>
                        <div class="bg-slate-50 border border-slate-200 p-2 rounded-xl">
                            <span class="text-[10px] font-bold text-slate-500 block">Đánh giá năng lực</span>
                            <?php if($assessmentScore !== null): ?>
                                <span class="text-base font-black text-coffee-900"><?php echo e($assessmentScore); ?>/100</span>
                                <span class="text-[10px] font-semibold block <?php echo e($assessmentStatus === 'Đạt' ? 'text-emerald-700' : ($assessmentStatus === 'Chưa đạt' ? 'text-red-700' : 'text-amber-700')); ?>"><?php echo e($assessmentStatus); ?></span>
                            <?php else: ?>
                                <span class="text-xs font-semibold text-slate-500">Chưa đánh giá</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Xem hồ sơ -->
                    <div class="lg:col-span-2 flex items-center lg:justify-end">
                        <a href="<?php echo e(route('leader.staffProfile', $staff->id)); ?>" class="font-bold text-amber-800 hover:text-coffee-900 transition flex items-center text-xs">
                            <i class="fa-solid fa-address-card mr-1.5" aria-hidden="true"></i>Xem hồ sơ <i class="fa-solid fa-angle-right text-[10px] ml-1" aria-hidden="true"></i>
                        </a>
                    </div>

                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="bg-white p-8 text-center rounded-2xl border border-dashed border-slate-300">
                <p class="text-sm font-semibold text-slate-600">Hiện chưa có dữ liệu nhân viên trong cơ sở dữ liệu.</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/leader/staff_list.blade.php ENDPATH**/ ?>