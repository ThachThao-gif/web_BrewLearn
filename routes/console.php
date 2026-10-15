<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('brewlearn:calculate-risk')->dailyAt('01:00')->timezone('Asia/Ho_Chi_Minh')->withoutOverlapping();
Schedule::command('brewlearn:remind-assessments')->dailyAt('08:00')->timezone('Asia/Ho_Chi_Minh');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
