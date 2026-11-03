<?php

namespace App\Http\Controllers;

use App\Models\NotificationDetails;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * Thông báo của người dùng đang đăng nhập.
     */
    private function mine(): Builder
    {
        return DB::table('notifications')->where('user_id', auth()->id());
    }

    public function index(): View
    {
        $notifications = $this->mine()->orderByDesc('created_at')->get();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Trả nội dung chi tiết của một thông báo và đánh dấu đã đọc.
     */
    public function show(int $id): JsonResponse
    {
        $notification = $this->mine()->where('id', $id)->first();
        if (! $notification) {
            return response()->json(['error' => 'Không tìm thấy thông báo.'], 404);
        }
        if (! $notification->is_read) {
            $this->mine()->where('id', $id)->update(['is_read' => 1, 'updated_at' => now()]);
        }

        return response()->json([
            'id' => $notification->id,
            'title' => $notification->title ?? 'Thông báo hệ thống',
            'message' => $notification->message ?? 'Không có nội dung.',
            'details' => app(NotificationDetails::class)->parse($notification->message ?? ''),
            'type' => $notification->type ?? 'general',
            'is_read' => 1,
            'created_at' => $notification->created_at ? Carbon::parse($notification->created_at)->format('H:i:s d/m/Y') : null,
        ]);
    }

    public function markAsRead(Request $request, int $id): JsonResponse|RedirectResponse
    {
        if (! $this->mine()->where('id', $id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo.'], 404);
        }
        $this->mine()->where('id', $id)->update(['is_read' => 1, 'updated_at' => now()]);

        // Trang thông báo của quản lý gửi form thường, các trang khác gọi bằng fetch
        if (! $request->expectsJson() && str_contains(url()->previous(), '/manager/')) {
            return back()->with('success', 'Đã đánh dấu thông báo đã đọc.');
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): RedirectResponse
    {
        $this->mine()->where('is_read', 0)->update(['is_read' => 1, 'updated_at' => now()]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc!');
    }

    public function destroy(int $id): JsonResponse
    {
        if (! $this->mine()->where('id', $id)->delete()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông báo hoặc bạn không có quyền xóa.'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa thông báo thành công!']);
    }
}
