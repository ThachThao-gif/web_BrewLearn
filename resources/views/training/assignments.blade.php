@extends('layouts.app')
@section('content')
@php
    $maxRounds = \App\Http\Controllers\ManagerController::MAX_RETRAINING_ROUNDS;
    $today = now()->startOfDay();
    foreach ($rounds as $round) {
        $round->deadline_at = $round->deadline ? \Carbon\Carbon::parse($round->deadline)->startOfDay() : null;
        $round->days_left = $round->deadline_at ? (int) $today->diffInDays($round->deadline_at, false) : null;
        $round->learning_done = max($round->eligibility['required'] - count($round->eligibility['missing']), 0);
        $round->state = $round->confirmed_at ? 'confirmed' : ($round->eligibility['ready'] ? 'ready' : 'waiting');
    }
    $stateLabels = [
        'confirmed' => 'Đã xác nhận, chờ quản lý xếp lịch đánh giá lại',
        'ready' => 'Nhân viên đã hoàn thành học lại, chờ bạn xác nhận',
        'waiting' => 'Chờ nhân viên hoàn thành học lại và kiểm tra',
    ];
    $summary = [
        ['Đang phụ trách', $rounds->count(), 'text-coffee-900'],
        ['Sẵn sàng xác nhận', $rounds->where('state', 'ready')->count(), 'text-amber-600'],
        ['Chờ nhân viên hoàn thành', $rounds->where('state', 'waiting')->count(), 'text-slate-500'],
        ['Quá hạn chưa xác nhận', $rounds->filter(fn ($round) => $round->state !== 'confirmed' && $round->days_left !== null && $round->days_left < 0)->count(), 'text-rose-600'],
    ];
@endphp
<div class="max-w-5xl mx-auto space-y-5">
    <div class="page-banner"><h1>Đào tạo lại tôi phụ trách</h1><p class="text-sm mt-2">Theo dõi nhân viên học lại, kiểm tra điều kiện và xác nhận phần thực hành trước khi quản lý tổ chức đánh giá lại.</p></div>
    @if(session('success'))<p role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</p>@endif
    @if($errors->any())<div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif

    @if($rounds->isNotEmpty())
        <section aria-label="Tóm tắt" class="grid grid-cols-2 overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm md:grid-cols-4 md:divide-x divide-coffee-100">
            @foreach($summary as [$label, $count, $tone])
                <div class="px-5 py-4"><span class="block text-2xl font-extrabold {{ $tone }}">{{ $count }}</span><span class="text-xs text-slate-500">{{ $label }}</span></div>
            @endforeach
        </section>
    @endif

    @forelse($rounds as $round)
        @php
            $stateLabel = $stateLabels[$round->state];
            $required = $round->eligibility['required'];
        @endphp
        <article class="overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm">
            @php
                $th = 'w-56 border-b border-r border-slate-200 bg-slate-50 px-5 py-3 text-left align-top font-semibold text-slate-600';
                $td = 'border-b border-slate-200 px-5 py-3 align-top text-slate-800';
                $overdue = $round->state !== 'confirmed' && $round->days_left !== null && $round->days_left < 0;
            @endphp
            <table class="w-full text-sm">
                <caption class="sr-only">Đào tạo lại của {{ $round->employee_name }}</caption>
                <tbody>
                    <tr><th scope="row" class="{{ $th }}">Nhân viên</th><td class="{{ $td }}"><span class="font-semibold">{{ $round->employee_name }}</span> · {{ $round->employee_position ?? 'Nhân viên' }}</td></tr>
                    <tr><th scope="row" class="{{ $th }}">Vòng đào tạo lại</th><td class="{{ $td }}">{{ $round->round_number }}/{{ $maxRounds }}</td></tr>
                    <tr><th scope="row" class="{{ $th }}">Hạn hoàn thành</th><td class="{{ $td }} {{ $overdue ? 'text-rose-700' : '' }}">
                        {{ $round->deadline_at?->format('d/m/Y') ?? '—' }}
                        @if($round->deadline_at && $round->state !== 'confirmed')
                            ({{ $round->days_left < 0 ? 'quá hạn '.abs($round->days_left).' ngày' : ($round->days_left === 0 ? 'hôm nay' : 'còn '.$round->days_left.' ngày') }})
                        @endif
                    </td></tr>
                    <tr><th scope="row" class="{{ $th }}">Trạng thái</th><td class="{{ $td }} font-semibold">{{ $stateLabel }}</td></tr>
                    @if($round->ly_do)
                        <tr><th scope="row" class="{{ $th }}">Lý do đào tạo lại</th><td class="{{ $td }}">{{ $round->ly_do }}</td></tr>
                    @endif
                    <tr><th scope="row" class="{{ $th }}">Nội dung được giao</th><td class="{{ $td }} whitespace-pre-line">{{ $round->instructions ?: 'Chưa có hướng dẫn chi tiết.' }}</td></tr>
                    <tr><th scope="row" class="{{ $th }}">Học lại và kiểm tra</th><td class="{{ $td }}">
                        @if($required > 0)
                            Đã hoàn thành {{ $round->learning_done }}/{{ $required }} yêu cầu
                            @if($round->eligibility['missing'])
                                <ul class="mt-1 list-disc pl-5 text-rose-700">
                                    @foreach($round->eligibility['missing'] as $missing)<li>{{ $missing }}</li>@endforeach
                                </ul>
                            @endif
                        @else
                            Vòng này không yêu cầu bài học hay bài kiểm tra.
                        @endif
                    </td></tr>
                    <tr><th scope="row" class="{{ $th }} border-b-0">Ghi nhận thực hành</th><td class="{{ $td }} border-b-0">
                        @if($round->confirmed_at)
                            Đã xác nhận lúc {{ \Carbon\Carbon::parse($round->confirmed_at)->format('H:i d/m/Y') }}
                            <span class="mt-1 block whitespace-pre-line text-slate-600">{{ $round->confirmation_note }}</span>
                        @elseif($round->eligibility['ready'])
                            Chưa ghi nhận. Nhập ghi nhận và xác nhận ở bên dưới.
                        @else
                            Thực hiện sau khi nhân viên hoàn thành học lại và kiểm tra.
                        @endif
                    </td></tr>
                </tbody>
            </table>
            <footer class="border-t border-slate-200 bg-slate-50/60 px-5 py-4">
                @if($round->eligibility['ready'])
                    <details @if(! $round->confirmed_at) open @endif>
                        <summary class="cursor-pointer text-sm font-bold text-coffee-800 {{ $round->confirmed_at ? '' : 'sr-only' }}">Cập nhật ghi nhận</summary>
                        <form method="POST" action="{{ route('training.confirm', $round->id) }}" class="mt-2 space-y-3">
                            @csrf
                            <label class="block text-sm font-semibold text-coffee-900">Ghi nhận thực hành / hành vi quan sát
                                <textarea required name="confirmation_note" maxlength="2000" rows="3" class="mt-2 block w-full rounded-xl border border-slate-300 bg-white p-3 font-normal" placeholder="Nội dung đã thực hiện, số ca theo dõi và nhận xét...">{{ old('confirmation_note', $round->confirmation_note) }}</textarea>
                            </label>
                            <button class="rounded-xl bg-coffee-800 px-4 py-2.5 text-sm font-bold text-white hover:bg-coffee-900">{{ $round->confirmed_at ? 'Lưu ghi nhận' : 'Xác nhận đã hoàn thành nội dung được giao' }}</button>
                        </form>
                    </details>
                @else
                    <p class="text-sm text-slate-600">Bạn có thể ghi nhận và xác nhận sau khi nhân viên hoàn thành các yêu cầu học lại và kiểm tra.</p>
                @endif
            </footer>
        </article>
    @empty
        <div class="rounded-2xl border border-dashed border-coffee-300 bg-white p-8 text-center">
            <i class="fa-solid fa-mug-hot text-2xl text-coffee-300" aria-hidden="true"></i>
            <p class="mt-2 text-sm font-semibold text-slate-600">Không có kế hoạch đào tạo lại đang thực hiện cần bạn phụ trách.</p>
        </div>
    @endforelse
</div>
@endsection
