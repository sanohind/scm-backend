<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DNDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'part_no' => $this->part_no,
            'item_desc_a' => $this->item_desc_a,
            'dn_unit' => $this->dn_unit,
            'dn_snp' => $this->dn_snp,
            'dn_qty' => $this->dn_qty,
            'qty_confirm' => $this->qty_confirm,
            'receipt_qty' => $this->receipt_qty,
        ];
    }
}
