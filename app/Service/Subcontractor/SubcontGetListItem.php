<?php

namespace App\Service\Subcontractor;


use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use App\Http\Resources\Subcontractor\SubcontListItemResource;

class SubcontGetListItem
{
    /**
     * Get list of item based of user session
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getList($param) {
        // Show all subcont item data based on authorized user
        $check = Auth::user()->role;

        if ($check == 6) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 4) {
            $user = $param->query("bp_code");
        }

        // Check if user exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 404);
        }

        // Get record of subcont item data
        $data = SubcontItem::select('item_code','item_name')
            ->where('bp_code', $user)
            ->orderBy('item_name', 'asc')
            ->get();

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
                'data' => SubcontListItemResource::collection($data)
            ], 200);
        }
    }
}
