<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Resources\User\PartnerResource;
use App\Models\Users\BusinessPartner;

class BusinessPartnerController
{
    /**
     * Get Business Partner data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getBusinessPartner()
    {
        $users = BusinessPartner::select('bp_code', 'bp_name', 'adr_line_1')
            ->where('bp_code', 'like', 'SL%')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN Detail Successfully',
            'data' => PartnerResource::collection($users),
        ], 200);
    }
}
