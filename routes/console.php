<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('kyp:iris-auto-checkout')->everyMinute()->withoutOverlapping();
Schedule::command('kyp:backup')->dailyAt('02:15')->withoutOverlapping();
