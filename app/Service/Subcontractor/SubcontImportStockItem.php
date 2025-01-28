<?php

namespace App\Service\Subcontractor;

use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontImportStockItem
{
    public function __construct(protected SubcontCreateStock $subcontCreateStock) {}

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
            // Query to get sub_item_id form table subcont_item
            $getItem = SubcontItem::where('bp_code', $bpCode)
            ->where('item_code', $partNumber)
            ->value('sub_item_id');

            // Check stock record availability
            $this->subcontCreateStock->createAndCheckStock($partNumber, $getItem);

            // Query to get stock record form table subcont_stock
            $getStock = SubcontStock::where('sub_item_id', $getItem)
            ->where('item_code', $partNumber)
            ->first();


            // Update/import stock
            DB::transaction(function () use(
                $getStock,
                $freshIncomingItems,
                $freshProcessItems,
                $freshNgItems,
                $replatingIncomingItems,
                $replatingProcessItems,
                $replatingNgItems,
                ) {
                $getStock->update([
                    "incoming_fresh_stock" => $freshIncomingItems,
                    "process_fresh_stock" => $freshProcessItems,
                    "ng_fresh_stock" => $freshNgItems,
                    "incoming_replating_stock" => $replatingIncomingItems,
                    "process_replating_stock" => $replatingProcessItems,
                    "ng_replating_stock" => $replatingNgItems,
                ]);
            });
        } catch (\Throwable $th) {
            // Generate random request id
            $randomReqId = "error_".Carbon::now()->format("Ymd;H:i:s")."_".\Str::random(10);

            // Log error to channel internal system error
            Log::error("
                Message => Input Import Stock Data Error,
                Error => {$th->getMessage()},
                File => {$th->getFile()},
                Line => {$th->getLine()},
                RequestId => $randomReqId,
            ");

            // Response
            throw new HttpResponseException(
                response()->json([
                    "status" => false,
                    "message" => "Internal error while input stock (Request_id:$randomReqId)",
                    "error" => "Internal error while input stock (Request_id:$randomReqId)",
                ],500)
            );
        }

   }
}
