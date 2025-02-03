<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontTransaction;
use Illuminate\Support\Facades\Auth;

class SubcontUpdateTransaction
{
    public function updateTransaction(
        int $subTransactionId,
        int $actualQtyOk,
        int $actualQtyNg,
    ) {
        // Check user role
        $check = Auth::user()->role;

        if ($check == 4 || $check == 9) {
        } else {
            throw new \Exception('User Forbidden', 403);
        }

        try {
            // Update record transaction
            $findRecord = SubcontTransaction::findOrFail($subTransactionId);

            $findRecord->update([
                'actual_qty_ok_receive' => $actualQtyOk,
                'actual_qty_ng_receive' => $actualQtyNg,
                'response' => 'Receipt',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Transaction Update Successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Request data format error',
            ], 422);
        }
    }
}
