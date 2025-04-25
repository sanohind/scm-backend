<?php

namespace App\Http\Controllers\Api\V1\Syncronization;

use Carbon\Carbon;
use App\Models\DeliveryNote\DnDetailErp;
use App\Models\DeliveryNote\DnHeaderErp;
use App\Models\PurchaseOrder\PoDetailErp;
use App\Models\PurchaseOrder\PoHeaderErp;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncSubcontItemData;
use App\Service\Syncronization\SyncDeliveryNoteData;
use App\Service\Syncronization\SyncPurchaseOrderData;
use App\Service\Syncronization\SyncBusinessPartnerData;

class SyncController
{
    public function __construct(
        protected SyncBusinessPartnerData $syncBusinessPartnerData,
        protected SyncPurchaseOrderData $syncPurchaseOrderData,
        protected SyncDeliveryNoteData $syncDeliveryNoteData,
        protected SyncSubcontItemData $syncSubcontItemData,
        protected SyncDeleteData $syncDeleteData,
    ) {
    }

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

            if (!empty($purchaseOrder)) {
                $this->syncDeliveryNoteData->syncDeliveryNote($purchaseOrder); // Delivery Note

                // delete data
                $this->syncDeleteData->deletePo(); // Delete Purchase Order
                $this->syncDeleteData->deleteDn(); // Delete Delivery Note
            }
        } catch (\Throwable $th) {
            \Log::error("$th");
        }

        // Response
        return response()->json([
            'message' => 'Sync Data Successfuly',
        ]);
    }

    /**
     * Test to count how many record per sync
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function syncTest()
    {
        // Initialize variable
        $actualPeriod = Carbon::now()->month;
        $actualYear = Carbon::now()->year;
        $oneMonthBefore = Carbon::now()->subMonths(1)->month; // Change subMonths value if you want to sync within range 1 month (Running every ten minute)

        // Get Purchase Order from range 1 month ago till now on this year
        $sqlsrvDataPoHeader = PoHeaderErp::whereBetween('po_period', [$oneMonthBefore, $actualPeriod])
            ->where('po_year', $actualYear)
            ->get();

        $poNo = $sqlsrvDataPoHeader->pluck('po_no')->toArray();

        $collect3 = collect();
        foreach (array_chunk($poNo, 2000) as $chunk3) {
            $result3 = PoDetailErp::whereIn('po_no', $chunk3)->get();
            $collect3 = $collect3->merge($result3);
        }

        $collect1 = collect();
        foreach (array_chunk($poNo, 2000) as $chunk) {
            $result = DnHeaderErp::whereIn('po_no', $chunk)->get();
            $collect1 = $collect1->merge($result);
        }

        $dnNo = $collect1->pluck('no_dn')->toArray();
        $collect2 = collect();
        foreach (array_chunk($dnNo, 2000) as $chunk2) {
            $result2 = DnDetailErp::whereIn('no_dn', $chunk2)->get();
            $collect2 = $collect2->merge($result2);
        }

        return response()->json([
            'Date' => Carbon::now()->format('d-m-y h:i'),
            'pohead' => count($poNo),
            'podetail' => $collect3->count(),
            'dnhead' => $collect1->count(),
            'dndetail' => $collect2->count(),
        ]);
    }
}
