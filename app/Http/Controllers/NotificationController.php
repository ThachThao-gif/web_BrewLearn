<?php

namespace App\Http\Controllers;

use App\Models\NotificationDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    /**
     * =========================================================
     * 1. DANH SÁCH THÔNG BÁO CỦA USER ĐANG ĐĂNG NHẬP
     * =========================================================
     */
    public function index(Request $request)
    {
        $currentUser = auth()->user();

        if (! $currentUser) {
            return redirect()->route('login');
        }

        $userId = auth()->id();

        $notifications = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return view('notifications.index', compact(
            'notifications',
            'currentUser'
        ));
    }

    /**
     * =========================================================
     * 3. XEM CHI TIẾT THÔNG BÁO
     * =========================================================
     */
    public function show($id)
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json([
                'error' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $notification = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (! $notification) {
            return response()->json([
                'error' => 'Không tìm thấy thông báo.',
            ], 404);
        }

        if ((int) $notification->is_read === 0) {
            DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->update([
                    'is_read' => 1,
                    'updated_at' => now(),
                ]);
        }

        $title = $notification->title
            ?? $notification->tieu_de
            ?? 'Thông báo hệ thống';

        $message = $notification->message
            ?? $notification->noi_dung
            ?? $notification->content
            ?? 'Không có nội dung.';

        $type = $notification->type ?? 'general';

        return response()->json([
            'id' => $notification->id,
            'title' => $title,
            'message' => $message,
            'details' => app(NotificationDetails::class)->parse($message),
            'type' => $type,
            'is_read' => 1,
            'created_at' => $notification->created_at
                ? Carbon::parse($notification->created_at)->format('H:i:s d/m/Y')
                : null,
        ]);
    }

    /**
     * =========================================================
     * 4. ĐÁNH DẤU MỘT THÔNG BÁO ĐÃ ĐỌC
     * =========================================================
     */
    public function markAsRead($id)
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $updated = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update([
                'is_read' => 1,
                'updated_at' => now(),
            ]);

        if ($updated === 0) {
            $exists = DB::table('notifications')
                ->where('id', $id)
                ->where('user_id', $userId)
                ->exists();

            if (! $exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông báo.',
                ], 404);
            }
        }

        if (! request()->expectsJson() && str_contains(url()->previous(), '/manager/')) {
            return back()->with('success', 'Đã đánh dấu thông báo đã đọc.');
        }

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * =========================================================
     * 5. ĐÁNH DẤU TẤT CẢ THÔNG BÁO ĐÃ ĐỌC
     * =========================================================
     */
    public function markAllAsRead(Request $request)
    {
        $userId = auth()->id();

        if (! $userId) {
            return redirect()->route('login');
        }

        DB::table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update([
                'is_read' => 1,
                'updated_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Đã đánh dấu tất cả thông báo là đã đọc!'
            );
    }

    /**
     * =========================================================
     * 6. XÓA MỘT THÔNG BÁO
     * =========================================================
     */
    public function destroy($id)
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $deleted = DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();

        if (! $deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông báo hoặc bạn không có quyền xóa.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa thông báo thành công!',
        ]);
    }
}
