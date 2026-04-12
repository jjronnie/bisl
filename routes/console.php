<?php

use App\Jobs\CalculateMonthlyInterestJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new CalculateMonthlyInterestJob)
    ->cron('0 23 L * *')
    ->timezone('Africa/Kampala')
    ->name('calculate-monthly-interest')
    ->onOneServer()
    ->withoutOverlapping();
