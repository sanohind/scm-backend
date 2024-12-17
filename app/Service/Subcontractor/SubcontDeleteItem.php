<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use DB;

class SubcontDeleteItem
{
    public function deleteItem($data) {
        // Get record subcont item
        $itemRecord = SubcontItem::findOrFail( $data['sub_item_id'], 'sub_item_id');

        $result = DB::transaction(function () use($itemRecord) {
            $itemRecord->delete();

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
