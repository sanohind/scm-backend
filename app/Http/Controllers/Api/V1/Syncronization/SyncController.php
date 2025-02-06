<?php

namespace App\Http\Controllers\Api\V1\Syncronization;

use App\Service\Syncronization\SyncBusinessPartnerData;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncDeliveryNoteData;
use App\Service\Syncronization\SyncPurchaseOrderData;
use App\Service\Syncronization\SyncSubcontItemData;

class SyncController
{
    public function __construct(
        protected SyncBusinessPartnerData $syncBusinessPartnerData,
        protected SyncPurchaseOrderData $syncPurchaseOrderData,
        protected SyncDeliveryNoteData $syncDeliveryNoteData,
        protected SyncSubcontItemData $syncSubcontItemData,
        protected SyncDeleteData $syncDeleteData,
    ) {}

    /**
     * Sync data from erp to data
     * List Sync;
     * 1. Business Partner
     * 2. Subcont Item
     * 3. PurchaseOrder
     * 4. DeliveryNote
     * 5. Delete PurchaseOrder
     * 6. Delete DeliveryNote
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function sync()
    {
        try {
            set_time_limit(0);
            // sync data
            $this->syncBusinessPartnerData->syncBusinessPartner(); // Business Partner

            $this->syncSubcontItemData->syncSubcontItem(); // Subcont Item

            $purchaseOrder = $this->syncPurchaseOrderData->syncPurchaseOrder(); // Purchase Order *note: must return array

            if (! empty($purchaseOrder)) {
                $this->syncDeliveryNoteData->syncDeliveryNote($purchaseOrder); // Delivery Note

                // delete data

                $this->syncDeleteData->deletePo(); // Delete Purchase Order

                $this->syncDeleteData->deleteDn(); // Delete Delivery Note

            }
        } catch (\Throwable $th) {
            // throw $th;
            \Log::error("$th");
        }

        // Response
        return response()->json([
            'message' => 'Sync Data Successfuly',
        ]);
    }
}
