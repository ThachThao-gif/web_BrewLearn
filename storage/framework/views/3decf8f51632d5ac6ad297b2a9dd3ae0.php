<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['retraining']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['retraining']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<?php
    $round = $retraining['round'];
    $checklist = collect($retraining['checklist']);
    $lessons = $checklist->where('type', 'lesson');
    $quizzes = $checklist->where('type', 'quiz');
    $pending = $checklist->reject(fn (array $item) => $item['done']);
    $deadline = $round?->deadline ? \Carbon\Carbon::parse($round->deadline)->startOfDay() : null;
    $daysLeft = $deadline ? (int) now()->startOfDay()->diffInDays($deadline, false) : null;
    $state = match ($retraining['state']) {
        'waiting_assignment' => 'Chờ quản lý giao kế hoạch',
        'learning' => 'Đang học lại',
        'waiting_confirmation' => 'Đã học xong, chờ người phụ trách xác nhận',
        'confirmed' => 'Đã được xác nhận, chờ lịch đánh giá lại',
        'scheduled' => 'Đã có lịch đánh giá lại',
        default => 'Đang chờ kết quả đánh giá lại',
    };
    $items = ['Trạng thái' => $state];
    if ($round) {
        $items['Người phụ trách'] = $retraining['owner'] ?? '—';
        $items['Hạn hoàn thành'] = ($deadline?->format('d/m/Y') ?? '—')
            .($deadline && in_array($retraining['state'], ['learning', 'waiting_confirmation']) ? ' ('.($daysLeft < 0 ? 'quá hạn '.abs($daysLeft).' ngày' : ($daysLeft === 0 ? 'hôm nay' : 'còn '.$daysLeft.' ngày')).')' : '');
        if ($lessons->isNotEmpty()) {
            $items['Bài học lại'] = 'đã hoàn thành '.$lessons->where('done', true)->count().'/'.$lessons->count().' bài';
        }
        if ($quizzes->isNotEmpty()) {
            $items['Bài kiểm tra'] = 'đã đạt '.$quizzes->where('done', true)->count().'/'.$quizzes->count().' bài';
        }
    }
?>
<section id="retraining-progress" aria-label="Tiến độ học lại" class="hidden scroll-mt-24 border-l-2 border-slate-300 pl-4 text-sm text-slate-600">
    <p class="flex flex-wrap gap-x-5 gap-y-1">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <span><?php echo e($label); ?>: <span class="font-semibold text-slate-800"><?php echo e($value); ?></span></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </p>
    <?php if($pending->isNotEmpty()): ?>
        <p class="mt-1">Còn cần làm:
            <?php $__currentLoopData = $pending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e($item['type'] === 'lesson' ? route('courses.lesson', $item['id']) : route('tests.show', $item['id'])); ?>" class="font-semibold text-slate-800 underline underline-offset-2"><?php echo e($item['name']); ?></a><?php if(! $loop->last): ?>, <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </p>
    <?php endif; ?>
</section>
<?php /**PATH D:\wamp64\Brewlearn\resources\views/components/my-retraining.blade.php ENDPATH**/ ?>