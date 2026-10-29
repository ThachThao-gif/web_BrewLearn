<?php $__env->startSection('title', 'Tổng quan cửa hàng • BrewLearn'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto space-y-5">
    <?php echo $__env->make('manager.banner', ['heading' => 'Tổng quan cửa hàng', 'description' => 'Việc cần xử lý và tình hình đào tạo của cửa hàng.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
        <?php $__currentLoopData = [
            ['Nhân viên đang làm', $activeCount, 'users', 'manager.staff', 'text-coffee-700 bg-coffee-100'],
            ['Đánh giá chờ duyệt', $pendingCount, 'clipboard-check', 'manager.assessments', 'text-amber-700 bg-amber-50'],
            ['Sắp đến hạn / quá hạn', $staff->where('needs_reminder', true)->count(), 'calendar-days', 'manager.staff', 'text-rose-700 bg-rose-50'],
            ['Đào tạo lại đang xử lý', $trainingCount, 'arrows-rotate', 'manager.training', 'text-emerald-700 bg-emerald-50'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $count, $icon, $link, $tone]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($link, request()->only('shift', 'q'))); ?>" class="flex items-center gap-4 rounded-2xl border border-coffee-200 bg-white p-4 transition hover:border-amber-400">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl <?php echo e($tone); ?>"><i class="fa-solid fa-<?php echo e($icon); ?>"></i></span>
                <span><span class="block text-2xl font-extrabold leading-none text-coffee-900"><?php echo e($count); ?></span><span class="mt-1 block text-xs text-slate-500"><?php echo e($label); ?></span></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php echo $__env->make('manager.dashboard-charts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/dashboard.blade.php ENDPATH**/ ?>