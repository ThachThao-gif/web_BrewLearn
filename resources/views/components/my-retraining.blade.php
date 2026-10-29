@props(['retraining'])
@php
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
@endphp
<section id="retraining-progress" aria-label="Tiến độ học lại" class="hidden scroll-mt-24 border-l-2 border-slate-300 pl-4 text-sm text-slate-600">
    <p class="flex flex-wrap gap-x-5 gap-y-1">
        @foreach($items as $label => $value)
            <span>{{ $label }}: <span class="font-semibold text-slate-800">{{ $value }}</span></span>
        @endforeach
    </p>
    @if($pending->isNotEmpty())
        <p class="mt-1">Còn cần làm:
            @foreach($pending as $item)
                <a href="{{ $item['type'] === 'lesson' ? route('courses.lesson', $item['id']) : route('tests.show', $item['id']) }}" class="font-semibold text-slate-800 underline underline-offset-2">{{ $item['name'] }}</a>@if(! $loop->last), @endif
            @endforeach
        </p>
    @endif
</section>
