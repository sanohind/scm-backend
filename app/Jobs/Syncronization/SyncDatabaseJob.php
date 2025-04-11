<?php

namespace App\Jobs\Syncronization;

use Throwable;
use App\Trait\ErrorLog;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncDatabaseJob implements ShouldQueue
{
    use ErrorLog, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
    ): void {
        Bus::chain([
            new SyncBusinessPartnerJob,
            new SyncSubcontItemJob,
            new SyncPurchaseOrderJob,
            new SyncDeliveryNoteJob,
        ])->dispatch();
    }
}
