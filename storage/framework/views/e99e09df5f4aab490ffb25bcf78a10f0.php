<?php
    $savedLabels = ['daotaobosung' => 'Đào tạo bổ sung', 'hoclaibaihoc' => 'Học lại bài học liên quan', 'theodoica' => 'Theo dõi thêm trong ca', 'danhgialai' => 'Đánh giá lại'];
    $savedCriteria = ['nghiep_vu' => 'Kiến thức nghiệp vụ', 'ky_nang' => 'Thao tác công việc', 'quy_trinh' => 'Tuân thủ quy trình', 'phuc_vu' => 'Thái độ phục vụ', 'tac_phong' => 'Tác phong & phối hợp'];
?>
<details class="mt-2 text-xs font-normal">
    <summary class="cursor-pointer font-bold <?php echo e(($muted ?? false) ? 'text-honey-700' : 'text-amber-700'); ?>">Xem nội dung đề xuất đã gửi</summary>
    <div class="mt-2 space-y-3 min-w-48 max-w-sm">
        <?php $__currentLoopData = ($savedProposal['details'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="<?php echo e(($muted ?? false) ? 'bg-honey-50 border border-honey-100' : 'bg-amber-50 border border-amber-100'); ?> rounded-lg p-3 space-y-1">
                <p class="font-bold"><?php echo e($savedLabels[$type] ?? $type); ?></p>
                <?php if($type === 'daotaobosung'): ?>
                    <p>Kỹ năng: <?php echo e($plan['skill'] ?? ''); ?></p>
                    <p class="whitespace-pre-line">Hướng dẫn: <?php echo e($plan['instructions'] ?? ''); ?></p>
                    <p>Phụ trách dự kiến: <?php echo e($proposalOwners->firstWhere('id', $plan['owner_id'] ?? null)?->name ?? 'Chưa xác định'); ?></p>
                <?php elseif($type === 'hoclaibaihoc'): ?>
                    <p>Khóa: <?php echo e($proposalCourses->firstWhere('MaCTDT', $plan['course_id'] ?? null)?->TenCTDT ?? 'Khóa không còn tồn tại'); ?></p>
                    <ul class="list-disc pl-4">
                        <?php $__currentLoopData = ($plan['lesson_ids'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lessonId): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($proposalLessons->firstWhere('MaBaiHoc', $lessonId)?->TenBaiHoc ?? 'Bài học không còn tồn tại'); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php elseif($type === 'theodoica'): ?>
                    <p class="whitespace-pre-line">Quan sát: <?php echo e($plan['behavior'] ?? ''); ?></p>
                    <?php if(!empty($plan['shift_count'])): ?><p>Thời lượng: <?php echo e($plan['shift_count']); ?> ca</p><?php endif; ?>
                    <?php if(!empty($plan['until_date'])): ?><p>Đến ngày: <?php echo e(\Carbon\Carbon::parse($plan['until_date'])->format('d/m/Y')); ?></p><?php endif; ?>
                <?php elseif($type === 'danhgialai'): ?>
                    <p>Ngày dự kiến: <?php echo e(\Carbon\Carbon::parse($plan['assessment_date'])->format('d/m/Y')); ?></p>
                    <p>Người đánh giá: <?php echo e($proposalOwners->firstWhere('id', $plan['assessor_id'] ?? null)?->name ?? 'Chưa xác định'); ?></p>
                    <p>Tiêu chí: <?php echo e(collect($plan['criteria'] ?? [])->map(fn($criterion) => $savedCriteria[$criterion] ?? $criterion)->implode(', ')); ?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</details>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/components/assessment-improvement-summary.blade.php ENDPATH**/ ?>