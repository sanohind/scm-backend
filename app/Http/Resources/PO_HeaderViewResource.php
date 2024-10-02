<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Illuminate\Http\Resources\Json\JsonResource;

class PO_HeaderViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'po_number' => $this->po_no,
            'po_date'  => $this->po_date,
            'po_type'  => $this->po_type_desc,
            'pr_no'  => $this->pr_no,
            'delivery_term'  => $this->deliveryTermCalculate(),
            'currency'  => $this->po_currency,
            'note'  => $this->note(),
            'supplier_name'  => $this->partner->adr_line_1 , //supplier_name, old value
            'supplier_code'  => $this->supplier_code,
            'planned_receipt_date' => $this->planned_receipt_date,
            'total_amount' => $this->totalAmountValue(),
            'ppn' => number_format($this->calculatePpn(), 2,',','.'),
            'total' => number_format($this->total(), 2,',','.'),
            'supplier_address' => $this->addrConcat(),
            'phone_number' => $this->partner->bp_phone,
            'fax_number' => $this->partner->bp_fax,
            'delivery_date' => $this->planned_receipt_date,
            'accepted_confirmed_by' => $this->partner->bp_name,
            'terms' => $this->payment_term,
            'attn' => $this->attn_name,
            'printed_date' => $this->po_printed_at,
            'detail' => PO_DetailViewResource::collection($this->whenLoaded('poDetail'))
        ];
    }

    // calculate the diffrence date for delivery term
    private function deliveryTermCalculate(){
        // value
        $formatted_planned_receipt_date = Carbon::Parse($this->planned_receipt_date);
        $formatted_purchase_order_date = Carbon::Parse($this->po_date);

        // calculate
        $diffrence = $formatted_planned_receipt_date->diffInDays($formatted_purchase_order_date);

        return $diffrence;
    }

    // concat ref 1 and ref 2
    // private function noteConcat(){
    //     //value
    //     $ref1 = $this->references_1;
    //     $ref2 = $this->references_2;

    //     $concat = $ref1.' '.$ref2;

    //     return $concat;
    // }

    // concat address
    private function addrConcat(){
        //value
        // $addr1 = $this->partner->adr_line_1;
        $addr2 = $this->partner->adr_line_2;
        $addr3 = $this->partner->adr_line_3;
        // $addr4 = $this->partner->adr_line_4;

        // $concat = $addr1.' '.$addr2.' '.$addr3.' '.$addr4; old concat
        $concat = $addr2.' '.$addr3;

        return $concat;
    }

    // Function for sum each
    private function totalAmountValue(){
        $totalAmount = $this->poDetail->sum('amount');

        $format_to_number = number_format($totalAmount, 2,',','.') ;

        return $format_to_number;
    }

    // Function for calculate PPN 11%
    private function calculatePpn(){
        $totalAmount = $this->poDetail->sum('amount');

        $ppn = $totalAmount * 0.11;

        return $ppn;
    }

    // Function for calculate total_amount + ppn 11%
    private function total(){
        $totalAmount = $this->poDetail->sum('amount');

        $ppn = $this->calculatePpn();

        $total = $totalAmount + $ppn;

        return $total;
    }

    private function note(){
        $value = ($this->reference_2 == null || $this->reference_2 == '') ? $this->reference_1 : $this->reference_2;

        return $value;
    }

}
