<form method="GET" class="flex flex-wrap items-center gap-3 rounded-2xl border border-coffee-200 bg-white p-4 shadow-sm">
    @isset($statusTab)<input type="hidden" name="status" value="{{ $statusTab }}">@endisset
    <label class="relative">
        <span class="sr-only">Ca làm việc</span>
        <i class="fa-solid fa-store pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-coffee-500" aria-hidden="true"></i>
        <select name="shift" aria-label="Ca làm việc" class="min-w-48 rounded-xl border border-coffee-200 bg-white py-2.5 pl-9 pr-8 text-sm text-coffee-900">
            <option value="">Toàn cửa hàng</option>
            <option value="none" @selected(request('shift') === 'none')>Chưa phân ca</option>
            @foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected((string) request('shift') === (string) $shift->id)>{{ $shift->ten_ca }}</option>@endforeach
        </select>
    </label>
    <label class="relative min-w-40 flex-1">
        <span class="sr-only">Tìm nhân viên</span>
        <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
        <input name="q" value="{{ request('q') }}" aria-label="Tìm nhân viên" placeholder="Tìm nhân viên..." class="w-full rounded-xl border border-coffee-200 bg-white py-2.5 pl-9 pr-3 text-sm">
    </label>
    <button class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-amber-700"><i class="fa-solid fa-filter text-xs" aria-hidden="true"></i>Lọc</button>
    @if(request()->filled('shift') || request()->filled('q'))
        <a href="{{ isset($statusTab) ? route('manager.assessments', ['status' => $statusTab]) : url()->current() }}" class="px-1 text-sm text-coffee-600 hover:underline">Xóa lọc</a>
    @endif
</form>
