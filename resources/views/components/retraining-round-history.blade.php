@if($plan->rounds->isNotEmpty())
<details class="text-sm">
    <summary class="cursor-pointer font-semibold text-coffee-700">Lịch sử {{ $plan->rounds->count() }} vòng đào tạo lại</summary>
    <ol class="mt-3 space-y-3 border-l-2 border-coffee-100 pl-4">
    @foreach($plan->rounds as $round)
        @php([$roundLabel, $roundClass] = ['Dat' => ['Đạt', ($muted ?? false) ? 'text-olive-700' : 'text-emerald-700'], 'ChuaDat' => ['Chưa đạt', ($muted ?? false) ? 'text-terra-700' : 'text-rose-700']][$round->result] ?? [$round->status === 'ChoDanhGiaLai' ? 'Chờ đánh giá lại' : 'Đang thực hiện', 'text-slate-600'])
        <li>
            <p class="flex flex-wrap items-baseline gap-2"><strong class="text-coffee-900">Đào tạo lại lần {{ $round->round_number }}</strong><span class="text-xs font-bold {{ $roundClass }}">{{ $roundLabel }}@if($round->score !== null) · {{ $round->score }}/100 @endif</span></p>
            <p class="mt-1 whitespace-pre-line text-slate-700">{{ $round->instructions }}</p>
            <p class="mt-1 text-xs text-slate-400">Phụ trách {{ $proposalOwners->firstWhere('id', $round->owner_id)?->name ?? 'chưa xác định' }} · Hạn {{ $round->deadline ? \Carbon\Carbon::parse($round->deadline)->format('d/m/Y') : '—' }}@if($round->assessment_id) · Đánh giá #{{ $round->assessment_id }}@endif</p>
            @if($round->confirmed_at)<p class="text-xs {{ ($muted ?? false) ? 'text-olive-700' : 'text-emerald-700' }}">Đã xác nhận hoàn thành{{ $round->confirmation_note ? ': '.$round->confirmation_note : '' }}</p>@endif
            @if($round->proposal)@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($round->proposal, true), 'muted' => $muted ?? false])@endif
        </li>
    @endforeach
    </ol>
</details>
@endif
