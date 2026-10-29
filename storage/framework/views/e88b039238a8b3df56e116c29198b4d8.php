<?php $__env->startSection('content'); ?>
<?php
    $maxRounds = \App\Http\Controllers\ManagerController::MAX_RETRAINING_ROUNDS;
    $today = now()->startOfDay();
    foreach ($rounds as $round) {
        $round->deadline_at = $round->deadline ? \Carbon\Carbon::parse($round->deadline)->startOfDay() : null;
        $round->days_left = $round->deadline_at ? (int) $today->diffInDays($round->deadline_at, false) : null;
        $round->learning_done = max($round->eligibility['required'] - count($round->eligibility['missing']), 0);
        $round->state = $round->confirmed_at ? 'confirmed' : ($round->eligibility['ready'] ? 'ready' : 'waiting');
    }
    $stateLabels = [
        'confirmed' => 'Đã xác nhận, chờ quản lý xếp lịch đánh giá lại',
        'ready' => 'Nhân viên đã hoàn thành học lại, chờ bạn xác nhận',
        'waiting' => 'Chờ nhân viên hoàn thành học lại và kiểm tra',
    ];
    $summary = [
        ['Đang phụ trách', $rounds->count(), 'text-coffee-900'],
        ['Sẵn sàng xác nhận', $rounds->where('state', 'ready')->count(), 'text-amber-600'],
        ['Chờ nhân viên hoàn thành', $rounds->where('state', 'waiting')->count(), 'text-slate-500'],
        ['Quá hạn chưa xác nhận', $rounds->filter(fn ($round) => $round->state !== 'confirmed' && $round->days_left !== null && $round->days_left < 0)->count(), 'text-rose-600'],
    ];
?>
<div class="max-w-5xl mx-auto space-y-5">
    <div class="page-banner"><h1>Đào tạo lại tôi phụ trách</h1><p class="text-sm mt-2">Theo dõi nhân viên học lại, kiểm tra điều kiện và xác nhận phần thực hành trước khi quản lý tổ chức đánh giá lại.</p></div>
    <?php if(session('success')): ?><p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800"><?php echo e(session('success')); ?></p><?php endif; ?>
    <?php if($errors->any()): ?><div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><p><?php echo e($error); ?></p><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></div><?php endif; ?>

    <?php if($rounds->isNotEmpty()): ?>
        <section aria-label="Tóm tắt" class="grid grid-cols-2 overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm md:grid-cols-4 md:divide-x divide-coffee-100">
            <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $count, $tone]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="px-5 py-4"><span class="block text-2xl font-extrabold <?php echo e($tone); ?>"><?php echo e($count); ?></span><span class="text-xs text-slate-500"><?php echo e($label); ?></span></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
    <?php endif; ?>

    <?php $__empty_1 = true; $__currentLoopData = $rounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $stateLabel = $stateLabels[$round->state];
            $required = $round->eligibility['required'];
        ?>
        <article class="overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm">
            <?php
                $th = 'w-56 border-b border-r border-slate-200 bg-slate-50 px-5 py-3 text-left align-top font-semibold text-slate-600';
                $td = 'border-b border-slate-200 px-5 py-3 align-top text-slate-800';
                $overdue = $round->state !== 'confirmed' && $round->days_left !== null && $round->days_left < 0;
            ?>
            <table class="w-full text-sm">
                <caption class="sr-only">Đào tạo lại của <?php echo e($round->employee_name); ?></caption>
                <tbody>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Nhân viên</th><td class="<?php echo e($td); ?>"><span class="font-semibold"><?php echo e($round->employee_name); ?></span> · <?php echo e($round->employee_position ?? 'Nhân viên'); ?></td></tr>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Vòng đào tạo lại</th><td class="<?php echo e($td); ?>"><?php echo e($round->round_number); ?>/<?php echo e($maxRounds); ?></td></tr>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Hạn hoàn thành</th><td class="<?php echo e($td); ?> <?php echo e($overdue ? 'text-rose-700' : ''); ?>">
                        <?php echo e($round->deadline_at?->format('d/m/Y') ?? '—'); ?>

                        <?php if($round->deadline_at && $round->state !== 'confirmed'): ?>
                            (<?php echo e($round->days_left < 0 ? 'quá hạn '.abs($round->days_left).' ngày' : ($round->days_left === 0 ? 'hôm nay' : 'còn '.$round->days_left.' ngày')); ?>)
                        <?php endif; ?>
                    </td></tr>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Trạng thái</th><td class="<?php echo e($td); ?> font-semibold"><?php echo e($stateLabel); ?></td></tr>
                    <?php if($round->ly_do): ?>
                        <tr><th scope="row" class="<?php echo e($th); ?>">Lý do đào tạo lại</th><td class="<?php echo e($td); ?>"><?php echo e($round->ly_do); ?></td></tr>
                    <?php endif; ?>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Nội dung được giao</th><td class="<?php echo e($td); ?> whitespace-pre-line"><?php echo e($round->instructions ?: 'Chưa có hướng dẫn chi tiết.'); ?></td></tr>
                    <tr><th scope="row" class="<?php echo e($th); ?>">Học lại và kiểm tra</th><td class="<?php echo e($td); ?>">
                        <?php if($required > 0): ?>
                            Đã hoàn thành <?php echo e($round->learning_done); ?>/<?php echo e($required); ?> yêu cầu
                            <?php if($round->eligibility['missing']): ?>
                                <ul class="mt-1 list-disc pl-5 text-rose-700">
                                    <?php $__currentLoopData = $round->eligibility['missing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($missing); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            <?php endif; ?>
                        <?php else: ?>
                            Vòng này không yêu cầu bài học hay bài kiểm tra.
                        <?php endif; ?>
                    </td></tr>
                    <tr><th scope="row" class="<?php echo e($th); ?> border-b-0">Ghi nhận thực hành</th><td class="<?php echo e($td); ?> border-b-0">
                        <?php if($round->confirmed_at): ?>
                            Đã xác nhận lúc <?php echo e(\Carbon\Carbon::parse($round->confirmed_at)->format('H:i d/m/Y')); ?>

                            <span class="mt-1 block whitespace-pre-line text-slate-600"><?php echo e($round->confirmation_note); ?></span>
                        <?php elseif($round->eligibility['ready']): ?>
                            Chưa ghi nhận. Nhập ghi nhận và xác nhận ở bên dưới.
                        <?php else: ?>
                            Thực hiện sau khi nhân viên hoàn thành học lại và kiểm tra.
                        <?php endif; ?>
                    </td></tr>
                </tbody>
            </table>
            <footer class="border-t border-slate-200 bg-slate-50/60 px-5 py-4">
                <?php if($round->eligibility['ready']): ?>
                    <details <?php if(! $round->confirmed_at): ?> open <?php endif; ?>>
                        <summary class="cursor-pointer text-sm font-bold text-coffee-800 <?php echo e($round->confirmed_at ? '' : 'sr-only'); ?>">Cập nhật ghi nhận</summary>
                        <form method="POST" action="<?php echo e(route('training.confirm', $round->id)); ?>" class="mt-2 space-y-3">
                            <?php echo csrf_field(); ?>
                            <label class="block text-sm font-semibold text-coffee-900">Ghi nhận thực hành / hành vi quan sát
                                <textarea required name="confirmation_note" maxlength="2000" rows="3" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white p-3 font-normal" placeholder="Nội dung đã thực hiện, số ca theo dõi và nhận xét..."><?php echo e(old('confirmation_note', $round->confirmation_note)); ?></textarea>
                            </label>
                            <button class="rounded-xl bg-coffee-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-coffee-900"><?php echo e($round->confirmed_at ? 'Lưu ghi nhận' : 'Xác nhận đã hoàn thành nội dung được giao'); ?></button>
                        </form>
                    </details>
                <?php else: ?>
                    <p class="text-sm text-slate-600">Bạn có thể ghi nhận và xác nhận sau khi nhân viên hoàn thành các yêu cầu học lại và kiểm tra.</p>
                <?php endif; ?>
            </footer>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="rounded-2xl border border-dashed border-coffee-300 bg-white p-8 text-center">
            <i class="fa-solid fa-mug-hot text-2xl text-coffee-300" aria-hidden="true"></i>
            <p class="mt-2 text-sm font-semibold text-slate-600">Không có kế hoạch đào tạo lại đang thực hiện cần bạn phụ trách.</p>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/training/assignments.blade.php ENDPATH**/ ?>