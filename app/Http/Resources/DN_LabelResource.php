<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_LabelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'dn_label_no' => $this->dn_label_no,
            'lot_number' => $this->dnDetail->lot_number,
            'qr_number' => $this->qrNumber(),
            'po_number' => $this->dnDetail->po_no,
            'dn_number' => $this->dnDetail->no_dn,
            'model' => $this->dnDetail->no_dn,
            'customer_name' => 'PT. Sanoh Indonesia',
            'supplier_name' => $this->null,//supplier_name,
            'part_number' => $this->dnDetail->part_no,
            'part_name' => $this->dnDetail->item_desc_a,
            'quantity' => $this->dnDetail->dn_snp,
            'delivery_date' => $this->dnDetail->no_dn,
            'printed_date' => $this->dnDetail->plan_delivery_date,
        ];
    }

    // concat qrNumber
    private function qrNumber(){
        $part_number = $this->dnDetail->part_no;
        $qty = $this->dnDetail->dn_qty;
        $lot = $this->dnDetail->lot_number;
        $line = $this->dnDetail->order_line;
        $seq = $this->dnDetail->order_seq;

        $concat = $part_number.';'.$qty.';'.$lot.';'.$line.';'.$seq;

        return $concat;
    }
}
