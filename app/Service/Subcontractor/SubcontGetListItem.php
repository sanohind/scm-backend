<?php

namespace App\Service\Subcontractor;

use App\Http\Resources\Subcontractor\SubcontAllListItemResource;
use App\Http\Resources\Subcontractor\SubcontListItemResource;
use App\Models\Subcontractor\SubcontItem;
use App\Models\Users\BusinessPartner;
use Illuminate\Support\Facades\Auth;

class SubcontGetListItem
{
    /**
     * Get list of item based of user session
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getList($param)
    {
        // Show all subcont item data based on authorized user
        $check = Auth::user()->role;

        if ($check == 6 || $check == 8) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 4 || $check == 9) {
            $user = $param->bp_code;
        }

        // Check if user exist
        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        // Get record of subcont item data
        $data = SubcontItem::select('item_code', 'item_name', 'item_old_name')
            ->where('bp_code', $user)
            ->where('status', '1')
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
                'data' => SubcontListItemResource::collection($data),
            ], 200);
        }
    }

    public function adminGetAllItemUser($bp_code)
    {
        // Check if user exist
        $user = BusinessPartner::findOrFail($bp_code, 'bp_code');

        if (! $user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        // Get all record of user subcont item data
        $data = SubcontItem::where('bp_code', $bp_code)
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
                'data' => SubcontAllListItemResource::collection($data),
            ], 200);
        }
    }
}
