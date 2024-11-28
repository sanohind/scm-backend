<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FilterByDateRequest;
use App\Service\Subcont\SubcontCreateItem;
use App\Service\Subcont\SubcontCreateTransaction;
use App\Service\Subcont\SubcontGetListItem;
use App\Service\Subcont\SubcontGetTransaction;
use Carbon\Carbon;
use App\Models\Subcont;
use App\Models\SubcontItem;
use App\Models\SubcontStock;
use App\Models\SubcontTransaction;
use FontLib\TrueType\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Service\Subcont\SubcontGetItem;
use App\Http\Requests\SubcontItemRequest;
use App\Http\Resources\SubcontItemResource;
use App\Http\Requests\SubcontTransactionRequest;
use App\Http\Resources\SubcontTransactionResource;
use LDAP\Result;
use Illuminate\Http\Request;
use Str;

class SubcontController
{

    public function __construct(
        protected SubcontGetItem $subcontGetItem,
        protected SubcontGetTransaction $subcontGetTransaction,
        protected SubcontCreateItem $subcontCreateItem,
        protected SubcontCreateTransaction $subcontCreateTransaction,
        protected SubcontGetListItem $subcontGetListItem
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
    public function indexTrans(FilterByDateRequest $request)
    {
        try {
            // dd($request);
            $result = $this->subcontGetTransaction->getAllTransactionSubcont($request->validated(), $request->bp_code ?? null);
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

    /**
     * Create new item
     * @param \App\Http\Requests\SubcontItemRequest $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function CreateItem(SubcontItemRequest $request)
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

        if ($result === false) {
            return response()->json([
                'status' => false,
                'message' => 'Request data format error',
            ], 422);
        } elseif ($result === true) {
            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Stored',
            ], 200);
        }

    }
}

