<?php if(session('success')): ?><div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"><?php echo e(session('success')); ?></div><?php endif; ?>
<?php if($errors->any()): ?><div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><p class="font-bold">Vui lòng kiểm tra thông tin:</p><ul class="list-disc pl-5"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
<form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
    <?php if(isset($statusTab)): ?><input type="hidden" name="status" value="<?php echo e($statusTab); ?>"><?php endif; ?>
    <select name="shift" aria-label="Ca làm việc" class="rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm">
        <option value="">Toàn cửa hàng</option>
        <option value="none" <?php if(request('shift') === 'none'): echo 'selected'; endif; ?>>Chưa phân ca</option>
        <?php $__currentLoopData = $shifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($shift->id); ?>" <?php if((string) request('shift') === (string) $shift->id): echo 'selected'; endif; ?>><?php echo e($shift->ten_ca); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <input name="q" value="<?php echo e(request('q')); ?>" aria-label="Tìm nhân viên" placeholder="Tìm nhân viên..." class="min-w-40 flex-1 rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm">
    <button class="rounded-xl bg-amber_brand px-4 py-2.5 text-sm font-bold text-white">Lọc</button>
    <?php if(request()->filled('shift') || request()->filled('q')): ?>
        <a href="<?php echo e(isset($statusTab) ? route('manager.assessments', ['status' => $statusTab]) : url()->current()); ?>" class="px-1 text-sm text-coffee-600 hover:underline">Xóa lọc</a>
    <?php endif; ?>
</form>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/filter.blade.php ENDPATH**/ ?>