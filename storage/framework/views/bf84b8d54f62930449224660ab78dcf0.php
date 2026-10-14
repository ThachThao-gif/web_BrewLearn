

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Bar Điều Hướng -->
    <div class="page-banner flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Quản Lý Đánh Giá Năng Lực</h1>
            <p class="text-sm text-slate-500 mt-1">Phân hệ điều hành và chấm điểm dành cho Trưởng Ca (Leader)</p>
        </div>
        
        <!-- Nút sang trang Danh sách nhân sự -->
        <div class="flex items-center gap-3">
            <a href="<?php echo e(route('leader.staffList')); ?>" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#8B5E3C] hover:bg-[#6F4E37] text-white rounded-xl text-sm font-semibold transition shadow-sm">
                <i class="fa-solid fa-users"></i>
                <span>Xem Danh Sách Nhân Sự</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Block 1: Phát thông báo lịch đánh giá -->
        <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-slate-100 h-fit">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center font-bold">1</div>
                <h2 class="text-base font-bold text-slate-800">Phát Thông Báo Lịch Đánh Giá</h2>
            </div>

            <form action="<?php echo e(route('leader.createSchedule')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Chọn Nhân Viên</label>
                    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php $__currentLoopData = $nhanViens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($nv->id); ?>"><?php echo e($nv->name ?? $nv->HoTen); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Tiêu Chuẩn Năng Lực</label>
                    <select name="MaCTDT" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        <option value="">-- Chọn chương trình/chuẩn --</option>
                        <?php $__currentLoopData = $chuongTrinhs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ct->MaCTDT); ?>"><?php echo e($ct->TenCTDT); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Ngày Đánh Giá Sát Hạch</label>
                    <input type="datetime-local" name="NgayDanhGia" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                </div>

                <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-xl text-sm transition shadow-sm">
                    Gửi Thông Báo Cho Nhân Viên
                </button>
            </form>
        </div>

        <!-- Block 2: Danh sách lịch sát hạch cần chấm điểm -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">2</div>
                    <h2 class="text-base font-bold text-slate-800">Lịch Sát Hạch Cần Chấm Điểm</h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-xs font-medium uppercase">
                            <th class="py-3 px-4">Nhân Viên</th>
                            <th class="py-3 px-4">Ngày Đánh Giá</th>
                            <th class="py-3 px-4">Trạng Thái</th>
                            <th class="py-3 px-4 text-right">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php $__empty_1 = true; $__currentLoopData = $danhSachDanhGia; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3 px-4 font-semibold text-slate-700">
                                    <?php echo e($item->nhanVien->name ?? $item->nhanVien->HoTen ?? 'Nhân viên'); ?>

                                </td>
                                <td class="py-3 px-4 text-slate-500">
                                    <?php echo e(\Carbon\Carbon::parse($item->NgayDanhGia)->format('Y-m-d H:i')); ?>

                                </td>
                                <td class="py-3 px-4">
                                    <?php if($item->KetQua == 'Dat'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Đạt</span>
                                    <?php elseif($item->KetQua == 'ChuaDat'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Chưa Đạt</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Chờ đánh giá</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="#" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        <span>Chấm Điểm</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-400">Chưa có lịch đánh giá nào được tạo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\users\leader.blade.php ENDPATH**/ ?>