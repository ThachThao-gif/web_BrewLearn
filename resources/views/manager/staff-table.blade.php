@php
    $dueTones = ['Quá hạn' => 'bg-rose-50 text-rose-700', 'Đến hạn' => 'bg-rose-50 text-rose-700', 'Sắp đến hạn' => 'bg-amber-50 text-amber-800'];
@endphp
<div class="overflow-hidden rounded-2xl border border-coffee-200 bg-white">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-coffee-50 text-xs text-coffee-700">
                <tr><th class="px-4 py-3">Nhân viên</th><th class="px-4 py-3">Cảnh báo</th><th class="px-4 py-3">Tiến độ</th><th class="px-4 py-3">Kết quả đã duyệt</th><th class="px-4 py-3">Hạn đánh giá</th><th class="px-4 py-3"><span class="sr-only">Hồ sơ</span></th></tr>
            </thead>
            <tbody class="divide-y divide-coffee-100">
                @forelse($staffPage ?? $staff as $person)
                    @php
                        $employment = $person->employment_status ?? 'DangLam';
                    @endphp
                    <tr class="hover:bg-coffee-50/60">
                        <td class="px-4 py-3">
                            <a href="{{ route('manager.profile', $person->id) }}" class="font-bold text-coffee-900 hover:underline">{{ $person->name }}</a>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $person->ChucVu }} · {{ $person->shift_name }}
                                @if($employment !== 'DangLam')<span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">{{ \App\Models\AssessmentReminder::EMPLOYMENT_LABELS[$employment] ?? 'Chưa xác định' }}</span>@endif
                            </p>
                        </td>
                        <td class="px-4 py-3">@include('manager.risk', ['risk' => $person->risk])</td>
                        <td class="min-w-32 px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-coffee-100"><div class="h-1.5 rounded-full bg-amber_brand" style="width: {{ min($person->training_progress, 100) }}%"></div></div>
                                <span class="text-xs font-bold text-coffee-900">{{ $person->training_progress }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @if($person->last_evaluation)
                                <span class="font-bold text-coffee-900">{{ $person->last_evaluation->Diem }}</span>
                                <span class="ml-1 text-xs font-semibold {{ $person->last_evaluation->IsPassed ? 'text-emerald-700' : 'text-rose-700' }}">{{ $person->last_evaluation->IsPassed === null ? 'Chưa xác định' : ($person->last_evaluation->IsPassed ? 'Đạt' : 'Chưa đạt') }}</span>
                                <span class="ml-1 text-xs text-slate-400">· {{ \Carbon\Carbon::parse($person->last_evaluation->NgayDanhGia ?? $person->last_evaluation->updated_at)->format('d/m') }}</span>
                            @else
                                <span class="text-xs text-slate-400">Chưa đánh giá</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <span class="text-slate-600">{{ $person->due_at?->format('d/m/Y') ?? $person->due_status }}</span>
                            @if($person->scheduled_assessment && isset($dueTones[$person->due_status]))
                                <span class="ml-1 inline-block rounded-full bg-blue-50 px-2 py-0.5 font-semibold text-blue-700">{{ $person->scheduled_assessment->TrangThai === 'ChoDanhGia' ? 'Đã xếp lịch' : 'Đang xử lý' }}</span>
                            @elseif(isset($dueTones[$person->due_status]))
                                <span class="ml-1 inline-block rounded-full px-2 py-0.5 font-semibold {{ $dueTones[$person->due_status] }}">{{ $person->due_status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('manager.profile', $person->id) }}" aria-label="Xem hồ sơ {{ $person->name }}" class="inline-flex items-center gap-1 whitespace-nowrap text-xs font-bold text-amber-700 hover:text-amber-800">Chi tiết <i class="fa-solid fa-chevron-right text-[10px]" aria-hidden="true"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-10 text-center text-slate-500">Không có nhân viên phù hợp bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($staffPage) && $staffPage->hasPages())
        <div class="border-t border-coffee-100 px-4 py-3">{{ $staffPage->links() }}</div>
    @endif
</div>
