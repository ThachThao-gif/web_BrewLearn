

<?php $__env->startSection('title', ($lesson->TenBaiHoc ?? 'Chi tiết bài học') . ' - BrewLearn'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-4">
    <!-- Thanh điều hướng trên cùng -->
    <div class="flex justify-between items-center bg-white px-6 py-3 rounded-2xl border border-coffee-200/60 shadow-sm">
        <a href="<?php echo e(route('courses.show', ['id' => $lesson->FK_MaCTDT ?? 1])); ?>" class="text-xs font-bold text-coffee-900 hover:text-amber_brand transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Quay lại khóa học</span>
        </a>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300/50">
            <?php echo e($course->ChucVuApDung ?? $lesson->chuongTrinh->ChucVuApDung ?? 'Nội dung'); ?>

        </span>
    </div>

    <!-- HEADER BÀI HỌC -->
    <div class="page-banner flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <h1 class="text-xl md:text-2xl font-extrabold text-coffee-900">
            Bài <?php echo e(sprintf('%02d', $lesson->ThuTu ?? 1)); ?>: <?php echo e($lesson->TenBaiHoc ?? 'Nội dung bài học'); ?>

        </h1>
    </div>
</div>

<!-- MAIN LESSON CONTENT GRID -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
    <!-- LEFT & CENTER: VIDEO PLAYER & LESSON DETAILS -->
    <div class="lg:col-span-2 space-y-5">
        <!-- KHUNG VIDEO PLAYER -->
        <div class="relative bg-black rounded-2xl overflow-hidden shadow-xl w-full aspect-video flex items-center justify-center">
            <video 
                id="lessonVideo"
                class="w-full h-full object-contain" 
                controls 
                preload="metadata"
                playsinline
                src="<?php echo e(asset('videos/' . trim($lesson->VideoURL))); ?>">
                
                <source src="<?php echo e(asset('videos/' . trim($lesson->VideoURL))); ?>" type="video/mp4">
                Trình duyệt không hỗ trợ phát video HTML5.
            </video>
        </div>

        <p class="text-xs text-slate-500">Bài học được ghi nhận hoàn thành khi xem từ 95% thời lượng video.</p>

        <!-- TAB NAVIGATION & CONTENT -->
        <div class="bg-white border border-[#E2D4C1] rounded-3xl p-5 space-y-5">
            <div class="flex items-center space-x-2 border-b border-slate-100 pb-3 overflow-x-auto">
                <button class="px-4 py-2 rounded-xl text-xs font-bold bg-[#EFE7DA] text-coffee-900 flex items-center space-x-2 shrink-0">
                    <i class="fa-solid fa-list-check text-coffee-800"></i>
                    <span>Nội dung bài học</span>
                </button>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-coffee-900">Chi tiết nội dung đào tạo</h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Yêu cầu hoàn thành các nội dung và chỉ dẫn bên dưới.</p>
                    </div>
                </div>

                <div class="bg-[#FAF7F2] border border-[#E8DFC8] rounded-2xl p-5 text-xs text-slate-700 leading-relaxed space-y-3">
                    <?php if(isset($lesson->NoiDung) && !empty($lesson->NoiDung)): ?>
                        <?php echo nl2br(e($lesson->NoiDung)); ?>

                    <?php else: ?>
                        <p class="italic text-slate-400">Bài học này chưa cập nhật nội dung chi tiết trong cơ sở dữ liệu.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDEBAR: PROGRAM PLAYLIST & ATTACHMENTS -->
    <div class="space-y-5">
        <?php
            $playlist = $allLessons ?? $lessons ?? [];
            $courseProgress = $course->TienDo ?? 0;
        ?>

        <!-- PROGRESS CARD -->
        <div class="bg-white border border-[#E2D4C1] rounded-3xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-900 bg-amber-100 px-2 py-0.5 rounded-md">TIẾN ĐỘ CHUYÊN ĐỀ</span>
                <span class="text-xs font-extrabold text-amber_brand" id="sidebarCoursePercent"><?php echo e($courseProgress); ?>%</span>
            </div>
            <h3 class="text-sm font-bold text-coffee-900"><?php echo e($course->TenCTDT ?? $lesson->chuongTrinh->TenCTDT ?? 'Chương trình đào tạo'); ?></h3>
            <p class="text-xs text-slate-500">Tổng số bài học: <?php echo e(count($playlist)); ?> bài</p>
            
            <div class="w-full bg-[#EFE7DA] rounded-full h-2 overflow-hidden">
                <div id="sidebarProgressBar" class="bg-amber_brand h-2 rounded-full transition-all duration-300" style="width: <?php echo e($courseProgress); ?>%"></div>
            </div>
        </div>

<!-- LESSON PLAYLIST -->
<div class="bg-white border border-[#E2D4C1] rounded-3xl p-5 space-y-4">
    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center space-x-2">
            <i class="fa-solid fa-list text-coffee-800 text-xs"></i>
            <h3 class="text-xs font-bold text-coffee-900">Danh sách bài học trong chương</h3>
        </div>
        <span class="text-[10px] font-semibold text-slate-400"><?php echo e(count($playlist ?? $lessonsInCourse)); ?> bài</span>
    </div>

    <div class="space-y-2">
        <?php $list = $playlist ?? $lessonsInCourse ?? []; ?>

        <?php if(count($list) > 0): ?>
            <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $itemId = $item->MaBaiHoc ?? $item->id;
                    $currentId = $lesson->MaBaiHoc ?? $lesson->id;
                    $isCurrent = ($itemId == $currentId);

                    // Kiểm tra bài học đã hoàn thành hay chưa
                    $isDone = ($item->is_completed ?? false) 
                               || (($item->LessonStatus ?? '') === 'HoanThanh') 
                               || (($item->TienDo ?? 0) >= 95);
                ?>
                
                <?php if($isCurrent): ?>
                    <!-- 1. BÀI ĐANG HỌC (ĐANG PHÁT) -->
                    <div class="p-2.5 rounded-2xl bg-[#EFE7DA] border-2 border-amber_brand flex items-center justify-between shadow-sm">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <i class="fa-solid fa-circle-play text-amber_brand text-sm shrink-0"></i>
                            <div class="truncate">
                                <div class="text-[10px] font-bold text-amber-800">ĐANG PHÁT • Bài <?php echo e(sprintf('%02d', $item->ThuTu ?? $loop->iteration)); ?></div>
                                <h4 class="text-xs font-extrabold text-coffee-900 truncate"><?php echo e($item->TenBaiHoc); ?></h4>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold text-amber-900 bg-amber-200 px-1.5 py-0.5 rounded shrink-0">Đang học</span>
                    </div>

                <?php elseif($isDone): ?>
                    <!-- 2. BÀI ĐÃ HOÀN THÀNH (TÍCH XANH & MÀU EMERALD) -->
                    <a href="<?php echo e(route('courses.lesson', ['id' => $itemId, 'user_id' => request('user_id')])); ?>" 
                       class="group p-2.5 rounded-2xl bg-emerald-50/60 hover:bg-emerald-100/70 border border-emerald-200/80 flex items-center justify-between transition min-w-0">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
                            <div class="truncate">
                                <div class="text-[10px] font-bold text-emerald-600">ĐÃ HOÀN THÀNH • Bài <?php echo e(sprintf('%02d', $item->ThuTu ?? $loop->iteration)); ?></div>
                                <h4 class="text-xs font-bold text-emerald-950 truncate group-hover:text-emerald-800"><?php echo e($item->TenBaiHoc); ?></h4>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold text-emerald-800 bg-emerald-200/80 px-1.5 py-0.5 rounded shrink-0">Đã xong</span>
                    </a>

                <?php else: ?>
                    <!-- 3. BÀI CHƯA HỌC -->
                    <a href="<?php echo e(route('courses.lesson', ['id' => $itemId, 'user_id' => request('user_id')])); ?>" 
                       class="group p-2.5 rounded-2xl hover:bg-[#FAF7F2] border border-transparent hover:border-[#E8DFC8] flex items-center justify-between transition min-w-0">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <i class="fa-solid fa-circle-check text-slate-300 text-sm shrink-0 group-hover:text-amber-500"></i>
                            <div class="truncate">
                                <div class="text-[10px] font-bold text-slate-400">Bài <?php echo e(sprintf('%02d', $item->ThuTu ?? $loop->iteration)); ?></div>
                                <h4 class="text-xs font-bold text-slate-700 truncate group-hover:text-coffee-900"><?php echo e($item->TenBaiHoc); ?></h4>
                            </div>
                        </div>
                        <span class="text-[10px] font-extrabold text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded shrink-0 group-hover:bg-amber-100 group-hover:text-amber-900">Xem</span>
                    </a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-xs text-slate-400">Chưa có bài học nào khác.</p>
        <?php endif; ?>
    </div>
</div>

<!-- POPUP MODAL CHỐNG GIAN LẬN KHI CHUYỂN TAB -->
<div id="tabSwitchModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-amber-100 text-center space-y-5">
        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto text-xl shadow-inner">
            <i class="fa-solid fa-exclamation"></i>
        </div>
        <div class="space-y-2">
            <h3 class="text-lg font-bold text-coffee-900">Tạm dừng ghi nhận thời gian học</h3>
            <p class="text-xs text-slate-600 leading-relaxed">
                Bạn đã rời khỏi tab nên hệ thống tạm ngừng ghi nhận. Thời gian học sẽ tiếp tục được tính khi bạn quay trở lại với bài học.
            </p>
        </div>
        <button id="btnResumeLearning" class="w-full bg-[#D97706] hover:bg-[#B45309] text-white font-bold py-3 px-6 rounded-2xl text-xs shadow-md transition-all cursor-pointer">
            Tiếp tục học
        </button>
    </div>
</div>

<!-- JAVASCRIPT XỬ LÝ TIẾN ĐỘ XEM VIDEO VÀ CHỐNG GIAN LẬN -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('lessonVideo');
    const modal = document.getElementById('tabSwitchModal');
    const btnResume = document.getElementById('btnResumeLearning');

    if (!video) return;

    const lessonId = <?php echo e($lesson->MaBaiHoc ?? $lesson->id ?? 0); ?>;
    const lastWatched = <?php echo e($progress->last_watched_seconds ?? 0); ?>;
    let lastSavedPercent = <?php echo e($progress->progress_percent ?? 0); ?>;

    // 1. Tự động nhảy đến thời gian đã học trước đó
    video.addEventListener('loadedmetadata', function () {
        if (lastWatched > 0 && lastWatched < video.duration - 2) {
            video.currentTime = lastWatched;
        }
    });

    // 2. Định kỳ lưu tiến độ lên Server (5 giây/lần)
    let lastSaveTime = 0;
    video.addEventListener('timeupdate', function () {
        const now = Date.now();
        if (now - lastSaveTime > 5000 && !video.paused) {
            lastSaveTime = now;
            sendProgress();
        }
    });

    // 3. Lưu tiến độ khi tạm dừng
    video.addEventListener('pause', function () {
        if (video.currentTime > 0) {
            sendProgress();
        }
    });

    // 4. Lưu 100% khi xem xong
    video.addEventListener('ended', function () {
        sendProgress(100);
    });

    // 5. CHỐNG GIAN LẬN: Tự động pause & hiện popup khi chuyển tab hoặc thu nhỏ trình duyệt
    function pauseDueToTabSwitch() {
        if (!video.paused && !video.ended) {
            video.pause();
            sendProgress(); // Lưu thời gian chính xác ngay lập tức trước khi rời tab
            if (modal) {
                modal.classList.remove('hidden');
            }
        }
    }

    // Phát hiện chuyển tab khác trên trình duyệt
    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            pauseDueToTabSwitch();
        }
    });

    // Phát hiện bấm ra ngoài trình duyệt (Alt + Tab hoặc click ứng dụng khác)
    window.addEventListener('blur', function () {
        pauseDueToTabSwitch();
    });

    // Sự kiện bấm nút "Tiếp tục học"
    if (btnResume) {
        btnResume.addEventListener('click', function () {
            if (modal) {
                modal.classList.add('hidden');
            }
            video.play();
        });
    }

    // Hàm gửi AJAX cập nhật CSDL
    function sendProgress(overridePercent = null) {
        if (!video.duration) return;

        const currentTime = video.currentTime;
        const computedPercent = Math.min(100, Math.round((currentTime / video.duration) * 100));
        const percent = overridePercent !== null ? overridePercent : Math.max(lastSavedPercent, computedPercent);

        fetch("<?php echo e(route('courses.updateProgress')); ?>", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            body: JSON.stringify({
                lesson_id: lessonId,
                current_time: currentTime,
                progress_percent: percent
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                lastSavedPercent = data.lesson_percent || percent;
                
                if (data.course_progress !== undefined) {
                    const sidebarPercent = document.getElementById('sidebarCoursePercent');
                    const sidebarBar = document.getElementById('sidebarProgressBar');
                    if (sidebarPercent) sidebarPercent.textContent = data.course_progress + '%';
                    if (sidebarBar) sidebarBar.style.width = data.course_progress + '%';
                }
            }
        })
        .catch(err => console.error("Lỗi cập nhật tiến độ:", err));
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/courses/lesson.blade.php ENDPATH**/ ?>