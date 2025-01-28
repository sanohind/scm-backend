<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use DB;

class SubcontDeleteItem
{
    public function deleteItem($data) {
        // Get record subcont item
        $itemRecord = SubcontItem::findOrFail( $data['sub_item_id'], 'sub_item_id');

        $getStock = SubcontStock::where('sub_item_id', $data['sub_item_id'])->firstOrFail();

        $result = DB::transaction(function () use($itemRecord, $getStock) {
            $itemRecord->delete();
            $getStock->delete();

            return true;
        });

        switch ($result) {
            case true:
                return response()->json([
                    "status" => true,
                    "message" => "Item Successfully Deleted",
                ]);

            default:
            throw new \Exception("Error sub_item_id not found", 403);
        }
    }
}
