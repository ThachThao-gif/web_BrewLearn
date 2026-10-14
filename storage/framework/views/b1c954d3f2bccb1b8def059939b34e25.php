

<?php $__env->startSection('title', $test->TenBaiKiemTra ?? 'Bài kiểm tra sát hạch'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- HEADER BÀI THI -->
    <div class="page-banner bg-white rounded-3xl border border-[#E2D4C1] p-6 shadow-sm space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="bg-amber-100 text-amber-900 text-xs font-extrabold px-3 py-1 rounded-full uppercase">
                    KHÓA BARISTA CƠ BẢN
                </span>
                <span class="text-xs font-bold text-slate-400">• MÃ ĐỀ: BRW-2026-EX<?php echo e(sprintf('%02d', $test->MaBaiKiemTra ?? 1)); ?></span>
            </div>
            <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Đang làm bài trực tuyến
            </span>
        </div>

        <h1 class="text-xl md:text-2xl font-black text-coffee-900">
            <?php echo e($test->TenBaiKiemTra ?? 'Bài Kiểm Tra Sát Hạch Barista'); ?>

        </h1>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
            <div class="bg-[#FAF7F2] p-3.5 rounded-2xl border border-[#E8DFC8] flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg">
                    <i class="fa-regular fa-clock"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase">Thời gian làm bài</div>
                    <div class="text-sm font-black text-coffee-900"><span id="timer"><?php echo e($test->ThoiGianLamBai ?? 20); ?>:00</span> phút</div>
                </div>
            </div>

            <div class="bg-[#FAF7F2] p-3.5 rounded-2xl border border-[#E8DFC8] flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-list-check"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase">Số lượng câu hỏi</div>
                    <div class="text-sm font-black text-coffee-900"><?php echo e(count($questions)); ?> câu hỏi</div>
                </div>
            </div>

            <div class="bg-[#FAF7F2] p-3.5 rounded-2xl border border-[#E8DFC8] flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase">Tiêu chuẩn vượt qua</div>
                    <div class="text-sm font-black text-coffee-900"><?php echo e($test->DiemChuan ?? 80); ?>% tổng số điểm</div>
                </div>
            </div>
        </div>
    </div>

    <!-- KHU VỰC LÀM BÀI & NAVIGATOR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- CỘT TRÁI: NỘI DUNG CÂU HỎI HIỆN TẠI -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-3xl border border-[#E2D4C1] p-6 shadow-sm space-y-6 min-h-[400px] flex flex-col justify-between">
                
                <?php $__empty_1 = true; $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index =>$q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div id="question_box_<?php echo e($index); ?>" class="question-box space-y-4 <?php echo e($index !== 0 ? 'hidden' : ''); ?>">
                    <div class="flex justify-between items-center border-b pb-3">
                        <span class="bg-amber-100 text-amber-900 text-xs font-bold px-3 py-1 rounded-xl">
                            <i class="fa-solid fa-lightbulb text-amber-600 mr-1"></i> Tình huống thực tế #<?php echo e(sprintf('%02d', $index + 1)); ?>

                        </span>
                        <span class="text-xs font-bold text-slate-400">Trọng số: 1 điểm</span>
                    </div>

                    <h3 class="text-sm md:text-base font-bold text-coffee-900 leading-relaxed">
                        <?php echo e($q->CauHoi); ?>

                    </h3>

                    <!-- CÁC PHƯƠNG ÁN LỰA CHỌN -->
                    <div class="space-y-2.5 pt-2">
                        <?php $__currentLoopData = ['A' => $q->PhuongAnA, 'B' =>$q->PhuongAnB, 'C' => $q->PhuongAnC, 'D' =>$q->PhuongAnD]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label onclick="selectAnswer(<?php echo e($index); ?>, '<?php echo e($key); ?>')" 
                               class="option-label-<?php echo e($index); ?> flex items-center gap-3 p-4 rounded-2xl border border-slate-200 hover:border-amber_brand hover:bg-amber-50/50 cursor-pointer transition">
                            <input type="radio" name="ans_<?php echo e($index); ?>" value="<?php echo e($key); ?>" class="hidden">
                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center shrink-0 radio-circle">
                                <div class="w-2.5 h-2.5 rounded-full bg-amber_brand hidden inner-dot"></div>
                            </div>
                            <span class="text-xs md:text-sm text-slate-700 font-medium">
                                <strong class="text-coffee-900"><?php echo e($key); ?>.</strong> <?php echo e($option); ?>

                            </span>
                        </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="py-12 text-center text-slate-400 italic">
                    Chưa có câu hỏi nào trong CSDL bài test này.
                </div>
                <?php endif; ?>

                <!-- NÚT CHUYỂN CÂU -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button id="btnPrev" onclick="changeQuestion(-1)" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-5 py-2.5 rounded-xl transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Câu trước
                    </button>
                    <button id="btnNext" onclick="changeQuestion(1)" class="bg-coffee-900 hover:bg-black text-white text-xs font-bold px-6 py-2.5 rounded-xl transition flex items-center gap-2">
                        <span>Lưu & Sang câu tiếp theo</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- CỘT PHẢI: NAVIGATOR CÂU HỎI -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl border border-[#E2D4C1] p-6 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b pb-3">
                    <h3 class="text-xs font-bold text-coffee-900 uppercase tracking-wider">Danh sách câu hỏi</h3>
                    <span class="text-xs font-bold text-slate-400"><?php echo e(count($questions)); ?> câu</span>
                </div>

                <!-- LƯỚI NÚT SỐ CÂU HỎI -->
                <div class="grid grid-cols-5 gap-2">
                    <?php $__currentLoopData = $questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index =>$q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button id="nav_btn_<?php echo e($index); ?>" onclick="jumpToQuestion(<?php echo e($index); ?>)" 
                            class="nav-btn w-full h-10 rounded-xl text-xs font-bold border border-slate-200 hover:border-amber_brand transition flex items-center justify-center <?php echo e($index === 0 ? 'bg-amber-100 border-amber_brand text-amber-900 border-2' : 'bg-slate-50 text-slate-600'); ?>">
                        <?php echo e(sprintf('%02d', $index + 1)); ?>

                    </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <button onclick="submitExam(false)" class="w-full bg-amber_brand hover:bg-[#C25A1A] text-white font-extrabold py-3 rounded-2xl text-xs flex items-center justify-center gap-2 transition shadow-md">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Nộp bài kiểm tra</span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JAVASCRIPT LOGIC -->
<script>
    let currentIndex = 0;
    const totalQuestions = <?php echo e(count($questions)); ?>;
    const userAnswers = {};
    const currentTestId = "<?php echo e($test->MaBaiKiemTra ?? $test->id ?? 1); ?>";

    function showQuestion(index) {
        document.querySelectorAll('.question-box').forEach((box, i) => {
            box.classList.toggle('hidden', i !== index);
        });

        // Cập nhật trạng thái active ô số bên navigator
        document.querySelectorAll('.nav-btn').forEach((btn, i) => {
            if (userAnswers[i]) {
                btn.className = "nav-btn w-full h-10 rounded-xl text-xs font-bold transition flex items-center justify-center bg-emerald-500 text-white border-emerald-600 shadow-sm";
            } else if (i === index) {
                btn.className = "nav-btn w-full h-10 rounded-xl text-xs font-bold transition flex items-center justify-center bg-amber-100 border-amber_brand text-amber-900 border-2";
            } else {
                btn.className = "nav-btn w-full h-10 rounded-xl text-xs font-bold transition flex items-center justify-center bg-slate-50 text-slate-600 border border-slate-200";
            }
        });

        // Toggle nút Câu trước
        document.getElementById('btnPrev').style.visibility = index === 0 ? 'hidden' : 'visible';
        
        // Đổi chữ nút Hoàn thành ở câu cuối
        const btnNext = document.getElementById('btnNext');
        if (index === totalQuestions - 1) {
            btnNext.innerHTML = '<span>Hoàn thành bài thi</span> <i class="fa-solid fa-check text-[10px]"></i>';
        } else {
            btnNext.innerHTML = '<span>Lưu & Sang câu tiếp theo</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>';
        }
    }

    function selectAnswer(qIndex, optionKey) {
        userAnswers[qIndex] = optionKey;
        
        // Cập nhật giao diện option chọn
        const labels = document.querySelectorAll(`.option-label-${qIndex}`);
        labels.forEach(label => {
            const input = label.querySelector('input');
            const circle = label.querySelector('.radio-circle');
            const dot = label.querySelector('.inner-dot');
            
            if (input.value === optionKey) {
                input.checked = true;
                label.classList.add('border-amber_brand', 'bg-amber-50');
                circle.classList.add('border-amber_brand');
                dot.classList.remove('hidden');
            } else {
                input.checked = false;
                label.classList.remove('border-amber_brand', 'bg-amber-50');
                circle.classList.remove('border-amber_brand');
                dot.classList.add('hidden');
            }
        });

        // Cập nhật ô số bên navigator
        const navBtn = document.getElementById(`nav_btn_${qIndex}`);
        if (navBtn) {
            navBtn.className = "nav-btn w-full h-10 rounded-xl text-xs font-bold transition flex items-center justify-center bg-emerald-500 text-white border-emerald-600 shadow-sm";
        }
    }

    function changeQuestion(step) {
        const nextIndex = currentIndex + step;
        if (nextIndex >= 0 && nextIndex < totalQuestions) {
            currentIndex = nextIndex;
            showQuestion(currentIndex);
        } else if (nextIndex >= totalQuestions) {
            submitExam(false);
        }
    }

    function jumpToQuestion(index) {
        currentIndex = index;
        showQuestion(currentIndex);
    }

    let submitting = false;
    function submitExam(isAutoSubmit = false) {
        if (submitting) return;
        const answeredCount = Object.keys(userAnswers).length;

        if (!isAutoSubmit) {
            if (!confirm(`Bạn đã trả lời ${answeredCount}/${totalQuestions} câu hỏi. Bạn có chắc chắn muốn nộp bài?`)) {
                return;
            }
        } else {
            alert('Đã hết thời gian làm bài! Hệ thống sẽ tự động nộp bài làm của bạn.');
        }

        submitting = true;

        // Gửi AJAX chấm điểm
        fetch(`/tests/${currentTestId}/submit`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
            },
            body: JSON.stringify({
                attempt_token: <?php echo json_encode($attemptToken, 15, 512) ?>,
                answers: userAnswers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Nộp bài thành công!');
                window.location.href = "<?php echo e(route('tests.index')); ?>";
            } else {
                submitting = false;
                alert(data.message || 'Có lỗi xảy ra khi chấm điểm.');
            }
        })
        .catch(error => {
            submitting = false;
            console.error('Error:', error);
            alert('Lỗi kết nối máy chủ khi nộp bài!');
        });
    }

    // Đếm ngược thời gian
    let durationInMinutes = <?php echo e($test->ThoiGianLamBai ?? 20); ?>;
    let timeLeft = durationInMinutes * 60;
    
    const timerInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            submitExam(true); // Tự động nộp bài khi hết giờ
            return;
        }
        timeLeft--;
        const m = Math.floor(timeLeft / 60);
        const s = timeLeft % 60;
        document.getElementById('timer').innerText = `${m}:${s < 10 ? '0' : ''}${s}`;
    }, 1000);

    // Khởi tạo
    showQuestion(0);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\tests\show.blade.php ENDPATH**/ ?>