<?php ($dueTones = ['Quá hạn' => 'bg-rose-50 text-rose-700', 'Đến hạn' => 'bg-rose-50 text-rose-700', 'Sắp đến hạn' => 'bg-amber-50 text-amber-800']); ?>
<div class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-coffee-50 text-xs text-coffee-700">
                <tr><th class="px-4 py-3">Nhân viên</th><th class="px-4 py-3">Cảnh báo</th><th class="px-4 py-3">Tiến độ</th><th class="px-4 py-3">Đánh giá gần nhất</th><th class="px-4 py-3">Hạn đánh giá</th></tr>
            </thead>
            <tbody class="divide-y divide-coffee-100">
                <?php $__empty_1 = true; $__currentLoopData = $staff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $person): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php ($employment = $person->employment_status ?? 'DangLam'); ?>
                    <tr class="align-top hover:bg-coffee-50/60">
                        <td class="px-4 py-3">
                            <a href="<?php echo e(route('manager.profile', $person->id)); ?>" class="font-bold text-coffee-900 hover:underline"><?php echo e($person->name); ?></a>
                            <p class="mt-0.5 text-xs text-slate-500"><?php echo e($person->ChucVu); ?> · <?php echo e($person->shift_name); ?></p>
                            <?php if($employment !== 'DangLam'): ?><span class="mt-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600"><?php echo e(\App\Models\AssessmentReminder::EMPLOYMENT_LABELS[$employment] ?? 'Chưa xác định'); ?></span><?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <details class="group">
                                <summary class="inline-flex cursor-pointer list-none items-center gap-1"><?php echo $__env->make('manager.risk', ['risk' => $person->risk], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition group-open:rotate-180"></i></summary>
                                <div class="mt-2 max-w-64"><?php echo $__env->make('manager.risk-details', ['showBadge' => false], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                            </details>
                        </td>
                        <td class="min-w-32 px-4 py-3">
                            <p class="text-sm font-bold text-coffee-900"><?php echo e($person->training_progress); ?>%</p>
                            <div class="mt-1.5 h-1.5 rounded-full bg-coffee-100"><div class="h-1.5 rounded-full bg-amber_brand" style="width: <?php echo e($person->training_progress); ?>%"></div></div>
                        </td>
                        <td class="px-4 py-3">
                            <?php if($person->last_evaluation): ?>
                                <p class="font-bold text-coffee-900"><?php echo e($person->last_evaluation->Diem); ?><span class="font-normal text-slate-400">/100</span>
                                    <span class="ml-1 text-xs <?php echo e($person->last_evaluation->IsPassed ? 'text-emerald-700' : 'text-rose-700'); ?>"><?php echo e($person->last_evaluation->IsPassed === null ? 'Chưa xác định' : ($person->last_evaluation->IsPassed ? 'Đạt' : 'Chưa đạt')); ?></span></p>
                                <p class="text-xs text-slate-400"><?php echo e(\Carbon\Carbon::parse($person->last_evaluation->NgayDanhGia ?? $person->last_evaluation->updated_at)->format('d/m/Y')); ?></p>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">Chưa đánh giá</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <p class="text-slate-600"><?php echo e($person->due_at?->format('d/m/Y') ?? $person->due_status); ?></p>
                            <?php if(isset($dueTones[$person->due_status])): ?><span class="mt-1 inline-block rounded-full px-2 py-0.5 font-semibold <?php echo e($dueTones[$person->due_status]); ?>"><?php echo e($person->due_status); ?></span><?php endif; ?>
                            <?php if($person->needs_reminder && ! ($person->eligibility['ready'] ?? false)): ?>
                                <details class="mt-1 text-amber-800"><summary class="cursor-pointer">Chưa đủ điều kiện</summary><ul class="mt-1 list-disc pl-4"><?php $__currentLoopData = $person->eligibility['missing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($missing); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></details>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="p-10 text-center text-slate-500">Không có nhân viên phù hợp bộ lọc.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/staff-table.blade.php ENDPATH**/ ?>