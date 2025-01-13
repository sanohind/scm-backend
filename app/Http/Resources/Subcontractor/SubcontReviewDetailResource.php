<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontReviewDetailResource extends JsonResource
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
            'sub_item_id' => $this->sub_item_id,
            'delivery_note' => $this->delivery_note,
            'transaction_type' => $this->transaction_type,
            'status' => $this->status,
            'part_name' => $this->subItem->item_name,
            'old_part_name' => $this->subItem->item_old_name,
            'part_number' => $this->item_code,
            'qty_ok' => $this->qty_ok,
            'qty_ng' => $this->qty_ng,
            'actual_qty_ok' => $this->actual_qty_ok_receive,
            'actual_qty_ng' => $this->actual_qty_ng_receive,
            'response' => $this->response,
        ];
    }
}
