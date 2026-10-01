

<?php $__env->startSection('title', 'Danh Sách Bài Kiểm Tra - BrewLearn'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <!-- HEADER TRANG -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-[#E8DFC8] shadow-sm">
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

    <?php
        // 1. Xác định vị trí / chức vụ của user hiện tại
        $currentUser = $user ?? auth()->user();
        $userChucVu = $currentUser->ChucVu ?? $currentUser->chuc_vu ?? $currentUser->vi_tri ?? 'Thu ngân';

        // 2. Lọc danh sách bài kiểm tra phù hợp với chức vụ của user
        $allTests = $tests ?? $baiKiemTra ?? [];
        $filteredTests = collect($allTests)->filter(function($test) use ($userChucVu) {
            $testChucVu = $test->ChucVu ?? $test->chuc_vu ?? $test->ViTri ?? $test->vi_tri ?? null;
            // Nếu bài test không khai báo chức vụ, hoặc ghi 'Tất cả', hoặc trùng chính xác chức vụ của user
            return empty($testChucVu) || $testChucVu === 'Tất cả' || strcasecmp($testChucVu, $userChucVu) === 0;
        })->values();
    ?>

    <!-- DANH SÁCH BÀI KIỂM TRA -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php $__empty_1 = true; $__currentLoopData = $filteredTests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $test): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
           <?php
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
?>

            <div class="bg-white rounded-3xl p-6 border border-[#E8DFC8] shadow-sm flex flex-col justify-between hover:shadow-md transition relative <?php echo e($isLocked ? 'opacity-75 bg-slate-50' : ''); ?>">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-lg uppercase">
                            Bài <?php echo e($index + 1); ?>

                        </span>
                        <span class="text-xs text-slate-400 font-medium">
                            <i class="fa-regular fa-clock mr-1"></i><?php echo e($test->ThoiGianLamBai ?? $test->thoi_gian ?? 15); ?> phút
                        </span>
                    </div>

                    <h3 class="font-bold text-coffee-900 text-base mb-2 line-clamp-2">
                        <?php echo e($test->TenBaiKiemTra ?? $test->ten_bai_kiem_tra ?? 'Bài kiểm tra năng lực'); ?>

                    </h3>

                    <p class="text-xs text-slate-500 line-clamp-3 mb-4">
                        <?php echo e($test->MoTa ?? $test->mo_ta ?? 'Kiểm tra kiến thức thực hành và quy trình tiêu chuẩn SOP.'); ?>

                    </p>
                </div>

                <?php if(($test->History ?? collect())->isNotEmpty()): ?>
                    <details class="text-xs text-slate-600 mb-3">
                        <summary class="cursor-pointer font-bold">Lịch sử <?php echo e($test->History->count()); ?> lượt thi · Điểm lần gần nhất: <?php echo e($diem); ?>/100</summary>
                        <ul class="mt-2 space-y-1">
                            <?php $__currentLoopData = $test->History; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attempt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($attempt->created_at ? \Carbon\Carbon::parse($attempt->created_at)->format('H:i d/m/Y') : 'Dữ liệu demo'); ?> — <?php echo e($attempt->Diem); ?>/100 · <?php echo e($attempt->Diem >= ($test->DiemChuan ?? 80) ? 'Đạt' : 'Chưa đạt'); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </details>
                <?php endif; ?>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <?php if($isLocked): ?>
                        <!-- TRẠNG THÁI BỊ KHÓA -->
                        <button disabled class="w-full text-center bg-slate-200 text-slate-500 text-xs font-bold py-2.5 rounded-xl cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-xs"></i>
                            Cần đạt bài trước để mở
                        </button>
                    <?php elseif($daLam): ?>
                        <!-- HIỂN THỊ NÚT THI LẠI VÀ ĐIỂM SỐ -->
                        <div class="w-full flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 px-3 py-2.5 rounded-xl border border-slate-200">
                                Điểm: <strong class="text-amber_brand"><?php echo e($diem); ?></strong>
                            </span>
                            
                            <a href="<?php echo e(route('tests.show', ['id' => $maBaiTest, 'user_id' => $userId])); ?>" 
                               class="flex-1 text-center bg-amber-100 hover:bg-amber-200 text-amber-900 text-xs font-bold py-2.5 rounded-xl transition border border-amber-300 shadow-sm flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-rotate-right text-xs"></i>
                                Thi lại
                            </a>
                        </div>
                    <?php else: ?>
                        <!-- BẮT ĐẦU LÀM BÀI -->
                        <a href="<?php echo e(route('tests.show', ['id' => $maBaiTest, 'user_id' => $userId])); ?>" 
                           class="w-full text-center bg-coffee-800 hover:bg-coffee-900 text-white text-xs font-bold py-2.5 rounded-xl transition shadow">
                            Bắt đầu làm bài
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-[#E8DFC8]">
                <div class="w-16 h-16 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-clipboard-question"></i>
                </div>
                <h3 class="text-base font-bold text-coffee-900 mb-1">Chưa có bài kiểm tra phù hợp</h3>
                <p class="text-xs text-slate-500">Hiện tại hệ thống chưa cập nhật danh sách bài đánh giá cho vị trí công việc của bạn.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/tests/index.blade.php ENDPATH**/ ?>