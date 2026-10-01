<p class="text-xs text-rose-700">Chọn ít nhất một phương án và ghi rõ cách thực hiện. Đây là đề xuất để quản lý xét duyệt, chưa phải kế hoạch đã được giao.</p>

@php
    $proposalLabels = [
        'daotaobosung' => 'Đào tạo bổ sung', 'hoclaibaihoc' => 'Học lại bài học liên quan',
        'theodoica' => 'Theo dõi thêm trong ca', 'danhgialai' => 'Đánh giá lại',
    ];
    $criteriaLabels = [
        'nghiep_vu' => 'Kiến thức nghiệp vụ', 'ky_nang' => 'Thao tác công việc',
        'quy_trinh' => 'Tuân thủ quy trình', 'phuc_vu' => 'Thái độ phục vụ', 'tac_phong' => 'Tác phong & phối hợp',
    ];
@endphp
@foreach($proposalLabels as $key => $label)
    <fieldset data-proposal="{{ $key }}" disabled class="hidden bg-white border border-rose-200 p-3 rounded-xl space-y-3 text-sm">
        <legend class="font-bold text-coffee-900 px-1">{{ $label }}</legend>
        @if($key === 'daotaobosung')
            <label class="block">Kỹ năng cần cải thiện <span class="text-red-600">*</span>
                <input name="proposal_details[daotaobosung][skill]" maxlength="1000" data-required class="w-full border rounded-lg p-2 mt-1" placeholder="VD: Bàn giao ca đúng quy trình">
            </label>
            <label class="block">Nội dung hướng dẫn <span class="text-red-600">*</span>
                <textarea name="proposal_details[daotaobosung][instructions]" maxlength="3000" rows="3" data-required class="w-full border rounded-lg p-2 mt-1" placeholder="Ghi việc cần hướng dẫn và cách thực hành"></textarea>
            </label>
            <label class="block">Người phụ trách dự kiến <span class="text-red-600">*</span>
                <select name="proposal_details[daotaobosung][owner_id]" data-required class="w-full border rounded-lg p-2 mt-1">
                    <option value="">Chọn người phụ trách</option>
                    @foreach($proposalOwners as $owner)<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endforeach
                </select>
            </label>
        @elseif($key === 'hoclaibaihoc')
            <label class="block">Khóa học cần học lại <span class="text-red-600">*</span>
                <select id="proposalCourse" name="proposal_details[hoclaibaihoc][course_id]" onchange="filterProposalLessons()" data-required class="w-full border rounded-lg p-2 mt-1">
                    <option value="">Chọn khóa học</option>
                    @foreach($proposalCourses as $course)<option value="{{ $course->MaCTDT }}">{{ $course->TenCTDT }}</option>@endforeach
                </select>
            </label>
            <label class="block">Bài học cụ thể <span class="text-red-600">*</span>
                <select id="proposalLessons" name="proposal_details[hoclaibaihoc][lesson_ids][]" multiple size="5" data-required class="w-full border rounded-lg p-2 mt-1">
                    @foreach($proposalLessons as $lesson)<option value="{{ $lesson->MaBaiHoc }}" data-course="{{ $lesson->FK_MaCTDT }}">{{ $lesson->TenBaiHoc }}</option>@endforeach
                </select>
            </label>
            <p class="text-xs text-slate-500">Chọn một hoặc nhiều bài thuộc khóa đã chọn; giữ Ctrl khi chọn nhiều bài trên máy tính.</p>
        @elseif($key === 'theodoica')
            <label class="block">Hành vi cần quan sát <span class="text-red-600">*</span>
                <textarea name="proposal_details[theodoica][behavior]" maxlength="3000" rows="3" data-required class="w-full border rounded-lg p-2 mt-1" placeholder="VD: Đúng giờ, kiểm tra và bàn giao quầy cuối ca"></textarea>
            </label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label>Số ca theo dõi
                    <input id="observationShifts" name="proposal_details[theodoica][shift_count]" type="number" min="1" max="90" oninput="updateObservationRequirement()" class="w-full border rounded-lg p-2 mt-1">
                </label>
                <label>Theo dõi đến ngày
                    <input id="observationDate" name="proposal_details[theodoica][until_date]" type="date" min="{{ now()->toDateString() }}" oninput="updateObservationRequirement()" class="w-full border rounded-lg p-2 mt-1">
                </label>
            </div>
            <p class="text-xs text-slate-500">Nhập số ca hoặc ngày kết thúc; có thể nhập cả hai.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label>Ngày đánh giá lại dự kiến <span class="text-red-600">*</span>
                    <input name="proposal_details[danhgialai][assessment_date]" type="date" min="{{ now()->toDateString() }}" data-required class="w-full border rounded-lg p-2 mt-1">
                </label>
                <label>Người đánh giá dự kiến <span class="text-red-600">*</span>
                    <select name="proposal_details[danhgialai][assessor_id]" data-required class="w-full border rounded-lg p-2 mt-1">
                        <option value="">Chọn người đánh giá</option>
                        @foreach($proposalOwners as $owner)<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endforeach
                    </select>
                </label>
            </div>
            <label class="block">Tiêu chí cần kiểm tra lại <span class="text-red-600">*</span>
                <select name="proposal_details[danhgialai][criteria][]" multiple size="5" data-required class="w-full border rounded-lg p-2 mt-1">
                    @foreach($criteriaLabels as $criterion => $criterionLabel)<option value="{{ $criterion }}">{{ $criterionLabel }}</option>@endforeach
                </select>
            </label>
        @endif
    </fieldset>
@endforeach
