<?php $__env->startSection('content'); ?>
<?php
    $trainingStates = [
        'ChoGiao' => ['Chờ giao', 'border-honey-200 text-honey-800', 'text-honey-700'],
        'DangThucHien' => ['Đang thực hiện', 'border-coffee-300 text-coffee-700', 'text-coffee-700'],
        'ChoDanhGiaLai' => ['Chờ đánh giá lại', 'border-caramel-200 text-caramel-800', 'text-caramel-700'],
        'HoanTat' => ['Hoàn tất', 'border-olive-200 text-olive-700', 'text-olive-700'],
        'DungDaoTao' => ['Dừng đào tạo lại', 'border-terra-200 text-terra-700', 'text-terra-700'],
    ];
    $stateFilter = array_key_exists(request('state'), $trainingStates) ? request('state') : null;
    $visible = $stateFilter ? $training->where('tien_trinh', $stateFilter) : $training;
    $filters = request()->only('shift', 'q');
    $inputClass = 'block w-full mt-1 rounded-xl border border-coffee-200 bg-white px-3 py-2 text-sm';
    $row = 'grid grid-cols-[9rem_1fr] gap-3 px-4 py-2.5';
    $grid = 'md:grid md:grid-cols-[3rem_minmax(0,2fr)_4.5rem_minmax(0,1.5fr)_6.5rem_7rem_8.5rem_1.5rem] md:items-center md:gap-3';
    $date = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '—';
?>
<div class="max-w-6xl mx-auto space-y-5">
    <?php echo $__env->make('manager.banner', ['heading' => 'Giao đào tạo lại', 'description' => 'Giao vòng, theo dõi tiến độ và tạo lịch đánh giá lại.'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('manager.filter', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <section class="rounded-2xl border border-coffee-200 bg-white" aria-labelledby="training-summary">
        <div class="flex items-center justify-between px-5 pt-4">
            <h2 id="training-summary" class="text-sm font-bold text-coffee-900">Tóm lược đào tạo lại</h2>
            <?php if($stateFilter): ?><a href="<?php echo e(route('manager.training', $filters)); ?>" class="text-xs font-semibold text-honey-700 hover:underline">Xem tất cả</a><?php endif; ?>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-5 md:divide-x divide-coffee-100">
            <?php $__currentLoopData = $trainingStates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state => [$label, $badge, $tone]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('manager.training', array_merge($filters, ['state' => $state]))); ?>" <?php if($stateFilter === $state): ?> aria-current="page" <?php endif; ?> class="block px-5 py-4 hover:bg-coffee-50">
                    <span class="block text-2xl font-extrabold <?php echo e($tone); ?>"><?php echo e($training->where('tien_trinh', $state)->count()); ?></span>
                    <span class="text-xs <?php echo e($stateFilter === $state ? 'font-bold text-coffee-900' : 'text-stone-500'); ?>"><?php echo e($label); ?></span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-coffee-200 bg-white" aria-label="Danh sách yêu cầu đào tạo lại">
        <p class="border-b border-coffee-100 px-5 py-3 text-sm font-bold text-coffee-900"><?php echo e($stateFilter ? $trainingStates[$stateFilter][0] : 'Tất cả yêu cầu'); ?> <span class="font-normal text-stone-400">(<?php echo e($visible->count()); ?>)</span></p>
        <div class="hidden <?php echo e($grid); ?> bg-coffee-50 px-5 py-2.5 text-xs font-semibold text-coffee-700">
            <span>STT</span><span>Nhân viên</span><span>Vòng</span><span>Phụ trách</span><span>Hạn</span><span>Đánh giá lại</span><span>Tiến trình</span><span class="sr-only">Xem</span>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $visible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $employee = $staff->firstWhere('id', $plan->user_id);
                [$stateLabel, $stateClass] = $plan->trang_thai === 'ChoDuyet' ? ['Chờ duyệt đề xuất', 'border-honey-200 text-honey-800']
                    : ($plan->trang_thai === 'TuChoi' ? ['Đã từ chối', 'border-stone-200 text-stone-600'] : ($trainingStates[$plan->tien_trinh] ?? [$plan->trang_thai, 'border-stone-200 text-stone-600']));
                $currentRound = $plan->tien_trinh === 'DangThucHien' ? $plan->rounds->last() : null;
                $canAssign = $plan->trang_thai === 'DaDuyet' && ($plan->tien_trinh === 'DangThucHien' || ($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached));
                $stopped = $plan->tien_trinh === 'DungDaoTao' || ($plan->tien_trinh === 'ChoGiao' && $plan->round_limit_reached);
                $owner = $proposalOwners->firstWhere('id', $plan->nguoi_phu_trach_id)?->name;
                $dialogId = 'training-'.$plan->id;
                $open = "document.getElementById('".$dialogId."').showModal()";
            ?>
            <div class="<?php echo e($grid); ?> grid grid-cols-2 gap-x-3 gap-y-1 border-t border-coffee-100 px-5 py-3 text-sm hover:bg-coffee-50/70">
                <span class="text-stone-500"><?php echo e($loop->iteration); ?></span>
                <span class="min-w-0"><span class="block truncate font-bold text-coffee-900"><?php echo e($employee->name); ?></span><span class="text-xs text-stone-400"><?php echo e($employee->ChucVu); ?> · <?php echo e($employee->shift_name); ?></span></span>
                <span class="text-stone-600"><?php echo e($plan->rounds->count()); ?>/3</span>
                <span class="truncate text-stone-600"><?php echo e($owner ?? '—'); ?></span>
                <span class="text-xs text-stone-500"><?php echo e($date($plan->han_hoan_thanh)); ?></span>
                <span class="text-xs text-stone-500"><?php echo e($date($plan->ngay_danh_gia_lai)); ?></span>
                <span><button type="button" onclick="<?php echo e($open); ?>" aria-haspopup="dialog" title="Xem chi tiết và xử lý" class="inline-block rounded-full border bg-white px-2.5 py-0.5 text-xs font-semibold hover:shadow-sm <?php echo e($stateClass); ?>"><?php echo e($stateLabel); ?></button></span>
                <button type="button" onclick="<?php echo e($open); ?>" aria-label="Xem chi tiết yêu cầu #<?php echo e($plan->id); ?>" class="text-stone-400 hover:text-coffee-700"><i class="fa-regular fa-eye"></i></button>
            </div>

            <dialog id="<?php echo e($dialogId); ?>" aria-labelledby="<?php echo e($dialogId); ?>-title" class="w-[min(820px,calc(100vw-2rem))] max-h-[90vh] rounded-2xl p-0 shadow-2xl backdrop:bg-coffee-900/40">
                <div class="flex items-center justify-between border-b border-coffee-100 px-6 py-4">
                    <h2 id="<?php echo e($dialogId); ?>-title" class="text-lg font-bold text-coffee-900">#<?php echo e($plan->id); ?> – Đào tạo lại <?php echo e($employee->name); ?></h2>
                    <form method="dialog"><button aria-label="Đóng" class="rounded-lg px-2 py-1 text-stone-400 hover:bg-coffee-50 hover:text-coffee-800"><i class="fa-solid fa-xmark"></i></button></form>
                </div>
                <div class="space-y-4 px-6 py-5">
                    <?php if($stopped): ?>
                        <p role="alert" class="rounded-xl bg-terra-50 px-4 py-3 text-sm font-bold text-terra-800">Đã đạt giới hạn 3 vòng đào tạo lại và vẫn chưa đạt. Không giao thêm vòng; quản lý xem xét hướng xử lý.</p>
                    <?php elseif($plan->tien_trinh === 'ChoDanhGiaLai'): ?>
                        <p class="rounded-xl bg-caramel-50 px-4 py-3 text-sm text-caramel-900">Chờ đánh giá #<?php echo e($plan->danh_gia_lai_id); ?> ngày <?php echo e($date($plan->ngay_danh_gia_lai)); ?>. Duyệt kết quả tại <a href="<?php echo e(route('manager.assessments')); ?>" class="font-bold underline">Duyệt đánh giá</a>.</p>
                    <?php elseif($currentRound): ?>
                        <div class="rounded-xl px-4 py-3 text-sm <?php echo e($currentRound->eligibility['ready'] ? 'bg-olive-50 text-olive-800' : 'bg-honey-50 text-honey-900'); ?>">
                            <p class="font-bold"><?php echo e($currentRound->eligibility['ready'] ? 'Đủ điều kiện tạo lịch đánh giá lại' : 'Chưa đủ điều kiện đánh giá lại'); ?></p>
                            <?php if($currentRound->eligibility['missing']): ?><ul class="mt-1 list-disc pl-5 text-xs"><?php $__currentLoopData = $currentRound->eligibility['missing']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $missing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($missing); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul><?php endif; ?>
                        </div>
                    <?php elseif($plan->tien_trinh === 'ChoGiao' && $plan->rounds->isNotEmpty()): ?>
                        <p class="rounded-xl bg-honey-50 px-4 py-3 text-sm text-honey-900">Vòng trước chưa đạt. Điều chỉnh nội dung và giao <strong>đào tạo lại lần <?php echo e($plan->next_round_number); ?></strong>.</p>
                    <?php endif; ?>

                    <section class="overflow-hidden rounded-xl border border-coffee-200">
                        <h3 class="bg-coffee-100 px-4 py-2.5 text-sm font-semibold text-coffee-800">Thông tin yêu cầu</h3>
                        <dl class="divide-y divide-coffee-100 text-sm">
                            <div class="<?php echo e($row); ?> bg-coffee-50/60"><dt class="font-semibold text-coffee-800">Nhân viên</dt><dd><a href="<?php echo e(route('manager.profile', $plan->user_id)); ?>" class="text-coffee-900 hover:underline"><?php echo e($employee->name); ?></a> · <?php echo e($employee->ChucVu); ?> · <?php echo e($employee->shift_name); ?></dd></div>
                            <div class="<?php echo e($row); ?>"><dt class="font-semibold text-coffee-800">Tiến trình</dt><dd><span class="inline-block rounded-full border px-2.5 py-0.5 text-xs font-semibold <?php echo e($stateClass); ?>"><?php echo e($stateLabel); ?></span> <span class="ml-1 text-xs text-stone-500">Đã qua <?php echo e($plan->rounds->count()); ?>/3 vòng</span></dd></div>
                            <div class="<?php echo e($row); ?> bg-coffee-50/60"><dt class="font-semibold text-coffee-800">Nguồn</dt><dd><?php echo e($plan->nguon_de_xuat ?? ($plan->du_doan_id ? 'Từ cảnh báo học tập' : 'Trưởng ca đề xuất')); ?></dd></div>
                            <?php if($plan->ly_do): ?><div class="<?php echo e($row); ?>"><dt class="font-semibold text-coffee-800">Lý do</dt><dd class="whitespace-pre-line"><?php echo e($plan->ly_do); ?></dd></div><?php endif; ?>
                            <?php if($plan->ghi_chu_thuc_hien && $plan->trang_thai !== 'TuChoi'): ?>
                                <div class="<?php echo e($row); ?> bg-coffee-50/60"><dt class="font-semibold text-coffee-800">Kế hoạch đã giao</dt><dd class="whitespace-pre-line"><?php echo e($plan->ghi_chu_thuc_hien); ?></dd></div>
                            <?php endif; ?>
                            <div class="<?php echo e($row); ?>"><dt class="font-semibold text-coffee-800">Phụ trách · Hạn</dt><dd><?php echo e($owner ?? 'Chưa xác định'); ?> · <?php echo e($date($plan->han_hoan_thanh)); ?></dd></div>
                            <div class="<?php echo e($row); ?> bg-coffee-50/60"><dt class="font-semibold text-coffee-800">Đánh giá lại</dt><dd><?php echo e($date($plan->ngay_danh_gia_lai)); ?></dd></div>
                            <?php if($plan->trang_thai === 'HoanThanh'): ?>
                                <div class="<?php echo e($row); ?>"><dt class="font-semibold text-coffee-800">Kết quả</dt><dd class="font-bold text-olive-700"><?php echo e($plan->ket_qua_sau_dao_tao_lai === 'Dat' ? 'Đạt' : $plan->ket_qua_sau_dao_tao_lai); ?></dd></div>
                            <?php endif; ?>
                            <?php if($plan->ke_hoach): ?>
                                <div class="<?php echo e($row); ?>"><dt class="font-semibold text-coffee-800">Đề xuất ban đầu</dt><dd><?php echo $__env->make('components.assessment-improvement-summary', ['savedProposal' => json_decode($plan->ke_hoach, true), 'muted' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></dd></div>
                            <?php endif; ?>
                        </dl>
                    </section>
                    <?php echo $__env->make('components.retraining-round-history', ['muted' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <?php if($canAssign): ?>
                        <details class="rounded-xl border border-coffee-200" <?php if($plan->tien_trinh === 'ChoGiao'): ?> open <?php endif; ?>>
                            <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-coffee-800"><?php echo e($plan->tien_trinh === 'ChoGiao' ? 'Giao vòng đào tạo lại lần '.$plan->next_round_number : 'Cập nhật kế hoạch vòng hiện tại'); ?></summary>
                            <form method="POST" action="<?php echo e(route('manager.training.assign', $plan->id)); ?>" class="space-y-3 border-t border-coffee-100 px-4 py-4">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <div class="grid gap-3 md:grid-cols-3">
                                    <label class="text-xs font-semibold text-coffee-700">Trưởng ca phụ trách
                                        <select required name="nguoi_phu_trach_id" class="<?php echo e($inputClass); ?>"><option value="">Chọn trưởng ca</option><?php $__currentLoopData = $trainingOwners->where('id', '!=', $plan->user_id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($candidate->id); ?>" <?php if($plan->nguoi_phu_trach_id == $candidate->id): echo 'selected'; endif; ?>><?php echo e($candidate->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                                    </label>
                                    <label class="text-xs font-semibold text-coffee-700">Hạn hoàn thành<input required type="date" name="han_hoan_thanh" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->han_hoan_thanh); ?>" class="<?php echo e($inputClass); ?>"></label>
                                    <label class="text-xs font-semibold text-coffee-700">Ngày dự kiến đánh giá lại<input required type="date" name="ngay_danh_gia_lai" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->ngay_danh_gia_lai); ?>" class="<?php echo e($inputClass); ?>"></label>
                                </div>
                                <label class="block text-xs font-semibold text-coffee-700">Nội dung thực hiện<textarea required name="ghi_chu_thuc_hien" rows="3" maxlength="5000" placeholder="Kỹ năng cần cải thiện, bài cần học lại, cách thực hành và tiêu chí hoàn thành..." class="<?php echo e($inputClass); ?>"><?php echo e($plan->ghi_chu_thuc_hien); ?></textarea></label>
                                <div class="flex justify-end"><button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Lưu & giao đào tạo lại</button></div>
                            </form>
                        </details>
                    <?php endif; ?>

                    <?php if($plan->tien_trinh === 'DangThucHien' && $plan->trang_thai === 'DaDuyet'): ?>
                        <details class="rounded-xl border border-coffee-200">
                            <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-coffee-800">Tạo lịch đánh giá lại</summary>
                            <form method="POST" action="<?php echo e(route('manager.training.reassess', $plan->id)); ?>" class="space-y-3 border-t border-coffee-100 px-4 py-4">
                                <?php echo csrf_field(); ?>
                                <div class="grid gap-3 md:grid-cols-3">
                                    <label class="text-xs font-semibold text-coffee-700">Trưởng ca đánh giá
                                        <select required name="assessor_id" class="<?php echo e($inputClass); ?>"><option value="">Chọn trưởng ca</option><?php $__currentLoopData = $proposalOwners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $candidate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(in_array(mb_strtolower(trim($candidate->ChucVu ?? '')), ['trưởng ca', 'truong_ca', 'leader'])): ?><option value="<?php echo e($candidate->id); ?>"><?php echo e($candidate->name); ?></option><?php endif; ?> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select>
                                    </label>
                                    <label class="text-xs font-semibold text-coffee-700">Ngày đánh giá<input required type="date" name="assessment_date" min="<?php echo e(today()->toDateString()); ?>" value="<?php echo e($plan->ngay_danh_gia_lai); ?>" class="<?php echo e($inputClass); ?>"></label>
                                    <label class="text-xs font-semibold text-coffee-700">Giờ đánh giá<input required type="time" name="assessment_time" value="09:00" class="<?php echo e($inputClass); ?>"></label>
                                </div>
                                <label class="block text-xs font-semibold text-coffee-700">Nội dung cần kiểm tra lại<textarea required name="criteria" rows="2" maxlength="2000" class="<?php echo e($inputClass); ?>" placeholder="Nội dung trưởng ca cần kiểm tra sau đào tạo..."></textarea></label>
                                <div class="flex justify-end"><button <?php if(! ($currentRound?->eligibility['ready'] ?? false)): echo 'disabled'; endif; ?> class="rounded-xl bg-coffee-800 px-5 py-2.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Tạo lịch đánh giá lại</button></div>
                            </form>
                        </details>
                    <?php endif; ?>
                </div>
                <?php if($plan->trang_thai === 'ChoDuyet'): ?>
                    <div class="flex flex-col gap-3 border-t border-coffee-100 bg-coffee-50/50 px-6 py-4 md:flex-row md:items-center">
                        <form method="POST" action="<?php echo e(route('manager.training.review', $plan->id)); ?>" class="flex flex-1 gap-2"><?php echo csrf_field(); ?><input type="hidden" name="action" value="reject"><input required name="reason" aria-label="Lý do từ chối" placeholder="Lý do từ chối..." class="min-w-0 flex-1 rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm"><button class="rounded-xl border border-terra-200 bg-white px-4 py-2.5 text-sm font-bold text-terra-700">Từ chối</button></form>
                        <form method="POST" action="<?php echo e(route('manager.training.review', $plan->id)); ?>"><?php echo csrf_field(); ?><input type="hidden" name="action" value="approve"><button class="w-full rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white md:w-auto">Duyệt đề xuất</button></form>
                    </div>
                <?php endif; ?>
            </dialog>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="border-t border-coffee-100 p-10 text-center text-stone-500"><?php echo e($training->isEmpty() ? 'Chưa có yêu cầu đào tạo lại. Yêu cầu xuất hiện khi đánh giá chưa đạt được duyệt.' : 'Không có yêu cầu ở tiến trình này.'); ?></p>
        <?php endif; ?>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/manager/training.blade.php ENDPATH**/ ?>