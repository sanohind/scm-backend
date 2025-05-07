<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'stock_id' => $this->sub_stock_id,
            'part_number' => $this->item_code,
            'part_name' => $this->item_name,
            'old_part_name' => $this->item_old_name ?? null,
            // Fresh
            'incoming_fresh_stock' => $this->subStock->incoming_fresh_stock ?? 0,
            'ready_fresh_stock' => $this->subStock->process_fresh_stock ?? 0,
            'ng_fresh_stock' => $this->subStock->ng_fresh_stock ?? 0,
            'min_stock_incoming' => is_null($this->min_stock_incoming) ? null : $this->min_stock_incoming,
            'min_stock_outgoing' => is_null($this->min_stock_outgoing) ? null : $this->min_stock_outgoing,

            // Replating
            'incoming_replating_stock' => $this->subStock->incoming_replating_stock ?? 0,
            'ready_replating_stock' => $this->subStock->process_replating_stock ?? 0,
            'ng_replating_stock' => $this->subStock->ng_replating_stock ?? 0,
        ];
    }
}
