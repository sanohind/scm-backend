<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use App\Service\User\BusinessPartnerUnifiedService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Log;

class SubcontImportStockItem
{
    public function __construct(
        protected SubcontCreateStock $subcontCreateStock,
        protected BusinessPartnerUnifiedService $businessPartnerUnifiedService
    ) {}

    public function importStockItem(
        string $bpCode,
        string $partNumber,
        int $freshIncomingItems,
        int $freshProcessItems,
        int $freshNgItems,
        int $replatingIncomingItems,
        int $replatingProcessItems,
        int $replatingNgItems,
    ) {
        try {
            // Get all related bp_codes (parent and children)
            $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($bpCode);
            $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

            // If no related codes found, use the original bp_code
            if (empty($supplierCodes)) {
                $supplierCodes = [$bpCode];
            }

            // Query to get sub_item_id from table subcont_item (unified)
            $getItem = SubcontItem::whereIn('bp_code', $supplierCodes)
                ->where('item_code', $partNumber)
                ->value('sub_item_id');

            // Check stock record availability
            $this->subcontCreateStock->createAndCheckStock($partNumber, $getItem);

            // Query to get stock record from table subcont_stock
            $getStock = SubcontStock::where('sub_item_id', $getItem)
                ->where('item_code', $partNumber)
                ->first();

            // Update/import stock
            DB::transaction(function () use (
                $getStock,
                $freshIncomingItems,
                $freshProcessItems,
                $freshNgItems,
                $replatingIncomingItems,
                $replatingProcessItems,
                $replatingNgItems,
            ) {
                $getStock->update([
                    'incoming_fresh_stock' => $freshIncomingItems,
                    'process_fresh_stock' => $freshProcessItems,
                    'ng_fresh_stock' => $freshNgItems,
                    'incoming_replating_stock' => $replatingIncomingItems,
                    'process_replating_stock' => $replatingProcessItems,
                    'ng_replating_stock' => $replatingNgItems,
                ]);
            });

            return response()->json([
                'status' => true,
                'message' => 'Import Stock Items Successfully',
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error importing stock item: ' . $e->getMessage());
            throw new HttpResponseException(
                response()->json([
                    'status' => false,
                    'message' => 'Failed to import stock item: ' . $e->getMessage(),
                ], 500)
            );
        }
    }
}
