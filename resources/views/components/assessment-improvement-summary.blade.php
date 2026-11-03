@php
    $savedLabels = ['daotaobosung' => 'Đào tạo bổ sung', 'hoclaibaihoc' => 'Học lại bài học liên quan', 'theodoica' => 'Theo dõi thêm trong ca', 'danhgialai' => 'Đánh giá lại'];
    $savedCriteria = ['nghiep_vu' => 'Kiến thức nghiệp vụ', 'ky_nang' => 'Thao tác công việc', 'quy_trinh' => 'Tuân thủ quy trình', 'phuc_vu' => 'Thái độ phục vụ', 'tac_phong' => 'Tác phong & phối hợp'];
@endphp
<details class="mt-2 text-xs font-normal">
    <summary class="cursor-pointer font-bold text-amber-700">Xem nội dung đề xuất đã gửi</summary>
    <div class="mt-2 space-y-3 min-w-48 max-w-sm">
        @foreach(($savedProposal['details'] ?? []) as $type => $plan)
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 space-y-1">
                <p class="font-bold">{{ $savedLabels[$type] ?? $type }}</p>
                @if($type === 'daotaobosung')
                    <p>Kỹ năng: {{ $plan['skill'] ?? '' }}</p>
                    <p class="whitespace-pre-line">Hướng dẫn: {{ $plan['instructions'] ?? '' }}</p>
                    <p>Phụ trách dự kiến: {{ $proposalOwners->firstWhere('id', $plan['owner_id'] ?? null)?->name ?? 'Chưa xác định' }}</p>
                @elseif($type === 'hoclaibaihoc')
                    <p>Khóa: {{ $proposalCourses->firstWhere('MaCTDT', $plan['course_id'] ?? null)?->TenCTDT ?? 'Khóa không còn tồn tại' }}</p>
                    <ul class="list-disc pl-4">
                        @foreach(($plan['lesson_ids'] ?? []) as $lessonId)<li>{{ $proposalLessons->firstWhere('MaBaiHoc', $lessonId)?->TenBaiHoc ?? 'Bài học không còn tồn tại' }}</li>@endforeach
                    </ul>
                @elseif($type === 'theodoica')
                    <p class="whitespace-pre-line">Quan sát: {{ $plan['behavior'] ?? '' }}</p>
                    @if(!empty($plan['shift_count']))<p>Thời lượng: {{ $plan['shift_count'] }} ca</p>@endif
                    @if(!empty($plan['until_date']))<p>Đến ngày: {{ \Carbon\Carbon::parse($plan['until_date'])->format('d/m/Y') }}</p>@endif
                @elseif($type === 'danhgialai')
                    <p>Ngày dự kiến: {{ \Carbon\Carbon::parse($plan['assessment_date'])->format('d/m/Y') }}</p>
                    <p>Người đánh giá: {{ $proposalOwners->firstWhere('id', $plan['assessor_id'] ?? null)?->name ?? 'Chưa xác định' }}</p>
                    <p>Tiêu chí: {{ collect($plan['criteria'] ?? [])->map(fn($criterion) => $savedCriteria[$criterion] ?? $criterion)->implode(', ') }}</p>
                @endif
            </div>
        @endforeach
    </div>
</details>
