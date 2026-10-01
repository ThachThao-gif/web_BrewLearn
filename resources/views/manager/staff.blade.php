@extends('layouts.app')
@section('content')
<div class="max-w-7xl mx-auto space-y-6">
@include('manager.banner', ['heading' => 'Hồ sơ nhân viên', 'description' => 'Xem toàn cửa hàng, phân ca và mở lịch sử học tập, quiz, đánh giá của từng nhân viên.'])
@include('manager.filter')
@include('manager.staff-table')
<details class="bg-white rounded-2xl border border-coffee-200 p-5">
    <summary class="font-bold text-coffee-900 cursor-pointer">Cập nhật trạng thái làm việc</summary>
    <p class="text-xs text-slate-500 mt-2">Tạm ngưng / Nghỉ việc: ngừng nhắc hạn, giữ lịch sử học tập và đánh giá. Khi trở lại Đang làm, hạn được tính từ ngày vào làm hoặc đánh giá đã duyệt gần nhất.</p>
    <div class="mt-4 divide-y divide-coffee-100">
        @foreach($staff as $person)
            <form method="POST" action="{{ route('manager.employment.update', $person->id) }}" class="flex flex-wrap items-center gap-3 py-3">
                @csrf @method('PUT')
                <label for="employment-{{ $person->id }}" class="flex-1 min-w-36 text-sm font-semibold">{{ $person->name }}</label>
                <select id="employment-{{ $person->id }}" name="employment_status" class="border border-coffee-300 rounded-xl p-2 text-sm">
                    @foreach(\App\Models\AssessmentReminder::EMPLOYMENT_LABELS as $value => $label)
                        <option value="{{ $value }}" @selected(($person->employment_status ?? 'DangLam') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="bg-coffee-100 rounded-xl px-4 py-2 text-sm font-bold text-coffee-800">Lưu trạng thái</button>
            </form>
        @endforeach
    </div>
</details>
<details class="bg-white rounded-2xl border border-coffee-200 p-5"><summary class="font-bold text-coffee-900 cursor-pointer">Thiết lập ca làm việc & phân ca</summary><p class="text-xs text-slate-500 mt-2">Thông tin được lưu theo cửa hàng. Nhân viên chưa được phân ca vẫn hiển thị trong toàn cửa hàng.</p>
<form action="{{ route('manager.shifts.create') }}" method="POST" class="mt-5 flex flex-wrap gap-3 items-end">@csrf<label class="text-xs font-bold">Tên ca<input name="ten_ca" required maxlength="100" placeholder="Ví dụ: Ca sáng" class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-bold">Bắt đầu<input type="time" name="bat_dau" required class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-bold">Kết thúc<input type="time" name="ket_thuc" required class="block mt-1 border border-coffee-300 rounded-xl p-2.5"></label><button class="bg-amber_brand text-white rounded-xl px-5 py-2.5 text-sm font-bold">Tạo ca</button></form>
<div class="mt-5 divide-y divide-coffee-100">@foreach($staff as $person)<form action="{{ route('manager.shifts.assign', $person->id) }}" method="POST" class="flex flex-wrap items-center gap-3 py-3">@csrf @method('PUT')<span class="flex-1 text-sm font-semibold min-w-36">{{ $person->name }}</span><select name="ca_lam_viec_id" class="border border-coffee-300 rounded-xl p-2 text-sm"><option value="">Chưa phân ca</option>@foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected($person->ca_lam_viec_id === $shift->id)>{{ $shift->ten_ca }} ({{ substr($shift->bat_dau, 0, 5) }}–{{ substr($shift->ket_thuc, 0, 5) }})</option>@endforeach</select><button class="px-4 py-2 bg-coffee-100 text-coffee-800 rounded-xl text-sm font-bold">Lưu ca</button></form>@endforeach</div></details>
</div>
@endsection
