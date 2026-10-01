

<?php $__env->startSection('title', 'Chương Trình Đào Tạo & SOP - BrewLearn'); ?>

<?php $__env->startSection('content'); ?>
<?php
    // 1. CHUẨN HÓA DANH SÁCH KHÓA HỌC
    $items = is_array($courses) ? collect($courses) : ($courses ?? collect());
    $totalCount = count($items);$learningCount = 0;
    $completedCount = 0;

    foreach($items as $item) {$p = isset($item->TienDo) ? round($item->TienDo, 1) : 0;
        if ($p >= 100) {$completedCount++;
        } elseif ($p > 0) {$learningCount++;
        }
    }
    
    // 2. LẤY ĐÚNG USER_ID ĐANG ĐĂNG NHẬP / THAO TÁC (ĐẢM BẢO MULTI-USER)
    $currentUserId = auth()->id() ?? request()->query('user_id') ?? ($currentUser->id ?? null);
?>

<div class="space-y-6">
    <!-- HEADER TRANG -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl md:text-2xl font-extrabold text-coffee-900">Chương Trình Đào Tạo & SOP</h1>
            <p class="text-xs text-slate-500 mt-1">Danh sách các khóa học tiêu chuẩn Barista phân theo cấp độ và chuyên môn.</p>
        </div>

        <!-- SEARCH & FILTER -->
        <div class="flex items-center gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" id="searchInput" onkeyup="filterCourses()" placeholder="Tìm khóa học..." class="pl-8 pr-3 py-2 bg-white border border-coffee-200/60 rounded-xl text-xs w-48 md:w-60 focus:outline-none focus:border-amber_brand">
            </div>
            <select id="levelSelect" onchange="filterCourses()" class="px-3 py-2 bg-white border border-coffee-200/60 rounded-xl text-xs text-slate-600 focus:outline-none">
                <option value="">Tất cả cấp độ</option>
                <option value="CẤP ĐỘ 1">Cấp độ 1</option>
                <option value="CẤP ĐỘ 2">Cấp độ 2</option>
            </select>
        </div>
    </div>

    <!-- TABS LỌC KHÓA HỌC -->
    <div class="flex items-center space-x-6 border-b border-coffee-200/40 pb-3 text-xs font-bold text-slate-500">
        <button id="tab-all" onclick="switchTab('all')" class="tab-btn text-coffee-900 border-b-2 border-amber_brand pb-3 -mb-3 flex items-center gap-2">
            <i class="fa-solid fa-cubes text-amber_brand"></i>
            <span>Tất cả khóa học (<?php echo e($totalCount); ?>)</span>
        </button>
        <button id="tab-learning" onclick="switchTab('learning')" class="tab-btn hover:text-coffee-900 transition flex items-center gap-2">
            <i class="fa-regular fa-clock"></i>
            <span>Đang theo học (<?php echo e($learningCount); ?>)</span>
        </button>
        <button id="tab-completed" onclick="switchTab('completed')" class="tab-btn hover:text-coffee-900 transition flex items-center gap-2">
            <i class="fa-regular fa-circle-check"></i>
            <span>Đã hoàn thành (<?php echo e($completedCount); ?>)</span>
        </button>
    </div>

    <!-- DANH SÁCH KHÓA HỌC -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="courseGrid">
        <?php if($totalCount > 0): ?>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index =>$course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php 
                    $courseId = $course->MaCTDT ?? $course->id ?? '';
                    // Đảm bảo ép kiểu nguyên (int) để tránh lỗi định dạng chuỗi
                    $progress = round($course->TienDo ?? 0, 1);
                    $level =$course->CapDo ?? 'CẤP ĐỘ 1';
                    $tenCourse =$course->TenCTDT ?? 'Chưa có tên khóa học';
                    $moTa =$course->MoTa ?? '';
                    $hinhAnh = !empty($course->HinhAnh) ?$course->HinhAnh : 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&q=80&w=600';
                    $chucVu =$course->ChucVuApDung ?? 'THỰC HÀNH CA BARISTA';
                    $thoiLuong =$course->ThoiLuong ?? '1.5 giờ';
                    
                    // LẤY TRỰC TIẾP TRẠNG THÁI KHÓA ĐÃ TÍNH TỪ CONTROLLER
                    $isLocked =$course->IsLocked ?? false;

                    // TẠO ROUTE GIỮ NGUYÊN USER_ID TRÊN URL ĐỂ BÀI HỌC KHÔNG BỊ TRỞ VỀ USER 1
                    $routeParams = ['id' =>$courseId];
                    if ($currentUserId) {
                        $routeParams['user_id'] =$currentUserId;
                    }
                    $courseUrl = route('courses.show',$routeParams);
                ?>

                <div class="course-card bg-white rounded-3xl border border-[#E2D4C1] shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition <?php echo e($isLocked ? 'opacity-80' : ''); ?>"
                     data-title="<?php echo e(strtolower($tenCourse)); ?>"
                     data-progress="<?php echo e($progress); ?>"
                     data-level="<?php echo e($level); ?>">
                    
                    <div>
                        <!-- BANNER ẢNH KHÓA HỌC -->
                        <div class="relative h-44 bg-gradient-to-br from-coffee-900 to-coffee-800 p-4 flex flex-col justify-between overflow-hidden">
                            <img src="<?php echo e($hinhAnh); ?>" class="absolute inset-0 w-full h-full object-cover opacity-40 mix-blend-overlay" alt="Course Banner">
                            
                            <div class="flex justify-between items-center z-10">
                                <span class="bg-amber-500/90 backdrop-blur-sm text-white text-[10px] font-extrabold px-2.5 py-1 rounded-lg uppercase tracking-wider">
                                    <?php echo e($level); ?> • BẮT BUỘC
                                </span>
                                <span class="bg-black/60 backdrop-blur-sm text-white text-[10px] font-medium px-2 py-0.5 rounded-md flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> <?php echo e($thoiLuong); ?>

                                </span>
                            </div>
                        </div>

                        <!-- THÔNG TIN KHÓA HỌC -->
                        <div class="p-5 space-y-3">
                            <div class="text-[10px] font-bold text-amber_brand uppercase tracking-wider flex items-center gap-1.5">
                                <i class="fa-solid fa-mug-hot"></i>
                                <span><?php echo e($chucVu); ?></span>
                            </div>

                            <h3 class="text-sm font-bold text-coffee-900 line-clamp-2 leading-snug">
                                <?php echo e($tenCourse); ?>

                            </h3>

                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                <?php echo e(\Illuminate\Support\Str::limit(strip_tags($moTa), 90)); ?>

                            </p>
                        </div>
                    </div>

                    <!-- TIẾN ĐỘ & NÚT VÀO HỌC -->
                    <div class="p-5 pt-0 space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-[11px] font-bold">
                                <span class="text-slate-500">Tiến độ hoàn thành</span>
                                <span class="<?php echo e($progress >= 100 ? 'text-emerald-600' : 'text-coffee-900'); ?>"><?php echo e($progress); ?>%</span>
                            </div>
                            <div class="w-full bg-[#EFE7DA] h-1.5 rounded-full overflow-hidden">
                                <div class="<?php echo e($progress >= 100 ? 'bg-emerald-500' : 'bg-amber_brand'); ?> h-full rounded-full transition-all duration-300" style="width: <?php echo e(min(100, $progress)); ?>%"></div>
                            </div>
                        </div>

                        <!-- XỬ LÝ NÚT BẤM DỰA TRÊN TRẠNG THÁI & TIẾN ĐỘ THỰC TẾ -->
                        <?php if($isLocked): ?>
                            <!-- 1. NÚT BỊ KHÓA -->
                            <button disabled class="w-full bg-slate-200 text-slate-400 font-bold py-2.5 rounded-2xl text-xs flex items-center justify-center gap-2 cursor-not-allowed">
                                <i class="fa-solid fa-lock text-[10px]"></i>
                                <span>Khóa học đang khóa</span>
                            </button>
                        <?php elseif($progress >= 100): ?>
                            <!-- 2. NÚT ĐÃ HOÀN THÀNH -->
                            <a href="<?php echo e($courseUrl); ?>" class="w-full bg-emerald-800 hover:bg-emerald-900 text-white font-bold py-2.5 rounded-2xl text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                <i class="fa-solid fa-rotate-right text-[10px]"></i>
                                <span>Xem lại bài học</span>
                            </a>
                        <?php elseif($progress > 0): ?>
                            <!-- 3. NÚT ĐANG HỌC DỞ -->
                            <a href="<?php echo e($courseUrl); ?>" class="w-full bg-coffee-900 hover:bg-black text-white font-bold py-2.5 rounded-2xl text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                <span>Học tiếp ngay</span>
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        <?php else: ?>
                            <!-- 4. NÚT CHƯA HỌC -->
                            <a href="<?php echo e($courseUrl); ?>" class="w-full bg-amber_brand hover:bg-amber-600 text-white font-bold py-2.5 rounded-2xl text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                <span>Bắt đầu học</span>
                                <i class="fa-solid fa-play text-[10px]"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <div class="col-span-full py-10 text-center bg-white rounded-3xl border border-[#E2D4C1]">
                <p class="text-xs text-slate-400 italic">Chưa có khóa học nào trong CSDL.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    let currentTab = 'all';

    function switchTab(tab) {
        currentTab = tab;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-coffee-900', 'border-b-2', 'border-amber_brand');
            btn.classList.add('hover:text-coffee-900');
        });
        const activeBtn = document.getElementById(`tab-${tab}`);
        if(activeBtn) {
            activeBtn.classList.add('text-coffee-900', 'border-b-2', 'border-amber_brand');
        }
        filterCourses();
    }

    function filterCourses() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const selectedLevel = document.getElementById('levelSelect').value;
        const cards = document.querySelectorAll('.course-card');

        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            const progress = parseInt(card.getAttribute('data-progress'));
            const level = card.getAttribute('data-level');

            let matchesSearch = title.includes(query);
            let matchesLevel = !selectedLevel || level.includes(selectedLevel);
            let matchesTab = true;

            if (currentTab === 'learning') {
                matchesTab = progress > 0 && progress < 100;
            } else if (currentTab === 'completed') {
                matchesTab = progress >= 100;
            }

            if (matchesSearch && matchesLevel && matchesTab) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views/courses/index.blade.php ENDPATH**/ ?>