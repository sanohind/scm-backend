<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontAllListItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_id' => $this->sub_item_id,
            'part_number' => $this->item_code,
            'part_name' => $this->item_name,
            'old_part_name' => $this->item_old_name,
            'status' => $this->status,
            'min_stock_incoming' => ($this->min_stock_incoming <= 0) ? null : $this->min_stock_incoming,
            'min_stock_outgoing' => ($this->min_stock_outgoing <= 0) ? null : $this->min_stock_outgoing,
        ];
    }
}
