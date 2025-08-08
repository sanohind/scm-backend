<?php

namespace App\Http\Resources\DeliveryNote;

use App\Models\DeliveryNote\DnHeader;
use App\Models\Users\BusinessPartner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DnLabelResource extends JsonResource
{
    private $currentQuantity;

    public function __construct($resource, $currentQuantity = null)
    {
        parent::__construct($resource);
        $this->currentQuantity = $currentQuantity ?? $resource->dn_snp;
    }

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
            'quantity' => $this->currentQuantity,
            'delivery_date' => $this->deliveryDate(),
            'printed_date' => $this->printedDate(), // Updated to current date
        ];
    }

    private function checkQuantity()
    {
        $qtyConfirm = $this->qty_confirm;
        $dnSnp = $this->dn_snp;

        $total = $qtyConfirm % $dnSnp;

        if ($total != 0) {
            return $qtyConfirm;
        } else {
            return $dnSnp;
        }
    }

    // concat qrNumber
    private function qrNumber()
    {
        $part_number = $this->part_no ?? '';
        $qty = $this->dn_snp ?? ''; // Selalu menggunakan dn_snp untuk konsistensi
        $lot = $this->lot_number ?? '';
        $customer = $this->item_customer ?? '';
        $poLine = $this->order_line ?? '';
        $seq = $this->order_seq ?? '';
        $dnNo = $this->no_dn ?? '';
        $dnLine = $this->dn_line ?? '';

        $concat = "$part_number;$qty;$lot;$customer;$poLine;$seq;$dnNo;$dnLine";

        return $concat;
    }

    // format delivery_date
    private function deliveryDate()
    {
        $plan_delivery_date = Carbon::parse($this->plan_delivery_date);
        $formattedDate = $plan_delivery_date->format('d M Y');

        return $formattedDate;
    }

    // format delivery_date
    private function printedDate()
    {
        $printed_date = Carbon::now();
        $formattedDate = $printed_date->format('dmy H:i');

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
