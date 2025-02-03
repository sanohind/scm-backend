<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DnDetailOutstandingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'no_dn' => $this->no_dn ?? 'no_data',
            'dn_detail_no' => $this->dn_detail_no ?? 'no_data',
            'qty_outstanding' => $this->qty_outstanding ?? 'no_data',
            'add_outstanding_date' => $this->add_outstanding_date ?? 'no_data',
            'add_outstanding_time' => $this->add_outstanding_time ?? 'no_data',
            'outstanding_wave' => $this->wave ?? 'no_data',
        ];
    }
}
