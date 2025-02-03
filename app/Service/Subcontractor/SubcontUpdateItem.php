<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use Illuminate\Support\Facades\DB;

class SubcontUpdateItem
{
    public function updateItem($data)
    {
        // Get record subcont item
        $itemRecord = SubcontItem::findOrFail($data['sub_item_id']);
        // dd($data);
        $result = DB::transaction(function () use ($data, $itemRecord) {
            $itemRecord->update([
                'item_code' => $data['part_number'] ?? $itemRecord->item_code,
                'item_name' => $data['part_name'] ?? $itemRecord->item_name,
                'item_old_name' => $data['old_part_name'] ?? $itemRecord->item_old_name,
                'status' => $data['status'] ?? $itemRecord->status,
            ]);

            if (! empty($itemRecord)) {
            } else {
                throw new \Exception('Item not found', 403);
            }

            return true;
        });

        switch ($result) {
            case true:
                return response()->json([
                    'status' => true,
                    'message' => 'Item Successfully Updated',
                ]);

            default:
                throw new \Exception('Error part_number and part_name', 403);
        }
    }
}
