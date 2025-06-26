<?php

namespace App\Service\Subcontractor;

use App\Http\Resources\Subcontractor\SubcontTransactionResource;
use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontTransaction;
use Illuminate\Support\Facades\Auth;
use Log;

class SubcontGetTransaction
{
    /**
     * Get all log user transaction
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getAllTransactionSubcont($start_date, $end_date, $bp_code)
    {
        // Show all subcont transaction data based on authorized user
        $check = Auth::user()->role;

        if ($check == 6 || $check == 8) {
            $bpCode = Auth::user()->bp_code;
        } elseif ($check == 4 || $check == 9) {
            $bpCode = $bp_code;
        }

        // Check if user exist
        if (! $bpCode) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ], 404);
        }

        // Get record of subcont transaction data
        $data = SubcontTransaction::with('subItem')->where('bp_code', $bpCode)
        ->whereBetween('transaction_date', [$start_date, $end_date])
        ->orderBy('transaction_date', 'desc')
        ->orderBy('transaction_time', 'desc')
        ->get();
        
        // Check if data exist
        if ($data->isEmpty()) {
            // response when empty
            return response()->json([
                'status' => true,
                'message' => 'Subcont Transaction Data Not Found',
                'data' => [],
            ], 200);
        } else {
            // response when success
            return response()->json([
                'status' => true,
                'message' => 'Display List Subcont Transaction Successfully',
                'data' => SubcontTransactionResource::collection($data),
            ], 200);
        }
    }
}
