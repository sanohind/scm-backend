<?php

namespace App\Http\Controllers\Api\V1\Subcontractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subcontractor\SubcontReviewTransactionRequest;
use App\Service\Subcontractor\SubcontCreateTransaction;
use App\Service\Subcontractor\SubcontReceiveDetail;
use App\Service\Subcontractor\SubcontReceiveHeader;
use App\Service\Subcontractor\SubcontUpdateTransaction;
use Illuminate\Support\Facades\DB;

class SubcontReceiveController extends Controller
{
    public function __construct(
        protected SubcontReceiveHeader $subcontReceiveHeader,
        protected SubcontReceiveDetail $subcontReceiveDetail,
        protected SubcontCreateTransaction $subcontCreateTransaction,
        protected SubcontUpdateTransaction $subcontUpdateTransaction,
    ) {}

    public function reviewHeader($bp_code)
    {
        try {
            $result = $this->subcontReceiveHeader->getHeader($bp_code);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage().' (On line '.$th->getLine().')',
            ], 500);
        }

        return $result;
    }

    public function reviewDetail($no_dn)
    {
        try {
            $result = $this->subcontReceiveDetail->getDetail($no_dn);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage().' (On line '.$th->getLine().')',
            ], 500);
        }

        return $result;
    }

    public function reviewUpdate(SubcontReviewTransactionRequest $request)
    {
        try {
            // Validated request
            $validated = $request->validated();

            foreach ($validated['data'] as $data) {
                DB::transaction(function () use ($data) {
                    // Update record transaction after review
                    $this->subcontUpdateTransaction->updateTransaction(
                        $data['sub_transaction_id'],
                        $data['actual_qty_ok'],
                        $data['actual_qty_ng'],
                    );

                    // Create transaction diffrence review
                    $this->subcontCreateTransaction->createSubcontTransactionDifference(
                        $data['sub_transaction_id'],
                        $data['sub_item_id'],
                        $data['actual_qty_ok'],
                        $data['actual_qty_ng'],
                    );
                });
            }

            // Result
            return response()->json([
                'status' => true,
                'message' => 'System Transaction Review Diffrence Successfuly',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage().' (On line '.$th->getLine().')',
            ], 500);
        }
    }
}
