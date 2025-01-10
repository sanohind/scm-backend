<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubcontReviewHeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'delivery_note' => $this->delivery_note,
            'transaction_type' => $this->transaction_type,
            'transaction_date' => $this->transaction_date,
            'transaction_time' => $this->transaction_time,
            'response' => $this->response ?? 'Under Review',
        ];
    }
}
