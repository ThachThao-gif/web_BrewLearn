<?php
    $dueTones = ['Quá hạn' => 'bg-rose-50 text-rose-700', 'Đến hạn' => 'bg-rose-50 text-rose-700', 'Sắp đến hạn' => 'bg-amber-50 text-amber-800'];
?>
<div class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-coffee-50 text-xs text-coffee-700">
                <tr><th class="px-4 py-3">Nhân viên</th><th class="px-4 py-3">Cảnh báo</th><th class="px-4 py-3">Tiến độ</th><th class="px-4 py-3">Kết quả đã duyệt</th><th class="px-4 py-3">Hạn đánh giá</th><th class="px-4 py-3"><span class="sr-only">Hồ sơ</span></th></tr>
            </thead>
            <tbody class="divide-y divide-coffee-100">
                <?php $__empty_1 = true; $__currentLoopData = $staffPage ?? $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $employment = $person->employment_status ?? 'DangLam';
                    ?>
                    <tr class="hover:bg-coffee-50/60">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('manager.profile', $person->id)); ?>" class="font-bold text-coffee-900 hover:underline"><?php echo e($person->name); ?></a>
                            <p class="mt-0.5 text-xs text-slate-500"><?php echo e($person->ChucVu); ?> · <?php echo e($person->shift_name); ?>

                                <?php if($employment !== 'DangLam'): ?><span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><?php echo e(\App\Models\AssessmentReminder::EMPLOYMENT_LABELS[$employment] ?? 'Chưa xác định'); ?></span><?php endif; ?>
                            </p>
                        </td>
                        <td class="px-4 py-3"><?php echo $__env->make('manager.risk', ['risk' => $person->risk], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        <td class="min-w-32 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-coffee-100"><div class="h-1.5 rounded-full bg-amber_brand" style="width: <?php echo e(min($person->training_progress, 100)); ?>%"></div></div>
                                <span class="text-xs font-bold text-coffee-900"><?php echo e($person->training_progress); ?>%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($person->last_evaluation): ?>
                                <span class="font-bold text-coffee-900"><?php echo e($person->last_evaluation->Diem); ?></span>
                                <span class="ml-1 text-xs font-semibold <?php echo e($person->last_evaluation->IsPassed ? 'text-emerald-700' : 'text-rose-700'); ?>"><?php echo e($person->last_evaluation->IsPassed === null ? 'Chưa xác định' : ($person->last_evaluation->IsPassed ? 'Đạt' : 'Chưa đạt')); ?></span>
                                <span class="ml-1 text-xs text-slate-400">· <?php echo e(\Carbon\Carbon::parse($person->last_evaluation->NgayDanhGia ?? $person->last_evaluation->updated_at)->format('d/m')); ?></span>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">Chưa đánh giá</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="text-slate-600"><?php echo e($person->due_at?->format('d/m/Y') ?? $person->due_status); ?></span>
                            <?php if($person->scheduled_assessment && isset($dueTones[$person->due_status])): ?>
                                <span class="ml-1 inline-block rounded-full bg-blue-50 px-2 py-0.5 font-semibold text-blue-700"><?php echo e($person->scheduled_assessment->TrangThai === 'ChoDanhGia' ? 'Đã xếp lịch' : 'Đang xử lý'); ?></span>
                            <?php elseif(isset($dueTones[$person->due_status])): ?>
                                <span class="ml-1 inline-block rounded-full px-2 py-0.5 font-semibold <?php echo e($dueTones[$person->due_status]); ?>"><?php echo e($person->due_status); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="<?php echo e(route('manager.profile', $person->id)); ?>" aria-label="Xem hồ sơ <?php echo e($person->name); ?>" class="inline-flex items-center gap-1 whitespace-nowrap text-xs font-bold text-amber-700 hover:text-amber-800">Chi tiết <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="p-10 text-center text-slate-500">Không có nhân viên phù hợp bộ lọc.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if(isset($staffPage) && $staffPage->hasPages()): ?>
        <div class="border-t border-coffee-100 px-4 py-3"><?php echo e($staffPage->links()); ?></div>
    <?php endif; ?>
</div>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/staff-table.blade.php ENDPATH**/ ?>