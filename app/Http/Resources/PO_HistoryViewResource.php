<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PO_HistoryViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'po_number' => $this->po_no,
            'po_date' => $this->po_date,
            'po_status' => $this->po_status,
            'note' => $this->references_1,
            'detail' => PO_DetailViewResource::collection($this->whenLoaded('poDetail')),
        ];
    }
}
