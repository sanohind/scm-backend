<?php

use App\Jobs\Email\EmailNotificationDailyJob;
use App\Jobs\Syncronization\SyncDatabaseJob;

// Synchronize Database Job
Schedule::job(new SyncDatabaseJob())->everyTenMinutes()->withoutOverlapping(10);

// Mail to supplier
Schedule::job(new EmailNotificationDailyJob())->dailyAt('07:00')->withoutOverlapping();
Schedule::job(new EmailNotificationDailyJob())->dailyAt('15:30')->withoutOverlapping();
