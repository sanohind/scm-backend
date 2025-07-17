<?php

namespace App\Http\Resources\DeliveryNote;

use App\Models\DeliveryNote\DnHeader;
use App\Models\Users\BusinessPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DnLabelAllResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'lot_number' => $this->lot_number,
            'qr_number' => $this->qrNumber(),
            'po_number' => $this->dnHeader->po_no,
            'customer_name' => $this->getAdrLine1(),
            'supplier_name' => $this->dnHeader->supplier_name, // Updated supplier_name
            'part_number' => $this->part_no,
            'part_name' => $this->item_desc_a,
            'quantity' => $this->dn_snp,
            'delivery_date' => $this->deliveryDate(),
            'printed_date' => $this->printedDate(), // Updated to current date
        ];
    }

    // concat qrNumber
    private function qrNumber()
    {
        $part_number = $this->part_no;
        $qty = $this->dn_qty;
        $lot = $this->lot_number;
        $poLine = $this->order_line;
        $seq = $this->order_seq;
        $dnNo = $this->no_dn;
        $dnLine = $this->dn_line;
        $customer = $this->item_customer ?? '';

        $concat = "$part_number;$qty;$lot;$poLine;$customer;$seq;$dnNo;$dnLine";

        return $concat;
    }

    // format delivery_date
    private function deliveryDate()
    {
        $plan_delivery_date = Carbon::parse($this->plan_delivery_date);
        // $plan_delivery_time = Carbon::parse($this->plan_delivery_time);

        // $formattedTime = $plan_delivery_time->format('H:i');
        $formattedDate = $plan_delivery_date->format('d M Y');

        // $concat = $formattedDate.' '.$formattedTime;

        // return $concat;
        return $formattedDate;
    }

    // format delivery_date
    private function printedDate()
    {
        $printed_date = Carbon::now();
        // $plan_delivery_time = Carbon::parse($this->plan_delivery_time);

        // $formattedTime = $plan_delivery_time->format('H:i');
        $formattedDate = $printed_date->format('dmy H:i');

        // $concat = $formattedDate.' '.$formattedTime;

        // return $concat;
        return $formattedDate;
    }

    // get adr_line-1
    private function getAdrLine1()
    {
        // query get supplier_code/bp_code
        $getBpCode = DnHeader::where('no_dn', $this->no_dn)->value('supplier_code');
        // query get adr_line_1
        $getAdrLine1 = BusinessPartner::where('bp_code', $getBpCode)->value('adr_line_1');

        return $getAdrLine1;
    }
}
