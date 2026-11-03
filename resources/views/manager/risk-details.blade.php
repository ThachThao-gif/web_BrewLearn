@if($showBadge ?? true)@include('manager.risk', ['risk' => $person->risk])@endif
@if($person->risk === 'KhongApDung')
    <p class="text-xs text-slate-500 mt-2">Cảnh báo chỉ áp dụng cho vị trí Barista, Thu ngân và Phục vụ.</p>
@elseif($person->prediction)
    @php
        $hasWarningReasons = isset($person->risk_details['details']['ly_do_canh_bao']);
        $warningReasons = array_filter($person->risk_details['details']['ly_do_canh_bao'] ?? $person->risk_details['missing_reasons'] ?? []);
        $spacious = $spacious ?? false;
    @endphp
    <ul class="list-disc {{ $spacious ? 'mt-6 space-y-4 pl-5 text-sm text-coffee-900 marker:text-coffee-800' : 'mt-2 space-y-1 pl-4 text-xs text-slate-600' }}">
        @if($person->prediction->diem_rui_ro !== null)
            <li><span class="text-slate-500">Điểm cảnh báo</span> <strong class="text-coffee-900">{{ (int) round($person->prediction->diem_rui_ro * 10) }}/10</strong> <span class="text-slate-400">(Thấp 0–2 · Trung bình 3–4 · Cao 5–10)</span></li>
        @endif
        @if(isset($person->risk_details['completed_quiz_count'], $person->risk_details['applicable_quiz_count']))
            <li>Đã làm <strong class="text-coffee-900">{{ $person->risk_details['completed_quiz_count'] }}/{{ $person->risk_details['applicable_quiz_count'] }}</strong> bài kiểm tra</li>
        @endif
        @if($warningReasons)
            <li>{{ $hasWarningReasons ? 'Lý do cần lưu ý:' : 'Chưa đủ dữ liệu:' }}
                <ul class="list-disc {{ $spacious ? 'mt-2 space-y-1.5 pl-6 text-slate-600' : 'mt-1 space-y-0.5 pl-4' }}">
                    @foreach($warningReasons as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                </ul>
            </li>
        @elseif(($person->risk_details['details']['tong_diem'] ?? null) === 0)
            <li>Chưa có dấu hiệu cần lưu ý</li>
            <li>Điểm thấp chưa chứng minh đã đạt kỹ năng thực hành</li>
        @endif
        @if($referenceOnly ?? false)
            <li>Chỉ tham khảo, không ảnh hưởng điểm hay việc phê duyệt</li>
        @endif
        <li class="{{ $spacious ? 'pt-1 text-xs' : 'text-[11px]' }} text-slate-400">@if($spacious)<i class="fa-regular fa-clock mr-1.5" aria-hidden="true"></i>@endif Cập nhật {{ \Carbon\Carbon::parse($person->prediction->ngay_du_doan, 'UTC')->timezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') }}</li>
    </ul>
@else
    <p class="text-xs text-slate-500 mt-2">Chưa có kết quả cảnh báo cho nhân viên này.</p>
@endif
