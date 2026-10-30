<?php if($showBadge ?? true): ?><?php echo $__env->make('manager.risk', ['risk' => $person->risk], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?>
<?php if($person->risk === 'KhongApDung'): ?>
    <p class="text-xs text-slate-500 mt-2">Bộ luật chỉ áp dụng cho Barista, Thu ngân và Phục vụ.</p>
<?php elseif($person->prediction): ?>
    <?php if($person->prediction->diem_rui_ro !== null): ?>
        <p class="text-xs font-semibold text-coffee-800 mt-2">Điểm cảnh báo <?php echo e((int) round($person->prediction->diem_rui_ro * 10)); ?>/10</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Thấp 0–2 · Trung bình 3–4 · Cao 5–10</p>
    <?php endif; ?>
    <?php if(isset($person->risk_details['completed_quiz_count'], $person->risk_details['applicable_quiz_count'])): ?>
        <p class="text-xs text-slate-600 mt-1">Đã làm <?php echo e($person->risk_details['completed_quiz_count']); ?>/<?php echo e($person->risk_details['applicable_quiz_count']); ?> bài kiểm tra</p>
    <?php endif; ?>
    <ul class="text-xs text-slate-600 list-disc pl-4 mt-2 space-y-1">
        <?php $__currentLoopData = ($person->risk_details['details']['ly_do_canh_bao'] ?? $person->risk_details['missing_reasons'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reason): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($reason); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <?php if(($person->risk_details['details']['tong_diem'] ?? null) === 0): ?>
        <p class="text-xs text-slate-600 mt-1">Không kích hoạt điều kiện cộng điểm; chưa xác nhận đạt năng lực thực hành.</p>
    <?php endif; ?>
    <p class="text-[10px] text-slate-500 mt-2">Thời điểm tính: <?php echo e(\Carbon\Carbon::parse($person->prediction->ngay_du_doan, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s')); ?> (giờ Việt Nam)</p>
<?php else: ?>
    <p class="text-xs text-slate-500 mt-2">Chưa có kết quả theo luật.</p>
<?php endif; ?>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/risk-details.blade.php ENDPATH**/ ?>