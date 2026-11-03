@extends('layouts.app')

@section('title', 'Tổng quan lộ trình - BrewLearn')

@section('content')
<div class="space-y-6">

    {{-- BANNER CHÀO CÁ NHÂN HÓA --}}
    <div class="page-banner relative bg-gradient-to-r from-[#2B1B15] via-[#3A261E] to-[#1A0F0B] text-white rounded-3xl p-6 md:p-8 shadow-xl overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-xl z-10">
            <div class="inline-flex items-center gap-2 bg-amber-500/20 text-amber-300 text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider border border-amber-500/30">
                <i class="fa-solid fa-graduation-cap"></i> LỘ TRÌNH: {{ strtoupper($currentUser->ChucVu ?? 'BARISTA') }}
            </div>
            
            <h1 class="text-2xl md:text-3xl font-black text-amber-50 flex items-center gap-2">
                Tổng quan lộ trình
            </h1>
            
            <p class="text-xs md:text-sm text-amber-200/80 leading-relaxed">
                Bạn đã hoàn thành <span class="font-black text-amber-400 text-sm">{{ $overallProgress ?? 0 }}%</span> lộ trình đào tạo được giao. Hãy tiếp tục duy trì phong độ nhé!
            </p>
        </div>
    </div>

    {{-- STATS OVERVIEW CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        
        {{-- 1. Khóa học được giao --}}
        <div class="bg-white border-2 border-amber-500/30 rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-book text-amber-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-amber-100 text-amber-900 px-2 py-0.5 rounded-md">Phân công</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $assignedCoursesCount ?? 0 }} <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Khóa học được giao</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Tổng số khóa thuộc lộ trình</div>
            </div>
        </div>

        {{-- 2. Đang theo học --}}
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-regular fa-hourglass-half text-amber-600 text-sm"></i>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md">Đang học</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $inProgressCoursesCount ?? 0 }} <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Đang theo học</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Các khóa đang thực hiện</div>
            </div>
        </div>

        {{-- 3. Tiến độ đào tạo --}}
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-chart-line text-blue-600 text-sm"></i>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">Trung bình</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $overallProgress ?? 0 }}<span class="text-xs font-normal text-slate-500">%</span></div>
                <div class="text-xs font-bold text-slate-700">Tiến độ đào tạo</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Tiến độ trung bình các khóa</div>
            </div>
        </div>

        {{-- 4. Đã hoàn thành --}}
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-regular fa-circle-check text-emerald-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md">Hoàn thành</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $completedCoursesCount ?? 0 }} <span class="text-xs font-normal text-slate-500">khóa</span></div>
                <div class="text-xs font-bold text-slate-700">Đã hoàn thành</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Khóa học đã xong 100%</div>
            </div>
        </div>

        {{-- 5. Điểm Quiz trung bình --}}
        <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
            <div class="flex justify-between items-center">
                <i class="fa-solid fa-graduation-cap text-indigo-600 text-sm"></i>
                <span class="text-[10px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-md">Điểm TB</span>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-800">{{ $avgQuizScore ?? 0 }} <span class="text-xs font-semibold text-slate-400">/100</span></div>
                <div class="text-xs font-bold text-slate-700">Điểm Quiz trung bình</div>
                <div class="text-[10px] text-slate-400 mt-0.5">Trung bình các lượt thi thuộc bài được phân công, gồm thi lại</div>
            </div>
        </div>

        {{-- 6. Bài kiểm tra đã hoàn thành --}}
<div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 space-y-2 shadow-sm">
    <div class="flex justify-between items-center">
        <i class="fa-solid fa-list-check text-teal-600 text-sm"></i>
        <span class="text-[10px] font-bold bg-teal-50 text-teal-700 px-2 py-0.5 rounded-md">Đạt</span>
    </div>
    <div>
        {{-- ĐÃ SỬA: Thay $completedQuizzesCount thành $completedTestsCount --}}
        <div class="text-2xl font-black text-slate-800">{{ $completedTestsCount ?? 0 }}/{{ $totalTestsCount ?? 0 }} <span class="text-xs font-normal text-slate-500">bài</span></div>
        <div class="text-xs font-bold text-slate-700">Bài kiểm tra đã hoàn thành</div>
        <div class="text-[10px] text-slate-400 mt-0.5">Số bài kiểm tra đã đạt chuẩn</div>
    </div>
</div>

    </div>

    {{-- KHÓA HỌC CỦA NHÂN VIÊN --}}
    <div class="bg-white border border-[#E2D4C1] rounded-3xl p-6 space-y-4 shadow-sm">
        <div class="flex justify-between items-center border-b border-slate-100 pb-3">
            <h2 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-cubes text-amber-600"></i>
                <span>Chương Trình Đào Tạo Phân Công Cho {{ $currentUser->name ?? 'Nhân Viên' }}</span>
            </h2>
            <span class="text-xs font-bold text-slate-400">{{ isset($assignedCourses) ? count($assignedCourses) : 0 }} khóa học</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($assignedCourses ?? [] as $course)
                @php $cId = $course->MaCTDT; @endphp
                <div class="p-5 rounded-2xl bg-[#FAF7F2] border border-[#E8DFC8] flex flex-col justify-between space-y-3 hover:border-amber-500 transition shadow-sm">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold bg-amber-100 text-amber-900 px-2.5 py-0.5 rounded-md uppercase">
                                {{ $course->ChucVuApDung ?? 'BARISTA' }}
                            </span>
                            <span class="text-xs font-black text-amber-600">{{ $course->TienDo ?? 0 }}%</span>
                        </div>

                        <h3 class="text-xs md:text-sm font-bold text-slate-800 line-clamp-2">
                            {{ $course->TenCTDT }}
                        </h3>

                        <p class="text-[11px] text-slate-500 line-clamp-2">
                            {{ Str::limit(strip_tags($course->MoTa ?? ''), 80) }}
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-200/60">
                        <div class="w-full bg-[#EFE7DA] h-2 rounded-full overflow-hidden">
                            <div class="bg-amber-600 h-full rounded-full transition-all duration-500" style="width: {{ $course->TienDo ?? 0 }}%;"></div>
                        </div>

                        <a href="{{ route('courses.show', ['id' => $cId]) }}" class="w-full bg-slate-900 hover:bg-black text-white text-xs font-bold py-2 rounded-xl flex items-center justify-center gap-1.5 transition mt-2">
                            <span>Vào học khóa này</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-xs text-slate-400 italic">Chưa có khóa học nào được giao trong CSDL cho nhân viên này.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
