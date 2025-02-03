<?php

namespace App\Service\Subcontractor;

use App\Http\Resources\Subcontractor\SubcontReviewHeaderResource;
use App\Models\Subcontractor\SubcontTransaction;
use Illuminate\Support\Facades\Auth;

class SubcontReceiveHeader
{
    public function getHeader($bp_code)
    {
        // Check role
        $check = Auth::user()->role;

        if ($check == 4 || $check == 9) {
            $user = $bp_code;
        } else {
            throw new \Exception('User Unauthorized', 403);
        }

        // Query Get Header
        $getHeader = SubcontTransaction::select('delivery_note', 'status', 'transaction_date', 'transaction_time', 'response')
            ->whereIn('transaction_type', ['Outgoing', 'outgoing'])
            ->whereNull('response')
            ->whereHas('subItem', function ($query) use ($user) {
                $query->where('bp_code', $user);
            })
            ->distinct()
            ->get();

        // return response

        return response()->json([
            'status' => true,
            'message' => 'Display List Subcont Review Header Successfully',
            'data' => SubcontReviewHeaderResource::collection($getHeader),
        ], 200);
    }
}
