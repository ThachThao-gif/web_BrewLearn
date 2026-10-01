<div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-coffee-900 via-coffee-800 to-coffee-700 px-6 py-7 md:px-8 text-white shadow-lg">
    <div class="absolute -right-5 -top-8 h-48 w-48 rounded-full border-[24px] border-white/5 pointer-events-none"></div>
    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-5">
        <div><p class="text-[10px] font-bold uppercase tracking-[.2em] text-coffee-300 mb-2"><i class="fa-solid fa-mug-hot mr-2"></i>BrewLearn • Quản lý cửa hàng</p><h1 class="text-2xl md:text-3xl font-extrabold">{{ $heading }}</h1><p class="text-sm text-coffee-200 mt-2 max-w-2xl">{{ $description }}</p></div>
        <div class="bg-white/10 rounded-2xl border border-white/10 p-4 text-sm shrink-0"><p class="font-bold">{{ $store->ten_cua_hang ?? auth()->user()->ChiNhanh }}</p><p class="text-coffee-200 text-xs mt-1">{{ auth()->user()->name }} • {{ now()->format('d/m/Y') }}</p></div>
    </div>
</div>
@if(session('success'))<div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 p-4 text-sm">{{ session('success') }}</div>@endif
@if($errors->any())<div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 p-4 text-sm"><p class="font-bold">Vui lòng kiểm tra thông tin:</p><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
