<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PODetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bp_part_no' => $this->bp_part_no,
            'bp_part_name' => $this->bp_part_name,
            'purchase_unit' => $this->purchase_unit,
            'po_qty' => $this->po_qty,
        ];
    }
}
