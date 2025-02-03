<?php

namespace App\Service\Subcontractor;

use App\Http\Resources\Subcontractor\SubcontReviewDetailResource;
use App\Models\Subcontractor\SubcontTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SubcontReceiveDetail
{
    public function getDetail($no_dn)
    {
        // Check role
        $check = Auth::user()->role;

        if ($check == 4 || $check == 9) {
            $deliveryNote = $no_dn;
        } else {
            throw new \Exception('User Unauthorized', 403);
        }

        try {
            // Query get detail
            $getDetail = SubcontTransaction::with('subItem')
                ->where('delivery_note', $deliveryNote)
                ->whereIn('transaction_type', ['Outgoing', 'outgoing'])
                ->get();

            // query get and format date & time
            $firstRecord = $getDetail->first();
            $formatDate = Carbon::parse($firstRecord->transaction_date)->format('Y-m-d');
            $formatTime = Carbon::parse($firstRecord->transaction_time)->format('h:i');
            $getDateTime = "$formatDate $formatTime";
        } catch (\Throwable $th) {
            return response()->json([
                'status' => true,
                'message' => 'Delivery Note Not Found',
            ], 200);
        }

        // return response
        return response()->json([
            'status' => true,
            'message' => 'Display List Subcont Review Detail Successfully',
            'data' => [
                'dn_number' => $deliveryNote,
                'date_time' => $getDateTime,
                'status' => $getDetail->first()->status,
                'status_confirm' => false,
                'detail' => SubcontReviewDetailResource::collection($getDetail),
            ],
        ], 200);
    }
}
