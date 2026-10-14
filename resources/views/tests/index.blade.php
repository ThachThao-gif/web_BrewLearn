@extends('layouts.app')

@section('title', 'Danh Sách Bài Kiểm Tra - BrewLearn')

@section('content')
<div class="space-y-6">
    <!-- HEADER TRANG -->
    <div class="page-banner flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-[#E8DFC8] shadow-sm">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-coffee-900 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-amber_brand"></i>
                Danh Sách Bài Kiểm Tra & Đánh Giá
            </h1>
            <p class="text-xs md:text-sm text-slate-500 mt-1">
                Hoàn thành các bài đánh giá năng lực dành riêng cho vị trí công việc của bạn.
            </p>
        </div>
    </div>

    @php
        // 1. Xác định vị trí / chức vụ của user hiện tại
        $currentUser = auth()->user() ?? $currentUser ?? $user ?? null;
        $userChucVu = $currentUser->ChucVu ?? $currentUser->chuc_vu ?? $currentUser->vi_tri ?? 'Thu ngân';

        // 2. Lọc danh sách bài kiểm tra phù hợp với chức vụ của user
        $allTests = $tests ?? $baiKiemTra ?? [];
        $filteredTests = collect($allTests)->values();
    @endphp

    <!-- DANH SÁCH BÀI KIỂM TRA -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($filteredTests as $index => $test)
           @php
    $maBaiTest = $test->MaBaiKiemTra ?? $test->id ?? ($index + 1);
    $userId = request()->query('user_id', $currentUser->id ?? 1);
    
    // Lấy điểm số từ object
    $diem = $test->Diem ?? $test->diem ?? $test->user_score ?? null;
    $daLam = !is_null($diem);
    
    // LOGIC TỰ ĐỘNG KHÓA/MỞ THEO MẢNG MỚI ĐÃ LỌC:
    if ($index === 0) {
        // Bài đầu tiên trong danh sách hiển thị luôn luôn được mở
        $isLocked = false;
    } else {
        // Lấy bài kiểm tra nằm ngay trước bài này trong danh sách $filteredTests
        $prevTest = $filteredTests[$index - 1];
        $prevScore = $prevTest->Diem ?? $prevTest->diem ?? $prevTest->user_score ?? null;
        $prevPassScore = $prevTest->DiemChuan ?? 80;

        // Nếu bài trước ĐÃ CÓ ĐIỂM và ĐẠT ĐIỂM CHUẨN -> Mở bài này ($isLocked = false)
        // Ngược lại nếu bài trước chưa đạt -> Khóa bài này ($isLocked = true)
        $isLocked = !($prevTest->HasPassed ?? false);
    }
@endphp

            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm flex flex-col justify-between hover:shadow-md transition relative {{ $isLocked ? 'opacity-75 bg-slate-50' : '' }}">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase">
                            Bài {{ $index + 1 }}
                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            <i class="fa-regular fa-clock mr-1"></i>{{ $test->ThoiGianLamBai ?? $test->thoi_gian ?? 15 }} phút
                        </span>
                    </div>

                    <h3 class="font-bold text-coffee-900 text-base mb-2 line-clamp-2">
                        {{ $test->TenBaiKiemTra ?? $test->ten_bai_kiem_tra ?? 'Bài kiểm tra năng lực' }}
                    </h3>

                    <p class="text-xs text-slate-500 line-clamp-3 mb-4">
                        {{ $test->MoTa ?? $test->mo_ta ?? 'Kiểm tra kiến thức thực hành và quy trình tiêu chuẩn SOP.' }}
                    </p>
                </div>

                @if(($test->History ?? collect())->isNotEmpty())
                    <details class="text-xs text-slate-600 mb-3">
                        <summary class="cursor-pointer font-bold">Lịch sử {{ $test->History->count() }} lượt thi · Điểm lần gần nhất: {{ $diem }}/100</summary>
                        <ul class="mt-2 space-y-1">
                            @foreach($test->History as $attempt)
                                <li>{{ $attempt->created_at ? \Carbon\Carbon::parse($attempt->created_at)->format('H:i d/m/Y') : 'Dữ liệu demo' }} — {{ $attempt->Diem }}/100 · {{ $attempt->Diem >= ($test->DiemChuan ?? 80) ? 'Đạt' : 'Chưa đạt' }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if ($isLocked)
                        <!-- TRẠNG THÁI BỊ KHÓA -->
                        <button disabled class="w-full text-center bg-slate-200 text-slate-500 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            Cần đạt bài trước để mở
                        </button>
                    @elseif ($daLam)
                        <!-- HIỂN THỊ NÚT THI LẠI VÀ ĐIỂM SỐ -->
                        <div class="w-full flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-2.5 rounded-xl border border-slate-200">
                                Điểm: <strong class="text-amber_brand">{{ $diem }}</strong>
                            </span>
                            
                            <a href="{{ route('tests.show', ['id' => $maBaiTest, 'user_id' => $userId]) }}" 
                               class="flex-1 text-center bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-bold py-2.5 rounded-xl transition border border-amber-300 shadow-sm flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-rotate-right text-xs"></i>
                                Thi lại
                            </a>
                        </div>
                    @else
                        <!-- BẮT ĐẦU LÀM BÀI -->
                        <a href="{{ route('tests.show', ['id' => $maBaiTest, 'user_id' => $userId]) }}" 
                           class="w-full text-center bg-coffee-800 hover:bg-coffee-900 text-white text-xs font-bold py-2.5 rounded-xl transition shadow">
                            Bắt đầu làm bài
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-[#E8DFC8]">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-clipboard-question"></i>
                </div>
                <h3 class="text-base font-bold text-coffee-900 mb-1">Chưa có bài kiểm tra phù hợp</h3>
                <p class="text-xs text-slate-500">Hiện tại hệ thống chưa cập nhật danh sách bài đánh giá cho vị trí công việc của bạn.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
