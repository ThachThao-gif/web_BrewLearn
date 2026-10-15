@php($dueTones = ['Quá hạn' => 'bg-rose-50 text-rose-700', 'Đến hạn' => 'bg-rose-50 text-rose-700', 'Sắp đến hạn' => 'bg-amber-50 text-amber-800'])
<div class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-coffee-50 text-xs text-coffee-700">
                <tr><th class="px-4 py-3">Nhân viên</th><th class="px-4 py-3">Cảnh báo</th><th class="px-4 py-3">Tiến độ</th><th class="px-4 py-3">Đánh giá gần nhất</th><th class="px-4 py-3">Hạn đánh giá</th></tr>
            </thead>
            <tbody class="divide-y divide-coffee-100">
                @forelse($staff as $person)
                    @php($employment = $person->employment_status ?? 'DangLam')
                    <tr class="align-top hover:bg-coffee-50/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.profile', $person->id) }}" class="font-bold text-coffee-900 hover:underline">{{ $person->name }}</a>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $person->ChucVu }} · {{ $person->shift_name }}</p>
                            @if($employment !== 'DangLam')<span class="mt-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ \App\Models\AssessmentReminder::EMPLOYMENT_LABELS[$employment] ?? 'Chưa xác định' }}</span>@endif
                        </td>
                        <td class="px-4 py-3">
                            <details class="group">
                                <summary class="inline-flex cursor-pointer list-none items-center gap-1">@include('manager.risk', ['risk' => $person->risk])<i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition group-open:rotate-180"></i></summary>
                                <div class="mt-2 max-w-64">@include('manager.risk-details', ['showBadge' => false])</div>
                            </details>
                        </td>
                        <td class="min-w-32 px-4 py-3">
                            <p class="text-sm font-bold text-coffee-900">{{ $person->training_progress }}%</p>
                            <div class="mt-1.5 h-1.5 rounded-full bg-coffee-100"><div class="h-1.5 rounded-full bg-amber_brand" style="width: {{ $person->training_progress }}%"></div></div>
                        </td>
                        <td class="px-4 py-3">
                            @if($person->last_evaluation)
                                <p class="font-bold text-coffee-900">{{ $person->last_evaluation->Diem }}<span class="font-normal text-slate-400">/100</span>
                                    <span class="ml-1 text-xs {{ $person->last_evaluation->IsPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $person->last_evaluation->IsPassed === null ? 'Chưa xác định' : ($person->last_evaluation->IsPassed ? 'Đạt' : 'Chưa đạt') }}</span></p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($person->last_evaluation->NgayDanhGia ?? $person->last_evaluation->updated_at)->format('d/m/Y') }}</p>
                            @else
                                <span class="text-xs text-slate-400">Chưa đánh giá</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <p class="text-slate-600">{{ $person->due_at?->format('d/m/Y') ?? $person->due_status }}</p>
                            @isset($dueTones[$person->due_status])<span class="mt-1 inline-block rounded-full px-2 py-0.5 font-semibold {{ $dueTones[$person->due_status] }}">{{ $person->due_status }}</span>@endisset
                            @if($person->needs_reminder && ! ($person->eligibility['ready'] ?? false))
                                <details class="mt-1 text-amber-800"><summary class="cursor-pointer">Chưa đủ điều kiện</summary><ul class="mt-1 list-disc pl-4">@foreach($person->eligibility['missing'] as $missing)<li>{{ $missing }}</li>@endforeach</ul></details>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-10 text-center text-slate-500">Không có nhân viên phù hợp bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
