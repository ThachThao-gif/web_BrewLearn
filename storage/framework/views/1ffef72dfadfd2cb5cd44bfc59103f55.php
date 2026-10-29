<?php $__env->startSection('content'); ?>
<?php
    $statusLabels = ['ChoPheDuyet' => ['Chờ duyệt', 'border-amber-300 text-amber-800'], 'TraLai' => ['Đã trả lại', 'border-rose-300 text-rose-700'], 'DaDuyet' => ['Đã duyệt', 'border-emerald-300 text-emerald-700'], 'HoanThanh' => ['Đã hoàn thành', 'border-emerald-300 text-emerald-700']];
    $criteria = ['Diem_NghiepVu' => ['Nghiệp vụ', '20%'], 'Diem_KyNang' => ['Kỹ năng', '30%'], 'Diem_QuyTrinh' => ['Quy trình', '20%'], 'Diem_PhucVu' => ['Phục vụ', '15%'], 'Diem_TacPhong' => ['Tác phong', '15%']];
    $filters = request()->only('shift', 'q');
    $summary = [
        ['Chờ duyệt', $statusCounts['ChoPheDuyet'] ?? 0, 'text-amber-600', 'pending'],
        ['Đã trả lại', $statusCounts['TraLai'] ?? 0, 'text-rose-600', 'pending'],
        ['Đã duyệt', $tabCounts['approved'], 'text-emerald-600', 'approved'],
        ['Chờ trưởng ca chấm', $statusCounts['ChoDanhGia'] ?? 0, 'text-coffee-500', null],
    ];
    $grid = 'md:grid md:grid-cols-[minmax(0,2fr)_minmax(0,2fr)_minmax(0,1.3fr)_7rem_5rem_6rem_7rem_1.5rem] md:items-center md:gap-3';
?>
<div class="max-w-6xl mx-auto space-y-5">
    <?php echo $__env->make('manager.banner', ['heading' => 'Duyệt đánh giá', 'description' => 'Phê duyệt hoặc trả lại kết quả trưởng ca đã chấm.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl border border-coffee-200 bg-white" aria-labelledby="summary-heading">
        <h2 id="summary-heading" class="px-5 pt-4 text-sm font-bold text-coffee-900">Tóm lược đánh giá</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 divide-coffee-100 md:divide-x">
            <?php $__currentLoopData = $summary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $count, $tone, $tab]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a <?php if($tab): ?> href="<?php echo e(route('manager.assessments', array_merge($filters, ['status' => $tab]))); ?>" <?php endif; ?>
                   <?php if($tab && $statusTab === $tab): ?> aria-current="page" <?php endif; ?>
                   class="block px-5 py-4 <?php echo e($tab ? 'hover:bg-coffee-50' : 'cursor-default'); ?>">
                    <span class="block text-2xl font-extrabold <?php echo e($tone); ?>"><?php echo e($count); ?></span>
                    <span class="text-xs <?php echo e($tab && $statusTab === $tab ? 'font-bold text-coffee-900' : 'text-slate-500'); ?>"><?php echo e($label); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-coffee-200 bg-white" aria-label="Danh sách đánh giá">
        <div class="flex items-center justify-between border-b border-coffee-100 px-5 py-3">
            <p class="text-sm font-bold text-coffee-900"><?php echo e($statusTab === 'pending' ? 'Đánh giá chưa duyệt' : 'Đánh giá đã duyệt'); ?> <span class="font-normal text-slate-400">(<?php echo e($assessments->count()); ?>)</span></p>
            <a href="<?php echo e(route('manager.assessments', array_merge($filters, ['status' => $statusTab === 'pending' ? 'approved' : 'pending']))); ?>" class="text-xs font-semibold text-amber-700 hover:underline"><?php echo e($statusTab === 'pending' ? 'Xem đã duyệt →' : '← Xem chưa duyệt'); ?></a>
        </div>
        <div class="hidden <?php echo e($grid); ?> bg-coffee-50 px-5 py-2.5 text-xs font-semibold text-coffee-700">
            <span>Nhân viên</span><span>Tiêu chuẩn</span><span>Người chấm</span><span>Ngày chấm</span><span class="text-right">Điểm</span><span>Kết quả</span><span>Trạng thái</span><span class="sr-only">Xem</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $assessments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $assessment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $employee = $staff->firstWhere('id', $assessment->user_id);
                [$statusLabel, $statusClass] = $statusLabels[$assessment->TrangThai] ?? [$assessment->TrangThai, 'border-slate-300 text-slate-600'];
                $hasScores = ! ($assessment->IsPassed === null && ! $assessment->ChiTietDiem);
                $leaderName = $proposalOwners->firstWhere('id', $assessment->NguoiDanhGia_id)?->name ?? '—';
                $gradedAt = \Carbon\Carbon::parse($assessment->NgayDanhGia ?? $assessment->updated_at)->format('d/m/Y H:i');
                $resultLabel = $assessment->IsPassed === null ? 'Thiếu dữ liệu' : ($assessment->IsPassed ? 'Đạt' : 'Chưa đạt');
                $resultClass = $assessment->IsPassed === null ? 'text-slate-500' : ($assessment->IsPassed ? 'text-emerald-700' : 'text-rose-700');
                $dialogId = 'assessment-'.$assessment->id;
                $row = 'grid grid-cols-[9rem_1fr] gap-3 px-4 py-2.5';
            ?>
            <div class="<?php echo e($grid); ?> grid grid-cols-2 gap-x-3 gap-y-1 border-t border-coffee-100 px-5 py-3 text-sm hover:bg-coffee-50/70">
                <span class="min-w-0"><span class="block truncate font-bold text-coffee-900"><?php echo e($employee->name); ?></span><span class="text-xs text-slate-400"><?php echo e($employee->shift_name); ?></span></span>
                <span class="truncate text-slate-600" title="<?php echo e($assessment->TieuChuan); ?>"><?php echo e($assessment->TieuChuan); ?></span>
                <span class="truncate text-slate-600"><?php echo e($leaderName); ?></span>
                <span class="text-xs text-slate-500"><?php echo e($gradedAt); ?></span>
                <span class="font-bold text-coffee-900 md:text-right"><?php echo e($assessment->Diem ?? '—'); ?></span>
                <span class="text-xs font-bold <?php echo e($resultClass); ?>"><?php echo e($resultLabel); ?></span>
                <span><button type="button" onclick="document.getElementById('<?php echo e($dialogId); ?>').showModal()" aria-haspopup="dialog" title="Xem chi tiết và xử lý" class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold hover:shadow-sm <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></button></span>
                <button type="button" onclick="document.getElementById('<?php echo e($dialogId); ?>').showModal()" aria-label="Xem chi tiết đánh giá <?php echo e($assessment->id); ?>" class="text-slate-400 hover:text-coffee-700"><i class="fa-regular fa-eye"></i></button>
            </div>

            <dialog id="<?php echo e($dialogId); ?>" aria-labelledby="<?php echo e($dialogId); ?>-title" class="w-[min(760px,calc(100vw-2rem))] max-h-[90vh] rounded-2xl border border-slate-200 p-0 shadow-xl backdrop:bg-slate-900/40">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                    <h2 id="<?php echo e($dialogId); ?>-title" class="text-lg font-semibold text-slate-900"><?php echo e($employee->name); ?></h2>
                    <form method="dialog"><button aria-label="Đóng" class="rounded-lg px-2 py-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700"><i class="fa-solid fa-xmark"></i></button></form>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <section class="overflow-hidden rounded-xl border border-slate-200">
                        <h3 class="border-b border-slate-200 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-700">Thông tin đánh giá</h3>
                        <dl class="divide-y divide-slate-100 text-sm">
                            <?php $__currentLoopData = [
                                'Nhân viên' => $employee->name.' · '.$employee->ChucVu.' · '.$employee->shift_name,
                                'Tiêu chuẩn' => $assessment->TieuChuan,
                                'Người chấm' => $leaderName,
                                'Ngày chấm' => $gradedAt,
                            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="<?php echo e($row); ?>"><dt class="text-slate-500"><?php echo e($label); ?></dt><dd class="text-slate-900"><?php echo e($value); ?></dd></div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Trạng thái</dt><dd><span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-medium <?php echo e($statusClass); ?>"><?php echo e($statusLabel); ?></span></dd></div>
                            <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Kết quả</dt><dd><span class="font-semibold text-slate-900"><?php echo e($assessment->Diem ?? '—'); ?>/100</span> <span class="ml-1 font-semibold <?php echo e($resultClass); ?>"><?php echo e($resultLabel); ?></span> <span class="ml-1 text-xs text-slate-400">(đạt khi tổng ≥ 80 và mọi tiêu chí ≥ 70)</span></dd></div>
                            <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Điểm tiêu chí</dt><dd class="flex flex-wrap gap-2">
                                <?php $__currentLoopData = $criteria; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column => [$label, $weight]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $score = $assessment->$column;
                                    ?>
                                    <span class="rounded-md border border-slate-200 px-2.5 py-1 text-xs text-slate-600"><?php echo e($label); ?> <span class="text-slate-400"><?php echo e($weight); ?></span> <strong class="<?php echo e($hasScores && $score !== null && $score < 70 ? 'text-rose-600' : 'text-slate-900'); ?>"><?php echo e($hasScores ? ($score ?? '—') : '—'); ?></strong></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </dd></div>
                            <?php if(trim($assessment->NhanXet ?? '') !== ''): ?>
                                <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Nhận xét</dt><dd class="whitespace-pre-line text-slate-900"><?php echo e($assessment->NhanXet); ?></dd></div>
                            <?php endif; ?>
                            <?php if($assessment->ChiTietDeXuat || $assessment->DeXuatXuLy): ?>
                                <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Đề xuất cải thiện</dt><dd>
                                    <?php if($assessment->ChiTietDeXuat): ?>
                                        <?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($assessment->ChiTietDeXuat, true)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                    <?php else: ?>
                                        <span class="text-amber-800">Đề xuất cũ chưa có nội dung thực hiện (<?php echo e(implode(', ', json_decode($assessment->DeXuatXuLy, true) ?? [])); ?>). Nên trả lại để bổ sung.</span>
                                    <?php endif; ?>
                                </dd></div>
                            <?php endif; ?>
                            <?php if($assessment->ly_do_tra_lai): ?>
                                <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Lý do trả lại</dt><dd class="text-rose-700"><?php echo e($assessment->ly_do_tra_lai); ?></dd></div>
                            <?php endif; ?>
                            <div class="<?php echo e($row); ?>"><dt class="text-slate-500">Cảnh báo học tập</dt><dd>
                                <?php echo $__env->make('manager.risk', ['risk' => $employee->risk], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                                <details class="mt-1 text-xs"><summary class="cursor-pointer text-slate-500">Xem lý do</summary><div class="mt-1"><?php echo $__env->make('manager.risk-details', ['person' => $employee, 'showBadge' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><p class="mt-2 text-[11px] text-slate-500">Chỉ để tham khảo, không cộng vào điểm và không chặn việc phê duyệt.</p></div></details>
                            </dd></div>
                        </dl>
                    </section>
                    <?php if($assessment->IsPassed === null): ?>
                        <p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">Thiếu điểm 5 tiêu chí. Hãy trả lại để trưởng ca bổ sung.</p>
                    <?php endif; ?>
                </div>
                <?php if($assessment->TrangThai === 'ChoPheDuyet'): ?>
                    <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 md:flex-row md:items-center">
                        <form method="POST" action="<?php echo e(route('manager.review', $assessment->id)); ?>" class="flex flex-1 gap-2">
                            <?php echo csrf_field(); ?><input type="hidden" name="action" value="return">
                            <input required name="reason" maxlength="2000" aria-label="Lý do trả lại đánh giá" placeholder="Lý do trả lại..." class="min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-slate-200">
                            <button class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-slate-50">Trả lại</button>
                        </form>
                        <form method="POST" action="<?php echo e(route('manager.review', $assessment->id)); ?>">
                            <?php echo csrf_field(); ?><input type="hidden" name="action" value="approve">
                            <button class="w-full rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-40 md:w-auto" <?php if($assessment->IsPassed === null || (! $assessment->IsPassed && ! $assessment->ChiTietDeXuat)): echo 'disabled'; endif; ?>>Phê duyệt<?php echo e($assessment->IsPassed === 0 ? ' & đề xuất' : ''); ?></button>
                        </form>
                    </div>
                <?php endif; ?>
            </dialog>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="border-t border-coffee-100 p-10 text-center text-slate-500"><?php echo e($statusTab === 'pending' ? 'Không có đánh giá chưa duyệt trong phạm vi đã chọn.' : 'Chưa có đánh giá đã duyệt trong phạm vi đã chọn.'); ?></p>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/assessments.blade.php ENDPATH**/ ?>