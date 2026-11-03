@if($plan->rounds->isNotEmpty())
@php
    $roundResults = [
        'Dat' => ['Đạt', 'bg-emerald-50 text-emerald-700', 'bg-emerald-500'],
        'ChuaDat' => ['Chưa đạt', 'bg-rose-50 text-rose-600', 'bg-amber-500'],
    ];
    $roundState = fn ($round) => $roundResults[$round->result] ?? [$round->status === 'ChoDanhGiaLai' ? 'Chờ đánh giá' : 'Đang thực hiện', 'bg-slate-100 text-slate-600', 'bg-slate-300'];
    $latestRound = $plan->rounds->last();
    $label = 'text-[10px] font-semibold uppercase tracking-wider text-amber-700';
@endphp
<section class="text-sm" aria-label="Các vòng đào tạo lại">
    <div class="mb-2 flex items-baseline justify-between gap-2">
        <h3 class="{{ $label }}">Nhật ký {{ $plan->rounds->count() }} vòng sát hạch</h3>
        <span class="text-xs text-slate-500">Đạt khi tổng <strong class="text-coffee-900">≥ 80</strong> và mọi tiêu chí <strong class="text-coffee-900">≥ 70</strong></span>
    </div>
    <ol class="grid gap-3 sm:grid-cols-3">
        @foreach($plan->rounds as $round)
            @php
                [$roundLabel, $roundBadge, $roundBar] = $roundState($round);
                $isLatest = $round->id === $latestRound->id;
                $barColor = $isLatest && $round->result === 'ChuaDat' ? 'bg-rose-500' : $roundBar;
            @endphp
            <li class="rounded-xl border bg-white p-3 {{ $isLatest ? 'border-coffee-400 shadow-sm' : 'border-slate-200' }}">
                <p class="flex items-center justify-between gap-2">
                    <span class="font-bold text-coffee-900">Lần {{ $round->round_number }}@if($isLatest && (int) $round->round_number === 3) (Chốt)@endif</span>
                    <span class="rounded px-1.5 py-0.5 text-[11px] font-semibold {{ $roundBadge }}">{{ $roundLabel }}</span>
                </p>
                <p class="mt-3 flex items-baseline justify-between text-xs text-slate-500">Kết quả:
                    @if($round->score !== null)
                        <span><strong class="text-base {{ $isLatest && $round->result === 'ChuaDat' ? 'text-rose-600' : 'text-coffee-900' }}">{{ $round->score }}</strong>/100</span>
                    @else
                        <span class="text-slate-400">Chưa chấm</span>
                    @endif
                </p>
                <div class="mt-1 h-1 rounded-full bg-slate-100"><div class="h-1 rounded-full {{ $barColor }}" style="width: {{ min(100, (float) ($round->score ?? 0)) }}%"></div></div>
                @if($round->weak_criteria ?? [])
                    <p class="mt-1.5 text-[11px] text-rose-600">Dưới 70: {{ implode(', ', $round->weak_criteria) }}</p>
                @endif
                <p class="mt-2 flex justify-between text-xs text-slate-500">Hạn chót: <span class="text-coffee-900">{{ $round->deadline ? \Carbon\Carbon::parse($round->deadline)->format('d/m') : '—' }}</span></p>
            </li>
        @endforeach
        @for($slot = $plan->rounds->count() + 1; $slot <= 3; $slot++)
            <li class="rounded-xl border border-dashed border-slate-200 p-3 text-slate-400">
                <p class="font-bold">Lần {{ $slot }}</p>
                <p class="mt-3 text-xs">Chưa giao</p>
            </li>
        @endfor
    </ol>
    <details class="mt-2">
        <summary class="cursor-pointer text-xs font-semibold text-coffee-700">Xem nội dung từng vòng</summary>
        <ol class="mt-2 space-y-3 border-l-2 border-slate-200 pl-4">
            @foreach($plan->rounds as $round)
                <li>
                    <p class="font-semibold text-coffee-900">Lần {{ $round->round_number }} <span class="font-normal text-xs text-slate-400">· {{ $proposalOwners->firstWhere('id', $round->owner_id)?->name ?? 'Chưa có người phụ trách' }}</span></p>
                    <p class="mt-0.5 whitespace-pre-line text-slate-700">{{ $round->instructions }}</p>
                    @if($round->confirmed_at)<p class="mt-0.5 text-xs text-slate-500">Đã xác nhận hoàn thành{{ $round->confirmation_note ? ': '.$round->confirmation_note : '' }}</p>@endif
                    @if($round->proposal && json_decode($round->proposal, true) !== json_decode($plan->ke_hoach ?? 'null', true))@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($round->proposal, true)])@endif
                </li>
            @endforeach
        </ol>
    </details>
</section>
@endif
