@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-lg font-bold text-slate-800 mb-4">
        Kết quả tìm kiếm cho: <span class="text-amber-800">"{{ $query }}"</span>
    </h2>

    @if(count($lessons) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($lessons as $item)
                <a href="{{ route('courses.lesson', ['id' => $item->MaBaiHoc, 'user_id' => $currentUser->id ?? 1]) }}" 
                   class="p-4 bg-white border border-[#E2D4C1] rounded-2xl hover:border-amber-500 hover:shadow-sm transition flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">Bài học</span>
                        <h4 class="text-xs font-bold text-slate-800 mt-1">{{ $item->TenBaiHoc }}</h4>
                    </div>
                    <span class="text-xs text-amber-800 font-bold">Vào học →</span>
                </a>
            @endforeach
        </div>
    @else
        <p class="text-xs text-slate-400">Không tìm thấy bài học nào phù hợp với từ khóa trên.</p>
    @endif
</div>
@endsection