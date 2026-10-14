<?php if($plan->rounds->isNotEmpty()): ?>
<details class="border border-coffee-200 rounded-xl p-4" open>
    <summary class="font-bold text-sm text-coffee-900 cursor-pointer">Lịch sử <?php echo e($plan->rounds->count()); ?> vòng đào tạo lại</summary>
    <div class="mt-3 space-y-3">
    <?php $__currentLoopData = $plan->rounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="rounded-xl p-4 <?php echo e($round->result === 'Dat' ? 'bg-emerald-50' : ($round->result === 'ChuaDat' ? 'bg-rose-50' : 'bg-coffee-50')); ?>">
            <div class="flex flex-wrap justify-between gap-2 text-sm"><strong>Đào tạo lại lần <?php echo e($round->round_number); ?></strong><span><?php echo e(['Dat' => 'Đạt • đã duyệt', 'ChuaDat' => 'Chưa đạt • đã duyệt'][$round->result] ?? ($round->status === 'ChoDanhGiaLai' ? 'Chờ đánh giá lại / phê duyệt' : 'Đang thực hiện')); ?></span></div>
            <p class="text-sm whitespace-pre-line mt-2"><?php echo e($round->instructions); ?></p>
            <?php if($round->confirmed_at): ?><p class="text-xs text-emerald-700">Đã xác nhận hoàn thành: <?php echo e($round->confirmed_at); ?> • <?php echo e($round->confirmation_note); ?></p><?php endif; ?>
            <p class="text-xs text-slate-500 mt-2">Phụ trách: <?php echo e($proposalOwners->firstWhere('id', $round->owner_id)?->name ?? 'Chưa xác định'); ?> • Hạn: <?php echo e($round->deadline); ?> • Ngày đánh giá lại: <?php echo e($round->assessment_date); ?></p>
            <?php if($round->assessment_id): ?><p class="text-xs mt-2">Đánh giá #<?php echo e($round->assessment_id); ?><?php if($round->score !== null): ?> • <?php echo e($round->score); ?>/100 • Duyệt lúc <?php echo e($round->approved_at); ?><?php endif; ?></p><?php endif; ?>
            <?php if($round->proposal): ?><?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($round->proposal, true)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</details>
<?php endif; ?>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/components/retraining-round-history.blade.php ENDPATH**/ ?>