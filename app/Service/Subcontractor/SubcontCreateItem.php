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
        // Store logic
        SubcontItem::create([
            "bp_code" => $data["bp_code"],
            "item_code" => $data["item_code"],
            "item_name" => $data["item_name"],
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "Data Successfuly Stored"
        ], 200);
    }
}
