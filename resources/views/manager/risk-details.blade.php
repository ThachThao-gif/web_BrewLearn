@if($showBadge ?? true)@include('manager.risk', ['risk' => $person->risk])@endif
@if($person->risk === 'KhongApDung')
    <p class="text-xs text-slate-500 mt-2">Bộ luật chỉ áp dụng cho Barista, Thu ngân và Phục vụ.</p>
@elseif($person->prediction)
    @if($person->prediction->diem_rui_ro !== null)
        <p class="text-xs font-semibold text-coffee-800 mt-2">Điểm cảnh báo {{ (int) round($person->prediction->diem_rui_ro * 10) }}/10</p>
        <p class="text-[11px] text-slate-500 mt-0.5">Thấp 0–2 · Trung bình 3–4 · Cao 5–10</p>
    @endif
    @if(isset($person->risk_details['completed_quiz_count'], $person->risk_details['applicable_quiz_count']))
        <p class="text-xs text-slate-600 mt-1">Đã làm {{ $person->risk_details['completed_quiz_count'] }}/{{ $person->risk_details['applicable_quiz_count'] }} bài kiểm tra</p>
    @endif
    <ul class="text-xs text-slate-600 list-disc pl-4 mt-2 space-y-1">
        @foreach(($person->risk_details['details']['ly_do_canh_bao'] ?? $person->risk_details['missing_reasons'] ?? []) as $reason)
            <li>{{ $reason }}</li>
        @endforeach
    </ul>
    @if(($person->risk_details['details']['tong_diem'] ?? null) === 0)
        <p class="text-xs text-slate-600 mt-1">Không kích hoạt điều kiện cộng điểm; chưa xác nhận đạt năng lực thực hành.</p>
    @endif
    <p class="text-[10px] text-slate-500 mt-2">Thời điểm tính: {{ \Carbon\Carbon::parse($person->prediction->ngay_du_doan, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }} (giờ Việt Nam)</p>
@else
    <p class="text-xs text-slate-500 mt-2">Chưa có kết quả theo luật.</p>
@endif
