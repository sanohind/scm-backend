<?php

namespace App\Service\Subcont;

use Exception;
use Carbon\Carbon;
use App\Models\SubcontItem;
use App\Models\SubcontStock;
use App\Models\SubcontTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubcontCreateTransaction
{
    /**
     * Create new transaction bussiness logic
     * @param mixed $data
     * @throws \Exception
     * @return bool
     */
    public function createTransactionSubcont($data): bool
    {
        // Get sub_item_id
        $subItemId = SubcontItem::where('item_code', $data["item_code"])
            ->where('bp_code', Auth::user()->bp_code)
            ->value('sub_item_id');


            $result = DB::transaction(function () use ($data, $subItemId) {
            if ($data["delivery_note"] == null || $data["delivery_note"] == '') {
                $today = Carbon::now()->format("dmY");
                $unique_dn_process = uniqid("PC/$today/");
            }

            SubcontTransaction::create([
                'delivery_note' => ($data['delivery_note'] == null) ? $unique_dn_process : $data['delivery_note'],
                'sub_item_id' => $subItemId,
                'transaction_date' => Carbon::now()->format("Y-m-d"),
                'transaction_time' => Carbon::now()->format("H:i:s"),
                'transaction_type' => $data['transaction_type'],
                'item_code' => $data['item_code'],
                'status' => $data['status'],
                'qty_ok' => $data['qty_ok'],
                'qty_ng' => $data['qty_ng'],
            ]);

            // Check stock record
            $checkStockRecordAvaibility = $this->checkStockRecordAvaibility($data["item_code"], $subItemId);

            // get stock
            $stock = SubcontStock::where('sub_item_id', $subItemId)
                ->where('item_code', $data["item_code"])
                ->first();

            // Validate then calculating
            if ($checkStockRecordAvaibility == true && !empty($stock)) {
                // calculating
                $calculating = $this->calculatingStock($data["status"], $data["transaction_type"], $data["qty_ok"], $data["qty_ng"], $stock);
            } else {
                throw new Exception("Error Processing check stock record avaibility", 500);
            }

            return $calculating;

        });

        return $result;
    }

    /**
     * Calculating the new request stock
     * @param string $status
     * @param string $type
     * @param int $qtyOk
     * @param int $qtyNg
     * @param \App\Models\SubcontStock $stock
     * @throws \Exception
     * @return bool
     */
    private function calculatingStock(string $status, string $type, int $qtyOk, int $qtyNg, SubcontStock $stock): bool
    {
        switch ($status) {
            // Fresh
            case 'Fresh':
                switch ($type) {
                    case 'In':
                        // qty_ok
                        $stock->increment('incoming_fresh_stock', $qtyOk);

                        // qty_ng
                        if (!empty($qtyNg)) {
                            $stock->increment('ng_fresh_stock', $qtyNg);
                        }
                        break;

                    case 'Process':
                        // $qty_ok
                        if ($stock->incoming_fresh_stock < $qtyOk) {
                            throw new Exception("Incoming fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_fresh_stock", $qtyOk);
                            $stock->increment("process_fresh_stock", $qtyOk);
                        }

                        // $qty_ng
                        if ($stock->incoming_fresh_stock < $qtyNg) {
                            throw new Exception("Incoming fresh stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_fresh_stock", $qtyNg);
                            $stock->increment("ng_fresh_stock", $qtyNg);
                        }
                        break;

                    case 'Out':
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
                        throw new Exception("Bad request", 400);
                }
                break;

            // Replating
            case 'Replating':
                switch ($type) {
                    case 'In':
                        // qty_ok
                        $stock->increment('incoming_replating_stock', $qtyOk);

                        // qty_ng
                        if (!empty($qtyNg)) {
                            $stock->increment('ng_replating_stock', $qtyNg);
                        }
                        break;

                    case 'Process':
                        // $qty_ok
                        if ($stock->incoming_replating_stock < $qtyOk) {
                            throw new Exception("Incoming replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_replating_stock", $qtyOk);
                            $stock->increment("process_replating_stock", $qtyOk);
                        }

                        // $qty_ng
                        if ($stock->incoming_replating_stock < $qtyNg) {
                            throw new Exception("Incoming replating stock cannot be below 0 / minus", 422);
                        } else {
                            $stock->decrement("incoming_replating_stock", $qtyNg);
                            $stock->increment("ng_replating_stock", $qtyNg);
                        }
                        break;

                    case 'Out':
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
                        throw new Exception("Bad request", 400);
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
    private function checkStockRecordAvaibility($item_code, $subItemId): bool
    {
        // Check if data stock exist
        $checkAvaiblelity = SubcontStock::where('sub_item_id', $subItemId)
            ->where('item_code', $item_code)
            ->exists();

        if ($checkAvaiblelity == false) {
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

            $checkAvaiblelity = true;
        }

        return $checkAvaiblelity;
    }


}
