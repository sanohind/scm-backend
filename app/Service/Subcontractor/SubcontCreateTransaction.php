<?php

namespace App\Service\Subcontractor;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use App\Models\Subcontractor\SubcontTransaction;
use Log;

class SubcontCreateTransaction
{
    /**
     * Create new transaction business logic for multiple items
     * @param array $data
     * @throws \Exception
     * @return bool
     */
    public function createTransactionSubcont($data)
    {
        // check user role
        $check = Auth::user()->role;

        if ($check == 6 || $check == 8) {
            $bp_code = Auth::user()->bp_code;
            // dd($bp_code);
        } else if ($check == 9) {
            $bp_code = $data['bp_code'];
        }

        foreach ($data['data'] as $dataTransaction) {
            // Get sub_item_id for each item
            $subItemId = SubcontItem::where('item_code', $dataTransaction["item_code"])
                ->where('bp_code', $bp_code)
                ->value('sub_item_id');
            // Generate unique delivery note if not provided
            if (empty($dataTransaction["delivery_note"])) {
                $todayLatestProcess = Carbon::now()->format("Ymd");
                $today = Carbon::now()->format("dmy");
                $user = substr(Auth::user()->bp_code, strpos(Auth::user()->bp_code, 'SLS') + 3, 5);
                $getLatestProcess = SubcontTransaction::where('delivery_note', 'like', "$user$today-%")
                    ->where('transaction_type', 'Process')
                    ->where('transaction_date', $todayLatestProcess)
                    ->count();
                // dd($getLatestProcess);
                // dd("$user-$today-");
                $unique_dn_process = "$user$today-" . ($getLatestProcess + 1);
                $dataTransaction['delivery_note'] = $unique_dn_process;
            }

            $result = DB::transaction(function () use ($dataTransaction, $subItemId) {

                // Create the transaction
                SubcontTransaction::create([
                    'delivery_note' => $dataTransaction['delivery_note'],
                    'sub_item_id' => $subItemId,
                    'transaction_type' => $dataTransaction['transaction_type'],
                    'actual_transaction_date' => $dataTransaction['actual_transaction_date'],
                    'actual_transaction_time' => $dataTransaction['actual_transaction_time'],
                    'transaction_date' => Carbon::now()->format("Y-m-d"),
                    'transaction_time' => Carbon::now()->format("H:i:s"),
                    'item_code' => $dataTransaction['item_code'],
                    'status' => $dataTransaction['status'],
                    'qty_ok' => $dataTransaction['qty_ok'],
                    'qty_ng' => $dataTransaction['qty_ng'],
                ]);

                // Check stock record availability
                $checkStockRecordAvaibility = $this->checkStockRecordAvailability($dataTransaction["item_code"], $subItemId);

                // Get stock
                $stock = SubcontStock::where('sub_item_id', $subItemId)
                    ->where('item_code', $dataTransaction["item_code"])
                    ->first();

                // Validate and calculate stock
                if ($checkStockRecordAvaibility && !empty($stock)) {
                    // Calculate stock
                    $calculate = $this->calculatingStock(
                        $dataTransaction["status"],
                        $dataTransaction["transaction_type"],
                        $dataTransaction["qty_ok"],
                        $dataTransaction["qty_ng"],
                        $stock
                    );
                } else {
                    throw new Exception("Error processing check stock record availability", 500);
                }

                // Check the if the process calculate stock complete
                if ($calculate == true) {
                    return true;
                } else {
                    return false;
                }
            });
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

    public function createSubcontTransactionDifference(
        int $subItemId,
        string $deliveryNote,
        int $partNumber,
        string $status,
        int $actualQtyOk,
        int $actualQtyNg,
    ) {
        // Check user role
        $check = Auth::user()->role;

        if ($check == 4 || $check == 9) {
        } else {
            throw new Exception("User Forbidden", 403);
        }

        // Create transaction diffrence
        try {
            DB::transaction(function () use(
                $subItemId,
                $deliveryNote,
                $partNumber,
                $status,
                $actualQtyOk,
                $actualQtyNg,
            ) {
                // Variable declaration
                $type = "Process"; // transaction_type

                // Format delivery note
                $formatDn = substr($deliveryNote, 2);

                // Create the transaction
                SubcontTransaction::create([
                    'delivery_note' => "SYS$formatDn",
                    'sub_item_id' => $subItemId,
                    'transaction_type' => $type,
                    'transaction_date' => Carbon::now()->format("Y-m-d"),
                    'transaction_time' => Carbon::now()->format("H:i:s"),
                    'item_code' => $partNumber,
                    'status' => $status,
                    'qty_ok' => $actualQtyOk,
                    'qty_ng' => $actualQtyNg,
                    'response' => "System Review Diffrence",
                ]);

                // Get stock
                $stock = SubcontStock::where('sub_item_id', $subItemId)
                ->where('item_code', $partNumber)
                ->first();

                // Calculate
                $this->calculatingStock(
                    $status,
                    $type,
                    $actualQtyOk,
                    $actualQtyNg,
                    $stock,
                );
            });
        } catch (\Throwable $th) {
            throw new Exception("Error processing check stock record availability (Error: $th)", 500);
        }
    }

    /**
     * Summary of calculatingStock
     * @param string $status
     * @param string $type the value must be type of transaction, like
     * @param int $qtyOk
     * @param int $qtyNg
     * @param \App\Models\Subcontractor\SubcontStock $stock
     * @throws \Exception
     * @return bool
     */
    private function calculatingStock(string $status, string $type, int $qtyOk, int $qtyNg, SubcontStock $stock): bool
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
                        // qty_ok
                        if ($stock->incoming_fresh_stock < $qtyOk) {
                            throw new Exception("Incoming fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_fresh_stock", $qtyOk);
                            $stock->increment("process_fresh_stock", $qtyOk);
                        }

                        // qty_ng
                        if ($stock->incoming_fresh_stock < $qtyNg) {
                            throw new Exception("Incoming fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_fresh_stock", $qtyNg);
                            $stock->increment("ng_fresh_stock", $qtyNg);
                        }
                        break;

                    case 'Outgoing':
                        // qty_ok
                        if ($stock->process_fresh_stock < $qtyOk) {
                            throw new Exception("Ready fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("process_fresh_stock", $qtyOk);
                        }

                        // qty_ng
                        if ($stock->ng_fresh_stock < $qtyNg) {
                            throw new Exception("NG fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("ng_fresh_stock", $qtyNg);
                        }
                        break;

                    default:
                        throw new Exception("Bad request calculating stock fresh", 400);
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
                        // qty_ok
                        if ($stock->incoming_replating_stock < $qtyOk) {
                            throw new Exception("Incoming replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_replating_stock", $qtyOk);
                            $stock->increment("process_replating_stock", $qtyOk);
                        }

                        // qty_ng
                        if ($stock->incoming_replating_stock < $qtyNg) {
                            throw new Exception("Incoming replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_replating_stock", $qtyNg);
                            $stock->increment("ng_replating_stock", $qtyNg);
                        }
                        break;

                    case 'Outgoing':
                        // qty_ok
                        if ($stock->process_replating_stock < $qtyOk) {
                            throw new Exception("Ready replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("process_replating_stock", $qtyOk);
                        }

                        // qty_ng
                        if ($stock->ng_replating_stock < $qtyNg) {
                            throw new Exception("NG replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("ng_replating_stock", $qtyNg);
                        }
                        break;

                    default:
                        throw new Exception("Bad request calculating stock replating", 400);
                }
                break;

            default:
                throw new Exception("Bad request calculating stock", 400);
        }

        return true;
    }

    /**
     * Check the item stock record
     * @param mixed $item_code
     * @param mixed $subItemId
     * @return bool
     */
    private function checkStockRecordAvailability($item_code, $subItemId): bool
    {
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

            $checkAvaibility = true;
        }

        return $checkAvaibility;
    }
}
