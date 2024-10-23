<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SubcontItemRequest;
use App\Http\Requests\SubcontTransactionRequest;
use App\Models\SubcontStock;
use Carbon\Carbon;
use App\Models\Subcont;
use App\Models\SubcontItem;
use App\Models\SubcontTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubcontItemResource;
use App\Http\Resources\SubcontTransactionResource;

class SubcontController
{
    /**
     * Display a listing of the resource.
     */
    public function indexItem()
    {
        // Show all subcont item data based on authorized user
        $user = Auth::user()->bp_code;

        // Check if user exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 404);
        }

        // Get record of subcont item data
        $data = SubcontItem::with('subTrans', 'subStock')
            ->where('bp_code', $user)
            ->orderBy('item_code', 'asc')
            ->get();

        // Check if data exist
        if ($data->isEmpty()) {
            // response when empty
            return response()->json([
                'status' => false,
                'message' => 'Subcont Transaction Data Not Found',
                'data' => [],
            ], 404);
        } else {
            // response when success
            return response()->json([
                'status' => true,
                'message' => 'Display List Subcont Transaction Successfully',
                'data' => SubcontItemResource::collection($data),
            ], 200);
        }
    }

    public function indexTrans()
    {
        // Show all subcont transaction data based on authorized user
        $user = Auth::user()->bp_code;

        // Check if user exist
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ], 404);
        }

        // Get record of subcont transaction data
        $data = SubcontTransaction::whereHas('subItem', function ($q) use ($user) {
            $q->where('bp_code', $user);
        })
            ->orderBy('transaction_date', 'desc')
            ->get();

        // Check if data exist
        if ($data->isEmpty()) {
            // response when empty
            return response()->json([
                'status' => false,
                'message' => 'Subcont Transaction Data Not Found',
                'data' => [],
            ], 404);
        } else {
            // response when success
            return response()->json([
                'status' => true,
                'message' => 'Display List Subcont Transaction Successfully',
                'data' => SubcontTransactionResource::collection($data),
            ], 200);
        }
    }

    // Item business logic
    public function item(SubcontItemRequest $request)
    {
        // Validate request data
        $validatedData = $request->validated();

        // Store logic
        SubcontItem::create([
            "bp_code" => Auth::user()->bp_code,
            "item_code" => $validatedData["item_code"],
            "item_name" => $validatedData["item_name"],
        ]);

        // Response
        return response()->json([
            "status" => true,
            "message" => "Data Successfuly Stored"
        ], 200);
    }

    // Transaction business logic
    public function transaction(SubcontTransactionRequest $request)
    {
        // Get sub_item_id based on ownership
        $subItemId = SubcontItem::where('item_code', $request->item_code)
            ->where('bp_code', Auth::user()->bp_code)
            ->value('sub_item_id');

        // Validate request data
        $validateData = $request->validated();

        // Store logic (testing the DB::transaction function for prevent store data from any error)
        $result = DB::transaction(function () use ($validateData, $subItemId) {
            // Transaction method
            SubcontTransaction::create([
                'sub_item_id' => $subItemId,
                'transaction_date' => Carbon::now(),
                'transaction_type' => $validateData['transaction_type'],
                'item_code' => $validateData['item_code'],
                'status' => $validateData['status'],
                'qty_ok' => $validateData['qty_ok'],
                'qty_ng' => $validateData['qty_ng'],
            ]);
            // Check status item for adding new stock
            switch ($validateData['status']) {
                // Fresh condition logic (Start)
                case 'Fresh':
                    // Check if data stock exist
                    $checkAvaiblelity = SubcontStock::where('sub_item_id', $subItemId)
                        ->where('item_code', $validateData['item_code'])
                        ->exists();

                    switch ($checkAvaiblelity) {
                        // True value logic
                        case true:
                            // Sum qty_ok and qty_ng
                            $qty_total = $validateData['qty_ok'] + $validateData['qty_ng'];

                            // Get fresh_stock value
                            $stock = SubcontStock::where('sub_item_id', $subItemId)
                                ->where('item_code', $validateData['item_code'])
                                ->first();

                            // Check the transaction_type and then update the stock
                            switch ($validateData['transaction_type']) {
                                case 'In':
                                    $stock->increment('fresh_stock', $qty_total);
                                    break;

                                case 'Out':
                                    if ($stock->fresh_stock < $qty_total) {
                                        return false;
                                    } else {
                                        $stock->decrement('fresh_stock', $qty_total);
                                    }
                                    break;

                                default:

                                    break;
                            }
                            break;

                        // False value logic
                        case false:
                            // Create table subcont stock
                            SubcontStock::create([
                                'sub_item_id' => $subItemId,
                                'item_code' => $validateData['item_code'],
                                'fresh_stock' => 0,
                                'replating_stock' => 0,
                            ]);

                            // Sum qty_ok and qty_ng
                            $qty_total = $validateData['qty_ok'] + $validateData['qty_ng'];

                            // get fresh_stocks
                            $stock = SubcontStock::where('sub_item_id', $subItemId)
                                ->where('item_code', $validateData['item_code'])
                                ->first();

                            // Check the transaction_type and then update the stock
                            switch ($validateData['transaction_type']) {
                                case 'In':
                                    $stock->increment('fresh_stock', $qty_total);
                                    break;

                                case 'Out':
                                    if ($stock->fresh_stock < $qty_total) {
                                        return false;
                                    } else {
                                        $stock->decrement('fresh_stock', $qty_total);
                                    }
                                    break;

                                default:

                                    break;
                            }
                            break;
                    }
                    break;
                // Fresh condition logic (end)
                // Replating condition logic (start)
                case 'Replating':
                    // Check if data stock exist
                    $checkAvaiblelity = SubcontStock::where('sub_item_id', $subItemId)
                        ->where('item_code', $validateData['item_code'])
                        ->exists();

                    switch ($checkAvaiblelity) {
                        // True value logic
                        case true:
                            // Sum qty_ok and qty_ng
                            $qty_total = $validateData['qty_ok'] + $validateData['qty_ng'];

                            // Get fresh_stock value
                            $stock = SubcontStock::where('sub_item_id', $subItemId)
                                ->where('item_code', $validateData['item_code'])
                                ->first();

                            // Check the transaction_type and then update the stock
                            switch ($validateData['transaction_type']) {
                                case 'In':
                                    $stock->increment('replating_stock', $qty_total);
                                    break;

                                case 'Out':
                                    if ($stock->replating_stock < $qty_total) {
                                        return false;
                                    } else {
                                        $stock->decrement('replating_stock', $qty_total);
                                    }
                                    break;

                                default:

                                    break;
                            }
                            break;

                        // False value logic
                        case false:
                            // Create table subcont stock
                            SubcontStock::create([
                                'sub_item_id' => $subItemId,
                                'item_code' => $validateData['item_code'],
                                'fresh_stock' => 0,
                                'replating_stock' => 0,
                            ]);

                            // Sum qty_ok and qty_ng
                            $qty_total = $validateData['qty_ok'] + $validateData['qty_ng'];

                            // get fresh_stocks
                            $stock = SubcontStock::where('sub_item_id', $subItemId)
                                ->where('item_code', $validateData['item_code'])
                                ->first();

                            // Check the transaction_type and then update the stock
                            switch ($validateData['transaction_type']) {
                                case 'In':
                                    dd($qty_total);
                                    $stock->increment('replating_stock', $qty_total);
                                    break;

                                case 'Out':
                                    if ($stock->replating_stock < $qty_total) {
                                        return false;
                                    } else {
                                        $stock->decrement('replating_stock', $qty_total);
                                    }
                                    break;

                                default:

                                    break;
                            }
                            break;
                    }
                    break;
                // Replating condition logic (end)

                default:
                    # code...
                    break;
            }
        });
        // dd($result);

        if ($result === false) {
            return response()->json([
                'status' => false,
                'message' => 'Stock cannot be below 0 / minus',
            ], 200);
        } elseif ($result === null) {
            return response()->json([
                'status' => true,
                'message' => 'Data Successfully Stored',
            ], 200);
        }

    }
}

// note = still trying to figure it out how the message logic should be (18/10/2024 = inprogress)|21/10/2024 =  Done
