<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
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
            'delivery_term'  => $this->delivery_term_calculate(),
            'currency'  => $this->po_currency,
            'note'  => $this->note_concat(),
            'supplier_nama'  => $this->partner->bp_name,
            'supplier_code'  => $this->partner_bp_code,
            'planned_receipt_date' => $this->planned_receipt_date,
            'total_amount' => $this->amount->sum('amount'),
            'supplier_address' => $this->addr_concat(),
            'phone_number' => $this->partner->bp_phone,
            'fax_number' => $this->partner->bp_fax,
            'delivery_date' => $this->planned_receipt_date,             // still dont know the value from = done
            'accepted_confirmed_by' => $this->partner->bp_name,
            'terms' => $this->delivery_term_calculate,                  // ambigu
            'attn' => $this->attn_name,
            'printed_date' => $this->po_printed_at,
        ];
    }

    // calculate the diffrence date for delivery term
    private function delivery_term_calculate(){
        // value
        $formatted_planned_receipt_date = Carbon::Parse($this->planned_receipt_date);
        $formatted_purchase_order_date = Carbon::Parse($this->po_date);

        // calculate
        $diffrence = $formatted_planned_receipt_date->diffInDays($formatted_purchase_order_date);

        return $diffrence;
    }

    // concat ref 1 and ref 2
    private function note_concat(){
        //value
        $ref1 = $this->references_1;
        $ref2 = $this->references_2;

        $concat = $ref1.' '.$ref2;

        return $concat;
    }

    // concat address
    private function addr_concat(){
        //value
        $addr1 = $this->partner->adr_line_1;
        $addr2 = $this->partner->adr_line_2;
        $addr3 = $this->partner->adr_line_3;
        $addr4 = $this->partner->adr_line_4;

        $concat = $addr1.' '.$addr2.' '.$addr3.' '.$addr4;

        return $concat;
    }

}
