@php
    $riskLabels = ['Cao' => ['Cao', 'bg-rose-50 text-rose-700 border-rose-200'], 'TrungBinh' => ['Trung bình', 'bg-amber-50 text-amber-800 border-amber-200'], 'Thap' => ['Thấp', 'bg-emerald-50 text-emerald-700 border-emerald-200'], 'ChuaCoKetQua' => ['Chưa có kết quả', 'bg-slate-100 text-slate-600 border-slate-200'], 'ChuaDuDuLieu' => ['Chưa đủ dữ liệu', 'bg-slate-100 text-slate-600 border-slate-200']];
    $riskLabels['KhongApDung'] = ['Không thuộc đối tượng cảnh báo', 'bg-slate-100 text-slate-600 border-slate-200'];
    [$riskLabel, $riskClass] = $riskLabels[$risk ?? 'ChuaDuDuLieu'] ?? $riskLabels['ChuaDuDuLieu'];
@endphp
<span class="inline-flex rounded-full border px-2.5 py-1 text-xs font-bold whitespace-nowrap {{ $riskClass }}">{{ $riskLabel }}</span>
