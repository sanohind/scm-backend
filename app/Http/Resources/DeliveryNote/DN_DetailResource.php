<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_DetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Group dnOutstanding items by wave
        $outstandingGrouped = $this->whenLoaded('dnOutstanding')->groupBy('wave')->map(function ($items, $wave) {
            return $items->pluck('qty_outstanding');
        });

        // Format the grouped items with keys like "wave_1", "wave_2", etc.
        $outstandingFormatted = [];
        foreach ($outstandingGrouped as $wave => $items) {
            $outstandingFormatted["wave_{$wave}"] = $items;
        }

        return [
            'dn_detail_no' => $this->dn_detail_no,
            'dn_line' => $this->dn_line,
            'part_no' => $this->part_no,
            'item_desc_a' => $this->item_desc_a,
            'dn_unit' => $this->dn_unit,
            'dn_snp' => $this->dn_snp,
            'dn_qty' => $this->dn_qty,
            'qty_confirm' => $this->qty_confirm,
            'receipt_qty' => $this->receipt_qty,
            'outstanding' => $outstandingFormatted,
        ];
    }
}
