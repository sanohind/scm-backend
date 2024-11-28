<?php

namespace App\Service\Subcont;

use App\Models\SubcontItem;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubcontItemResource;

class SubcontGetItem
{
    /**
     * Get subcont item data based of user session
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAllItemSubcont($param){
        // Show all subcont item data based on authorized user
        $check =Auth::user()->role;

        if ($check == 5) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 6) {
            $user = $param->query('bp_code');
        }

        // Check if user exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 404);
        }

        // Get record of subcont item data
        $data = SubcontItem::with( 'subStock')
            ->where('bp_code', $user)
            ->orderBy('item_code', 'asc')
            ->get();

        // Check if data exist
        if ($data->isEmpty()) {
            // response when empty
            return response()->json([
                'status' => false,
                'message' => 'Subcont Item Data Not Found',
                'data' => [],
            ], 200);
        } else {
            // response when success
            return response()->json([
                'status' => true,
                'message' => 'Display List Subcont Item Successfully',
                'data' => SubcontItemResource::collection($data),
            ], 200);
        }
    }
}
