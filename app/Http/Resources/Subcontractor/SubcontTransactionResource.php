<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sub_transaction_id' => $this->sub_transaction_id,
            'transaction_date' => $this->actual_transaction_date,
            'transaction_time' => $this->actual_transaction_time,
            'transaction_type'=> $this->transaction_type,
            'delivery_note' => $this->delivery_note,
            'part_number' => $this->item_code,
            'part_name' => $this->subItem->item_name,
            'old_part_name' => $this->subItem->item_old_name,
            'status' => $this->status,
            'qty_ok' => $this->qty_ok,
            'qty_ng' => $this->qty_ng,
        ];
    }
}
