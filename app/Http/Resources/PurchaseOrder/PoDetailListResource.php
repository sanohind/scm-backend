<?php

namespace App\Http\Resources\PurchaseOrder;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PoDetailListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'po_no' => $this->first()->po_no,
            'planned_receipt_date' => $this->first()->poHeader->planned_receipt_date,
            'note' => ($this->first()->poHeader->reference_2 == null) ? $this->first()->poHeader->reference_1 : $this->first()->poHeader->reference_2,
            'detail' => PoDetailResource::collection($this)
        ];
    }
}
