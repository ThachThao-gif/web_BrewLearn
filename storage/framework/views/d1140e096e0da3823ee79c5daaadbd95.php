<?php $__env->startSection('title', 'Tổng quan cửa hàng • BrewLearn'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">
<?php echo $__env->make('manager.banner', ['heading' => 'Tổng quan cửa hàng', 'description' => 'Theo dõi học tập, xem ưu tiên hỗ trợ và xử lý đề xuất đào tạo của toàn cửa hàng.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
<?php $__currentLoopData = [['Nhân sự trong phạm vi', $staff->count(), 'users', 'manager.staff'], ['Đánh giá chờ duyệt', $pendingCount, 'clipboard-check', 'manager.assessments'], ['Đến hạn trong 7 ngày / quá hạn', $staff->where('needs_reminder', true)->count(), 'calendar-days', 'manager.staff'], ['Đào tạo lại đang xử lý', $trainingCount, 'arrows-rotate', 'manager.training']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $count, $icon, $link]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<a href="<?php echo e(route($link, request()->only('shift', 'q'))); ?>" class="p-5 bg-white border border-coffee-200 rounded-2xl hover:border-amber-400 transition"><i class="fa-solid fa-<?php echo e($icon); ?> text-amber_brand"></i><p class="text-3xl font-extrabold text-coffee-900 mt-3"><?php echo e($count); ?></p><p class="text-xs text-slate-500 mt-1"><?php echo e($label); ?></p></a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<div class="flex flex-col sm:flex-row justify-between gap-2"><div><h2 class="text-lg font-extrabold text-coffee-900">Ưu tiên xem hồ sơ nhân viên</h2><p class="text-xs text-slate-500 mt-1">Cao → Trung bình → Thấp → Chưa đủ dữ liệu</p></div><span class="text-xs bg-amber-50 border border-amber-100 rounded-xl p-3 text-amber-900">Dự đoán chỉ giúp ưu tiên xem hồ sơ, không dùng để chấm điểm.</span></div>
<?php echo $__env->make('manager.staff-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<div class="rounded-2xl bg-coffee-100 border border-coffee-200 p-5 text-sm text-coffee-700"><p class="font-bold">Hai nguồn thông tin độc lập</p><p class="mt-1">Model đọc dữ liệu học tập và quiz. Điểm trưởng ca được lưu riêng. Quản lý xem từng nguồn và quyết định việc hỗ trợ, không gộp thành một điểm chung.</p></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/dashboard.blade.php ENDPATH**/ ?>