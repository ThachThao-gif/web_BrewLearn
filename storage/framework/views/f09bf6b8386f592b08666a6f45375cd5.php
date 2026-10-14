

<?php $__env->startSection('title', 'Tổng quan lộ trình - BrewLearn'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">

    <!-- BANNER CHÀO CÁ NHÂN HÓA -->
    <div class="page-banner relative bg-gradient-to-r from-[#2B1B15] via-[#3A261E] to-[#1A0F0B] text-white rounded-3xl p-6 md:p-8 shadow-xl overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-xl z-10">
            <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-300 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider border border-amber-500/30">
                <i class="fa-solid fa-graduation-cap"></i> LỘ TRÌNH: <?php echo e(strtoupper($currentUser->ChucVu ?? 'BARISTA')); ?>

            </div>
            
            <h1 class="text-2xl md:text-3xl font-black text-amber-50 flex items-center gap-2">
                Chào, <?php echo e($currentUser->name ?? 'Học viên'); ?>! 
            </h1>
            
            <p class="text-xs md:text-sm text-amber-200/80 leading-relaxed">
                Bạn đã hoàn thành <span class="font-black text-amber-400 text-sm"><?php echo e($overallProgress ?? 0); ?>%</span> lộ trình đào tạo được giao. Hãy tiếp tục duy trì phong độ nhé!
            </p>
        </div>

        <!-- THẺ BÀI HỌC TIẾP THEO -->
        <?php if(isset($currentLesson) && $currentLesson): ?>
        <div class="bg-white/10 backdrop-blur-md border border-white/15 p-4 rounded-2xl w-full md:w-80 space-y-3 z-10">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-black/40 overflow-hidden shrink-0 border border-white/20">
                    <img src="<?php echo e($currentLesson->HinhAnh ?? 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&q=80&w=200'); ?>" alt="Bài học" class="w-full h-full object-cover">
                </div>
                <div class="space-y-0.5">
                    <div class="text-[10px] font-bold text-amber-300 flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i> Bài <?php echo e($currentLesson->ThuTu ?? 1); ?> • Đang diễn ra
                    </div>
                    <h4 class="text-xs font-bold text-white line-clamp-1">
                        <?php echo e($currentLesson->TenBaiHoc); ?>

                    </h4>
                </div>
            </div>

            <a href="<?php echo e(route('courses.lesson', ['id' => $currentLesson->MaBaiHoc ?? $currentLesson->id])); ?>" class="w-full bg-white hover:bg-amber-50 text-amber-900 font-extrabold py-2 rounded-xl text-xs flex items-center justify-center gap-2 transition shadow">
                <i class="fa-solid fa-play text-amber-600 text-[10px]"></i>
                <span>Tiếp tục học ngay</span>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- STATS OVERVIEW CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <!-- 1. Khóa học được giao -->
        <div class="bg-white border-2 border-amber-500/30 rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-book text-amber-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-amber-100 text-amber-900 px-2 py-0.5 rounded-md">Phân công</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo e($assignedCoursesCount ?? 0); ?> <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Khóa học được giao</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Tổng số khóa thuộc lộ trình</div>
            </div>
        </div>

        <!-- 2. Đang theo học -->
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-regular fa-hourglass-half text-amber-600 text-sm"></i>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">Đang học</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo e($inProgressCoursesCount ?? 0); ?> <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Đang theo học</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Các khóa đang thực hiện</div>
            </div>
        </div>

        <!-- 3. Tiến độ đào tạo -->
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-chart-line text-blue-600 text-sm"></i>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Trung bình</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo e($overallProgress ?? 0); ?><span class="text-xs font-normal text-slate-500">%</span></div>
                <div class="text-xs font-bold text-slate-700">Tiến độ đào tạo</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Tiến độ trung bình các khóa</div>
            </div>
        </div>

        <!-- 4. Đã hoàn thành -->
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-regular fa-circle-check text-emerald-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md">Hoàn thành</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo e($completedCoursesCount ?? 0); ?> <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Đã hoàn thành</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Khóa học đã xong 100%</div>
            </div>
        </div>

        <!-- 5. Điểm Quiz trung bình -->
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-graduation-cap text-indigo-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md">Điểm TB</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800"><?php echo e($avgQuizScore ?? 0); ?> <span class="text-xs font-semibold text-slate-400">/100</span></div>
                <div class="text-xs font-bold text-slate-700">Điểm Quiz trung bình</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Trung bình các lượt thi thuộc bài được phân công, gồm thi lại</div>
            </div>
        </div>

        <!-- 6. Bài kiểm tra đã hoàn thành -->
<div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
    <div class="flex justify-between items-center">
        <i class="fa-solid fa-list-check text-teal-600 text-sm"></i>
        <span class="text-[10px] font-bold bg-teal-50 text-teal-700 px-2 py-0.5 rounded-md">Đạt</span>
    </div>
    <div>
        <!-- ĐÃ SỬA: Thay $completedQuizzesCount thành $completedTestsCount -->
        <div class="text-2xl font-black text-slate-800"><?php echo e($completedTestsCount ?? 0); ?>/<?php echo e($totalTestsCount ?? 0); ?> <span class="text-xs font-normal text-slate-500">bài</span></div>
        <div class="text-xs font-bold text-slate-700">Bài kiểm tra đã hoàn thành</div>
        <div class="text-[10px] text-slate-400 mt-0.5">Số bài kiểm tra đã đạt chuẩn</div>
    </div>
</div>

    </div>

    <!-- KHÓA HỌC CỦA NHÂN VIÊN -->
    <div class="bg-white border border-[#E2D4C1] rounded-3xl p-6 space-y-4 shadow-sm">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-cubes text-amber-600"></i>
                <span>Chương Trình Đào Tạo Phân Công Cho <?php echo e($currentUser->name ?? 'Nhân Viên'); ?></span>
            </h2>
            <span class="text-xs font-bold text-slate-400"><?php echo e(isset($assignedCourses) ? count($assignedCourses) : 0); ?> khóa học</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php $__empty_1 = true; $__currentLoopData = $assignedCourses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $cId = $course->MaCTDT ?? $course->id; ?>
                <div class="p-5 rounded-2xl bg-[#FAF7F2] border border-[#E8DFC8] flex flex-col justify-between space-y-3 hover:border-amber-500 transition shadow-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-900 px-2.5 py-0.5 rounded-md uppercase">
                                <?php echo e($course->ChucVuApDung ?? 'BARISTA'); ?>

                            </span>
                            <span class="text-xs font-black text-amber-600"><?php echo e($course->TienDo ?? 0); ?>%</span>
                        </div>

                        <h3 class="text-xs md:text-sm font-bold text-slate-800 line-clamp-2">
                            <?php echo e($course->TenCTDT); ?>

                        </h3>

                        <p class="text-[11px] text-slate-500 line-clamp-2">
                            <?php echo e(Str::limit(strip_tags($course->MoTa ?? ''), 80)); ?>

                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-200/60">
                        <div class="w-full bg-[#EFE7DA] h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-600 h-full rounded-full transition-all duration-500" style="width: <?php echo e($course->TienDo ?? 0); ?>%;"></div>
                        </div>

                        <a href="<?php echo e(route('courses.show', ['id' => $cId])); ?>" class="w-full bg-slate-900 hover:bg-black text-white text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1.5 transition mt-2">
                            <span>Vào học khóa này</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full py-8 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-400 italic">Chưa có khóa học nào được giao trong CSDL cho nhân viên này.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\welcome.blade.php ENDPATH**/ ?>