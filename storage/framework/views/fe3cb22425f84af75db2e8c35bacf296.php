

<?php $__env->startSection('title', ($course->TenCTDT ?? 'Chi tiết khóa học') . ' - BrewLearn'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // 1. Đồng bộ danh sách bài học
    $lessonList = collect($lessons ?? $allLessons ?? []);
    $totalLessons = $lessonList->count();
    
    // 2. Đếm số bài đã hoàn thành
    $completedLessons = $lessonList->filter(function($item) {
        return in_array($item->LessonStatus ?? $item->TrangThai ?? '', ['HoanThanh', 'DaHoc', 'Completed']);
    })->count();
    
    // 3. Tính % tiến độ
    $progressPercent = round($course->TienDo ?? 0, 1);

    // 4. Tìm bài học tiếp theo chưa học (nếu học xong hết thì lấy bài đầu tiên)
    $nextLesson = $lessonList->firstWhere('LessonStatus', '!=', 'HoanThanh') 
        ?? $lessonList->firstWhere('TrangThai', '!=', 'HoanThanh') 
        ?? $lessonList->first();

    $targetLessonId = $nextLesson->MaBaiHoc ?? $nextLesson->id ?? 1;

    $currentUserId = request()->query('user_id');
    $actionRouteParams = ['id' => $targetLessonId];
    if ($currentUserId) {
        $actionRouteParams['user_id'] = $currentUserId;
    }
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- CỘT TRÁI: TỔNG QUAN VÀ DANH SÁCH BÀI HỌC -->
    <div class="space-y-4">
        <!-- HÀNH TRÌNH HỌC TẬP CARD -->
        <div class="page-banner bg-gradient-to-br from-[#2D1E18] to-[#1F1410] text-white p-5 rounded-3xl space-y-4 shadow-lg">
            <div class="flex justify-between items-center text-[11px] font-bold tracking-wider text-amber-200/80 uppercase">
                <span>HÀNH TRÌNH HỌC TẬP</span>
                <i class="fa-solid fa-trophy text-amber-400 text-sm"></i>
            </div>
            
            <h2 class="text-base font-extrabold leading-snug">
                <?php echo e($course->TenCTDT ?? 'Làm Việc An Toàn - Kỹ Năng tại nơi Làm Việc Cho Nhân Viên'); ?>

            </h2>

            <!-- HIỂN THỊ TIẾN ĐỘ -->
            <div class="flex justify-between items-center mb-2 pt-2 border-t border-amber-900/40">
                <div>
                    <span class="text-xs text-gray-400 uppercase font-semibold">HOÀN THÀNH</span>
                    <div class="text-lg font-bold text-white"><?php echo e($completedLessons); ?>/<?php echo e($totalLessons); ?></div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-gray-400 uppercase font-semibold">TIẾN ĐỘ</span>
                    <div class="text-lg font-bold text-emerald-400"><?php echo e($progressPercent); ?>%</div>
                </div>
            </div>

            <!-- Thanh Progress Bar -->
            <div class="w-full bg-neutral-800 h-2 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full transition-all duration-500" style="width: <?php echo e($progressPercent); ?>%;"></div>
            </div>
        </div>

        <!-- DANH SÁCH BÀI HỌC -->
        <div class="bg-white border border-[#E2D4C1] rounded-3xl p-4 space-y-3 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <span class="text-xs font-bold text-coffee-900 flex items-center space-x-2">
                    <i class="fa-solid fa-chevron-right text-[10px] text-amber_brand"></i>
                    <span>DANH SÁCH BÀI HỌC</span>
                </span>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                    <?php echo e($totalLessons); ?>

                </span>
            </div>

            <!-- LOOP BÀI HỌC -->
            <div class="space-y-2">
                <?php if($totalLessons > 0): ?>
                    <?php $__currentLoopData = $lessonList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $isDone = in_array($item->LessonStatus ?? $item->TrangThai ?? '', ['HoanThanh', 'DaHoc', 'Completed']);
                            $itemRouteParams = ['id' => $item->MaBaiHoc ?? $item->id ?? ($index + 1)];
                            if ($currentUserId) {
                                $itemRouteParams['user_id'] = $currentUserId;
                            }
                            $itemUrl = route('courses.lesson', $itemRouteParams);
                        ?>

                        <a href="<?php echo e($itemUrl); ?>" class="group block p-3 rounded-2xl <?php echo e($isDone ? 'bg-emerald-50/60 border-emerald-200' : 'bg-[#FAF7F2] hover:bg-[#EFE7DA] border-[#E8DFC8]'); ?> border transition shadow-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full <?php echo e($isDone ? 'bg-emerald-500 text-white' : 'bg-amber-100 text-amber-900 group-hover:bg-amber_brand group-hover:text-white'); ?> flex items-center justify-center font-bold text-xs shrink-0 transition">
                                        <?php if($isDone): ?>
                                            <i class="fa-solid fa-check text-xs"></i>
                                        <?php else: ?>
                                            #<?php echo e(sprintf('%02d', $item->ThuTu ?? ($index + 1))); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-bold <?php echo e($isDone ? 'text-emerald-600' : 'text-amber_brand'); ?> uppercase">
                                            <?php echo e($isDone ? 'ĐÃ HOÀN THÀNH' : 'VIDEO'); ?>

                                        </div>
                                        <h4 class="text-xs font-bold text-coffee-900 group-hover:text-amber-900 line-clamp-1">
                                            <?php echo e($item->TenBaiHoc); ?>

                                        </h4>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold <?php echo e($isDone ? 'text-emerald-700' : 'text-amber_brand'); ?> group-hover:translate-x-0.5 transition flex items-center space-x-1">
                                    <span><?php echo e($isDone ? 'Xem lại' : 'Vào học'); ?></span>
                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="text-xs text-slate-400 italic text-center py-2">Chưa có bài học nào trong khóa học này.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: NỘI DUNG TỔNG QUAN & NÚT HỌC BÀI -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white border border-[#E2D4C1] rounded-3xl p-6 shadow-sm space-y-6">
            <div class="text-center space-y-2 pt-2">
                <h2 class="text-xl font-extrabold text-coffee-900 uppercase tracking-wide flex items-center justify-center gap-2">
                    <span>NHỮNG GÌ BẠN SẼ HỌC</span>
                    <i class="fa-solid fa-briefcase text-amber_brand text-lg"></i>
                </h2>
                <p class="text-xs text-slate-500">
                    Trong khoá học <strong class="text-coffee-900 uppercase"><?php echo e($course->TenCTDT ?? 'KỸ NĂNG AN TOÀN TẠI NƠI LÀM VIỆC'); ?></strong>, bạn sẽ học:
                </p>
            </div>

            <!-- TÓM TẮT NỘI DUNG -->
            <?php if(!empty($course->MoTa)): ?>
                <div class="text-xs text-slate-700 leading-relaxed px-2 space-y-2">
                    <?php echo nl2br(e($course->MoTa)); ?>

                </div>
            <?php else: ?>
                <ul class="space-y-3 text-xs text-slate-700 leading-relaxed px-2">
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[6px] text-coffee-900 mt-1.5 shrink-0"></i>
                        <span><strong>Mối nguy (hazards)</strong> là gì, vì sao xuất hiện và cách nhận biết nhanh trong mọi môi trường.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[6px] text-coffee-900 mt-1.5 shrink-0"></i>
                        <span>Cách <strong>đánh giá rủi ro & giảm thiểu rủi ro</strong> trước khi sự cố xảy ra.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[6px] text-coffee-900 mt-1.5 shrink-0"></i>
                        <span>Cách xếp dỡ hàng hoá, kê kệ, sắp xếp vật dụng đúng chuẩn an toàn.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[6px] text-coffee-900 mt-1.5 shrink-0"></i>
                        <span>Quy tắc di chuyển xe đẩy, xe hàng, xe trolley để tránh tai nạn nghiêm trọng.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fa-solid fa-circle text-[6px] text-coffee-900 mt-1.5 shrink-0"></i>
                        <span>Cách phòng tránh <em>trượt – vấp – ngã</em>, một trong những nguyên nhân gây tai nạn lao động phổ biến nhất.</span>
                    </li>
                </ul>
            <?php endif; ?>

            <!-- NÚT HỌC BÀI -->
            <?php if($totalLessons > 0): ?>
                <div class="pt-4 border-t border-slate-100 flex justify-start">
                    <a href="<?php echo e(route('courses.lesson', $actionRouteParams)); ?>" class="inline-flex items-center space-x-2 <?php echo e($progressPercent == 100 ? 'bg-emerald-100 hover:bg-emerald-200 text-emerald-900 border-emerald-300' : 'bg-amber-100 hover:bg-amber-200 text-amber-900 border-amber-300/50'); ?> font-extrabold px-5 py-3 rounded-2xl text-xs transition border shadow-sm">
                        <i class="fa-solid <?php echo e($completedLessons > 0 ? 'fa-play' : 'fa-location-arrow'); ?> text-amber_brand"></i>
                        <span><?php echo e($completedLessons > 0 ? ($progressPercent == 100 ? 'Xem lại bài học đầu tiên' : 'Học tiếp bài dở ngay') : 'Học bài đầu tiên'); ?></span>
                        <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\courses\show.blade.php ENDPATH**/ ?>