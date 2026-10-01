<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">
<?php echo $__env->make('manager.banner', ['heading' => 'Hồ sơ nhân viên', 'description' => 'Xem toàn cửa hàng, phân ca và mở lịch sử học tập, quiz, đánh giá của từng nhân viên.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('manager.staff-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<details class="bg-white rounded-2xl border border-coffee-200 p-5">
    <summary class="font-bold text-coffee-900 cursor-pointer">Cập nhật trạng thái làm việc</summary>
    <p class="text-xs text-slate-500 mt-2">Tạm ngưng / Nghỉ việc: ngừng nhắc hạn, giữ lịch sử học tập và đánh giá. Khi trở lại Đang làm, hạn được tính từ ngày vào làm hoặc đánh giá đã duyệt gần nhất.</p>
    <div class="mt-4 divide-y divide-coffee-100">
        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <form method="POST" action="<?php echo e(route('manager.employment.update', $person->id)); ?>" class="flex flex-wrap items-center gap-3 py-3">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <label for="employment-<?php echo e($person->id); ?>" class="flex-1 min-w-36 text-sm font-semibold"><?php echo e($person->name); ?></label>
                <select id="employment-<?php echo e($person->id); ?>" name="employment_status" class="border border-coffee-300 rounded-xl p-2 text-sm">
                    <?php $__currentLoopData = \App\Models\AssessmentReminder::EMPLOYMENT_LABELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if(($person->employment_status ?? 'DangLam') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <button class="bg-coffee-100 rounded-xl px-4 py-2 text-sm font-bold text-coffee-800">Lưu trạng thái</button>
            </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</details>
<details class="bg-white rounded-2xl border border-coffee-200 p-5"><summary class="font-bold text-coffee-900 cursor-pointer">Thiết lập ca làm việc & phân ca</summary><p class="text-xs text-slate-500 mt-2">Thông tin được lưu theo cửa hàng. Nhân viên chưa được phân ca vẫn hiển thị trong toàn cửa hàng.</p>
<form action="<?php echo e(route('manager.shifts.create')); ?>" method="POST" class="mt-5 flex flex-wrap gap-3 items-end"><?php echo csrf_field(); ?><label class="text-xs font-bold">Tên ca<input name="ten_ca" required maxlength="100" placeholder="Ví dụ: Ca sáng" class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-bold">Bắt đầu<input type="time" name="bat_dau" required class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-bold">Kết thúc<input type="time" name="ket_thuc" required class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><button class="bg-amber_brand text-white rounded-xl px-5 py-2.5 text-sm font-bold">Tạo ca</button></form>
<div class="mt-5 divide-y divide-coffee-100"><?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><form action="<?php echo e(route('manager.shifts.assign', $person->id)); ?>" method="POST" class="flex flex-wrap items-center gap-3 py-3"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?><span class="flex-1 text-sm font-semibold min-w-36"><?php echo e($person->name); ?></span><select name="ca_lam_viec_id" class="border border-coffee-300 rounded-xl p-2 text-sm"><option value="">Chưa phân ca</option><?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($shift->id); ?>" <?php if($person->ca_lam_viec_id === $shift->id): echo 'selected'; endif; ?>><?php echo e($shift->ten_ca); ?> (<?php echo e(substr($shift->bat_dau, 0, 5)); ?>–<?php echo e(substr($shift->ket_thuc, 0, 5)); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><button class="px-4 py-2 bg-coffee-100 text-coffee-800 rounded-xl text-sm font-bold">Lưu ca</button></form><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div></details>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/staff.blade.php ENDPATH**/ ?>