<?php

use App\Jobs\PartnerJob;
use App\Jobs\SyncDatabaseJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Synchronize Database Job
// Schedule::job(new SyncDatabaseJob)->twiceDaily(10,18);
//test
Schedule::job(new SyncDatabaseJob)->everyMinute();

// Schedule::job(new PartnerJob)->everyMinute();
