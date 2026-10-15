@extends('layouts.app')
@section('content')
<style>
    .training-page { color: #3e342b; }
    .training-page > .relative { border-radius: 20px; padding: 24px 28px; box-shadow: 0 6px 18px #49321512; }
    .training-page h1 { letter-spacing: -.035em; }
    .training-page > form { box-shadow: 0 2px 8px #49321504; padding: 20px; }
    .training-page > article { padding: 26px; border-color: #e9e3d9; border-radius: 20px; box-shadow: 0 4px 20px #49321506; }
    .training-page > article > div:first-child { padding-bottom: 18px; border-bottom: 1px solid #f0ebe4; align-items: center; }
    .training-page > article > div:first-child a { font-size: 20px; letter-spacing: -.025em; }
    .training-page > article > div:first-child > span { background: #f5efe4; color: #795722; border: 1px solid #e9ddc7; }
    .training-page > article > p.whitespace-pre-line { line-height: 1.75; color: #61564a; }
    .training-page article > details[open] { background: #fcfbf8; }
    .training-page article > details { border-color: #e9e3d9; padding: 18px; border-radius: 14px; }
    .training-page summary { border-radius: 6px; line-height: 1.6; }
    .training-page summary:focus-visible { outline: 2px solid #bd7b28; outline-offset: 4px; }
    .training-page article > details > div > div { background: white; border: 1px solid #ece6dd; border-left: 3px solid #c4ad88; padding: 20px; }
    .training-page article > details > div > .bg-rose-50 { border-left-color: #d58d8d; }
    .training-page article > details > div > .bg-emerald-50 { border-left-color: #69a98b; }
    .training-page article > details > div > div > div:first-child > span { font-size: 12px; font-weight: 600; border-radius: 999px; padding: 4px 10px; background: #f5f1eb; color: #786650; }
    .training-page article > details > div > .bg-rose-50 > div:first-child > span { background: #fcf0f0; color: #a14e4e; }
    .training-page article > details > div > .bg-emerald-50 > div:first-child > span { background: #edf7f1; color: #357657; }
    .training-page article > details > div > div > p { line-height: 1.7; }
    .training-page details details > div { max-width: none; display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr)); gap: 12px; }
    .training-page details details > div > div { margin-top: 0; background: #faf7ef; border-color: #eee5d2; padding: 16px; line-height: 1.7; }
    .training-page article > form { padding: 22px; background: #faf9f6; border-color: #e9e3d9; }
    .training-page input, .training-page select, .training-page textarea { background-color: white; color: #493c2f; border-color: #ded5c6; transition: border-color .15s, box-shadow .15s; }
    .training-page input:focus, .training-page select:focus, .training-page textarea:focus { outline: none; border-color: #b78540; box-shadow: 0 0 0 3px #b785401a; }
    .training-page button { transition: filter .15s, box-shadow .15s; }
    .training-page button:hover:not(:disabled) { filter: brightness(.95); box-shadow: 0 3px 8px #49321512; }
    .training-page button:focus-visible, .training-page a:focus-visible { outline: 2px solid #bd7b28; outline-offset: 3px; }
    @media (max-width: 640px) {
        .training-page > .relative, .training-page > article { padding: 20px; }
        .training-page > form > label { width: 100%; }
        .training-page > form select { width: 100%; }
        .training-page article > details, .training-page article > form { padding: 14px; }
        .training-page article > details > div > div { padding: 14px; }
    }
</style>
<div class="training-page max-w-7xl mx-auto space-y-6">
@include('manager.banner', ['heading' => 'Giao & theo dõi đào tạo lại', 'description' => 'Duyệt đề xuất, xác định người phụ trách, hạn hoàn thành và tổ chức đánh giá lại.'])
@include('manager.filter')
@if($errors->any())<div role="alert" class="bg-rose-50 text-rose-800 p-4 rounded-xl">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@php($trainingStates = ['ChoGiao' => 'Chờ giao đào tạo', 'DangThucHien' => 'Đang thực hiện', 'ChoDanhGiaLai' => 'Chờ đánh giá lại', 'HoanTat' => 'Hoàn tất', 'DungDaoTao' => 'Dừng đào tạo lại (đủ 3 vòng)'])
@forelse($training as $plan)
@php($employee = $staff->firstWhere('id', $plan->user_id))
<article class="rounded-2xl bg-white border border-coffee-200 p-5 space-y-4"><div class="flex flex-wrap justify-between gap-3"><div><a href="{{ route('manager.profile', $plan->user_id) }}" class="font-bold text-lg text-coffee-900">{{ $employee->name }}</a><p class="text-xs text-slate-500 mt-1">Kế hoạch #{{ $plan->id }} • {{ $employee->shift_name }} • {{ $plan->nguon_de_xuat ?? ($plan->du_doan_id ? 'Model đề xuất' : 'Trưởng ca đề xuất') }}</p></div><span class="text-xs font-bold h-fit bg-coffee-100 rounded-full px-3 py-2">{{ $plan->trang_thai === 'ChoDuyet' ? 'Chờ duyệt đề xuất' : ($plan->trang_thai === 'TuChoi' ? 'Đã từ chối' : ($trainingStates[$plan->tien_trinh] ?? $plan->trang_thai)) }}</span></div><p class="text-sm whitespace-pre-line">{{ $plan->ly_do }}</p>
@if($plan->ke_hoach)@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($plan->ke_hoach, true)])@endif
@include('components.retraining-round-history')
@if($plan->tien_trinh === 'DungDaoTao' || ($plan->tien_trinh === 'ChoGiao' && $plan->round_limit_reached))<p role="alert" class="rounded-xl bg-rose-50 text-rose-800 p-4 text-sm font-bold">Đã đạt giới hạn 3 vòng đào tạo lại và vẫn chưa đạt. Dừng giao vòng mới; quản lý cần xem xét hướng xử lý tiếp theo.</p>@endif
@if($plan->tien_trinh === 'DangThucHien')
@php($currentRound = $plan->rounds->last())
<div class="p-4 rounded-xl {{ $currentRound?->eligibility['ready'] ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-900' }} text-sm">
<p class="font-bold">{{ $currentRound?->eligibility['ready'] ? 'Đủ điều kiện tạo lịch đánh giá lại' : 'Chưa đủ điều kiện đánh giá lại' }}</p>
@foreach($currentRound?->eligibility['missing'] ?? [] as $missing)<p class="mt-1">{{ $missing }}</p>@endforeach
<p class="text-xs mt-2">Người phụ trách đăng nhập và xác nhận tại <a href="{{ route('training.assignments') }}" class="underline font-bold">Đào tạo lại tôi phụ trách</a>.</p>
</div>
@endif
@if($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached && $plan->rounds->isNotEmpty())<p class="rounded-xl bg-amber-50 text-amber-900 p-4 text-sm">Vòng trước chưa đạt. Điều chỉnh nội dung bên dưới và giao <strong>đào tạo lại lần {{ $plan->next_round_number }}</strong>. Lịch sử các vòng trước được giữ nguyên.</p>@endif
@if($plan->ghi_chu_thuc_hien && $plan->trang_thai !== 'TuChoi')
<div class="bg-coffee-50 border border-coffee-100 rounded-xl p-4 text-sm"><p class="font-bold text-coffee-900">Kế hoạch đã giao</p><p class="mt-2 whitespace-pre-line">{{ $plan->ghi_chu_thuc_hien }}</p><p class="mt-2 text-xs text-slate-500">Phụ trách: {{ $proposalOwners->firstWhere('id', $plan->nguoi_phu_trach_id)?->name ?? 'Chưa xác định' }} • Hạn hoàn thành: {{ $plan->han_hoan_thanh ? \Carbon\Carbon::parse($plan->han_hoan_thanh)->format('d/m/Y') : 'Chưa có' }}</p></div>
@endif
@if($plan->trang_thai === 'ChoDuyet')<div class="flex flex-wrap gap-3"><form method="POST" action="{{ route('manager.training.review', $plan->id) }}">@csrf<input type="hidden" name="action" value="approve"><button class="bg-amber_brand text-white rounded-xl px-5 py-2.5 text-sm font-bold">Duyệt đề xuất</button></form><form method="POST" action="{{ route('manager.training.review', $plan->id) }}" class="flex flex-wrap gap-2">@csrf<input type="hidden" name="action" value="reject"><input required name="reason" placeholder="Lý do từ chối" class="border border-coffee-300 rounded-xl p-2.5 text-sm"><button class="border border-coffee-300 rounded-xl px-4 py-2.5 text-sm">Từ chối</button></form></div>@endif
@if($plan->trang_thai === 'DaDuyet' && ($plan->tien_trinh === 'DangThucHien' || ($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached)))
<form method="POST" action="{{ route('manager.training.assign', $plan->id) }}" class="p-4 rounded-xl bg-coffee-50 border border-coffee-100 space-y-3">@csrf @method('PUT')<h3 class="font-bold text-sm text-coffee-900">{{ $plan->tien_trinh === 'ChoGiao' ? 'Giao kế hoạch thực hiện' : 'Cập nhật kế hoạch' }}</h3><div class="grid md:grid-cols-3 gap-3"><label class="text-xs font-semibold">Trưởng ca phụ trách<select required name="nguoi_phu_trach_id" class="block w-full mt-1 border border-coffee-300 rounded-xl p-2.5 bg-white"><option value="">Chọn trưởng ca phụ trách</option>@foreach($trainingOwners->where('id', '!=', $plan->user_id) as $owner)<option value="{{ $owner->id }}" @selected($plan->nguoi_phu_trach_id == $owner->id)>{{ $owner->name }} • {{ $owner->ChucVu }}</option>@endforeach</select></label><label class="text-xs font-semibold">Hạn hoàn thành<input required type="date" name="han_hoan_thanh" min="{{ today()->toDateString() }}" value="{{ $plan->han_hoan_thanh }}" class="block w-full mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-semibold">Ngày dự kiến đánh giá lại<input required type="date" name="ngay_danh_gia_lai" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="block w-full mt-1 border border-coffee-300 rounded-xl p-2.5"></label></div><label class="block text-xs font-semibold">Nội dung & cách thực hiện đã chốt<textarea required name="ghi_chu_thuc_hien" rows="3" maxlength="5000" placeholder="Ghi rõ kỹ năng cần cải thiện, bài cần học lại, cách thực hành / theo dõi và tiêu chí hoàn thành..." class="block w-full mt-1 border border-coffee-300 rounded-xl p-3 text-sm">{{ $plan->ghi_chu_thuc_hien }}</textarea></label><p class="text-xs text-slate-500">Đối chiếu nội dung trưởng ca đề xuất ở trên; thông báo giao việc gửi cho nhân viên và người phụ trách.</p><button class="bg-amber_brand text-white rounded-xl px-5 py-2.5 font-bold text-sm">Lưu & giao đào tạo lại</button></form>
@endif
@if($plan->tien_trinh === 'DangThucHien' && $plan->trang_thai === 'DaDuyet')<details class="border-t border-coffee-100 pt-4"><summary class="cursor-pointer text-sm font-bold text-coffee-800">Đã thực hiện xong → Tạo lịch đánh giá lại</summary><form method="POST" action="{{ route('manager.training.reassess', $plan->id) }}" class="mt-3 space-y-3">@csrf<div class="grid md:grid-cols-3 gap-3"><label class="text-xs font-semibold">Trưởng ca đánh giá<select required name="assessor_id" class="block w-full border border-coffee-300 rounded-xl p-2.5 mt-1"><option value="">Chọn trưởng ca</option>@foreach($proposalOwners as $owner)@if(in_array(mb_strtolower(trim($owner->ChucVu ?? '')), ['trưởng ca', 'truong_ca', 'leader']))<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endif
@endforeach</select></label><label class="text-xs font-semibold">Ngày đánh giá<input required type="date" name="assessment_date" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="block w-full mt-1 border border-coffee-300 rounded-xl p-2.5"></label><label class="text-xs font-semibold">Giờ đánh giá<input required type="time" name="assessment_time" value="09:00" class="block w-full mt-1 border border-coffee-300 rounded-xl p-2.5"></label></div><label class="block text-xs font-semibold">Tiêu chí / nội dung cần kiểm tra lại<textarea required name="criteria" rows="2" maxlength="2000" class="block w-full mt-1 border border-coffee-300 rounded-xl p-3" placeholder="Nội dung cần trưởng ca kiểm tra sau đào tạo..."></textarea></label><button @disabled(!($currentRound?->eligibility['ready'] ?? false)) class="rounded-xl bg-coffee-800 text-white px-5 py-2.5 font-bold text-sm disabled:opacity-40 disabled:cursor-not-allowed">Tạo lịch đánh giá lại</button></form></details>@endif
@if($plan->tien_trinh === 'ChoDanhGiaLai')<div class="rounded-xl bg-amber-50 p-4 text-sm text-amber-900">Đang chờ đánh giá #{{ $plan->danh_gia_lai_id }} ngày {{ $plan->ngay_danh_gia_lai }}. Sau khi trưởng ca chấm điểm, quản lý duyệt tại mục <a href="{{ route('manager.assessments') }}" class="underline font-bold">Duyệt đánh giá</a>.</div>@endif
@if($plan->trang_thai === 'HoanThanh')<p class="rounded-xl bg-emerald-50 text-emerald-700 p-4 text-sm font-bold">Hoàn tất • Kết quả sau đào tạo: {{ $plan->ket_qua_sau_dao_tao_lai === 'Dat' ? 'Đạt' : $plan->ket_qua_sau_dao_tao_lai }}</p>@endif
</article>
@empty<div class="rounded-2xl bg-white border border-coffee-200 text-center p-10 text-slate-500">Chưa có đề xuất đào tạo lại. Đề xuất của trưởng ca xuất hiện sau khi đánh giá chưa đạt được duyệt.</div>@endforelse
</div>
@endsection
