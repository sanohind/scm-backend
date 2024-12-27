<?php

namespace App\Service\Subcontractor;

use Illuminate\Support\Facades\DB;
use App\Models\Subcontractor\SubcontItem;

class SubcontUpdateItem
{
    public function updateItem($data){
        // Get record subcont item
        $itemRecord = SubcontItem::findOrFail( $data['sub_item_id'], 'sub_item_id');

        $result = DB::transaction(function () use($data,$itemRecord) {
            if (!empty($itemRecord)) {
                $itemRecord->update($data);
            } else {
                throw new \Exception("Item not found", 403);
            }

            return true;
        });

        switch ($result) {
            case true:
                return response()->json([
                    "status" => true,
                    "message" => "Item Successfully Updated",
                ]);

            default:
            throw new \Exception("Error part_number and part_name", 403);
        }
    }
}
