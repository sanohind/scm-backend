<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_HeaderViewResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'dn_number' => $this->dn_no,
            'po_number' => $this->po_no,
            'supplier_name' => $this->po_header->partner->bp_name,
            'supplier_code' => $this->po_header->partner->bp_code,
            'planned_receipt_date' => $this->plan_concat(),
            // 'actual_received_date' => $this->plan_concat(), kosongkan karna ditulis tangan
            'total_box' => $this->plan_concat(),
            'printed_date' => $this->dn_printed_at,
        ];
    }

    // concat plan date and plan time
    private function plan_concat(){
        //value
        $pd = $this->plan_delivery_date;
        $pt = $this->plan_delivery_time;

        $concat = $pd.' '.$pt;

        return $concat;
    }
}
