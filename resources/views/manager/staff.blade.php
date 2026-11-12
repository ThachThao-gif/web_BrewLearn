@extends('layouts.app')
@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    @include('manager.banner', ['heading' => 'Hồ sơ nhân viên', 'description' => 'Tiến độ, đánh giá và hạn đánh giá của từng nhân viên.'])
    @include('manager.filter')
    @include('manager.staff-table')

    <details id="phan-ca" class="rounded-2xl border border-coffee-200 bg-white" @if(request()->has('shift_page')) open @endif>
        <summary class="cursor-pointer px-5 py-4 font-bold text-coffee-900">Phân ca & trạng thái làm việc</summary>
        <div class="border-t border-coffee-100 px-5 py-4 space-y-4">
            <form action="{{ route('manager.shifts.create') }}" method="POST" class="flex flex-wrap items-center gap-2">
                @csrf
                <span class="text-xs font-bold text-coffee-700">Tạo ca mới</span>
                <input name="ten_ca" required maxlength="100" aria-label="Tên ca" placeholder="Tên ca, ví dụ Ca sáng" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <input type="time" name="bat_dau" required aria-label="Giờ bắt đầu" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <input type="time" name="ket_thuc" required aria-label="Giờ kết thúc" class="rounded-xl border border-coffee-200 px-3 py-2 text-sm">
                <button class="rounded-xl bg-amber_brand px-4 py-2 text-sm font-bold text-white">Tạo ca</button>
            </form>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-xs text-slate-500"><tr><th class="py-2">Nhân viên</th><th class="py-2">Ca làm việc</th><th class="py-2">Trạng thái làm việc</th></tr></thead>
                    <tbody class="divide-y divide-coffee-100">
                        @foreach($shiftPage as $person)
                            <tr>
                                <td class="py-2 pr-3 font-semibold">{{ $person->name }}</td>
                                <td class="py-2 pr-3">
                                    <form action="{{ route('manager.shifts.assign', $person->id) }}" method="POST" class="flex gap-2">
                                        @csrf @method('PUT')
                                        <select name="ca_lam_viec_id" aria-label="Ca của {{ $person->name }}" class="rounded-xl border border-coffee-200 px-2 py-1.5 text-sm">
                                            <option value="">Chưa phân ca</option>
                                            @foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected($person->ca_lam_viec_id === $shift->id)>{{ $shift->ten_ca }} ({{ substr($shift->bat_dau, 0, 5) }}–{{ substr($shift->ket_thuc, 0, 5) }})</option>@endforeach
                                        </select>
                                        <button class="rounded-xl bg-coffee-100 px-3 py-1.5 text-xs font-bold text-coffee-800">Lưu</button>
                                    </form>
                                </td>
                                <td class="py-2">
                                    <form method="POST" action="{{ route('manager.employment.update', $person->id) }}" class="flex gap-2">
                                        @csrf @method('PUT')
                                        <select id="employment-{{ $person->id }}" name="employment_status" aria-label="Trạng thái của {{ $person->name }}" class="rounded-xl border border-coffee-200 px-2 py-1.5 text-sm">
                                            @foreach(\App\Models\AssessmentReminder::EMPLOYMENT_LABELS as $value => $label)<option value="{{ $value }}" @selected(($person->employment_status ?? 'DangLam') === $value)>{{ $label }}</option>@endforeach
                                        </select>
                                        <button class="rounded-xl bg-coffee-100 px-3 py-1.5 text-xs font-bold text-coffee-800">Lưu</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $shiftPage->links() }}
        </div>
    </details>
</div>
@endsection
