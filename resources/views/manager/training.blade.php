@extends('layouts.app')
@section('content')
@php
    $trainingStates = [
        'ChoGiao' => ['Chờ giao', 'bg-honey-50 text-honey-800 border-honey-200'],
        'DangThucHien' => ['Đang thực hiện', 'bg-coffee-100 text-coffee-700 border-coffee-300'],
        'ChoDanhGiaLai' => ['Chờ đánh giá lại', 'bg-caramel-50 text-caramel-800 border-caramel-200'],
        'HoanTat' => ['Hoàn tất', 'bg-olive-50 text-olive-700 border-olive-200'],
        'DungDaoTao' => ['Dừng đào tạo lại', 'bg-terra-50 text-terra-700 border-terra-200'],
    ];
    $inputClass = 'block w-full mt-1 rounded-xl border border-coffee-200 bg-white px-3 py-2 text-sm';
@endphp
<div class="max-w-6xl mx-auto space-y-5">
    @include('manager.banner', ['heading' => 'Giao đào tạo lại', 'description' => 'Giao vòng, theo dõi tiến độ và tạo lịch đánh giá lại.'])
    @include('manager.filter')

    @if($training->isNotEmpty())
        <div class="flex flex-wrap gap-2 text-xs">
            @foreach($trainingStates as $state => [$label, $class])
                @php
                    $count = $training->where('tien_trinh', $state)->count();
                @endphp
                @if($count)<span class="rounded-full border px-3 py-1 font-semibold {{ $class }}">{{ $label }} · {{ $count }}</span>@endif
            @endforeach
        </div>
    @endif

    @forelse($training as $plan)
        @php
            $employee = $staff->firstWhere('id', $plan->user_id);
            [$stateLabel, $stateClass] = $plan->trang_thai === 'ChoDuyet' ? ['Chờ duyệt đề xuất', 'bg-honey-50 text-honey-800 border-honey-200']
                : ($plan->trang_thai === 'TuChoi' ? ['Đã từ chối', 'bg-stone-100 text-stone-600 border-stone-200'] : ($trainingStates[$plan->tien_trinh] ?? [$plan->trang_thai, 'bg-stone-100 text-stone-600 border-stone-200']));
            $currentRound = $plan->tien_trinh === 'DangThucHien' ? $plan->rounds->last() : null;
            $canAssign = $plan->trang_thai === 'DaDuyet' && ($plan->tien_trinh === 'DangThucHien' || ($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached));
            $stopped = $plan->tien_trinh === 'DungDaoTao' || ($plan->tien_trinh === 'ChoGiao' && $plan->round_limit_reached);
        @endphp
        <article class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
            <header class="flex flex-wrap items-start justify-between gap-3 p-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('manager.profile', $plan->user_id) }}" class="text-lg font-bold text-coffee-900 hover:underline">{{ $employee->name }}</a>
                        <span class="rounded-full border px-2.5 py-0.5 text-xs font-bold {{ $stateClass }}">{{ $stateLabel }}</span>
                        @if($plan->rounds->isNotEmpty())<span class="text-xs text-stone-500">Đã qua {{ $plan->rounds->count() }}/3 vòng</span>@endif
                    </div>
                    <p class="mt-1 text-xs text-stone-400">Yêu cầu #{{ $plan->id }} · {{ $employee->shift_name }} · {{ $plan->nguon_de_xuat ?? ($plan->du_doan_id ? 'Từ cảnh báo học tập' : 'Trưởng ca đề xuất') }}</p>
                    @if($plan->ly_do)<p class="mt-2 text-sm text-stone-600 whitespace-pre-line">{{ $plan->ly_do }}</p>@endif
                </div>
            </header>

            <div class="space-y-3 border-t border-coffee-100 px-5 py-4">
                @if($stopped)
                    <p role="alert" class="rounded-xl bg-terra-50 px-4 py-3 text-sm font-bold text-terra-800">Đã đạt giới hạn 3 vòng đào tạo lại và vẫn chưa đạt. Không giao thêm vòng; quản lý xem xét hướng xử lý.</p>
                @elseif($plan->trang_thai === 'HoanThanh')
                    <p class="rounded-xl bg-olive-50 px-4 py-3 text-sm font-bold text-olive-700">Hoàn tất · Kết quả sau đào tạo: {{ $plan->ket_qua_sau_dao_tao_lai === 'Dat' ? 'Đạt' : $plan->ket_qua_sau_dao_tao_lai }}</p>
                @elseif($plan->tien_trinh === 'ChoDanhGiaLai')
                    <p class="rounded-xl bg-caramel-50 px-4 py-3 text-sm text-caramel-900">Chờ đánh giá #{{ $plan->danh_gia_lai_id }} ngày {{ $plan->ngay_danh_gia_lai ? \Carbon\Carbon::parse($plan->ngay_danh_gia_lai)->format('d/m/Y') : '—' }}. Duyệt kết quả tại <a href="{{ route('manager.assessments') }}" class="font-bold underline">Duyệt đánh giá</a>.</p>
                @elseif($currentRound)
                    <div class="rounded-xl px-4 py-3 text-sm {{ $currentRound->eligibility['ready'] ? 'bg-olive-50 text-olive-800' : 'bg-honey-50 text-honey-900' }}">
                        <p class="font-bold">{{ $currentRound->eligibility['ready'] ? 'Đủ điều kiện tạo lịch đánh giá lại' : 'Chưa đủ điều kiện đánh giá lại' }}</p>
                        @if($currentRound->eligibility['missing'])<ul class="mt-1 list-disc pl-5 text-xs">@foreach($currentRound->eligibility['missing'] as $missing)<li>{{ $missing }}</li>@endforeach</ul>@endif
                    </div>
                @elseif($plan->tien_trinh === 'ChoGiao' && $plan->rounds->isNotEmpty())
                    <p class="rounded-xl bg-honey-50 px-4 py-3 text-sm text-honey-900">Vòng trước chưa đạt. Điều chỉnh nội dung và giao <strong>đào tạo lại lần {{ $plan->next_round_number }}</strong>.</p>
                @endif

                @if($plan->ghi_chu_thuc_hien && $plan->trang_thai !== 'TuChoi' && ! $stopped && $plan->trang_thai !== 'HoanThanh')
                    <div class="text-sm">
                        <p class="text-xs font-bold text-coffee-700">Kế hoạch đang thực hiện</p>
                        <p class="mt-1 whitespace-pre-line text-coffee-900">{{ $plan->ghi_chu_thuc_hien }}</p>
                        <p class="mt-1 text-xs text-stone-500">Phụ trách: {{ $proposalOwners->firstWhere('id', $plan->nguoi_phu_trach_id)?->name ?? 'Chưa xác định' }} · Hạn: {{ $plan->han_hoan_thanh ? \Carbon\Carbon::parse($plan->han_hoan_thanh)->format('d/m/Y') : 'Chưa có' }}</p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-x-6 gap-y-2">
                    @if($plan->ke_hoach)@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($plan->ke_hoach, true), 'muted' => true])@endif
                </div>
                @include('components.retraining-round-history', ['muted' => true])
            </div>

            @if($plan->trang_thai === 'ChoDuyet')
                <footer class="flex flex-wrap gap-3 border-t border-coffee-100 bg-coffee-50/40 px-5 py-4">
                    <form method="POST" action="{{ route('manager.training.review', $plan->id) }}">@csrf<input type="hidden" name="action" value="approve"><button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Duyệt đề xuất</button></form>
                    <form method="POST" action="{{ route('manager.training.review', $plan->id) }}" class="flex flex-1 gap-2">@csrf<input type="hidden" name="action" value="reject"><input required name="reason" aria-label="Lý do từ chối" placeholder="Lý do từ chối..." class="min-w-0 flex-1 rounded-xl border border-coffee-200 bg-white px-3 py-2.5 text-sm"><button class="rounded-xl border border-coffee-300 bg-white px-4 py-2.5 text-sm font-bold text-coffee-700">Từ chối</button></form>
                </footer>
            @endif

            @if($canAssign)
                <details class="border-t border-coffee-100 bg-coffee-50/40" @if($plan->tien_trinh === 'ChoGiao') open @endif>
                    <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-coffee-800">{{ $plan->tien_trinh === 'ChoGiao' ? 'Giao vòng đào tạo lại lần '.$plan->next_round_number : 'Cập nhật kế hoạch vòng hiện tại' }}</summary>
                    <form method="POST" action="{{ route('manager.training.assign', $plan->id) }}" class="space-y-3 px-5 pb-4">
                        @csrf @method('PUT')
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="text-xs font-semibold text-coffee-700">Trưởng ca phụ trách
                                <select required name="nguoi_phu_trach_id" class="{{ $inputClass }}"><option value="">Chọn trưởng ca</option>@foreach($trainingOwners->where('id', '!=', $plan->user_id) as $owner)<option value="{{ $owner->id }}" @selected($plan->nguoi_phu_trach_id == $owner->id)>{{ $owner->name }}</option>@endforeach</select>
                            </label>
                            <label class="text-xs font-semibold text-coffee-700">Hạn hoàn thành<input required type="date" name="han_hoan_thanh" min="{{ today()->toDateString() }}" value="{{ $plan->han_hoan_thanh }}" class="{{ $inputClass }}"></label>
                            <label class="text-xs font-semibold text-coffee-700">Ngày dự kiến đánh giá lại<input required type="date" name="ngay_danh_gia_lai" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="{{ $inputClass }}"></label>
                        </div>
                        <label class="block text-xs font-semibold text-coffee-700">Nội dung thực hiện<textarea required name="ghi_chu_thuc_hien" rows="3" maxlength="5000" placeholder="Kỹ năng cần cải thiện, bài cần học lại, cách thực hành và tiêu chí hoàn thành..." class="{{ $inputClass }}">{{ $plan->ghi_chu_thuc_hien }}</textarea></label>
                        <button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Lưu & giao đào tạo lại</button>
                    </form>
                </details>
            @endif

            @if($plan->tien_trinh === 'DangThucHien' && $plan->trang_thai === 'DaDuyet')
                <details class="border-t border-coffee-100">
                    <summary class="cursor-pointer px-5 py-3 text-sm font-bold text-coffee-800">Tạo lịch đánh giá lại</summary>
                    <form method="POST" action="{{ route('manager.training.reassess', $plan->id) }}" class="space-y-3 px-5 pb-4">
                        @csrf
                        <div class="grid gap-3 md:grid-cols-3">
                            <label class="text-xs font-semibold text-coffee-700">Trưởng ca đánh giá
                                <select required name="assessor_id" class="{{ $inputClass }}"><option value="">Chọn trưởng ca</option>@foreach($proposalOwners as $owner)@if(in_array(mb_strtolower(trim($owner->ChucVu ?? '')), ['trưởng ca', 'truong_ca', 'leader']))<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endif @endforeach</select>
                            </label>
                            <label class="text-xs font-semibold text-coffee-700">Ngày đánh giá<input required type="date" name="assessment_date" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="{{ $inputClass }}"></label>
                            <label class="text-xs font-semibold text-coffee-700">Giờ đánh giá<input required type="time" name="assessment_time" value="09:00" class="{{ $inputClass }}"></label>
                        </div>
                        <label class="block text-xs font-semibold text-coffee-700">Nội dung cần kiểm tra lại<textarea required name="criteria" rows="2" maxlength="2000" class="{{ $inputClass }}" placeholder="Nội dung trưởng ca cần kiểm tra sau đào tạo..."></textarea></label>
                        <button @disabled(! ($currentRound?->eligibility['ready'] ?? false)) class="rounded-xl bg-coffee-800 px-5 py-2.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Tạo lịch đánh giá lại</button>
                    </form>
                </details>
            @endif
        </article>
    @empty
        <div class="rounded-2xl border border-coffee-200 bg-white p-10 text-center text-stone-500">Chưa có yêu cầu đào tạo lại. Yêu cầu xuất hiện khi đánh giá chưa đạt được duyệt.</div>
    @endforelse
</div>
@endsection
