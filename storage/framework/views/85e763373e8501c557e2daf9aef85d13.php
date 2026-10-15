<?php $__env->startSection('content'); ?>
<?php
    $trainingStates = [
        'ChoGiao' => ['Chờ giao', 'bg-honey-50 text-honey-800 border-honey-200'],
        'DangThucHien' => ['Đang thực hiện', 'bg-coffee-100 text-coffee-700 border-coffee-300'],
        'ChoDanhGiaLai' => ['Chờ đánh giá lại', 'bg-caramel-50 text-caramel-800 border-caramel-200'],
        'HoanTat' => ['Hoàn tất', 'bg-olive-50 text-olive-700 border-olive-200'],
        'DungDaoTao' => ['Dừng đào tạo lại', 'bg-terra-50 text-terra-700 border-terra-200'],
    ];
    $inputClass = 'block w-full mt-1 rounded-xl border border-coffee-200 bg-white px-3 py-2 text-sm';
?>
<div class="max-w-6xl mx-auto space-y-5">
    <?php echo $__env->make('manager.banner', ['heading' => 'Giao đào tạo lại', 'description' => 'Giao vòng, theo dõi tiến độ và tạo lịch đánh giá lại.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php if($training->isNotEmpty()): ?>
        <div class="flex flex-wrap gap-2 text-xs">
            <?php $__currentLoopData = $trainingStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state => [$label, $class]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $count = $training->where('tien_trinh', $state)->count();
                ?>
                <?php if($count): ?><span class="rounded-full border px-3 py-1 font-semibold <?php echo e($class); ?>"><?php echo e($label); ?> · <?php echo e($count); ?></span><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php endif; ?>

    <?php $__empty_1 = true; $__currentLoopData = $training; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
            $employee = $staff->firstWhere('id', $plan->user_id);
            [$stateLabel, $stateClass] = $plan->trang_thai === 'ChoDuyet' ? ['Chờ duyệt đề xuất', 'bg-honey-50 text-honey-800 border-honey-200']
                : ($plan->trang_thai === 'TuChoi' ? ['Đã từ chối', 'bg-stone-100 text-stone-600 border-stone-200'] : ($trainingStates[$plan->tien_trinh] ?? [$plan->trang_thai, 'bg-stone-100 text-stone-600 border-stone-200']));
            $currentRound = $plan->tien_trinh === 'DangThucHien' ? $plan->rounds->last() : null;
            $canAssign = $plan->trang_thai === 'DaDuyet' && ($plan->tien_trinh === 'DangThucHien' || ($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached));
            $stopped = $plan->tien_trinh === 'DungDaoTao' || ($plan->tien_trinh === 'ChoGiao' && $plan->round_limit_reached);
        ?>
        <article class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
            <header class="flex flex-wrap items-start justify-between gap-3 p-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="<?php echo e(route('manager.profile', $plan->user_id)); ?>" class="text-lg font-bold text-coffee-900 hover:underline"><?php echo e($employee->name); ?></a>
                        <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold <?php echo e($stateClass); ?>"><?php echo e($stateLabel); ?></span>
                        <?php if($plan->rounds->isNotEmpty()): ?><span class="text-xs text-stone-500">Đã qua <?php echo e($plan->rounds->count()); ?>/3 vòng</span><?php endif; ?>
                    </div>
                    <p class="mt-1 text-xs text-stone-400">Yêu cầu #<?php echo e($plan->id); ?> · <?php echo e($employee->shift_name); ?> · <?php echo e($plan->nguon_de_xuat ?? ($plan->du_doan_id ? 'Từ cảnh báo học tập' : 'Trưởng ca đề xuất')); ?></p>
                    <?php if($plan->ly_do): ?><p class="mt-2 text-sm text-stone-600 whitespace-pre-line"><?php echo e($plan->ly_do); ?></p><?php endif; ?>
                </div>
            </header>

            <div class="space-y-3 border-t border-coffee-100 px-5 py-4">
                <?php if($stopped): ?>
                    <p role="alert" class="rounded-xl bg-terra-50 px-4 py-3 text-sm font-bold text-terra-800">Đã đạt giới hạn 3 vòng đào tạo lại và vẫn chưa đạt. Không giao thêm vòng; quản lý xem xét hướng xử lý.</p>
                <?php elseif($plan->trang_thai === 'HoanThanh'): ?>
                    <p class="rounded-xl bg-olive-50 px-4 py-3 text-sm font-bold text-olive-700">Hoàn tất · Kết quả sau đào tạo: <?php echo e($plan->ket_qua_sau_dao_tao_lai === 'Dat' ? 'Đạt' : $plan->ket_qua_sau_dao_tao_lai); ?></p>
                <?php elseif($plan->tien_trinh === 'ChoDanhGiaLai'): ?>
                    <p class="rounded-xl bg-caramel-50 px-4 py-3 text-sm text-caramel-900">Chờ đánh giá #<?php echo e($plan->danh_gia_lai_id); ?> ngày <?php echo e($plan->ngay_danh_gia_lai ? \Carbon\Carbon::parse($plan->ngay_danh_gia_lai)->format('d/m/Y') : '—'); ?>. Duyệt kết quả tại <a href="<?php echo e(route('manager.assessments')); ?>" class="font-bold underline">Duyệt đánh giá</a>.</p>
                <?php elseif($currentRound): ?>
                    <div class="rounded-xl px-4 py-3 text-sm <?php echo e($currentRound->eligibility['ready'] ? 'bg-olive-50 text-olive-800' : 'bg-honey-50 text-honey-900'); ?>">
                        <p class="font-bold"><?php echo e($currentRound->eligibility['ready'] ? 'Đủ điều kiện tạo lịch đánh giá lại' : 'Chưa đủ điều kiện đánh giá lại'); ?></p>
                        <?php if($currentRound->eligibility['missing']): ?><ul class="mt-1 list-disc pl-5 text-xs"><?php $__currentLoopData = $currentRound->eligibility['missing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($missing); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul><?php endif; ?>
                    </div>
                <?php elseif($plan->tien_trinh === 'ChoGiao' && $plan->rounds->isNotEmpty()): ?>
                    <p class="rounded-xl bg-honey-50 px-4 py-3 text-sm text-honey-900">Vòng trước chưa đạt. Điều chỉnh nội dung và giao <strong>đào tạo lại lần <?php echo e($plan->next_round_number); ?></strong>.</p>
                <?php endif; ?>

                <?php if($plan->ghi_chu_thuc_hien && $plan->trang_thai !== 'TuChoi' && ! $stopped && $plan->trang_thai !== 'HoanThanh'): ?>
                    <div class="text-sm">
                        <p class="text-xs font-bold text-coffee-700">Kế hoạch đang thực hiện</p>
                        <p class="mt-1 whitespace-pre-line text-coffee-900"><?php echo e($plan->ghi_chu_thuc_hien); ?></p>
                        <p class="mt-1 text-xs text-stone-500">Phụ trách: <?php echo e($proposalOwners->firstWhere('id', $plan->nguoi_phu_trach_id)?->name ?? 'Chưa xác định'); ?> · Hạn: <?php echo e($plan->han_hoan_thanh ? \Carbon\Carbon::parse($plan->han_hoan_thanh)->format('d/m/Y') : 'Chưa có'); ?></p>
                    </div>
                <?php endif; ?>

                <div class="flex flex-wrap gap-x-6 gap-y-2">
                    <?php if($plan->ke_hoach): ?><?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($plan->ke_hoach, true), 'muted' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php endif; ?>
                </div>
                <?php echo $__env->make('components.retraining-round-history', ['muted' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <?php if($plan->trang_thai === 'ChoDuyet'): ?>
                <footer class="flex flex-wrap gap-3 border-t border-coffee-100 bg-coffee-50/40 px-5 py-4">
                    <form method="POST" action="<?php echo e(route('manager.training.review', $plan->id)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="approve"><button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Duyệt đề xuất</button></form>
                    <form method="POST" action="<?php echo e(route('manager.training.review', $plan->id)); ?>" class="flex flex-1 gap-2"><?php echo csrf_field(); ?><input type="hidden" name="action" value="reject"><input required name="reason" aria-label="Lý do từ chối" placeholder="Lý do từ chối..." class="min-w-0 flex-1 rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm"><button class="rounded-xl border border-coffee-300 bg-white px-4 py-2.5 text-sm font-bold text-coffee-700">Từ chối</button></form>
                </footer>
            <?php endif; ?>

            <?php if($canAssign): ?>
                <details class="border-t border-coffee-100 bg-coffee-50/40" <?php if($plan->tien_trinh === 'ChoGiao'): ?> open <?php endif; ?>>
                    <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-coffee-800"><?php echo e($plan->tien_trinh === 'ChoGiao' ? 'Giao vòng đào tạo lại lần '.$plan->next_round_number : 'Cập nhật kế hoạch vòng hiện tại'); ?></summary>
                    <form method="POST" action="<?php echo e(route('manager.training.assign', $plan->id)); ?>" class="space-y-3 px-5 pb-4">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="text-xs font-semibold text-coffee-700">Trưởng ca phụ trách
                                <select required name="nguoi_phu_trach_id" class="<?php echo e($inputClass); ?>"><option value="">Chọn trưởng ca</option><?php $__currentLoopData = $trainingOwners->where('id', '!=', $plan->user_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($owner->id); ?>" <?php if($plan->nguoi_phu_trach_id == $owner->id): echo 'selected'; endif; ?>><?php echo e($owner->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                            </label>
                            <label class="text-xs font-semibold text-coffee-700">Hạn hoàn thành<input required type="date" name="han_hoan_thanh" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->han_hoan_thanh); ?>" class="<?php echo e($inputClass); ?>"></label>
                            <label class="text-xs font-semibold text-coffee-700">Ngày dự kiến đánh giá lại<input required type="date" name="ngay_danh_gia_lai" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->ngay_danh_gia_lai); ?>" class="<?php echo e($inputClass); ?>"></label>
                        </div>
                        <label class="block text-xs font-semibold text-coffee-700">Nội dung thực hiện<textarea required name="ghi_chu_thuc_hien" rows="3" maxlength="5000" placeholder="Kỹ năng cần cải thiện, bài cần học lại, cách thực hành và tiêu chí hoàn thành..." class="<?php echo e($inputClass); ?>"><?php echo e($plan->ghi_chu_thuc_hien); ?></textarea></label>
                        <button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Lưu & giao đào tạo lại</button>
                    </form>
                </details>
            <?php endif; ?>

            <?php if($plan->tien_trinh === 'DangThucHien' && $plan->trang_thai === 'DaDuyet'): ?>
                <details class="border-t border-coffee-100">
                    <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-coffee-800">Tạo lịch đánh giá lại</summary>
                    <form method="POST" action="<?php echo e(route('manager.training.reassess', $plan->id)); ?>" class="space-y-3 px-5 pb-4">
                        <?php echo csrf_field(); ?>
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="text-xs font-semibold text-coffee-700">Trưởng ca đánh giá
                                <select required name="assessor_id" class="<?php echo e($inputClass); ?>"><option value="">Chọn trưởng ca</option><?php $__currentLoopData = $proposalOwners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $owner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(in_array(mb_strtolower(trim($owner->ChucVu ?? '')), ['trưởng ca', 'truong_ca', 'leader'])): ?><option value="<?php echo e($owner->id); ?>"><?php echo e($owner->name); ?></option><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                            </label>
                            <label class="text-xs font-semibold text-coffee-700">Ngày đánh giá<input required type="date" name="assessment_date" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->ngay_danh_gia_lai); ?>" class="<?php echo e($inputClass); ?>"></label>
                            <label class="text-xs font-semibold text-coffee-700">Giờ đánh giá<input required type="time" name="assessment_time" value="09:00" class="<?php echo e($inputClass); ?>"></label>
                        </div>
                        <label class="block text-xs font-semibold text-coffee-700">Nội dung cần kiểm tra lại<textarea required name="criteria" rows="2" maxlength="2000" class="<?php echo e($inputClass); ?>" placeholder="Nội dung trưởng ca cần kiểm tra sau đào tạo..."></textarea></label>
                        <button <?php if(! ($currentRound?->eligibility['ready'] ?? false)): echo 'disabled'; endif; ?> class="rounded-xl bg-coffee-800 px-5 py-2.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Tạo lịch đánh giá lại</button>
                    </form>
                </details>
            <?php endif; ?>
        </article>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="rounded-2xl border border-coffee-200 bg-white p-10 text-center text-stone-500">Chưa có yêu cầu đào tạo lại. Yêu cầu xuất hiện khi đánh giá chưa đạt được duyệt.</div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/training.blade.php ENDPATH**/ ?>