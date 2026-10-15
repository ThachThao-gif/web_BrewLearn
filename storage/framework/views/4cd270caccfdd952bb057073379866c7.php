<?php $__env->startSection('content'); ?>
<div class="max-w-6xl mx-auto space-y-5">
    <?php echo $__env->make('manager.banner', ['heading' => 'Hồ sơ nhân viên', 'description' => 'Tiến độ, đánh giá và hạn đánh giá của từng nhân viên.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.staff-table', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <details class="rounded-2xl border border-coffee-200 bg-white">
        <summary class="cursor-pointer px-5 py-4 font-bold text-coffee-900">Phân ca & trạng thái làm việc</summary>
        <div class="border-t border-coffee-100 px-5 py-4 space-y-4">
            <form action="<?php echo e(route('manager.shifts.create')); ?>" method="POST" class="flex flex-wrap items-center gap-2">
                <?php echo csrf_field(); ?>
                <span class="text-xs font-bold text-coffee-700">Tạo ca mới</span>
                <input name="ten_ca" required maxlength="100" aria-label="Tên ca" placeholder="Tên ca, ví dụ Ca sáng" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <input type="time" name="bat_dau" required aria-label="Giờ bắt đầu" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <input type="time" name="ket_thuc" required aria-label="Giờ kết thúc" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <button class="rounded-xl bg-amber_brand px-4 py-2 text-sm font-bold text-white">Tạo ca</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-slate-500"><tr><th class="py-2">Nhân viên</th><th class="py-2">Ca làm việc</th><th class="py-2">Trạng thái làm việc</th></tr></thead>
                    <tbody class="divide-y divide-coffee-100">
                        <?php $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="py-2 pr-3 font-semibold"><?php echo e($person->name); ?></td>
                                <td class="py-2 pr-3">
                                    <form action="<?php echo e(route('manager.shifts.assign', $person->id)); ?>" method="POST" class="flex gap-2">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <select name="ca_lam_viec_id" aria-label="Ca của <?php echo e($person->name); ?>" class="rounded-xl border border-coffee-200 px-2 py-1.5 text-sm">
                                            <option value="">Chưa phân ca</option>
                                            <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($shift->id); ?>" <?php if($person->ca_lam_viec_id === $shift->id): echo 'selected'; endif; ?>><?php echo e($shift->ten_ca); ?> (<?php echo e(substr($shift->bat_dau, 0, 5)); ?>–<?php echo e(substr($shift->ket_thuc, 0, 5)); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button class="rounded-xl bg-coffee-100 px-3 py-1.5 text-xs font-bold text-coffee-800">Lưu</button>
                                    </form>
                                </td>
                                <td class="py-2">
                                    <form method="POST" action="<?php echo e(route('manager.employment.update', $person->id)); ?>" class="flex gap-2">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                        <select id="employment-<?php echo e($person->id); ?>" name="employment_status" aria-label="Trạng thái của <?php echo e($person->name); ?>" class="rounded-xl border border-coffee-200 px-2 py-1.5 text-sm">
                                            <?php $__currentLoopData = \App\Models\AssessmentReminder::EMPLOYMENT_LABELS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($value); ?>" <?php if(($person->employment_status ?? 'DangLam') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                        <button class="rounded-xl bg-coffee-100 px-3 py-1.5 text-xs font-bold text-coffee-800">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <p class="text-[11px] text-slate-400">Tạm ngưng hoặc nghỉ việc sẽ dừng nhắc hạn đánh giá, lịch sử vẫn được giữ.</p>
        </div>
    </details>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/staff.blade.php ENDPATH**/ ?>