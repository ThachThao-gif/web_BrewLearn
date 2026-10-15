<?php if($plan->rounds->isNotEmpty()): ?>
<details class="text-sm">
    <summary class="cursor-pointer font-semibold text-coffee-700">Lịch sử <?php echo e($plan->rounds->count()); ?> vòng đào tạo lại</summary>
    <ol class="mt-3 space-y-3 border-l-2 border-coffee-100 pl-4">
    <?php $__currentLoopData = $plan->rounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php ([$roundLabel, $roundClass] = ['Dat' => ['Đạt', ($muted ?? false) ? 'text-olive-700' : 'text-emerald-700'], 'ChuaDat' => ['Chưa đạt', ($muted ?? false) ? 'text-terra-700' : 'text-rose-700']][$round->result] ?? [$round->status === 'ChoDanhGiaLai' ? 'Chờ đánh giá lại' : 'Đang thực hiện', 'text-slate-600']); ?>
        <li>
            <p class="flex flex-wrap items-baseline gap-2"><strong class="text-coffee-900">Đào tạo lại lần <?php echo e($round->round_number); ?></strong><span class="text-xs font-bold <?php echo e($roundClass); ?>"><?php echo e($roundLabel); ?><?php if($round->score !== null): ?> · <?php echo e($round->score); ?>/100 <?php endif; ?></span></p>
            <p class="mt-1 whitespace-pre-line text-slate-700"><?php echo e($round->instructions); ?></p>
            <p class="mt-1 text-xs text-slate-400">Phụ trách <?php echo e($proposalOwners->firstWhere('id', $round->owner_id)?->name ?? 'chưa xác định'); ?> · Hạn <?php echo e($round->deadline ? \Carbon\Carbon::parse($round->deadline)->format('d/m/Y') : '—'); ?><?php if($round->assessment_id): ?> · Đánh giá #<?php echo e($round->assessment_id); ?><?php endif; ?></p>
            <?php if($round->confirmed_at): ?><p class="text-xs <?php echo e(($muted ?? false) ? 'text-olive-700' : 'text-emerald-700'); ?>">Đã xác nhận hoàn thành<?php echo e($round->confirmation_note ? ': '.$round->confirmation_note : ''); ?></p><?php endif; ?>
            <?php if($round->proposal): ?><?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($round->proposal, true), 'muted' => $muted ?? false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?>
        </li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</details>
<?php endif; ?>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/components/retraining-round-history.blade.php ENDPATH**/ ?>