

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-lg font-bold text-slate-800 mb-4">
        Kết quả tìm kiếm cho: <span class="text-amber-800">"<?php echo e($query); ?>"</span>
    </h2>

    <?php if(count($lessons) > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('courses.lesson', ['id' => $item->MaBaiHoc, 'user_id' => $currentUser->id ?? 1])); ?>" 
                   class="p-4 bg-white border border-[#E2D4C1] rounded-2xl hover:border-amber-500 hover:shadow-sm transition flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Bài học</span>
                        <h4 class="text-xs font-bold text-slate-800 mt-1"><?php echo e($item->TenBaiHoc); ?></h4>
                    </div>
                    <span class="text-xs text-amber-800 font-bold">Vào học →</span>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    <?php else: ?>
        <p class="text-xs text-slate-400">Không tìm thấy bài học nào phù hợp với từ khóa trên.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\search\results.blade.php ENDPATH**/ ?>