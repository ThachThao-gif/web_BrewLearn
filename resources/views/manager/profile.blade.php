@extends('layouts.app')

@section('title', 'Hồ sơ nhân viên')

@section('content')
@php
    $requestLabels = ['ChoGiao' => 'Chờ giao', 'DangThucHien' => 'Đang thực hiện', 'ChoDanhGiaLai' => 'Chờ đánh giá lại', 'HoanTat' => 'Hoàn tất', 'DungDaoTao' => 'Dừng đào tạo lại'];
    $resultLabels = ['ChuaCoKetQua' => 'Chưa có kết quả', 'Dat' => 'Đạt', 'ChuaDat' => 'Chưa đạt'];
@endphp
<div class="max-w-6xl mx-auto space-y-5" x-data="{ tab: 'info' }">
    @include('components.staff-profile', [
        'staff' => $person,
        'backUrl' => route('manager.staff'),
        'backLabel' => 'Danh sách nhân viên',
        'assessmentLink' => [route('manager.assessments', ['q' => $person->name]), 'Mở màn hình duyệt đánh giá'],
        'showRisk' => true,
        'extraTabs' => [
            'risk' => ['Cảnh báo', 'fa-triangle-exclamation', null],
            'training' => ['Đào tạo lại', 'fa-rotate', $training->count()],
        ],
    ])

    {{-- Cảnh báo nguy cơ (chỉ quản lý) --}}
    <section id="panel-risk" role="tabpanel" aria-labelledby="tab-risk" x-show="tab === 'risk'" x-cloak class="rounded-2xl border border-coffee-200 bg-white p-6 shadow-sm md:p-8">
        <h2 class="text-xl font-bold text-coffee-900 md:text-2xl">Cảnh báo nguy cơ theo bộ quy tắc</h2>
        <p class="mt-6 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900"><i class="fa-solid fa-circle-info mt-0.5 text-amber-700" aria-hidden="true"></i>Chỉ hỗ trợ xem xét hồ sơ. Điểm cảnh báo không phải xác suất, không cộng vào điểm năng lực và không quyết định kết quả đánh giá.</p>
        <div class="mt-6">@include('manager.risk-details', ['spacious' => true])</div>
    </section>

    {{-- Đào tạo lại (chỉ quản lý) --}}
    <section id="panel-training" role="tabpanel" aria-labelledby="tab-training" x-show="tab === 'training'" x-cloak class="rounded-2xl border border-coffee-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-base font-bold text-coffee-900">Lịch sử đào tạo lại</h2>
            <a href="{{ route('manager.training', ['q' => $person->name]) }}" class="text-sm font-bold text-amber-700 hover:text-amber-800">Xem / giao đào tạo lại →</a>
        </div>
        @forelse($training as $plan)
            <article class="mt-3 border-t border-coffee-100 pt-4 text-sm">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="font-semibold text-coffee-900">Yêu cầu đào tạo lại · {{ $plan->nguon_de_xuat ?? 'Trưởng ca đề xuất' }}</p>
                    <span class="text-xs text-slate-600">{{ $requestLabels[$plan->display_state] ?? 'Chưa có tiến trình' }} · <span class="font-semibold">{{ $resultLabels[$plan->ket_qua_sau_dao_tao_lai] ?? 'Chưa có kết quả' }}</span></span>
                </div>
                <p class="mt-1 text-xs text-slate-500">{{ $plan->ly_do }}</p>
                <div class="mt-2">@include('components.retraining-round-history')</div>
            </article>
        @empty
            <p class="mt-3 border-t border-coffee-100 pt-4 text-sm text-slate-500">Chưa có yêu cầu đào tạo lại.</p>
        @endforelse
    </section>
</div>
@endsection
