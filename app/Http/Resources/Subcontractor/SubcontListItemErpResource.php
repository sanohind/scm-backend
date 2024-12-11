<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontListItemErpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'item_name' => $this->item,
            'alias' => $this->old_item,
        ];
    }
}
