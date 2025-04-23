<?php

use App\Jobs\Email\EmailNotificationDailyJob;
use App\Jobs\Syncronization\SyncDatabaseJob;

// Synchronize Database Job
Schedule::job(new SyncDatabaseJob())->everyThirtyMinutes()->withoutOverlapping();

// Mail to supplier
Schedule::job(new EmailNotificationDailyJob())->dailyAt('08:00')->withoutOverlapping();
Schedule::job(new EmailNotificationDailyJob())->dailyAt('15:30')->withoutOverlapping();
