<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        if ($demoToday = config('brewlearn.demo_today')) {
            $this->fixDemoToday($demoToday);
        }

        // Mọi view đều có người dùng hiện tại và danh sách thông báo (dùng cho thanh trên cùng)
        View::composer('*', function ($view): void {
            if (! request()->attributes->has('brewlearn.view-context')) {
                $user = auth()->user();
                $notifications = collect();
                if ($user) {
                    $query = DB::table('notifications')->where('user_id', $user->id);
                    // Quản lý cửa hàng xem cảnh báo nguy cơ ở hồ sơ nhân viên, không nhận dạng thông báo
                    if ($user->isStoreManager()) {
                        $query->where('type', '!=', 'risk');
                    }
                    $notifications = $query->orderByDesc('id')->get();
                }
                request()->attributes->set('brewlearn.view-context', ['currentUser' => $user, 'notifications' => $notifications]);
            }
            $view->with(request()->attributes->get('brewlearn.view-context'));
        });
    }

    /**
     * Dữ liệu minh họa được dựng tại một ngày cố định: mọi phép tính "hôm nay" (PHP và giá trị mặc định
     * thời gian của MySQL) dùng ngày đó, kèm giờ phút thật để thao tác mới vẫn sắp xếp đúng thứ tự.
     */
    private function fixDemoToday(string $demoToday): void
    {
        $vietnam = new DateTimeZone('Asia/Ho_Chi_Minh');
        $demoNow = fn (): Carbon => Carbon::parse($demoToday.' '.(new DateTimeImmutable('now', $vietnam))->format('H:i:s.u'), $vietnam)
            ->setTimezone(config('app.timezone'));
        Carbon::setTestNow($demoNow);
        CarbonImmutable::setTestNow(fn (): CarbonImmutable => $demoNow()->toImmutable());

        $fixMysql = function (Connection $connection) use ($demoNow): void {
            if ($connection->getDriverName() === 'mysql') {
                $connection->statement('SET timestamp = ?', [$demoNow()->getTimestamp()]);
            }
        };
        // Kết nối đã mở trước khi provider này chạy, và các kết nối mở sau đó
        foreach (DB::getConnections() as $connection) {
            $fixMysql($connection);
        }
        Event::listen(ConnectionEstablished::class, fn (ConnectionEstablished $event) => $fixMysql($event->connection));

        // Trình duyệt dùng đồng hồ thật: hạn của cookie (phiên đăng nhập, CSRF) phải tính theo giờ thật,
        // nếu không cookie bị coi là đã hết hạn và người dùng không đăng nhập được.
        Event::listen(RequestHandled::class, function (RequestHandled $event) use ($demoNow): void {
            $offset = time() - $demoNow()->getTimestamp();
            foreach ($event->response->headers->getCookies() as $cookie) {
                if ($cookie->getExpiresTime() > 0) {
                    $event->response->headers->setCookie($cookie->withExpires($cookie->getExpiresTime() + $offset));
                }
            }
        });
    }
}
