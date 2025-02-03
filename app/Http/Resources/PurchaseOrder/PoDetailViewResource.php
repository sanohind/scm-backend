<?php

namespace App\Http\Resources\PurchaseOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoDetailViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'purchase_order_detail_id' => $this->po_detail_no,
            'po_number' => $this->po_no,
            'line_no' => $this->po_line,
            'seq_no' => $this->po_sequence,
            'part_number' => $this->bp_part_no,
            'part_name' => $this->bp_part_name,
            'delivery_date' => $this->planned_receipt_date,
            'quantity' => $this->po_qty,
            'receipt_qty' => $this->receipt_qty,
            'unit' => $this->purchase_unit,
            'unit_price' => number_format($this->price, 2, ',', '.'),
            'amount' => number_format($this->amount, 2, ',', '.'),
        ];
    }
}
