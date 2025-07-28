<?php

namespace App\Http\Controllers\Api\V1\Subcontractor;

use App\Http\Requests\Subcontractor\SubcontUpdateTransactionRequest;
use App\Models\Subcontractor\SubcontTransaction;
use App\Trait\ResponseApi;
use Illuminate\Http\Request;
use App\Trait\AuthorizationRole;
use App\Service\Subcontractor\SubcontGetItem;
use App\Service\Subcontractor\SubcontCreateItem;
use App\Service\Subcontractor\SubcontDeleteItem;
use App\Service\Subcontractor\SubcontUpdateItem;
use App\Service\Subcontractor\SubcontGetListItem;
use App\Service\Subcontractor\SubcontGetListItemErp;
use App\Service\Subcontractor\SubcontGetTransaction;
use App\Service\Subcontractor\SubcontImportStockItem;
use App\Http\Requests\Subcontractor\SubcontItemRequest;
use App\Service\Subcontractor\SubcontCreateTransaction;
use App\Http\Requests\Subcontractor\SubcontItemUpdateRequest;
use App\Http\Requests\Subcontractor\SubcontTransactionRequest;
use App\Http\Requests\Subcontractor\SubcontImportStockItemRequest;
use Log;

class SubcontController
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     * 2. AuthorizationRole = for checking permissible user role
     */
    use AuthorizationRole, ResponseApi;

    public function __construct(
        protected SubcontGetItem $subcontGetItem,
        protected SubcontGetTransaction $subcontGetTransaction,
        protected SubcontCreateItem $subcontCreateItem,
        protected SubcontCreateTransaction $subcontCreateTransaction,
        protected SubcontGetListItem $subcontGetListItem,
        protected SubcontGetListItemErp $subcontGetListItemErp,
        protected SubcontUpdateItem $subcontUpdateItem,
        protected SubcontDeleteItem $subcontDeleteItem,
        protected SubcontImportStockItem $subcontImportStockItem,
    ) {
    }

    /**
     * To get item record user
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexItem(Request $param)
    {
        try {
            $result = $this->subcontGetItem->getAllItemSubcont($param ?? null);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 'Failed',
                'error' => "{$ex->getMessage()} (On line {$ex->getLine()}) {$ex->getFile()}",
            ], 500);
        }

        return $result;
    }

    /**
     * to get transaction record user
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexTrans(Request $request)
    {
        try {
            $result = $this->subcontGetTransaction->getAllTransactionSubcont($request->start_date ?? null, $request->end_date ?? null, $request->bp_code ?? null);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
            ], 500);
        }

        return $result;
    }

    /**
     * Get list item user
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListItem(Request $param)
    {
        try {
            $result = $this->subcontGetListItem->getList($param ?? null);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage() . ' (On line ' . $th->getLine() . ')',
            ], 500);
        }

        return $result;
    }

    public function adminGetAllItem(Request $bp_code)
    {
        try {
            $result = $this->subcontGetListItem->adminGetAllItemUser($bp_code->bp_code ?? null);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage() . ' (On line ' . $th->getLine() . ')',
            ], 500);
        }

        return $result;
    }

    /**
     * Summary of getListItemErp
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListItemErp()
    {
        try {
            $result = $this->subcontGetListItemErp->getListErp();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage() . ' (On line ' . $th->getLine() . ')',
            ], 500);
        }

        return $result;
    }

    /**
     * Create new item
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createItem(SubcontItemRequest $request)
    {
        // try {
        // Validate request data and process
        $result = $this->subcontCreateItem->createItemSubcont($request->validated());

        // } catch (\Exception $ex) {
        //     return response()->json([
        //         'error' => $ex->getMessage()
        //     ], 500);
        // }
        return $result;
    }

    /**
     * Summary of updateItem
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateItem(SubcontItemUpdateRequest $request)
    {
        try {
            $result = $this->subcontUpdateItem->updateItem($request->validated());
        } catch (\Throwable $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
            ], 500);
        }

        return $result;
    }

    public function deleteItem(SubcontItemUpdateRequest $request)
    {
        try {
            $result = $this->subcontDeleteItem->deleteItem($request->validated());
        } catch (\Throwable $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
            ], 500);
        }

        return $result;
    }

    /**
     * Create new transaction
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createTransaction(SubcontTransactionRequest $request)
    {
        $result = $this->subcontCreateTransaction->createTransactionSubcont($request->validated());

        // Return response
        if ($result == true) {
            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Stored',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Request data format error',
            ], 422);
        }

    }

    /**
     * Update after post transaction subcont
     * @param \App\Http\Requests\Subcontractor\SubcontUpdateTransactionRequest $request
     * @return 
     */
    public function updateTransaction(SubcontUpdateTransactionRequest $request)
    {
        $request->validated();

        $this->subcontCreateTransaction->updateTransactionSubcont(
            $request->transaction_id,
            is_null($request->qty_ok) ? 0 : $request->qty_ok,
            is_null($request->qty_ng) ? 0 : $request->qty_ng,
        );

        return response()->json([
            'status' => true,
            'message' => 'Update Transaction Successful',
        ], 200);
    }

    public function importStockItems(SubcontImportStockItemRequest $request)
    {
        // validated request data
        $validateData = $request->validated();

        foreach ($validateData['data'] as $data) {
            $this->subcontImportStockItem->importStockItem(
                $data['bp_code'],
                $data['part_number'],
                $data['fresh_unprocess_incoming_items'],
                $data['fresh_ready_delivery_items'],
                $data['fresh_ng_items'],
                $data['replating_unprocess_incoming_items'],
                $data['replating_ready_delivery_items'],
                $data['replating_ng_items'],
            );
        }

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Import Stock Items Successfully',
        ], 200);
    }

    public function patchOldRecord()
    {
        try {
            $data = SubcontTransaction::with('subItem')->whereNull('bp_code')->get();

            foreach ($data as $sub) {
                SubcontTransaction::updateOrCreate(
                [
                    'sub_transaction_id' => $sub->sub_transaction_id,
                ],
                [
                    'bp_code' => $sub->subItem->bp_code ?? null,
                    'item_name' => $sub->subItem->item_name ?? null,
                ]
            );
            }
        } catch (\Throwable $th) {
            Log::info('error get record');
        }

        return response()->json([
            'message' => 'berhasil'
        ]);

    }
}
