@extends('layouts.app')
@section('content')
@php
    $trainingStates = [
        'ChoGiao' => ['Chờ giao', 'border-amber-300 bg-amber-50 text-amber-800', 'text-amber-600', 'bg-amber-500'],
        'DangThucHien' => ['Đang thực hiện', 'border-blue-200 bg-blue-50 text-blue-700', 'text-blue-600', 'bg-blue-500'],
        'ChoDanhGiaLai' => ['Chờ đánh giá lại', 'border-violet-200 bg-violet-50 text-violet-700', 'text-violet-600', 'bg-violet-500'],
        'HoanTat' => ['Hoàn tất', 'border-emerald-200 bg-emerald-50 text-emerald-700', 'text-emerald-600', 'bg-emerald-500'],
        'DungDaoTao' => ['Dừng đào tạo lại', 'border-rose-200 bg-rose-50 text-rose-700', 'text-rose-600', 'bg-rose-500'],
    ];
    $otherStates = [
        'ChoDuyet' => ['Chờ duyệt đề xuất', 'border-amber-300 bg-amber-50 text-amber-800', 'text-amber-600', 'bg-amber-500'],
        'TuChoi' => ['Đã từ chối', 'border-slate-200 bg-slate-50 text-slate-600', 'text-slate-500', 'bg-slate-400'],
    ];
    $stateFilter = array_key_exists(request('state'), $trainingStates) ? request('state') : null;
    $visible = $stateFilter ? $training->where('display_state', $stateFilter) : $training;
    $filters = request()->only('shift', 'q');
    $inputClass = 'block w-full mt-1 rounded-xl border border-coffee-200 bg-white px-3 py-2 text-sm';
    $row = 'grid grid-cols-[9rem_1fr] gap-3 px-4 py-3';
    $sectionLabel = 'text-[10px] font-semibold uppercase tracking-wider text-amber-700';
    $grid = 'md:grid md:grid-cols-[3rem_minmax(0,2fr)_4.5rem_minmax(0,1.5fr)_6.5rem_7rem_8.5rem_2rem] md:items-center md:gap-3';
    $date = fn ($value) => $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '—';
@endphp
<div class="max-w-6xl mx-auto space-y-5">
    @include('manager.banner', ['heading' => 'Giao đào tạo lại', 'description' => 'Giao vòng, theo dõi tiến độ và tạo lịch đánh giá lại.'])
    @include('manager.filter')

    <section class="rounded-2xl border border-coffee-200 bg-white p-5 shadow-sm" aria-labelledby="training-summary">
        <div class="flex items-center justify-between">
            <h2 id="training-summary" class="flex items-center gap-2 text-sm font-bold text-coffee-900"><span class="h-4 w-1.5 rounded-full bg-amber-600" aria-hidden="true"></span>Tóm lược đào tạo lại</h2>
            @if($stateFilter)
                <a href="{{ route('manager.training', $filters) }}" class="text-xs font-semibold text-amber-700 hover:underline">Xem tất cả</a>
            @else
                <span class="text-[11px] text-slate-400">Bấm vào ô để lọc</span>
            @endif
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-5">
            @foreach($trainingStates as $state => [$label, $badge, $tone, $dot])
                <a href="{{ route('manager.training', array_merge($filters, ['state' => $state])) }}" @if($stateFilter === $state) aria-current="page" @endif
                   class="relative block rounded-xl border px-4 py-4 transition hover:shadow-sm {{ $badge }} {{ $stateFilter === $state ? 'ring-2 ring-offset-1 ring-current' : '' }}">
                    <span class="absolute right-4 top-1/2 h-1.5 w-1.5 -translate-y-1/2 rounded-full {{ $dot }}" aria-hidden="true"></span>
                    <span class="block text-3xl font-bold {{ $tone }}">{{ $training->where('display_state', $state)->count() }}</span>
                    <span class="mt-1 block text-xs font-medium text-coffee-900">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-coffee-200 bg-white shadow-sm" aria-label="Danh sách yêu cầu đào tạo lại">
        <p class="flex items-center gap-2 px-5 py-4 text-sm font-bold text-coffee-900">{{ $stateFilter ? $trainingStates[$stateFilter][0] : 'Tất cả yêu cầu' }} <span class="rounded-full bg-coffee-100 px-2 py-0.5 text-xs font-semibold text-coffee-700">{{ $visible->count() }}</span></p>
        <div class="hidden {{ $grid }} border-y border-coffee-100 bg-coffee-50 px-5 py-3 text-xs font-semibold text-coffee-700">
            <span>STT</span><span>Nhân viên</span><span>Vòng</span><span>Phụ trách</span><span>Hạn</span><span>Đánh giá lại</span><span>Tiến trình</span><span class="sr-only">Xem</span>
        </div>

        @forelse($visible as $plan)
            @php
                $employee = $staff->firstWhere('id', $plan->user_id);
                [$stateLabel, $stateClass, , $stateDot] = $otherStates[$plan->trang_thai] ?? $trainingStates[$plan->display_state] ?? [$plan->trang_thai, 'border-slate-200 bg-slate-50 text-slate-600', '', 'bg-slate-400'];
                $currentRound = $plan->tien_trinh === 'DangThucHien' ? $plan->rounds->last() : null;
                $canAssign = $plan->trang_thai === 'DaDuyet' && ($plan->tien_trinh === 'DangThucHien' || ($plan->tien_trinh === 'ChoGiao' && ! $plan->round_limit_reached));
                $stopped = $plan->display_state === 'DungDaoTao';
                $owner = $proposalOwners->firstWhere('id', $plan->nguoi_phu_trach_id)?->name;
                $dialogId = 'training-'.$plan->id;
                $open = "document.getElementById('".$dialogId."').showModal()";
                $initials = collect(explode(' ', trim($employee->name)))->slice(-2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
            @endphp
            <div class="{{ $grid }} grid grid-cols-2 gap-x-3 gap-y-2 border-t border-coffee-100 px-5 py-3.5 text-sm hover:bg-coffee-50/70">
                <span class="text-slate-500">{{ $loop->iteration }}</span>
                <span class="flex min-w-0 items-center gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-coffee-200 bg-coffee-100 text-xs font-bold text-coffee-800" aria-hidden="true">{{ $initials }}</span>
                    <span class="min-w-0"><span class="block truncate font-bold text-coffee-900">{{ $employee->name }}</span><span class="text-xs text-slate-400">{{ $employee->ChucVu }} • {{ $employee->shift_name }}</span></span>
                </span>
                <span><span class="rounded-md border border-coffee-200 bg-coffee-50 px-2 py-0.5 text-xs font-bold text-coffee-800">{{ $plan->rounds->count() }}/3</span></span>
                <span class="truncate text-coffee-900">{{ $owner ?? '—' }}</span>
                <span class="text-coffee-900">{{ $date($plan->han_hoan_thanh) }}</span>
                <span class="text-coffee-900">{{ $date($plan->ngay_danh_gia_lai) }}</span>
                <span><button type="button" onclick="{{ $open }}" aria-haspopup="dialog" title="Xem chi tiết và xử lý" class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold hover:shadow-sm {{ $stateClass }}"><span class="h-1.5 w-1.5 rounded-full {{ $stateDot }}" aria-hidden="true"></span>{{ $stateLabel }}</button></span>
                <button type="button" onclick="{{ $open }}" aria-label="Xem chi tiết yêu cầu {{ $plan->id }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-coffee-200 text-slate-500 hover:bg-coffee-50 hover:text-coffee-800"><i class="fa-regular fa-eye text-xs"></i></button>
            </div>

            <dialog id="{{ $dialogId }}" aria-labelledby="{{ $dialogId }}-title" class="w-[min(820px,calc(100vw-2rem))] max-h-[90vh] rounded-2xl p-0 shadow-2xl backdrop:bg-coffee-900/40">
                <div class="flex items-start justify-between gap-3 border-b border-coffee-100 px-6 py-4">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 id="{{ $dialogId }}-title" class="text-lg font-bold text-coffee-900">Đào tạo lại {{ $employee->name }}</h2>
                            <span class="inline-flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-[11px] font-semibold {{ $stateClass }}"><span class="h-1.5 w-1.5 rounded-full {{ $stateDot }}" aria-hidden="true"></span>{{ $stateLabel }}</span>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $employee->ChucVu }} • {{ $employee->shift_name }} •
                            <a href="{{ route('manager.profile', $plan->user_id) }}" class="font-semibold text-coffee-800 underline underline-offset-2 hover:text-amber-700">Xem hồ sơ <i class="fa-solid fa-arrow-up-right-from-square text-[9px]" aria-hidden="true"></i></a>
                        </p>
                    </div>
                    <form method="dialog"><button aria-label="Đóng" class="rounded-lg px-2 py-1 text-slate-400 hover:bg-coffee-50 hover:text-coffee-800"><i class="fa-solid fa-xmark"></i></button></form>
                </div>
                <div class="space-y-4 px-6 py-5">
                    @if($stopped)
                        <p role="alert" class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>Đã đạt giới hạn 3 vòng đào tạo lại và vẫn chưa đạt. Không giao thêm vòng.</p>
                    @elseif($plan->tien_trinh === 'ChoDanhGiaLai')
                        <p class="rounded-xl bg-violet-50 px-4 py-3 text-sm text-violet-900">Chờ đánh giá lại ngày {{ $date($plan->ngay_danh_gia_lai) }}. Duyệt kết quả tại <a href="{{ route('manager.assessments') }}" class="font-bold underline">Duyệt đánh giá</a>.</p>
                    @elseif($currentRound)
                        <div class="rounded-xl px-4 py-3 text-sm {{ $currentRound->eligibility['ready'] ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-900' }}">
                            <p class="font-bold">{{ $currentRound->eligibility['ready'] ? 'Đủ điều kiện tạo lịch đánh giá lại' : 'Chưa đủ điều kiện đánh giá lại' }}</p>
                            @if($currentRound->eligibility['missing'])<ul class="mt-1 list-disc pl-5 text-xs">@foreach($currentRound->eligibility['missing'] as $missing)<li>{{ $missing }}</li>@endforeach</ul>@endif
                        </div>
                    @elseif($plan->tien_trinh === 'ChoGiao' && $plan->rounds->isNotEmpty())
                        <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-900">Vòng trước chưa đạt. Điều chỉnh nội dung và giao <strong>đào tạo lại lần {{ $plan->next_round_number }}</strong>.</p>
                    @endif

                    <dl class="grid grid-cols-2 gap-3 text-sm md:grid-cols-4">
                        @foreach([
                            'Số vòng' => $plan->rounds->count().'/3',
                            'Phụ trách' => $owner ?? ($plan->tien_trinh === 'ChoGiao' ? 'Chưa giao' : 'Chưa xác định'),
                            'Hạn hoàn thành' => $plan->han_hoan_thanh ? $date($plan->han_hoan_thanh) : ($plan->tien_trinh === 'ChoGiao' ? 'Chưa giao' : '—'),
                            'Đánh giá lại' => $plan->ngay_danh_gia_lai ? $date($plan->ngay_danh_gia_lai) : ($plan->tien_trinh === 'ChoGiao' ? 'Chưa giao' : '—'),
                        ] as $label => $value)
                            <div class="rounded-xl border border-slate-200 bg-white px-3 py-2.5">
                                <dt class="{{ $sectionLabel }}">{{ $label }}</dt>
                                <dd class="mt-1 truncate font-bold text-coffee-900 {{ $loop->first ? 'text-lg' : '' }}">{{ $value }}@if($loop->first && $plan->rounds->count() >= 3) <span class="text-xs font-semibold text-rose-600">Tối đa</span>@endif</dd>
                            </div>
                        @endforeach
                    </dl>

                    @include('components.retraining-round-history')

                    <dl class="divide-y divide-slate-100 rounded-xl border border-slate-200 text-sm">
                        @if($plan->ly_do)
                            <div class="{{ $row }}"><dt class="{{ $sectionLabel }} pt-0.5">Lý do</dt><dd class="whitespace-pre-line text-coffee-900">{{ $plan->ly_do }}</dd></div>
                        @endif
                        @if($plan->ghi_chu_thuc_hien && $plan->trang_thai !== 'TuChoi')
                            <div class="{{ $row }}"><dt class="{{ $sectionLabel }} pt-0.5">Kế hoạch đã giao</dt><dd class="whitespace-pre-line text-coffee-900">{{ $plan->ghi_chu_thuc_hien }}</dd></div>
                        @endif
                        @if($plan->trang_thai === 'HoanThanh')
                            <div class="{{ $row }}"><dt class="{{ $sectionLabel }} pt-0.5">Kết quả</dt><dd class="font-bold text-emerald-700">{{ $plan->ket_qua_sau_dao_tao_lai === 'Dat' ? 'Đạt' : $plan->ket_qua_sau_dao_tao_lai }}</dd></div>
                        @endif
                        <div class="{{ $row }}"><dt class="{{ $sectionLabel }} pt-0.5">Nguồn</dt><dd class="text-coffee-900">{{ $plan->nguon_de_xuat ?? ($plan->du_doan_id ? 'Từ cảnh báo học tập' : 'Trưởng ca đề xuất') }}
                            @if($plan->ke_hoach)@include('components.assessment-improvement-summary', ['savedProposal' => json_decode($plan->ke_hoach, true)])@endif
                        </dd></div>
                    </dl>

                    @if($canAssign)
                        <details class="rounded-xl border border-coffee-200" @if($plan->tien_trinh === 'ChoGiao') open @endif>
                            <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-coffee-800">{{ $plan->tien_trinh === 'ChoGiao' ? 'Giao vòng đào tạo lại lần '.$plan->next_round_number : 'Cập nhật kế hoạch vòng hiện tại' }}</summary>
                            <form method="POST" action="{{ route('manager.training.assign', $plan->id) }}" class="space-y-3 border-t border-coffee-100 px-4 py-4">
                                @csrf @method('PUT')
                                <div class="grid gap-3 md:grid-cols-3">
                                    <label class="text-xs font-semibold text-coffee-700">Trưởng ca phụ trách
                                        <select required name="nguoi_phu_trach_id" class="{{ $inputClass }}"><option value="">Chọn trưởng ca</option>@foreach($trainingOwners->where('id', '!=', $plan->user_id) as $candidate)<option value="{{ $candidate->id }}" @selected($plan->nguoi_phu_trach_id == $candidate->id)>{{ $candidate->name }}</option>@endforeach</select>
                                    </label>
                                    <label class="text-xs font-semibold text-coffee-700">Hạn hoàn thành<input required type="date" name="han_hoan_thanh" min="{{ today()->toDateString() }}" value="{{ $plan->han_hoan_thanh }}" class="{{ $inputClass }}"></label>
                                    <label class="text-xs font-semibold text-coffee-700">Ngày dự kiến đánh giá lại<input required type="date" name="ngay_danh_gia_lai" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="{{ $inputClass }}"></label>
                                </div>
                                <label class="block text-xs font-semibold text-coffee-700">Nội dung thực hiện<textarea required name="ghi_chu_thuc_hien" rows="3" maxlength="5000" placeholder="Kỹ năng cần cải thiện, bài cần học lại, cách thực hành và tiêu chí hoàn thành..." class="{{ $inputClass }}">{{ $plan->ghi_chu_thuc_hien }}</textarea></label>
                                <div class="flex justify-end"><button class="rounded-xl bg-amber_brand px-5 py-2.5 text-sm font-bold text-white">Lưu & giao đào tạo lại</button></div>
                            </form>
                        </details>
                    @endif

                    @if($plan->tien_trinh === 'DangThucHien' && $plan->trang_thai === 'DaDuyet')
                        <details class="rounded-xl border border-coffee-200">
                            <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-coffee-800">Tạo lịch đánh giá lại</summary>
                            <form method="POST" action="{{ route('manager.training.reassess', $plan->id) }}" class="space-y-3 border-t border-coffee-100 px-4 py-4">
                                @csrf
                                <div class="grid gap-3 md:grid-cols-3">
                                    <label class="text-xs font-semibold text-coffee-700">Trưởng ca đánh giá
                                        <select required name="assessor_id" class="{{ $inputClass }}"><option value="">Chọn trưởng ca</option>@foreach($proposalOwners as $candidate)@if(in_array(mb_strtolower(trim($candidate->ChucVu ?? '')), ['trưởng ca', 'truong_ca', 'leader']))<option value="{{ $candidate->id }}">{{ $candidate->name }}</option>@endif @endforeach</select>
                                    </label>
                                    <label class="text-xs font-semibold text-coffee-700">Ngày đánh giá<input required type="date" name="assessment_date" min="{{ today()->toDateString() }}" value="{{ $plan->ngay_danh_gia_lai }}" class="{{ $inputClass }}"></label>
                                    <label class="text-xs font-semibold text-coffee-700">Giờ đánh giá<input required type="time" name="assessment_time" value="09:00" class="{{ $inputClass }}"></label>
                                </div>
                                <label class="block text-xs font-semibold text-coffee-700">Nội dung cần kiểm tra lại<textarea required name="criteria" rows="2" maxlength="2000" class="{{ $inputClass }}" placeholder="Nội dung trưởng ca cần kiểm tra sau đào tạo..."></textarea></label>
                                <div class="flex justify-end"><button @disabled(! ($currentRound?->eligibility['ready'] ?? false)) class="rounded-xl bg-coffee-800 px-5 py-2.5 text-sm font-bold text-white disabled:cursor-not-allowed disabled:opacity-40">Tạo lịch đánh giá lại</button></div>
                            </form>
                        </details>
                    @endif
                </div>
            </dialog>
        @empty
            <p class="border-t border-coffee-100 p-10 text-center text-slate-500">{{ $training->isEmpty() ? 'Chưa có yêu cầu đào tạo lại. Yêu cầu xuất hiện khi đánh giá chưa đạt được duyệt.' : 'Không có yêu cầu ở tiến trình này.' }}</p>
        @endforelse
    </section>
</div>
@endsection
