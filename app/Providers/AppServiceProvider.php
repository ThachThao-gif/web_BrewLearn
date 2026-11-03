<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Mọi view đều có người dùng hiện tại và danh sách thông báo (dùng cho thanh trên cùng)
        View::composer('*', function ($view): void {
            if (! request()->attributes->has('brewlearn.view-context')) {
                $user = auth()->user();
                $notifications = collect();
                if ($user) {
                    $query = DB::table('notifications')->where('user_id', $user->id);
                    // Quản lý cửa hàng xem cảnh báo nguy cơ ở hồ sơ nhân viên, không nhận dạng thông báo
                    if ($user->isStoreManager()) {
                        $query->whereNotIn('type', ['risk', 'prediction']);
                    }
                    $notifications = $query->orderByDesc('id')->get();
                }
                request()->attributes->set('brewlearn.view-context', ['currentUser' => $user, 'notifications' => $notifications]);
            }
            $view->with(request()->attributes->get('brewlearn.view-context'));
        });
    }
}
