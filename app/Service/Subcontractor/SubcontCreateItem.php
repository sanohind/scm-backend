<?php

namespace App\Service\Subcontractor;

use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;

class SubcontCreateItem
{
    public function __construct(protected SubcontCreateStock $subcontCreateStock) {}

    /**
     * create new data item subcont
     * @param mixed $data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createItemSubcont($data) {
        foreach ($data['data'] as $dataItem) {
            // Store logic
            $createItem = SubcontItem::create([
                "bp_code" => $dataItem["bp_code"],
                "item_code" => $dataItem["part_number"],
                "item_name" => $dataItem["part_name"],
                "item_old_name" => $dataItem["old_part_name"],
                "status" => "1",
            ]);

            // Check stock record availability
            $this->subcontCreateStock->createAndCheckStock($dataItem["part_number"], $createItem->sub_item_id);
        }

        // Response
        return response()->json([
            "status" => true,
            "message" => "Data Successfuly Stored"
        ], 200);
    }
}
