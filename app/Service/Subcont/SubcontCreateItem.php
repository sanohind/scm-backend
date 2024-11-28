<?php

namespace App\Service\Subcont;

use App\Models\SubcontItem;
use Illuminate\Support\Facades\Auth;

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
