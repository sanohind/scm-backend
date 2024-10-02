<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PO_HeaderResource extends JsonResource
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
            'note' => $this->note(),
            'po_revision_no' => $this->po_revision_no,
            'po_revision_date' => $this->po_revision_date,
            'po_status' => $this->po_status,
            'response' => $this->response,
            'reason' => $this->reason,
            'detail' => PO_DetailResource::collection($this->whenLoaded('poDetail'))
        ];
    }

    private function note(){
        $value = ($this->reference_2 == null || $this->reference_2 == '') ? $this->reference_1 : $this->reference_2;

        return $value;
    }
}

