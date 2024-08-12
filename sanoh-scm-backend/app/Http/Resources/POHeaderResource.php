<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class POHeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'po_no' => $this->po_no,
            'po_date' => $this->po_date,
            'planned_receipt_date' => $this->planned_receipt_date,
            'po_revision_no' => $this->po_revision_no,
            'po_revision_date' => $this->po_revision_date,
            'po_status' => $this->po_status,
            'response' => $this->response,
        ];
    }
}
