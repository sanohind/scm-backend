<?php

namespace App\Jobs\Syncronization;

use App\Trait\ErrorLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncPurchaseOrderData;

class SyncPurchaseOrderJob implements ShouldQueue
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
    public function handle(SyncPurchaseOrderData $syncPurchaseOrderData, SyncDeleteData $syncDeleteData): void
    {
        try {
            $syncPurchaseOrderData->syncPurchaseOrder();
            $syncDeleteData->deletePo();
        } catch (\Throwable $th) {
            $this->syncError(
                'Sync Purchase Order Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $this->job->getJobId()
            );
        }
    }
}
