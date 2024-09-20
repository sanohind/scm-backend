<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PO_DetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'po_no' => $this->po_no,
            'bp_part_no' => $this->bp_part_no,
            'item_desc_a' => $this->item_desc_a,
            'price' => $this->price,
            'purchase_unit' => $this->purchase_unit,
            'po_qty' => $this->po_qty,
            'receipt_qty' => $this->receipt_qty
        ];
    }
}
