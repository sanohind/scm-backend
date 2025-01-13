<?php

namespace App\Http\Controllers\Api\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Subcontractor\SubcontTransaction;
use App\Service\Subcontractor\SubcontReceiveDetail;
use App\Service\Subcontractor\SubcontReceiveHeader;
use App\Http\Requests\SubcontReviewTransactionRequest;
use App\Service\Subcontractor\SubcontCreateTransaction;
use App\Service\Subcontractor\SubcontUpdateTransaction;
use App\Http\Resources\Subcontractor\SubcontReviewHeaderResource;

class SubcontReceiveController extends Controller
{
    public function __construct(
        protected SubcontReceiveHeader $subcontReceiveHeader,
        protected SubcontReceiveDetail $subcontReceiveDetail,
        protected SubcontCreateTransaction $subcontCreateTransaction,
        protected SubcontUpdateTransaction $subcontUpdateTransaction,
    ) {}

    public function reviewHeader($bp_code) {
        try {
            $result = $this->subcontReceiveHeader->getHeader($bp_code);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }
        return $result;
    }

    public function reviewDetail($no_dn) {
        try {
            $result = $this->subcontReceiveDetail->getDetail($no_dn);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }
        return $result;
    }

    public function reviewUpdate(SubcontReviewTransactionRequest $request) {
        try {
            // Validated request
            $validated = $request->validated();

            foreach ($validated['data'] as $data) {
                DB::transaction(function () use($data) {
                    // Update record transaction after review
                    $this->subcontUpdateTransaction->updateTransaction(
                        $data['sub_transaction_id'],
                        $data['actual_qty_ok'],
                        $data['actual_qty_ng'],
                        $data['response'],
                    );

                    // Create transaction diffrence review
                    $this->subcontCreateTransaction->createSubcontTransactionDifference(
                        $data['sub_item_id'],
                        $data['delivery_note'],
                        $data['part_number'],
                        $data['status'],
                        $data['actual_qty_ok'],
                        $data['actual_qty_ng'],
                    );
                });
            }

            // Result
            return response()->json([
                'status' => true,
                'message' => "System Transaction Review Diffrence Successfuly"
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }
    }
}
