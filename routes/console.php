<?php

use App\Jobs\Email\MailPoNotificationJob;
use App\Jobs\SyncData\SyncDatabaseJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Synchronize Database Job
Schedule::job(new SyncDatabaseJob)->twiceDaily(8, 18);

// Mail to supplier
Schedule::job(new MailPoNotificationJob)->dailyAt('10:00');

//test
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
