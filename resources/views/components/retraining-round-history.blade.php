@if($plan->rounds->isNotEmpty())
<details class="border border-coffee-200 rounded-xl p-4" open>
    <summary class="font-bold text-sm text-coffee-900 cursor-pointer">Lịch sử {{ $plan->rounds->count() }} vòng đào tạo lại</summary>
    <div class="mt-3 space-y-3">
    @foreach($plan->rounds as $round)
        <div class="rounded-xl p-4 {{ $round->result === 'Dat' ? 'bg-emerald-50' : ($round->result === 'ChuaDat' ? 'bg-rose-50' : 'bg-coffee-50') }}">
            <div class="flex flex-wrap justify-between gap-2 text-sm"><strong>Đào tạo lại lần {{ $round->round_number }}</strong><span>{{ ['Dat' => 'Đạt • đã duyệt', 'ChuaDat' => 'Chưa đạt • đã duyệt'][$round->result] ?? ($round->status === 'ChoDanhGiaLai' ? 'Chờ đánh giá lại / phê duyệt' : 'Đang thực hiện') }}</span></div>
            <p class="text-sm whitespace-pre-line mt-2">{{ $round->instructions }}</p>
            @if($round->confirmed_at)<p class="text-xs text-emerald-700">Đã xác nhận hoàn thành: {{ $round->confirmed_at }} • {{ $round->confirmation_note }}</p>@endif
            <p class="text-xs text-slate-500 mt-2">Phụ trách: {{ $proposalOwners->firstWhere('id', $round->owner_id)?->name ?? 'Chưa xác định' }} • Hạn: {{ $round->deadline }} • Ngày đánh giá lại: {{ $round->assessment_date }}</p>
            @if($round->assessment_id)<p class="text-xs mt-2">Đánh giá #{{ $round->assessment_id }}@if($round->score !== null) • {{ $round->score }}/100 • Duyệt lúc {{ $round->approved_at }}@endif</p>@endif
            @if($round->proposal)@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($round->proposal, true)])@endif
        </div>
    @endforeach
    </div>
</details>
@endif
