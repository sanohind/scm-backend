<?php

namespace App\Http\Resources\DeliveryNote;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_LabelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // 'dn_label_no' => $this->dn_label_no,
            'lot_number' => $this->lot_number,
            'qr_number' => $this->qrNumber(),
            'po_number' => $this->dnHeader->po_no,
            // 'dn_number' => $this->no_dn,
            // 'model' => $this->no_dn,
            'customer_name' => Str::upper('PT. Sanoh Indonesia'),
            'supplier_name' => $this->dnHeader->supplier_name, // Updated supplier_name
            'part_number' => $this->part_no,
            'part_name' => $this->item_desc_a,
            'quantity' => $this->dn_snp,
            'delivery_date' => $this->deliveryDate(),
            'printed_date' => $this->printedDate(), // Updated to current date
        ];
    }

    // concat qrNumber
    private function qrNumber(){
        $part_number = $this->part_no;
        $qty = $this->dn_qty;
        $lot = $this->lot_number;
        $line = $this->order_line;
        $seq = $this->order_seq;

        $concat = $part_number.';'.$qty.';'.$lot.';'.$line.';'.$seq;

        return $concat;
    }

    // format delivery_date
    private function deliveryDate(){
        $plan_delivery_date = Carbon::parse($this->plan_delivery_date);
        // $plan_delivery_time = Carbon::parse($this->plan_delivery_time);

        // $formattedTime = $plan_delivery_time->format('H:i');
        $formattedDate = $plan_delivery_date->format('d M Y');

        // $concat = $formattedDate.' '.$formattedTime;

        // return $concat;
        return $formattedDate;
    }

    // format delivery_date
    private function printedDate(){
        $printed_date = Carbon::now();
        // $plan_delivery_time = Carbon::parse($this->plan_delivery_time);

        // $formattedTime = $plan_delivery_time->format('H:i');
        $formattedDate = $printed_date->format('dmy H:i');

        // $concat = $formattedDate.' '.$formattedTime;

        // return $concat;
        return $formattedDate;
    }
}
