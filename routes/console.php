<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengecekan otomatis keterlambatan setiap hari pukul 00:01
Schedule::command('loans:check-overdue')->dailyAt('00:01');
