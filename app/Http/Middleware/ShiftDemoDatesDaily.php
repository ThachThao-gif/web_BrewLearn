<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ShiftDemoDatesDaily
{
    /**
     * Mỗi ngày, ở lần truy cập đầu tiên, dời dữ liệu demo sang hôm nay để các mốc thời gian luôn hợp lý.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('brewlearn.demo_auto_shift') && ! app()->runningUnitTests()) {
            $today = now('Asia/Ho_Chi_Minh')->toDateString();
            $anchorFile = storage_path('app/demo-anchor-date.txt');
            $anchor = is_file($anchorFile) ? trim((string) file_get_contents($anchorFile)) : null;

            if ($anchor !== $today) {
                Cache::lock('brewlearn-demo-shift', 60)->get(function () use ($anchorFile, $today): void {
                    if (! is_file($anchorFile) || trim((string) file_get_contents($anchorFile)) !== $today) {
                        Artisan::call('brewlearn:shift-demo-dates', ['--to' => $today, '--skip-risk' => true]);
                    }
                });
            }
        }

        return $next($request);
    }
}
