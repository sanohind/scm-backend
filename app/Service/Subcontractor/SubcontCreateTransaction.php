<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use App\Models\Subcontractor\SubcontTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class SubcontCreateTransaction
{
    /**
     * Create new transaction business logic for multiple items
     *
     * @param  array  $data
     * @return bool
     *
     * @throws \Exception
     */
    public function createTransactionSubcont($data)
    {
        // check user role
        $check = Auth::user()->role;

        if ($check == 6 || $check == 8) {
            $bp_code = Auth::user()->bp_code;
            // dd($bp_code);
        } elseif ($check == 9) {
            $bp_code = $data['bp_code'];
        }

        // Start foreach loop subcont transaction
        foreach ($data['data'] as $dataTransaction) {
            // Check item statuus
            $itemStatus = $this->checkItemStatus($bp_code, $dataTransaction['item_code']);

            // handle if check item status return false
            if ($itemStatus == false) {
                // Response
                throw new HttpResponseException(
                    response()->json([
                        'status' => false,
                        'message' => 'The item is inactive and cannot be used.',
                    ], 403)
                );
            }

            // Get sub_item_id for each item
            $subItemId = SubcontItem::where('item_code', $dataTransaction['item_code'])
                ->where('bp_code', $bp_code)
                ->value('sub_item_id');

            try {
                // Generate unique delivery note if not provided
                if (empty($dataTransaction['delivery_note'])) {
                    $todayLatestProcess = Carbon::now()->format('Ymd');
                    $today = Carbon::now()->format('dmy');
                    $user = substr(Auth::user()->bp_code, strpos(Auth::user()->bp_code, 'SLS') + 3, 5);
                    $getLatestProcess = SubcontTransaction::where('delivery_note', 'like', "$user$today-%")
                        ->where('transaction_type', 'Process')
                        ->where('transaction_date', $todayLatestProcess)
                        ->count();
                    $unique_dn_process = "$user$today-" . ($getLatestProcess + 1);
                    $dataTransaction['delivery_note'] = $unique_dn_process;
                }
            } catch (\Throwable $th) {
                // Generate random request id
                $randomReqId = 'error_' . Carbon::now()->format('Ymd;H:i:s') . '_' . \Str::random(10);

                // Log error to channel internal system error
                Log::error("
                    Message => Generate Delivery Note error
                    Error => {$th->getMessage()},
                    File => {$th->getFile()},
                    Line => {$th->getLine()},
                    RequestId => $randomReqId,
                ");

                // Response
                throw new HttpResponseException(
                    response()->json([
                        'status' => false,
                        'message' => "Internal error while generate delivery note (Request_id:$randomReqId)",
                        'error' => "Internal error while generate delivery note (Request_id:$randomReqId)",
                    ], 500)
                );
            }

            // Start DB::transaction, Create transaction
            DB::transaction(function () use ($dataTransaction, $subItemId) {
                // Create the transaction
                SubcontTransaction::create([
                    'delivery_note' => $dataTransaction['delivery_note'],
                    'sub_item_id' => $subItemId,
                    'transaction_type' => $dataTransaction['transaction_type'],
                    'actual_transaction_date' => $dataTransaction['actual_transaction_date'],
                    'actual_transaction_time' => $dataTransaction['actual_transaction_time'],
                    'transaction_date' => Carbon::now()->format('Y-m-d'),
                    'transaction_time' => Carbon::now()->format('H:i:s'),
                    'item_code' => $dataTransaction['item_code'],
                    'status' => $dataTransaction['status'],
                    'qty_ok' => $dataTransaction['qty_ok'],
                    'qty_ng' => $dataTransaction['qty_ng'],
                ]);

                // Check stock record availability
                $checkStockRecordAvaibility = $this->checkStockRecordAvailability($dataTransaction['item_code'], $subItemId);

                // Get stock
                $stock = SubcontStock::with('subItem')
                    ->where('sub_item_id', $subItemId)
                    ->where('item_code', $dataTransaction['item_code'])
                    ->first();

                // Validate and calculate stock
                if ($checkStockRecordAvaibility && !empty($stock)) {
                    // Calculate stock
                    $this->calculatingStock(
                        $dataTransaction['status'],
                        $dataTransaction['transaction_type'],
                        $dataTransaction['qty_ok'],
                        $dataTransaction['qty_ng'],
                        $stock
                    );
                } else {
                    throw new Exception('Error processing check stock record availability', 500);
                }
            });
            // End DB::transaction, Create transaction
        }
        // End foreach loop subcont transaction

        return true;
    }

    /**
     * Update the existing transaction
     * @param string $subTransactionId
     * @param int $qtyOk
     * @param int $qtyNg
     * @throws \Exception
     * @return void
     */
    public function updateTransactionSubcont(
        string $subTransactionId,
        int $qtyOk = 0,
        int $qtyNg = 0,
    ) {
        $transactionData = SubcontTransaction::where('sub_transaction_id', $subTransactionId)->first();
        if ($transactionData->transaction_date) {
            $transactionDate = Carbon::parse($transactionData->transaction_date);
            $currentDate = Carbon::now();

            if ($transactionDate->diffInDays($currentDate) > 7) {
                throw new HttpResponseException(
                    response()->json([
                        'status' => false,
                        'message' => 'The transaction record is older than 7 days and cannot be updated.',
                    ], 403)
                );
            }
        }

        $stockData = SubcontStock::where('sub_item_id', $transactionData->sub_item_id)->first();

        try {
            DB::transaction(callback: function () use ($transactionData, $stockData, $qtyOk, $qtyNg) {
                $diffrenceQtyNg = $transactionData->qty_ng - $qtyNg;
                $diffrenceQtyOk = $transactionData->qty_ok - $qtyOk;

                switch ($transactionData->status) {
                    case 'Fresh':
                        if ($transactionData->transaction_type == 'Incoming') {
                            $transactionType = 'Incoming';

                            if ($qtyOk != 0) {
                                $stockData->increment('incoming_fresh_stock', $diffrenceQtyOk);
                            }

                            if ($qtyNg != 0) {
                                $stockData->increment('ng_fresh_stock', $diffrenceQtyNg);
                            }
                        } elseif ($transactionData->transaction_type == 'Process') {
                            $transactionType = 'Process';

                            if ($qtyOk != 0) {
                                $stockData->increment('process_fresh_stock', $diffrenceQtyOk);
                            }

                            if ($qtyNg != 0) {
                                $stockData->increment('ng_fresh_stock', $diffrenceQtyNg);
                            }
                        }
                        break;

                    case 'Replating':
                        if ($transactionData->transaction_type == 'Incoming') {
                            $transactionType = 'Incoming';

                            if ($qtyOk != 0) {
                                $stockData->increment('incoming_replating_stock', $diffrenceQtyOk);
                            }

                            if ($qtyNg != 0) {
                                $stockData->increment('ng_replating_stock', $diffrenceQtyNg);
                            }
                        } elseif ($transactionData->transaction_type == 'Process') {
                            $transactionType = 'Process';

                            if ($qtyOk != 0) {
                                $stockData->increment('process_replating_stock', $diffrenceQtyOk);
                            }

                            if ($qtyNg != 0) {
                                $stockData->increment('ng_replating_stock', $diffrenceQtyNg);
                            }
                        }
                        break;
                }

                // Create transaction update log
                // SubcontTransaction::create([
                //     'delivery_note' => "Edit-{$transactionData->delivery_note}",
                //     'sub_item_id' => $transactionData->sub_item_id,
                //     'transaction_type' => $transactionType,
                //     'transaction_date' => Carbon::now()->format('Y-m-d'),
                //     'transaction_time' => Carbon::now()->format('H:i:s'),
                //     'item_code' => $transactionData->item_code,
                //     'status' => $transactionData->status,
                //     'qty_ok' => ($qtyOk != 0) ? $diffrenceQtyOk : 0,
                //     'qty_ng' => ($qtyNg != 0) ? $diffrenceQtyNg : 0,
                // ]);

                // Update transaction record
                $transactionData->update([
                    'qty_ok' => ($qtyOk != 0) ? $qtyOk : $transactionData->qty_ok,
                    'qty_ng' => ($qtyNg != 0) ? $qtyNg : $transactionData->qty_ng,
                    'response' => "Edited"
                ]);

            });
        } catch (\Throwable $th) {
            throw new Exception('Error processing update transaction', 500);
        }
    }

    /**
     * Summary of createSubcontTransactionDifference
     * create system transaction after review the diffrance receipt
     *
     * @return void
     *
     * @throws \Exception
     */
    public function createSubcontTransactionDifference(
        string $subTransactionId,
        int $subItemId,
        int $actualQtyOk,
        int $actualQtyNg,
    ) {
        // Check user role
        $check = Auth::user()->role;

        if ($check == 4 || $check == 9) {
        } else {
            throw new Exception('User Forbidden', 403);
        }

        try {
            DB::transaction(function () use ($subTransactionId, $subItemId, $actualQtyOk, $actualQtyNg, ) {
                // Get transaction record
                $getTrans = SubcontTransaction::where('sub_transaction_id', $subTransactionId)->first();

                // Declare variable
                $dnNo = $getTrans->delivery_note;
                $itemCode = $getTrans->item_code;
                $status = $getTrans->status;
                $type = 'Process';
                $diffrenceQtyOk = $getTrans->qty_ok - $actualQtyOk;
                $diffrenceQtyNg = $getTrans->qty_ng - $actualQtyNg;

                if ($diffrenceQtyOk + $diffrenceQtyNg != 0) {
                    // Create the transaction
                    SubcontTransaction::create([
                        'delivery_note' => "System-$dnNo",
                        'sub_item_id' => $subItemId,
                        'transaction_type' => $type,
                        'transaction_date' => Carbon::now()->format('Y-m-d'),
                        'transaction_time' => Carbon::now()->format('H:i:s'),
                        'item_code' => $itemCode,
                        'status' => $status,
                        'qty_ok' => $diffrenceQtyOk,
                        'qty_ng' => $diffrenceQtyNg,
                        'response' => "System Review-$dnNo",
                    ]);

                    // Get stock
                    $stock = SubcontStock::with('subItem')
                        ->where('sub_item_id', $subItemId)
                        ->where('item_code', $itemCode)
                        ->first();

                    // Calculate
                    $this->calculatingStock(
                        $status,
                        $type,
                        $diffrenceQtyOk,
                        $diffrenceQtyNg,
                        $stock,
                        'yes'
                    );
                }
            });
        } catch (\Throwable $th) {
            throw new Exception('Error processing transaction review', 500);
        }
    }

    /**
     * Summary of calculatingStock
     * add new item stock
     * Adding and reducing item stock.
     *
     * @param  mixed  $system,  if no the transaction will be identified as human interaction, then if yes the transaction will be identified as system interaction
     *
     * @throws \Exception
     */
    private function calculatingStock(string $status, string $type, int $qtyOk, int $qtyNg, SubcontStock $stock, $system = 'no'): bool
    {
        switch ($status) {
            // Fresh
            case 'Fresh':
                switch ($type) {
                    case 'Incoming':
                        // qty_ok
                        $stock->increment('incoming_fresh_stock', $qtyOk);

                        // qty_ng
                        if (!empty($qtyNg)) {
                            $stock->increment('ng_fresh_stock', $qtyNg);
                        }
                        break;

                    case 'Process':
                        // Start Check system value
                        switch ($system) {
                            case 'yes':
                                // Qty Ok
                                $stock->increment('process_fresh_stock', $qtyOk);

                                //Qty Ng
                                $stock->increment('ng_fresh_stock', $qtyNg);
                                break;
                            case 'no':
                                // qty_ok
                                if ($stock->incoming_fresh_stock < $qtyOk) {
                                    // Throw response
                                    throw new HttpResponseException(
                                        response()->json([
                                            'status' => false,
                                            'message' => 'Insufficient stock.',
                                            'error' => [
                                                "The remaining unprocess fresh stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->incoming_fresh_stock}\".",
                                            ],
                                        ], 422)
                                    );
                                } else {
                                    $stock->decrement('incoming_fresh_stock', $qtyOk);
                                    $stock->increment('process_fresh_stock', $qtyOk);
                                }

                                // qty_ng
                                if ($stock->incoming_fresh_stock < $qtyNg) {
                                    // Throw Response
                                    throw new HttpResponseException(
                                        response()->json([
                                            'status' => false,
                                            'message' => 'Insufficient stock.',
                                            'error' => [
                                                "The remaining unprocess fresh stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->incoming_fresh_stock}\".",
                                            ],
                                        ], 422)
                                    );
                                } else {
                                    $stock->decrement('incoming_fresh_stock', $qtyNg);
                                    $stock->increment('ng_fresh_stock', $qtyNg);
                                }
                                break;
                            default:
                                throw new Exception('Error for System: Only accept "yes/no" value', 500);
                        }
                        // End Check system value
                        break;

                    case 'Outgoing':
                        // qty_ok
                        if ($stock->process_fresh_stock < $qtyOk) {
                            // Throw response
                            throw new HttpResponseException(
                                response()->json([
                                    'status' => false,
                                    'message' => 'Insufficient stock.',
                                    'error' => [
                                        "The remaining ready fresh stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->process_fresh_stock}\".",
                                    ],
                                ], 422)
                            );
                        } else {
                            $stock->decrement('process_fresh_stock', $qtyOk);
                        }

                        // qty_ng
                        if ($stock->ng_fresh_stock < $qtyNg) {
                            // Throw response
                            throw new HttpResponseException(
                                response()->json([
                                    'status' => false,
                                    'message' => 'Insufficient stock.',
                                    'error' => [
                                        "The remaining NG fresh stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->ng_fresh_stock}\".",
                                    ],
                                ], 422)
                            );
                        } else {
                            $stock->decrement('ng_fresh_stock', $qtyNg);
                        }
                        break;

                    default:
                        throw new Exception('Bad request calculating stock fresh', 400);
                }
                break;

            // Replating
            case 'Replating':
                switch ($type) {
                    case 'Incoming':
                        // qty_ok
                        $stock->increment('incoming_replating_stock', $qtyOk);

                        // qty_ng
                        if (!empty($qtyNg)) {
                            $stock->increment('ng_replating_stock', $qtyNg);
                        }
                        break;

                    case 'Process':
                        // Start Check system value
                        switch ($system) {
                            case 'yes':
                                // Qty Ok
                                $stock->increment('process_replating_stock', $qtyOk);

                                //Qty Ng
                                $stock->increment('ng_replating_stock', $qtyNg);
                                break;
                            case 'no':
                                // qty_ok
                                if ($stock->incoming_replating_stock < $qtyOk) {
                                    // Throw response
                                    throw new HttpResponseException(
                                        response()->json([
                                            'status' => false,
                                            'message' => 'Insufficient stock.',
                                            'error' => [
                                                "The remaining unprocess replating stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->incoming_replating_stock}\".",
                                            ],
                                        ], 422)
                                    );
                                } else {
                                    $stock->decrement('incoming_replating_stock', $qtyOk);
                                    $stock->increment('process_replating_stock', $qtyOk);
                                }

                                // qty_ng
                                if ($stock->incoming_replating_stock < $qtyNg) {
                                    // Throw response
                                    throw new HttpResponseException(
                                        response()->json([
                                            'status' => false,
                                            'message' => 'Insufficient stock.',
                                            'error' => [
                                                "The remaining unprocess replating stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->incoming_replating_stock}\".",
                                            ],
                                        ], 422)
                                    );
                                } else {
                                    $stock->decrement('incoming_replating_stock', $qtyNg);
                                    $stock->increment('ng_replating_stock', $qtyNg);
                                }
                                break;

                            default:
                                throw new Exception('Error for System: Only accept "yes/no" value', 500);
                        }
                        // End Check system value
                        break;

                    case 'Outgoing':
                        // qty_ok
                        if ($stock->process_replating_stock < $qtyOk) {
                            // Throw response
                            throw new HttpResponseException(
                                response()->json([
                                    'status' => false,
                                    'message' => 'Insufficient stock.',
                                    'error' => [
                                        "The remaining ready replating stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->process_replating_stock}\".",
                                    ],
                                ], 422)
                            );
                        } else {
                            $stock->decrement('process_replating_stock', $qtyOk);
                        }

                        // qty_ng
                        if ($stock->ng_replating_stock < $qtyNg) {
                            // Throw response
                            throw new HttpResponseException(
                                response()->json([
                                    'status' => false,
                                    'message' => 'Insufficient stock.',
                                    'error' => [
                                        "The remaining NG replating stock (Item: {$stock->subItem->item_name}) is currently \"{$stock->ng_replating_stock}\".",
                                    ],
                                ], 422)
                            );
                        } else {
                            $stock->decrement('ng_replating_stock', $qtyNg);
                        }
                        break;

                    default:
                        throw new Exception('Bad request calculating stock replating', 400);
                }
                break;

            default:
                throw new Exception('Bad request calculating stock', 400);
        }

        return true;
    }

    /**
     * Check the item stock record
     *
     * @param  mixed  $item_code
     * @param  mixed  $subItemId
     */
    private function checkStockRecordAvailability($item_code, $subItemId): bool
    {
        try {
            // Check if data stock exists
            $checkAvaibility = SubcontStock::where('sub_item_id', $subItemId)
                ->where('item_code', $item_code)
                ->exists();

            if (!$checkAvaibility) {
                SubcontStock::create([
                    'sub_item_id' => $subItemId,
                    'item_code' => $item_code,
                    'incoming_fresh_stock' => 0,
                    'incoming_replating_stock' => 0,
                    'process_fresh_stock' => 0,
                    'process_replating_stock' => 0,
                    'ng_fresh_stock' => 0,
                    'ng_replating_stock' => 0,
                ]);
            }
        } catch (\Throwable $th) {
            // Generate random request id
            $randomReqId = 'error_' . Carbon::now()->format('Ymd;H:i:s') . '_' . \Str::random(10);

            // Log error to channel internal system error
            Log::error("
                Message => Generate Delivery Note error
                Error => {$th->getMessage()},
                File => {$th->getFile()},
                Line => {$th->getLine()},
                RequestId => $randomReqId,
            ");

            // Response
            throw new HttpResponseException(
                response()->json([
                    'status' => false,
                    'message' => "Internal error while checking stock item (Request_id:$randomReqId)",
                    'error' => "Internal error while checking stock item (Request_id:$randomReqId)",
                ], 500)
            );

        }

        return true;
    }

    private function checkItemStatus(string $bp_code, string $partNumber)
    {
        // query get items status
        $getStatus = SubcontItem::where('bp_code', $bp_code)
            ->where('item_code', $partNumber)
            ->value('status');

        if ($getStatus == 1) {
            return true;
        } else {
            return false;
        }

    }
}
