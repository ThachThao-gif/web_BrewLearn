@php
    $colors = ['Cao' => '#ef4444', 'TrungBinh' => '#f59e0b', 'Thap' => '#10b981', 'ChuaDuDuLieu' => '#94a3b8', 'ChuaCoKetQua' => '#cbd5e1'];
    $labels = ['Cao' => 'Cao', 'TrungBinh' => 'Trung bình', 'Thap' => 'Thấp', 'ChuaDuDuLieu' => 'Chưa đủ dữ liệu', 'ChuaCoKetQua' => 'Chưa có kết quả'];
    $riskTotal = $riskDistribution->sum();
    $stops = []; $offset = 0;
    foreach ($riskDistribution as $level => $count) {
        $next = $offset + ($riskTotal ? $count / $riskTotal * 100 : 0);
        $stops[] = $colors[$level].' '.$offset.'% '.$next.'%';
        $offset = $next;
    }
@endphp
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
    <section class="xl:col-span-2 rounded-2xl border border-coffee-200 bg-white p-5" aria-labelledby="trend-heading">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 id="trend-heading" class="text-base font-extrabold text-coffee-900">Kết quả đánh giá đã duyệt <span class="text-xs font-normal text-slate-400">số lượt mỗi tháng · {{ $trendYear ? 'năm '.$trendYear : '6 tháng gần nhất' }}</span></h2>
            <form method="GET" action="{{ route('manager.dashboard') }}" class="flex items-center gap-2 text-xs">
                @foreach(request()->only('shift', 'q') as $name => $value)<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endforeach
                <label for="trend-year" class="text-slate-500">Xem theo</label>
                <select id="trend-year" name="year" onchange="this.form.submit()" class="rounded-lg border border-coffee-200 bg-white px-2 py-1 text-xs font-semibold text-coffee-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">6 tháng gần nhất</option>
                    @foreach($trendYears as $year)<option value="{{ $year }}" @selected($trendYear === $year)>Năm {{ $year }}</option>@endforeach
                </select>
                <noscript><button class="rounded-lg bg-coffee-800 px-2 py-1 text-white">Xem</button></noscript>
            </form>
            <div class="flex gap-4 text-xs text-slate-600"><span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-emerald-500"></span>Đạt</span><span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-red-500"></span>Chưa đạt</span><span class="text-slate-400">· số nhân viên</span></div>
        </div>
        @php
            $trendMax = max(4, (int) $assessmentTrend->max('total'));
            $trendStep = (int) ceil($trendMax / 4);
            $trendTop = $trendStep * 4;
            $chartBase = 235;
            $chartHeight = 200;
            $slotWidth = 570 / max($assessmentTrend->count(), 1);
            $barWidth = min(46, $slotWidth * 0.55);
            $toY = fn (float $value) => $chartBase - $value / $trendTop * $chartHeight;
        @endphp
        <svg viewBox="0 0 650 280" class="mt-3 h-56 w-full" role="img" aria-labelledby="trend-heading trend-description">
            <desc id="trend-description">Số nhân viên Đạt và Chưa đạt theo tháng (cột chồng). Tháng không có đánh giá để trống. Bảng số liệu nằm dưới biểu đồ.</desc>
            @for($tick = 0; $tick <= $trendTop; $tick += $trendStep)
                <line x1="55" x2="625" y1="{{ $toY($tick) }}" y2="{{ $toY($tick) }}" stroke="#e2e8f0" stroke-width="1" />
                <text x="45" y="{{ $toY($tick) + 4 }}" text-anchor="end" fill="#64748b" font-size="12">{{ $tick }}</text>
            @endfor
            @foreach($assessmentTrend as $index => $month)
                @php
                    $centerX = 55 + $slotWidth * ($index + 0.5);
                    $barX = $centerX - $barWidth / 2;
                    $passTop = $toY($month['passed']);
                    $totalTop = $toY($month['total']);
                @endphp
                <g>
                    <title>{{ $month['label'] }}: {{ $month['total'] }} nhân viên được duyệt đánh giá — Đạt {{ $month['passed'] }}, Chưa đạt {{ $month['failed'] }}</title>
                    <rect x="{{ $barX - 14 }}" y="{{ $chartBase - $chartHeight }}" width="{{ $barWidth + 28 }}" height="{{ $chartHeight }}" fill="transparent" />
                    @if($month['passed'] > 0)
                        <rect x="{{ $barX }}" y="{{ $passTop }}" width="{{ $barWidth }}" height="{{ $chartBase - $passTop }}" rx="{{ $month['failed'] > 0 ? 0 : 4 }}" fill="#10b981" />
                        @if($month['failed'] === 0)<rect x="{{ $barX }}" y="{{ $chartBase - 4 }}" width="{{ $barWidth }}" height="4" fill="#10b981" />@endif
                    @endif
                    @if($month['failed'] > 0)
                        <rect x="{{ $barX }}" y="{{ $totalTop }}" width="{{ $barWidth }}" height="{{ $passTop - $totalTop - ($month['passed'] > 0 ? 2 : 0) }}" rx="4" fill="#ef4444" />
                        <rect x="{{ $barX }}" y="{{ $passTop - ($month['passed'] > 0 ? 2 : 0) - 4 }}" width="{{ $barWidth }}" height="4" fill="#ef4444" />
                    @endif
                    <text x="{{ $centerX }}" y="{{ $month['total'] > 0 ? $totalTop - 8 : $chartBase - 8 }}" text-anchor="middle" font-size="12" fill="{{ $month['total'] > 0 ? '#1e293b' : '#94a3b8' }}" font-weight="{{ $month['total'] > 0 ? 600 : 400 }}">{{ $month['total'] > 0 ? $month['total'] : ($trendYear ? '–' : 'Không có') }}</text>
                </g>
                <text x="{{ $centerX }}" y="258" text-anchor="middle" fill="#64748b" font-size="12">{{ $trendYear ? $month['short'] : $month['label'] }}</text>
            @endforeach
            <line x1="55" x2="625" y1="{{ $chartBase }}" y2="{{ $chartBase }}" stroke="#94a3b8" stroke-width="1" />
        </svg>
        <p class="mt-1 text-[11px] text-slate-500">Mỗi nhân viên được đánh giá định kỳ 3 tháng/lần và lần đầu sau 3 tháng làm việc, nên mỗi tháng chỉ một phần nhân viên đến kỳ.</p>
        @if($assessmentTrend->sum('total') === 0)
            <p class="text-center text-sm text-slate-500">Chưa có kết quả đánh giá đã duyệt trong 6 tháng này.</p>
        @endif
        <details class="mt-2 text-xs text-slate-600">
            <summary class="cursor-pointer font-semibold text-coffee-700">Xem số liệu từng tháng</summary>
            <div class="mt-2 overflow-x-auto"><table class="w-full text-left"><thead class="text-slate-500"><tr><th class="py-2">Tháng</th><th>Nhân viên</th><th>Đạt</th><th>Chưa đạt</th></tr></thead><tbody>
            @foreach($assessmentTrend as $month)
                <tr class="border-t border-coffee-100"><td class="py-1.5">{{ $month['label'] }}</td><td>{{ $month['total'] }}</td><td>{{ $month['passRate'] === null ? '—' : $month['passed'].' ('.$month['passRate'].'%)' }}</td><td>{{ $month['failRate'] === null ? '—' : $month['failed'].' ('.$month['failRate'].'%)' }}</td></tr>
            @endforeach
            </tbody></table></div>
        </details>
    </section>

    <section class="rounded-2xl border border-coffee-200 bg-white p-5" aria-labelledby="risk-heading">
        <h2 id="risk-heading" class="text-base font-extrabold text-coffee-900">Cảnh báo học tập</h2>
        <div class="mt-4 flex items-center gap-5">
            <div class="relative shrink-0" style="width: 120px; height: 120px; border-radius: 50%; background: {{ $riskTotal ? 'conic-gradient('.implode(', ', $stops).')' : '#e2e8f0' }}" role="img" aria-label="Phân bố cảnh báo: {{ collect($labels)->map(fn ($label, $level) => $label.' '.$riskDistribution[$level])->implode(', ') }}">
                <div class="absolute flex flex-col items-center justify-center bg-white text-coffee-900" style="inset: 18px; border-radius: 50%"><strong class="text-2xl leading-none">{{ $riskTotal }}</strong><span class="text-[11px] text-slate-500">nhân viên</span></div>
            </div>
            <ul class="flex-1 space-y-2 text-sm">
                @foreach($riskDistribution as $level => $count)
                    <li class="flex items-center justify-between gap-2"><span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full" style="background: {{ $colors[$level] }}"></span>{{ $labels[$level] }}</span><strong>{{ $count }}</strong></li>
                @endforeach
            </ul>
        </div>
        @if($riskTotal === 0)<p class="mt-4 text-sm text-slate-500">Không có nhân viên đang làm trong phạm vi này.</p>@endif
        <p class="mt-4 text-[11px] text-slate-400">Chỉ để tham khảo, không thay kết quả đánh giá.</p>
    </section>
</div>
