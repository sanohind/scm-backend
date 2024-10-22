<?php

namespace App\Http\Resources;

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
            'item_code' => $this->item_code,
            'bp_code' => $this->bp_code,
            'item_name' => $this->item_name,
            'fresh_stock'=> $this->subStock->fresh_stock,
            'replating_stock'=> $this->subStock->replating_stock,
        ];
    }
}
