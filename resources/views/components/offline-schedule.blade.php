<!-- COMPONENT LỊCH HẸN OFFLINE -->
<div class="bg-white rounded-3xl border border-[#E2D4C1] p-5 shadow-sm space-y-4">
    
    <!-- Header Card -->
    <div class="flex justify-between items-center">
        <span class="bg-amber-100 text-amber-900 text-[11px] font-extrabold px-3 py-1 rounded-full flex items-center gap-1.5">
            Lịch hẹn sắp tới
        </span>
        <i class="fa-regular fa-calendar text-amber-900 text-base"></i>
    </div>

    <!-- Tiêu đề & Mô tả -->
    <div class="space-y-1">
        <h3 class="text-base font-extrabold text-coffee-900">Kiểm tra thực hành tại quầy</h3>
        <p class="text-xs text-slate-500 leading-relaxed">
            Đánh giá trực tiếp kỹ năng căn chỉnh máy xay & tốc độ ra món trong giờ cao điểm.
        </p>
    </div>

    <!-- Khung thông tin lịch hẹn -->
    <div class="bg-[#FBF6EE] rounded-2xl p-4 space-y-3 border border-[#F2E6D5]">
        <div class="flex items-center gap-3">
            <i class="fa-regular fa-calendar-days text-amber-900 text-sm w-4"></i>
            <span class="text-xs font-extrabold text-coffee-900">Ngày mai, 14:30 – 15:15</span>
        </div>

        <div class="flex items-start gap-3">
            <i class="fa-regular fa-user text-slate-500 text-sm w-4 mt-0.5"></i>
            <div class="text-xs">
                <span class="text-slate-500">Giám sát: </span>
                <span class="font-bold text-coffee-900">Trường ca Nguyễn Hoàng</span>
            </div>
        </div>

        <div class="flex items-start gap-3">
            <i class="fa-solid fa-location-dot text-slate-500 text-sm w-4 mt-0.5"></i>
            <div class="text-xs">
                <span class="font-bold text-coffee-900">Quầy Bar Chính • Chi nhánh Quận 1</span>
            </div>
        </div>
    </div>

    <!-- Thao tác Footer -->
    <div class="pt-2 flex items-center justify-between gap-2">
        <button type="button" onclick="openRubricModal()" class="text-xs font-extrabold text-amber-900 hover:underline">
            Xem tiêu chí chấm (Rubric)
        </button>

        <button id="btnConfirmSchedule" type="button" onclick="confirmSchedule()" class="bg-black hover:bg-slate-800 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition shadow-sm">
            Xác nhận lịch
        </button>
    </div>
</div>

<!-- MODAL TIÊU CHÍ CHẤM (RUBRIC) -->
<div id="rubricModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-4 shadow-xl border border-[#E2D4C1]">
        <div class="flex justify-between items-center border-b pb-3 border-slate-100">
            <h3 class="text-sm font-extrabold text-coffee-900 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-amber-800"></i>
                <span>Tiêu chí đánh giá thực hành tại quầy</span>
            </h3>
            <button onclick="closeRubricModal()" class="text-slate-400 hover:text-black">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="space-y-3 text-xs text-slate-600 max-h-80 overflow-y-auto pr-1">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="font-bold text-coffee-900 block mb-1">1. Kỹ năng căn chỉnh máy xay (30%)</span>
                <p>Thao tác chỉnh cỡ xay phù hợp, định lượng hạt chính xác, chiết xuất Espresso chuẩn 25-30 giây.</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="font-bold text-coffee-900 block mb-1">2. Tốc độ & Quy trình ra món (40%)</span>
                <p>Đảm bảo hoàn thành 3 món tiêu chuẩn trong vòng 5 phút giờ cao điểm, đúng thứ tự công đoạn.</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                <span class="font-bold text-coffee-900 block mb-1">3. Vệ sinh & An toàn quầy Bar (30%)</span>
                <p>Khăn lau phân màu đúng vị trí, giữ khu vực pha chế sạch sẽ, xả họng pha trước và sau khi chiết xuất.</p>
            </div>
        </div>

        <div class="pt-2 text-right">
            <button onclick="closeRubricModal()" class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold px-4 py-2 rounded-xl transition">
                Đóng
            </button>
        </div>
    </div>
</div>

<script>
    function confirmSchedule() {
        const userId = new URLSearchParams(window.location.search).get('user_id') || "1";

        fetch("{{ route('tests.offline.confirm') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                const btn = document.getElementById('btnConfirmSchedule');
                btn.className = "bg-emerald-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl cursor-default flex items-center gap-1";
                btn.innerHTML = `<i class="fa-solid fa-check text-[10px]"></i> Đã xác nhận`;
                btn.onclick = null;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Có lỗi xảy ra khi xác nhận lịch!");
        });
    }

    function openRubricModal() {
        document.getElementById('rubricModal').classList.remove('hidden');
    }

    function closeRubricModal() {
        document.getElementById('rubricModal').classList.add('hidden');
    }
</script>