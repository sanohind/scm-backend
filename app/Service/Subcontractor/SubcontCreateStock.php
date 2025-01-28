<?php

namespace App\Service\Subcontractor;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Subcontractor\SubcontStock;
use Illuminate\Http\Exceptions\HttpResponseException;

class SubcontCreateStock
{
    /**
     * Check the item stock record
     * @param mixed $item_code
     * @param mixed $subItemId
     * @return bool
     */
    public function createAndCheckStock($item_code, $subItemId): bool
    {
        try {
            // Check if data stock exists
            $checkAvaibility = SubcontStock::where('sub_item_id', $subItemId)
                ->where('item_code', $item_code)
                ->exists();

            if (!$checkAvaibility) {
                SubcontStock::create([
                    'sub_item_id' => $subItemId,
                    'item_code' => $item_code,
                    'incoming_fresh_stock' => 0,
                    'incoming_replating_stock' => 0,
                    'process_fresh_stock' => 0,
                    'process_replating_stock' => 0,
                    'ng_fresh_stock' => 0,
                    'ng_replating_stock' => 0,
                ]);
            }
        } catch (\Throwable $th) {
            // Generate random request id
            $randomReqId = "error_".Carbon::now()->format("Ymd;H:i:s")."_".\Str::random(10);

            // Log error to channel internal system error
            Log::error("
                Message => Generate Delivery Note error
                Error => {$th->getMessage()},
                File => {$th->getFile()},
                Line => {$th->getLine()},
                RequestId => $randomReqId,
            ");

            // Response
            throw new HttpResponseException(
                response()->json([
                    "status" => false,
                    "message" => "Internal error while checking stock item (Request_id:$randomReqId)",
                    "error" => "Internal error while checking stock item (Request_id:$randomReqId)",
                ],500)
            );

        }
        return true;
    }
}
