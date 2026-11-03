@extends('layouts.app')

@section('content')
<div class="w-full max-w-5xl mx-auto p-4 md:p-6 space-y-4">
    <div class="page-banner"><h1>Thông báo của tôi</h1><p class="text-sm mt-1">Theo dõi thông báo học tập, lịch đánh giá và kế hoạch đào tạo.</p></div>
    {{-- Header: Bộ lọc & Nút Đọc tất cả --}}
    <div class="bg-white border border-[#E2D4C1] rounded-2xl p-4 flex items-center justify-between shadow-sm">
        <span class="text-xs font-bold text-slate-500 flex items-center gap-2">
            <i class="fa-solid fa-arrow-down-short-wide"></i> Sắp xếp theo thời gian mới nhất
        </span>

        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="text-xs font-bold text-amber-800 hover:text-amber-950 flex items-center gap-1 transition cursor-pointer">
                <i class="fa-solid fa-check-double text-amber-600"></i> Đọc tất cả
            </button>
        </form>
    </div>

    {{-- Danh sách Thông báo --}}
    <div class="space-y-4">
        @forelse($notifications as $item)
            @php 
                $isUnread = ($item->is_read == 0);$titleText     = $item->title ?? 'Thông báo hệ thống';
                $messageText   =$item->message ?? $item->content ?? '';
                $typeText      =$item->type ?? 'general'; 
                $formattedTime = \Carbon\Carbon::parse($item->created_at)->format('H:i:s d/m/Y');
                $details       = app(\App\Models\NotificationDetails::class)->parse($messageText);
            @endphp

            {{-- Card thông báo --}}
            <div id="notification-card-{{ $item->id }}" class="bg-white border border-[#E2D4C1] rounded-2xl p-5 shadow-sm relative overflow-hidden transition hover:border-amber-400">
                {{-- Vạch đỏ bên trái cho tin chưa đọc --}}
                @if($isUnread)
                    <div class="unread-bar absolute left-0 top-0 bottom-0 w-1.5 bg-red-600"></div>
                @endif

                <div class="content-wrapper {{ $isUnread ? 'pl-2' : '' }}">
                    {{-- Header thẻ thông báo --}}
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="unread-dot w-2.5 h-2.5 rounded-full {{ $isUnread ? 'bg-red-500' : 'bg-amber-400' }}"></span>
                            <h3 class="text-xs font-extrabold text-slate-900">
                                @if(in_array($typeText, ['warning', 'incident']))
                                    Sự cố / Nhắc nhở
                                @elseif($typeText === 'schedule')
                                    Lịch sát hạch & Đào tạo
                                @else
                                    Thông báo chung
                                @endif
                            </h3>
                        </div>
                        <span class="text-xs font-semibold text-slate-400">
                            {{ $formattedTime }}
                        </span>
                    </div>

                    {{-- Tiêu đề --}}
                    <h2 class="text-sm md:text-base font-extrabold text-slate-900 mb-2 leading-snug">
                        {{ $titleText }}
                    </h2>

                    {{-- Nội dung tóm tắt --}}
                    <p class="text-xs text-slate-600 leading-relaxed mb-4 line-clamp-2 whitespace-pre-line break-words">
                        {{ $messageText }}
                    </p>

                    {{-- Footer thẻ: Phân loại nhãn & Nút Chi tiết --}}
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                        <div class="flex flex-wrap items-center gap-2">
                            @if(in_array($typeText, ['warning', 'incident']))
                                <span class="bg-red-100 text-red-700 text-[10px] font-black px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i> Nhắc nhở vận hành
                                </span>
                            @elseif($typeText === 'schedule')
                                <span class="bg-amber-100 text-amber-900 text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-check text-[9px]"></i> Lịch sát hạch
                                </span>
                            @else
                                <span class="bg-blue-100 text-blue-900 text-[10px] font-bold px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <i class="fa-solid fa-bell text-[9px]"></i> Thông báo hệ thống
                                </span>
                            @endif
                        </div>

                        {{-- Nút xem chi tiết --}}
                        <button type="button" 
                                onclick='openNotificationDetail({
                                    id: {{ $item->id }},
                                    title: @json($titleText),
                                    message: @json($messageText),
                                    details: @json($details),
                                    type: @json($typeText),
                                    created_at: @json($formattedTime),
                                    is_read: {{ $item->is_read }}
                                })' 
                                class="flex items-center gap-1.5 text-slate-500 hover:text-amber-800 text-xs font-bold transition cursor-pointer border-0 bg-transparent">
                            <span>Chi tiết</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center bg-white rounded-2xl border border-[#E2D4C1]">
                <p class="text-xs text-slate-400">Hiện tại bạn không có thông báo nào.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal xem Chi tiết Thông báo --}}
<div id="notificationModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4" onclick="if(event.target === this) closeNotificationDetail()">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-xl relative border border-[#E2D4C1] max-h-[90vh] flex flex-col">
        {{-- Header Modal --}}
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

        {{-- Body Modal --}}
        <div class="py-4 overflow-y-auto flex-1">
            <div id="modalMessage" class="text-sm text-slate-700 leading-relaxed whitespace-pre-line break-words bg-slate-50 p-4 rounded-xl border border-slate-100"></div>
            <p id="modalSummary" class="hidden text-sm font-semibold text-slate-700 mb-3 whitespace-pre-line break-words"></p>
            <table id="modalDetailTable" class="hidden w-full text-sm border-separate border-spacing-0 border border-slate-200 rounded-xl overflow-hidden">
                <tbody id="modalDetailRows"></tbody>
            </table>
        </div>

        {{-- Footer Modal --}}
        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
            <a id="modalActionLink" href="#" class="hidden px-4 py-2 border border-amber-800 text-amber-800 rounded-xl text-xs font-bold hover:bg-amber-50 transition"></a>
            <button type="button" onclick="closeNotificationDetail()" class="px-4 py-2 bg-amber-800 text-white rounded-xl text-xs font-bold hover:bg-amber-900 transition cursor-pointer">
                Đóng
            </button>
        </div>
    </div>
</div>

{{-- Script xử lý mở/đóng Modal & Đánh dấu đã đọc --}}
<script>
    function openNotificationDetail(item) {
        if (!item) return;

        // 1. Tiêu đề & Nội dung
        document.getElementById('modalTitle').innerText = item.title || 'Thông báo hệ thống';
        renderNotificationBody(item);
        renderNotificationAction(item);

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
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
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

    function renderNotificationBody(item) {
        const message = document.getElementById('modalMessage');
        const summary = document.getElementById('modalSummary');
        const table = document.getElementById('modalDetailTable');
        const rows = document.getElementById('modalDetailRows');
        const details = item.details || { summary: [], rows: [] };
        const useTable = details.rows.length >= 2;

        message.classList.toggle('hidden', useTable);
        summary.classList.toggle('hidden', !useTable || details.summary.length === 0);
        table.classList.toggle('hidden', !useTable);
        rows.replaceChildren();

        if (!useTable) {
            message.innerText = item.message || 'Không có nội dung.';
            return;
        }

        summary.innerText = details.summary.join('\n');
        details.rows.forEach(({ label, value }, index) => {
            const row = document.createElement('tr');
            const head = document.createElement('th');
            const cell = document.createElement('td');
            const divider = index > 0 ? ' border-t border-slate-200' : '';
            head.className = 'w-52 bg-slate-50 px-4 py-3 text-left align-middle font-semibold text-slate-600 sm:whitespace-nowrap border-r border-slate-200' + divider;
            cell.className = 'px-4 py-3 align-middle text-slate-800 whitespace-pre-line break-words' + divider;
            head.textContent = label;
            cell.textContent = value;
            row.append(head, cell);
            rows.append(row);
        });
    }

    const notificationActions = {
        'Kế hoạch đào tạo lại': ['Xem tiến độ học lại', @json(route('courses.index').'#retraining-progress')],
        'Phân công phụ trách đào tạo lại': ['Xem đào tạo lại phụ trách', @json(route('training.assignments'))],
    };

    function renderNotificationAction(item) {
        const link = document.getElementById('modalActionLink');
        const action = notificationActions[item.title];
        link.classList.toggle('hidden', !action);
        if (action) {
            [link.textContent, link.href] = action;
        }
    }

    function closeNotificationDetail() {
        document.getElementById('notificationModal').classList.add('hidden');
    }
</script>
@endsection
