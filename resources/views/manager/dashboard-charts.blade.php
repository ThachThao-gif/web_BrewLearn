@php
    $colors = ['Cao' => '#ef4444', 'TrungBinh' => '#f59e0b', 'Thap' => '#10b981', 'ChuaDuDuLieu' => '#94a3b8', 'ChuaCoKetQua' => '#cbd5e1'];
    $labels = ['Cao' => 'Cao', 'TrungBinh' => 'Trung bình', 'Thap' => 'Thấp', 'ChuaDuDuLieu' => 'Chưa đủ dữ liệu', 'ChuaCoKetQua' => 'Chưa có kết quả theo luật'];
    $riskTotal = $riskDistribution->sum();
    $stops = []; $offset = 0;
    foreach ($riskDistribution as $level => $count) {
        $next = $offset + ($riskTotal ? $count / $riskTotal * 100 : 0);
        $stops[] = $colors[$level].' '.$offset.'% '.$next.'%';
        $offset = $next;
    }
@endphp
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <section class="xl:col-span-2 bg-white border border-coffee-200 rounded-2xl p-5" aria-labelledby="trend-heading">
        <h2 id="trend-heading" class="font-extrabold text-coffee-900 text-lg">Xu hướng kết quả đánh giá</h2>
        <p class="text-xs text-slate-500 mt-1">6 tháng gần nhất · Nhân viên đang làm theo bộ lọc · Kết quả đã duyệt cuối cùng của mỗi nhân viên trong tháng đánh giá.</p>
        <div class="flex gap-5 text-sm mt-4"><span class="text-emerald-700">● Đạt</span><span class="text-red-600">● Chưa đạt</span></div>
        <svg viewBox="0 0 650 290" class="w-full mt-3" role="img" aria-labelledby="trend-heading trend-description">
            <desc id="trend-description">Tỷ lệ Đạt và Chưa đạt theo tháng. Tháng không có đánh giá để trống. Bảng số liệu nằm dưới biểu đồ.</desc>
            @foreach([0, 25, 50, 75, 100] as $rate)
                <line x1="55" x2="620" y1="{{ 235 - $rate * 2 }}" y2="{{ 235 - $rate * 2 }}" stroke="#e2e8f0" />
                <text x="45" y="{{ 239 - $rate * 2 }}" text-anchor="end" fill="#64748b" font-size="12">{{ $rate }}%</text>
            @endforeach
            @foreach($assessmentTrend as $index => $month)
                <text x="{{ 65 + $index * 108 }}" y="265" text-anchor="middle" fill="#64748b" font-size="12">{{ $month['label'] }}</text>
                @if($month['total'] === 0)
                    <text x="{{ 65 + $index * 108 }}" y="282" text-anchor="middle" fill="#94a3b8" font-size="10">Chưa có</text>
                @endif
            @endforeach
            @foreach(['passRate' => '#10b981', 'failRate' => '#ef4444'] as $key => $color)
                @foreach($assessmentTrend as $index => $month)
                    @if($month[$key] !== null)
                        @if($index > 0 && $assessmentTrend[$index - 1][$key] !== null)
                            <line x1="{{ 65 + ($index - 1) * 108 }}" y1="{{ 235 - $assessmentTrend[$index - 1][$key] * 2 }}" x2="{{ 65 + $index * 108 }}" y2="{{ 235 - $month[$key] * 2 }}" stroke="{{ $color }}" stroke-width="3" stroke-dasharray="{{ $key === 'failRate' ? '6 4' : 'none' }}" />
                        @endif
                        <circle cx="{{ 65 + $index * 108 }}" cy="{{ 235 - $month[$key] * 2 }}" r="{{ $key === 'failRate' ? 3 : 6 }}" fill="{{ $color }}" stroke="white" stroke-width="1"><title>{{ $month['label'] }}: {{ $key === 'passRate' ? 'Đạt' : 'Chưa đạt' }} {{ $month[$key] }}% ({{ $month['total'] }} nhân viên được đánh giá)</title></circle>
                    @endif
                @endforeach
            @endforeach
        </svg>
        @if($assessmentTrend->sum('total') === 0)
            <p class="text-sm text-slate-500 text-center">Chưa có kết quả đánh giá đã duyệt trong 6 tháng này.</p>
        @endif
        <details class="mt-3 text-xs text-slate-600">
            <summary class="cursor-pointer font-semibold">Xem số liệu từng tháng</summary>
            <div class="overflow-x-auto mt-2"><table class="w-full text-left"><thead><tr><th class="py-2">Tháng</th><th>Nhân viên</th><th>Đạt</th><th>Chưa đạt</th></tr></thead><tbody>
            @foreach($assessmentTrend as $month)
                <tr><td class="py-1">{{ $month['label'] }}</td><td>{{ $month['total'] }}</td><td>{{ $month['passRate'] === null ? '—' : $month['passed'].' ('.$month['passRate'].'%)' }}</td><td>{{ $month['failRate'] === null ? '—' : $month['failed'].' ('.$month['failRate'].'%)' }}</td></tr>
            @endforeach
            </tbody></table></div>
        </details>
    </section>
    <section class="bg-white border border-coffee-200 rounded-2xl p-5" aria-labelledby="risk-heading">
        <h2 id="risk-heading" class="font-extrabold text-coffee-900 text-lg">Cảnh báo theo luật</h2>
        <p class="text-xs text-slate-500 mt-1">Kết quả luật mới nhất của từng nhân viên đang làm trong phạm vi bộ lọc. Thời điểm tính và lý do hiển thị trong danh sách, hồ sơ.</p>
        <div class="relative mx-auto my-6" style="width: 190px; height: 190px; border-radius: 50%; background: {{ $riskTotal ? 'conic-gradient('.implode(', ', $stops).')' : '#e2e8f0' }}" role="img" aria-label="Phân bố nguy cơ: {{ collect($labels)->map(fn ($label, $level) => $label.' '.$riskDistribution[$level])->implode(', ') }}">
            <div class="absolute flex flex-col items-center justify-center bg-white text-coffee-900" style="inset: 25px; border-radius: 50%"><strong class="text-3xl">{{ $riskTotal }}</strong><span class="text-xs text-slate-500">nhân viên</span></div>
        </div>
        <ul class="space-y-3 text-sm">
            @foreach($riskDistribution as $level => $count)
                <li class="flex justify-between gap-2"><span class="flex items-center gap-2"><span style="width: 10px; height: 10px; border-radius: 50%; background: {{ $colors[$level] }}"></span>{{ $labels[$level] }}</span><strong>{{ $count }} <span class="font-normal text-slate-500">({{ $riskTotal ? round($count / $riskTotal * 100, 1) : 0 }}%)</span></strong></li>
            @endforeach
        </ul>
        @if($riskTotal === 0)<p class="text-sm text-slate-500 mt-4">Không có nhân viên đang làm trong phạm vi này.</p>@endif
        <p class="text-xs text-slate-500 mt-5">Chưa có kết quả được tách riêng khỏi chưa đủ dữ liệu. Cảnh báo hỗ trợ xem hồ sơ, không quyết định kết quả đánh giá.</p>
    </section>
</div>
