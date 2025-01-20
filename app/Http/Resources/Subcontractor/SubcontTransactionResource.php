<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sub_transaction_id' => $this->sub_transaction_id,
            'transaction_date' => $this->actual_transaction_date ?? $this->transaction_date,
            'transaction_time' => $this->actual_transaction_time ?? $this->transaction_time,
            'transaction_type'=> $this->transaction_type,
            'delivery_note' => $this->delivery_note,
            'part_number' => $this->item_code,
            'part_name' => $this->subItem->item_name,
            'old_part_name' => $this->subItem->item_old_name,
            'status' => $this->status,
            'qty_ok' => $this->qty_ok,
            'qty_ng' => $this->qty_ng,
            'qty_total' => ($this->qty_ok == 0 && $this->qty_ng == 0) ? null : ($this->qty_ok + $this->qty_ng),
            'actual_qty_ok' => $this->actual_qty_ok_receive,
            'actual_qty_ng' => $this->actual_qty_ng_receive,
            'actual_qty_total' => ($this->actual_qty_ok_receive == 0 && $this->actual_qty_ng_receive == 0) ? null : ($this->actual_qty_ok_receive + $this->actual_qty_ng_receive),
            'response' => $this->transactionResponse(),
        ];
    }

    //
    public function qtyOkCheck(){
        $qtyOkSystem = $this->qty_ok;

        $responseSystem = $this->response;

        if (strpos($responseSystem, "System Review") !== false) {
            return -$qtyOkSystem;
        } else{
            return $qtyOkSystem;
        }
    }

    //
    public function qtyNgCheck(){
        $qtyNgSystem = $this->qty_ng;

        $responseSystem = $this->response;

        if (strpos($responseSystem, "System Review") !== false) {
            return -$qtyNgSystem;
        } else{
            return $qtyNgSystem;
        }
    }

    public function qtyTotal(){
        // Input by user
        $qtyOk = $this->qty_ok;
        $qtyNg = $this->qty_ng;

        // Input by system
        $responseSystem = $this->response;
        $qtyOkSystem = 0;
        $qtyNgSystem = 0;

        if (strpos($responseSystem, "System Review") !== false) {
            $qtyOkSystem = -$qtyOk;
            $qtyNgSystem = -$qtyNg;

            $qtyTotalSystem = $qtyOkSystem + $qtyNgSystem;

            return $qtyTotalSystem;
        } else {
            $qtyTotal = $qtyOk ?? 0 + $qtyNg ?? 0;

            return $qtyTotal;
        }
    }

    public function transactionResponse(){
        $transType = $this->transaction_type;
        $response = $this->response;

        if (($transType == "Incoming" && $response == null) || ($transType == "Process" && $response == null)) {
            return "-";
        }

        if ($transType == "Outgoing" && $response == null) {
            return "Under Review";
        }

        if ($transType == "Outgoing" && $response != null) {
            return "Receipt";
        }

        if ($transType == "Process" && $response != null) {
            return $response;
        }
    }
}
