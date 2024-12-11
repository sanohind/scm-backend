<?php

namespace App\Service\Subcontractor;

use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontStock;
use App\Models\Subcontractor\SubcontTransaction;

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

            $todayLatestProcess = Carbon::now()->format("Ymd");
                $today = Carbon::now()->format("dmy");
                // dd($today);
                $user = substr(Auth::user()->bp_code, strpos(Auth::user()->bp_code, 'SLS') + 3, 4);
                $getLatestProcess = SubcontTransaction::where('sub_item_id',$subItemId)
                ->where('transaction_type', 'Process')
                ->where('transaction_date', $todayLatestProcess)
                ->count();
                $unique_dn_process = "$user-$today-".($getLatestProcess + 1);
                dd($unique_dn_process);


            $result = DB::transaction(function () use ($data, $subItemId) {
            if ($data["delivery_note"] == null || $data["delivery_note"] == '') {
                $todayLatestProcess = Carbon::now()->format("Ymd");
                $today = Carbon::now()->format("dmy");
                // dd($today);
                $user = substr(Auth::user()->bp_code, strpos(Auth::user()->bp_code, 'SLS') + 3, 4);
                $getLatestProcess = SubcontTransaction::where('sub_item_id',$subItemId)
                ->where('transaction_type', 'Process')
                ->where('transaction_date', $todayLatestProcess)
                ->count();
                $unique_dn_process = "$user-$today-".($getLatestProcess + 1);
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
     * Summary of calculatingStock
     * @param string $status
     * @param string $type
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
                    case 'Ingoing':
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
                        throw new Exception("Bad request", 400);
                }
                break;

            // Replating
            case 'Replating':
                switch ($type) {
                    case 'Ingoing':
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
