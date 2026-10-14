@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto space-y-5">
<div class="page-banner"><h1>Đào tạo lại tôi phụ trách</h1><p class="text-sm mt-2">Kiểm tra nội dung đã thực hiện và xác nhận trước khi quản lý tổ chức đánh giá lại.</p></div>
@if(session('success'))<p class="bg-emerald-50 p-4 rounded-xl">{{ session('success') }}</p>@endif
@if($errors->any())<div role="alert" class="bg-rose-50 p-4 rounded-xl">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@forelse($rounds as $round)
<article class="bg-white border border-coffee-200 p-5 rounded-2xl space-y-3"><h2 class="font-bold">{{ $round->employee_name }} • Đào tạo lại lần {{ $round->round_number }}</h2><p class="whitespace-pre-line text-sm">{{ $round->instructions }}</p><p class="text-xs text-slate-500">Hạn: {{ $round->deadline }}</p>
@foreach($round->eligibility['missing'] as $missing)<p class="text-sm text-rose-700">{{ $missing }}</p>@endforeach
@if($round->confirmed_at)<p class="text-sm text-emerald-700">Đã xác nhận ngày {{ $round->confirmed_at }}: {{ $round->confirmation_note }}</p>@endif
@if($round->status === 'DangThucHien')<form method="POST" action="{{ route('training.confirm', $round->id) }}" class="space-y-3">@csrf<label class="block text-sm">Ghi nhận thực hành / hành vi quan sát<textarea required name="confirmation_note" maxlength="2000" class="block mt-2 w-full border border-coffee-300 rounded-xl p-3" placeholder="Nội dung đã thực hiện, số ca theo dõi và nhận xét...">{{ $round->confirmation_note }}</textarea></label><button @disabled(!$round->eligibility['ready']) class="bg-coffee-800 text-white rounded-xl p-3 disabled:opacity-40 disabled:cursor-not-allowed">Xác nhận đã hoàn thành nội dung được giao</button></form>@endif
</article>
@empty<p class="bg-white p-6 rounded-xl">Không có kế hoạch đào tạo lại đang thực hiện cần bạn phụ trách.</p>@endforelse
</div>
@endsection
