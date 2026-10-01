@extends('layouts.app')
@section('title', 'Tổng quan cửa hàng • BrewLearn')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
@include('manager.banner', ['heading' => 'Tổng quan cửa hàng', 'description' => 'Theo dõi học tập, xem ưu tiên hỗ trợ và xử lý đề xuất đào tạo của toàn cửa hàng.'])
@include('manager.filter')
<div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
@foreach([['Nhân sự trong phạm vi', $staff->count(), 'users', 'manager.staff'], ['Đánh giá chờ duyệt', $pendingCount, 'clipboard-check', 'manager.assessments'], ['Đến hạn trong 7 ngày / quá hạn', $staff->where('needs_reminder', true)->count(), 'calendar-days', 'manager.staff'], ['Đào tạo lại đang xử lý', $trainingCount, 'arrows-rotate', 'manager.training']] as [$label, $count, $icon, $link])
<a href="{{ route($link, request()->only('shift', 'q')) }}" class="p-5 bg-white border border-coffee-200 rounded-2xl hover:border-amber-400 transition"><i class="fa-solid fa-{{ $icon }} text-amber_brand"></i><p class="text-3xl font-extrabold text-coffee-900 mt-3">{{ $count }}</p><p class="text-xs text-slate-500 mt-1">{{ $label }}</p></a>
@endforeach
</div>
<div class="flex flex-col sm:flex-row justify-between gap-2"><div><h2 class="text-lg font-extrabold text-coffee-900">Ưu tiên xem hồ sơ nhân viên</h2><p class="text-xs text-slate-500 mt-1">Cao → Trung bình → Thấp → Chưa đủ dữ liệu</p></div><span class="text-xs bg-amber-50 border border-amber-100 rounded-xl p-3 text-amber-900">Dự đoán chỉ giúp ưu tiên xem hồ sơ, không dùng để chấm điểm.</span></div>
@include('manager.staff-table')
<div class="rounded-2xl bg-coffee-100 border border-coffee-200 p-5 text-sm text-coffee-700"><p class="font-bold">Hai nguồn thông tin độc lập</p><p class="mt-1">Model đọc dữ liệu học tập và quiz. Điểm trưởng ca được lưu riêng. Quản lý xem từng nguồn và quyết định việc hỗ trợ, không gộp thành một điểm chung.</p></div>
</div>
@endsection
