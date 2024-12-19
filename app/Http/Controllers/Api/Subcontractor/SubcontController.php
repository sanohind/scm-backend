<?php

namespace App\Http\Controllers\Api\Subcontractor;

use App\Http\Requests\SubcontItemUpdateRequest;
use App\Models\User;
use App\Service\Subcontractor\SubcontDeleteItem;
use App\Service\Subcontractor\SubcontUpdateItem;
use Str;

use LDAP\Result;

use Carbon\Carbon;
use Illuminate\Http\Request;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SubcontItemRequest;
use App\Http\Requests\FilterByDateRequest;
use App\Service\Subcontractor\SubcontGetItem;
use App\Http\Requests\SubcontTransactionRequest;
use App\Service\Subcontractor\SubcontCreateItem;
use App\Service\Subcontractor\SubcontGetListItem;
use App\Service\Subcontractor\SubcontGetListItemErp;
use App\Service\Subcontractor\SubcontGetTransaction;
use App\Service\Subcontractor\SubcontCreateTransaction;

class SubcontController
{

    public function __construct(
        protected SubcontGetItem $subcontGetItem,
        protected SubcontGetTransaction $subcontGetTransaction,
        protected SubcontCreateItem $subcontCreateItem,
        protected SubcontCreateTransaction $subcontCreateTransaction,
        protected SubcontGetListItem $subcontGetListItem,
        protected SubcontGetListItemErp $subcontGetListItemErp,
        protected SubcontUpdateItem $subcontUpdateItem,
        protected SubcontDeleteItem $subcontDeleteItem,
        ) {}

    /**
     * To get item record user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexItem(Request $param)
    {
        try {
            $result = $this->subcontGetItem->getAllItemSubcont($param ?? null);
        } catch (\Exception $ex) {
            return response()->json([
                'status' => "Failed",
                'error' => "{$ex->getMessage()} (On line {$ex->getLine()}) {$ex->getFile()}"
            ],500);
        }

        return $result;
    }

    /**
     * to get transaction record user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexTrans(Request $request)
    {
        try {
            $result = $this->subcontGetTransaction->getAllTransactionSubcont($request->start_date ?? null,$request->end_date ?? null, $request->bp_code ?? null);
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ],500);
        }

        return $result;
    }

    /**
     * Get list item user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListItem(Request $param) {
        try {
            $result = $this->subcontGetListItem->getList($param ?? null);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }

        return $result;
    }


    public function adminGetAllItem(Request $bp_code) {
        try {
            $result = $this->subcontGetListItem->adminGetAllItemUser($bp_code->bp_code ?? null);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }

        return $result;
    }

    /**
     * Summary of getListItemErp
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListItemErp() {
        try {
            $result = $this->subcontGetListItemErp->getListErp();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }

        return $result;
    }

    /**
     * Create new item
     * @param \App\Http\Requests\SubcontItemRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createItem(SubcontItemRequest $request)
    {
        try {
            // Validate request data and process
            $result = $this->subcontCreateItem->createItemSubcont($request->validated());
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ],500);
        }
        return $result;
    }

    /**
     * Summary of updateItem
     * @param \App\Http\Requests\SubcontItemUpdateRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateItem(SubcontItemUpdateRequest $request) {
        try {
            $result = $this->subcontUpdateItem->updateItem($request->validated());
        } catch (\Throwable $ex) {
            return response()->json([
                'error' => $ex->getMessage(),
            ],500);
        }
        return $result;
    }

    public function deleteItem(SubcontItemUpdateRequest $request){
        try {
            $result = $this->subcontDeleteItem->deleteItem($request->validated());
        } catch (\Throwable $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ],500);
        }
        return $result;
    }

    /**
     * Create new transaction
     * @param \App\Http\Requests\SubcontTransactionRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createTransaction(SubcontTransactionRequest $request)
    {
        try {
                $result = $this->subcontCreateTransaction->createTransactionSubcont($request->validated());

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }
        return $result;
    }
}
