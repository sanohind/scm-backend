<?php

use App\Jobs\Email\EmailNotificationDailyJob;
use App\Jobs\SyncData\SyncDatabaseJob;

// Synchronize Database Job
Schedule::job(new SyncDatabaseJob)->twiceDaily(8, 18)->withoutOverlapping();

// Mail to supplier
Schedule::job(new EmailNotificationDailyJob())->dailyAt('07:00')->withoutOverlapping();
Schedule::job(new EmailNotificationDailyJob())->dailyAt('15:30')->withoutOverlapping();
