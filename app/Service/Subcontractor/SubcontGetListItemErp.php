<?php

namespace App\Service\Subcontractor;

use App\Http\Resources\Subcontractor\SubcontListItemErpResource;
use App\Models\Subcontractor\SubcontItemErp;

class SubcontGetListItemErp
{
    public function getListErp()
    {
        // Get record of subcont item erp data
        $data = SubcontItemErp::select('item', 'description', 'old_item')->get();

        // Check if data exist
        if ($data->isEmpty()) {
            // response when empty
            return response()->json([
                'status' => true,
                'message' => 'Subcont Item Data Not Found',
                'data' => [],
            ], 200);
        } else {
            // response when success
            return response()->json([
                'status' => true,
                'message' => 'Display List Subcont Item Successfully',
                'data' => SubcontListItemErpResource::collection($data),
            ], 200);
        }
    }
}
