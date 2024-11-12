<?php

namespace App\Service\Subcont;

use App\Models\SubcontItem;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubcontItemResource;

class SubcontGetItem
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function getAllItemSubcont(){
        // Show all subcont item data based on authorized user
        $user = Auth::user()->bp_code;

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
        // dd($data);
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
