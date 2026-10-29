@extends('layouts.app')
@section('title', 'Tổng quan cửa hàng • BrewLearn')
@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    @include('manager.banner', ['heading' => 'Tổng quan cửa hàng', 'description' => 'Việc cần xử lý và tình hình đào tạo của cửa hàng.'])
    @include('manager.filter')

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
        @foreach([
            ['Nhân viên đang làm', $activeCount, 'users', 'manager.staff', 'text-coffee-700 bg-coffee-100'],
            ['Đánh giá chờ duyệt', $pendingCount, 'clipboard-check', 'manager.assessments', 'text-amber-700 bg-amber-50'],
            ['Sắp đến hạn / quá hạn', $staff->where('needs_reminder', true)->count(), 'calendar-days', 'manager.staff', 'text-rose-700 bg-rose-50'],
            ['Đào tạo lại đang xử lý', $trainingCount, 'arrows-rotate', 'manager.training', 'text-emerald-700 bg-emerald-50'],
        ] as [$label, $count, $icon, $link, $tone])
            <a href="{{ route($link, request()->only('shift', 'q')) }}" class="flex items-center gap-4 rounded-2xl border border-coffee-200 bg-white p-4 transition hover:border-amber-400">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fa-solid fa-{{ $icon }}"></i></span>
                <span><span class="block text-2xl font-extrabold leading-none text-coffee-900">{{ $count }}</span><span class="mt-1 block text-xs text-slate-500">{{ $label }}</span></span>
            </a>
        @endforeach
    </div>

    @include('manager.dashboard-charts')
</div>
@endsection
