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
        'part_number' => $this->item_code,
        'part_name' => $this->item_name,
        'part_old_name' => $this->item_old_name ?? 'null',
        // Fresh
        'incoming_fresh_stock' => $this->subStock->incoming_fresh_stock ?? 0,
        'ready_fresh_stock' => $this->subStock->process_fresh_stock ?? 0,
        'ng_fresh_stock' => $this->subStock->ng_fresh_stock ?? 0,
        // Replating
        'incoming_replating_stock' => $this->subStock->incoming_replating_stock ?? 0,
        'ready_replating_stock' => $this->subStock->process_replating_stock ?? 0,
        'ng_replating_stock' => $this->subStock->ng_replating_stock ?? 0,
    ];
}

}
