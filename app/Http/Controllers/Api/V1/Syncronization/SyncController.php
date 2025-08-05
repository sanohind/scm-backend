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
            
            $syncSteps = [];
            $startTime = microtime(true);
            
            // sync data
            try {
                $this->syncBusinessPartnerData->syncBusinessPartner(); // Business Partner
                $syncSteps['business_partner'] = 'success';
            } catch (\Throwable $th) {
                $syncSteps['business_partner'] = 'failed: ' . $th->getMessage();
                \Log::error("Business Partner Sync Error: " . $th->getMessage());
            }

            try {
                $this->syncSubcontItemData->syncSubcontItem(); // Subcont Item
                $syncSteps['subcont_item'] = 'success';
            } catch (\Throwable $th) {
                $syncSteps['subcont_item'] = 'failed: ' . $th->getMessage();
                \Log::error("Subcont Item Sync Error: " . $th->getMessage());
            }

            try {
                $purchaseOrder = $this->syncPurchaseOrderData->syncPurchaseOrder(); // Purchase Order *note: must return array
                $syncSteps['purchase_order'] = 'success';
            } catch (\Throwable $th) {
                $syncSteps['purchase_order'] = 'failed: ' . $th->getMessage();
                \Log::error("Purchase Order Sync Error: " . $th->getMessage());
                $purchaseOrder = [];
            }

            if (!empty($purchaseOrder)) {
                try {
                    $this->syncDeliveryNoteData->syncDeliveryNote($purchaseOrder); // Delivery Note
                    $syncSteps['delivery_note'] = 'success';
                } catch (\Throwable $th) {
                    $syncSteps['delivery_note'] = 'failed: ' . $th->getMessage();
                    \Log::error("Delivery Note Sync Error: " . $th->getMessage());
                }

                // delete data
                try {
                    $this->syncDeleteData->deletePo(); // Delete Purchase Order
                    $syncSteps['delete_po'] = 'success';
                } catch (\Throwable $th) {
                    $syncSteps['delete_po'] = 'failed: ' . $th->getMessage();
                    \Log::error("Delete PO Error: " . $th->getMessage());
                }
                
                try {
                    $this->syncDeleteData->deleteDn(); // Delete Delivery Note
                    $syncSteps['delete_dn'] = 'success';
                } catch (\Throwable $th) {
                    $syncSteps['delete_dn'] = 'failed: ' . $th->getMessage();
                    \Log::error("Delete DN Error: " . $th->getMessage());
                }
            } else {
                $syncSteps['delivery_note'] = 'skipped: no purchase order data';
                $syncSteps['delete_po'] = 'skipped: no purchase order data';
                $syncSteps['delete_dn'] = 'skipped: no purchase order data';
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
            
            // Check if all steps are successful
            $failedSteps = array_filter($syncSteps, function($step) {
                return strpos($step, 'failed:') !== false;
            });
            
            if (empty($failedSteps)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Sync Data Successfully',
                    'execution_time_ms' => $executionTime,
                    'sync_steps' => $syncSteps,
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ]);
            } else {
                return response()->json([
                    'status' => 'partial_success',
                    'message' => 'Sync completed with some errors',
                    'execution_time_ms' => $executionTime,
                    'sync_steps' => $syncSteps,
                    'failed_steps' => array_keys($failedSteps),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ], 200);
            }
            
        } catch (\Throwable $th) {
            \Log::error("Sync Controller Error: " . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync failed: ' . $th->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }

    /**
     * Test to count how many record per sync
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function syncTest()
    {
        try {
            // Initialize variable with better logic
            $now = Carbon::now();
            $actualPeriod = $now->month;
            $actualYear = $now->year;
            $oneMonthBefore = $now->copy()->subMonths(1)->month; // Use copy() to avoid modifying original

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
                'Date' => $now->format('d-m-y h:i'),
                'pohead' => count($poNo),
                'podetail' => $collect3->count(),
                'dnhead' => $collect1->count(),
                'dndetail' => $collect2->count(),
                'period_range' => [
                    'from_month' => $oneMonthBefore,
                    'to_month' => $actualPeriod,
                    'year' => $actualYear
                ]
            ]);
        } catch (\Throwable $th) {
            \Log::error("Sync Test Error: " . $th->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Sync test failed: ' . $th->getMessage(),
                'timestamp' => now()->format('Y-m-d H:i:s')
            ], 500);
        }
    }
}
