<?php

namespace App\Jobs\Syncronization;

use App\Service\Syncronization\SyncBusinessPartnerData;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncDeliveryNoteData;
use App\Service\Syncronization\SyncPurchaseOrderData;
use App\Service\Syncronization\SyncSubcontItemData;
use App\Trait\ErrorLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;

class SyncDatabaseJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, ErrorLog;

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
        SyncBusinessPartnerData $syncBusinessPartnerData,
        SyncPurchaseOrderData $syncPurchaseOrderData,
        SyncDeliveryNoteData $syncDeliveryNoteData,
        SyncSubcontItemData $syncSubcontItemData,
        SyncDeleteData $syncDeleteData
    ): void {
        try {
            $syncBusinessPartnerData->syncBusinessPartner();

            $syncSubcontItemData->syncSubcontItem();

            $poNumber = $syncPurchaseOrderData->syncPurchaseOrder();

            $syncDeliveryNoteData->syncDeliveryNote($poNumber);

            $syncDeleteData->deletePo();

            $syncDeleteData->deleteDn();
        } catch (\Throwable $th) {
            $this->syncError(
                'Cheking Data Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $this->job->getJobId()
            );
        }
    }
}
