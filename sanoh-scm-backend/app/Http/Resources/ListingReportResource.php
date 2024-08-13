<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'po_listing_no' => $this->po_listing_no,
            'bp_code' => $this->bp_code,
            'date' => $this->date,
            'file' => $this->file,
        ];
    }
}
