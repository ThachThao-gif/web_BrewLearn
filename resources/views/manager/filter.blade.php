@if(session('success'))<div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800"><p class="font-bold">Vui lòng kiểm tra thông tin:</p><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="GET" class="flex flex-1 flex-wrap items-center gap-2">
    @isset($statusTab)<input type="hidden" name="status" value="{{ $statusTab }}">@endisset
    <select name="shift" aria-label="Ca làm việc" class="rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm">
        <option value="">Toàn cửa hàng</option>
        <option value="none" @selected(request('shift') === 'none')>Chưa phân ca</option>
        @foreach($shifts as $shift)<option value="{{ $shift->id }}" @selected((string) request('shift') === (string) $shift->id)>{{ $shift->ten_ca }}</option>@endforeach
    </select>
    <input name="q" value="{{ request('q') }}" aria-label="Tìm nhân viên" placeholder="Tìm nhân viên..." class="min-w-40 flex-1 rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm">
    <button class="rounded-xl bg-amber_brand px-4 py-2.5 text-sm font-bold text-white">Lọc</button>
    @if(request()->filled('shift') || request()->filled('q'))
        <a href="{{ isset($statusTab) ? route('manager.assessments', ['status' => $statusTab]) : url()->current() }}" class="px-1 text-sm text-coffee-600 hover:underline">Xóa lọc</a>
    @endif
</form>
