

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-5xl mx-auto p-4 md:p-6 space-y-4">
    <!-- Header: Bộ lọc & Nút Đọc tất cả -->
    <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <span class="text-xs font-bold text-slate-500 flex items-center gap-2">
            <i class="fa-solid fa-arrow-down-short-wide"></i> Sắp xếp theo thời gian mới nhất
        </span>

        <form action="<?php echo e(route('notifications.readAll')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="user_id" value="<?php echo e(request('user_id', $currentUser->id ?? 2)); ?>">
            <button type="submit" class="text-xs font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1 transition cursor-pointer">
                <i class="fa-solid fa-check-double text-amber-600"></i> Đọc tất cả
            </button>
        </form>
    </div>

    <!-- Danh sách Thông báo -->
    <div class="space-y-4">
        <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php 
                $isUnread = ($item->is_read == 0);$titleText     = $item->title ?? $item->tieu_de ?? 'Thông báo hệ thống';
                $messageText   =$item->message ?? $item->noi_dung ?? $item->content ?? '';
                $typeText      =$item->type ?? 'general'; 
                $formattedTime = \Carbon\Carbon::parse($item->created_at)->format('H:i:s d/m/Y');
            ?>

            <!-- Card thông báo -->
            <div id="notification-card-<?php echo e($item->id); ?>" class="bg-white border border-[#E2D4C1] rounded-2xl p-5 shadow-sm relative overflow-hidden transition hover:border-amber-400">
                <!-- Vạch đỏ bên trái cho tin chưa đọc -->
                <?php if($isUnread): ?>
                    <div class="unread-bar absolute left-0 top-0 bottom-0 w-1.5 bg-red-600"></div>
                <?php endif; ?>

                <div class="content-wrapper <?php echo e($isUnread ? 'pl-2' : ''); ?>">
                    <!-- Header thẻ thông báo -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="unread-dot w-2.5 h-2.5 rounded-full <?php echo e($isUnread ? 'bg-red-500' : 'bg-amber-400'); ?>"></span>
                            <h3 class="text-xs font-extrabold text-slate-900">
                                <?php if(in_array($typeText, ['warning', 'incident'])): ?>
                                    Sự cố / Nhắc nhở
                                <?php elseif($typeText === 'schedule'): ?>
                                    Lịch sát hạch & Đào tạo
                                <?php else: ?>
                                    Thông báo chung
                                <?php endif; ?>
                            </h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-400">
                            <?php echo e($formattedTime); ?>

                        </span>
                    </div>

                    <!-- Tiêu đề -->
                    <h2 class="text-sm md:text-base font-extrabold text-slate-900 mb-2 leading-snug">
                        <?php echo e($titleText); ?>

                    </h2>

                    <!-- Nội dung tóm tắt -->
                    <p class="text-xs text-slate-600 leading-relaxed mb-4 line-clamp-2 whitespace-pre-line break-words">
                        <?php echo e($messageText); ?>

                    </p>

                    <!-- Footer thẻ: Phân loại nhãn & Nút Chi tiết -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <div class="flex flex-wrap items-center gap-2">
                            <?php if(in_array($typeText, ['warning', 'incident'])): ?>
                                <span class="bg-red-100 text-red-700 text-[10px] font-black px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Nhắc nhở vận hành
                                </span>
                            <?php elseif($typeText === 'schedule'): ?>
                                <span class="bg-amber-100 text-amber-900 text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-check text-[9px]"></i> Lịch sát hạch
                                </span>
                            <?php else: ?>
                                <span class="bg-blue-100 text-blue-900 text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-solid fa-bell text-[9px]"></i> Thông báo hệ thống
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Nút xem chi tiết -->
                        <button type="button" 
                                onclick='openNotificationDetail({
                                    id: <?php echo e($item->id); ?>,
                                    title: <?php echo json_encode($titleText, 15, 512) ?>,
                                    message: <?php echo json_encode($messageText, 15, 512) ?>,
                                    type: <?php echo json_encode($typeText, 15, 512) ?>,
                                    created_at: <?php echo json_encode($formattedTime, 15, 512) ?>,
                                    is_read: <?php echo e($item->is_read); ?>

                                })' 
                                class="flex items-center gap-1.5 text-slate-500 hover:text-amber-800 text-xs font-bold transition cursor-pointer border-0 bg-transparent">
                            <span>Chi tiết</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-8 text-center bg-white rounded-2xl border border-[#E2D4C1]">
                <p class="text-xs text-slate-400">Hiện tại bạn không có thông báo nào.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal xem Chi tiết Thông báo -->
<div id="notificationModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4" onclick="if(event.target === this) closeNotificationDetail()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl relative border border-[#E2D4C1] max-h-[90vh] flex flex-col">
        <!-- Header Modal -->
        <div class="flex items-start justify-between pb-3 border-b border-slate-100">
            <div>
                <span id="modalTypeBadge" class="inline-block mb-1 text-[10px] font-bold px-2.5 py-0.5 rounded-lg"></span>
                <h3 id="modalTitle" class="text-base font-bold text-slate-900"></h3>
                <span id="modalTime" class="text-xs text-slate-400 block mt-0.5"></span>
            </div>
            <button type="button" onclick="closeNotificationDetail()" class="text-slate-400 hover:text-slate-600 text-lg font-bold p-1 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Body Modal -->
        <div class="py-4 overflow-y-auto flex-1">
            <div id="modalMessage" class="text-sm text-slate-700 leading-relaxed whitespace-pre-line break-words bg-slate-50 p-4 rounded-xl border border-slate-100"></div>
        </div>

        <!-- Footer Modal -->
        <div class="pt-3 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeNotificationDetail()" class="px-4 py-2 bg-amber-800 text-white rounded-xl text-xs font-bold hover:bg-amber-900 transition cursor-pointer">
                Đóng
            </button>
        </div>
    </div>
</div>

<!-- Script xử lý mở/đóng Modal & Đánh dấu đã đọc -->
<script>
    function openNotificationDetail(item) {
        if (!item) return;

        // 1. Tiêu đề & Nội dung
        document.getElementById('modalTitle').innerText = item.title || 'Thông báo hệ thống';
        document.getElementById('modalMessage').innerText = item.message || 'Không có nội dung.';
        
        // 2. Thời gian
        document.getElementById('modalTime').innerText = item.created_at || '';

        // 3. Nhãn phân loại động trong Modal
        const badge = document.getElementById('modalTypeBadge');
        if (item.type === 'warning' || item.type === 'incident') {
            badge.className = 'inline-block mb-1 text-[10px] font-bold px-2.5 py-0.5 rounded-lg bg-red-100 text-red-700';
            badge.innerText = 'Nhắc nhở / Sự cố';
        } else if (item.type === 'schedule') {
            badge.className = 'inline-block mb-1 text-[10px] font-bold px-2.5 py-0.5 rounded-lg bg-amber-100 text-amber-900';
            badge.innerText = 'Lịch sát hạch';
        } else {
            badge.className = 'inline-block mb-1 text-[10px] font-bold px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-900';
            badge.innerText = 'Thông báo hệ thống';
        }

        // 4. Gọi API đánh dấu Đã đọc nếu chưa đọc
        if (item.id && item.is_read == 0) {
            fetch(`/notifications/${item.id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật UI ngay lập tức mà không cần reload
                    const card = document.getElementById(`notification-card-${item.id}`);
                    if (card) {
                        const unreadBar = card.querySelector('.unread-bar');
                        if (unreadBar) unreadBar.remove();

                        const unreadDot = card.querySelector('.unread-dot');
                        if (unreadDot) {
                            unreadDot.classList.remove('bg-red-500');
                            unreadDot.classList.add('bg-amber-400');
                        }

                        const wrapper = card.querySelector('.content-wrapper');
                        if (wrapper) wrapper.classList.remove('pl-2');
                    }
                    item.is_read = 1;
                }
            }).catch(error => console.error('Lỗi đánh dấu đã đọc:', error));
        }

        // 5. Hiển thị Modal
        document.getElementById('notificationModal').classList.remove('hidden');
    }

    function closeNotificationDetail() {
        document.getElementById('notificationModal').classList.add('hidden');
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\wamp64\Brewlearn\resources\views\notifications\index.blade.php ENDPATH**/ ?>