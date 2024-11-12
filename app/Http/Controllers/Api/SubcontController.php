<?php

namespace App\Http\Controllers\Api;

use App\Service\Subcont\SubcontCreateItem;
use App\Service\Subcont\SubcontCreateTransaction;
use App\Service\Subcont\SubcontGetListItem;
use App\Service\Subcont\SubcontGetTransaction;
use Carbon\Carbon;
use App\Models\Subcont;
use App\Models\SubcontItem;
use App\Models\SubcontStock;
use App\Models\SubcontTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Service\Subcont\SubcontGetItem;
use App\Http\Requests\SubcontItemRequest;
use App\Http\Resources\SubcontItemResource;
use App\Http\Requests\SubcontTransactionRequest;
use App\Http\Resources\SubcontTransactionResource;
use LDAP\Result;

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
     * Summary of indexItem
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexItem()
    {
        $result = $this->subcontGetItem->getAllItemSubcont();
        // try {
        // } catch (\Exception $ex) {
        //     return response()->json([
        //         'error' => $ex->getMessage()." (On line ".$ex->getLine().")".$ex->getFile()
        //     ],500);
        // }

        return $result;
    }

    public function indexTrans()
    {
        try {
            $result = $this->subcontGetTransaction->getAllTransactionSubcont();
        } catch (\Exception $ex) {
            return response()->json([
                'error' => $ex->getMessage()
            ],500);
        }

        return $result;
    }

    public function getListItem() {
        try {
            $result = $this->subcontGetListItem->getList();
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage()." (On line ".$th->getLine().")"
            ],500);
        }

        return $result;
    }

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

    // Transaction business logic
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

// note = still trying to figure it out how the message logic should be (18/10/2024 = inprogress)|21/10/2024 =  Done
