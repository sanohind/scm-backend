<?php

namespace App\Service\Subcontractor;

use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;

class SubcontCreateItem
{
    /**
     * create new data item subcont
     * @param mixed $data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createItemSubcont($data) {
        foreach ($data['data'] as $dataItem) {
            // Store logic
            SubcontItem::create([
                "bp_code" => $dataItem["bp_code"],
                "item_code" => $dataItem["item_code"],
                "item_name" => $dataItem["item_name"],
                "status" => "1",
            ]);
        }

        // Response
        return response()->json([
            "status" => true,
            "message" => "Data Successfuly Stored"
        ], 200);
    }
}
