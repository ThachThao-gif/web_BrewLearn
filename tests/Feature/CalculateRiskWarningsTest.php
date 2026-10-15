<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class CalculateRiskWarningsTest extends TestCase
{
    public function test_command_runs_the_rules_script_from_the_project_root(): void
    {
        Process::fake(['*' => Process::result(output: '{"calculated": 3, "insufficient": 1, "errors": 0}')]);

        $this->artisan('brewlearn:calculate-risk')
            ->expectsOutputToContain('Đã cập nhật cảnh báo nguy cơ.')
            ->assertSuccessful();

        Process::assertRan(fn (PendingProcess $process): bool => $process->path === base_path()
            && $process->command === [config('brewlearn.risk_python'), '-X', 'utf8', base_path('app/ml/ket_noi_db.py')]);
    }

    public function test_command_fails_when_the_script_reports_errors(): void
    {
        Process::fake(['*' => Process::result(errorOutput: 'Không tạo cảnh báo cho user_id=5', exitCode: 1)]);

        $this->artisan('brewlearn:calculate-risk')
            ->expectsOutputToContain('Tính cảnh báo nguy cơ thất bại (mã thoát 1).')
            ->assertFailed();
    }

    public function test_risk_warnings_are_scheduled_nightly_before_the_morning_reminders(): void
    {
        $events = collect(app(Schedule::class)->events());
        $risk = $events->first(fn (Event $event): bool => str_contains($event->command, 'brewlearn:calculate-risk'));

        $this->assertNotNull($risk);
        $this->assertSame('0 1 * * *', $risk->expression);
        $this->assertSame('Asia/Ho_Chi_Minh', $risk->timezone);
        $this->assertTrue($risk->withoutOverlapping);
    }
}
