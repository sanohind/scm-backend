<?php

namespace App\Http\Resources\DeliveryNote;

use App\Models\PurchaseOrder\PO_Detail;
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
        return [
            'dn_detail_no' => $this->dn_detail_no,
            'dn_line' => $this->dn_line,
            'part_no' => $this->part_no,
            'item_desc_a' => $this->item_desc_a,
            'dn_unit' => $this->dn_unit,
            'dn_snp' => $this->dn_snp,
            'po_qty' => $this->poQty(),
            'dn_qty' => $this->dn_qty,
            'qty_confirm' => $this->qty_confirm,
            'receipt_qty' => $this->receipt_qty,
            'qty_delivery' => $this->qtyDelivery(),
            'outstanding' => $this->dnOutstandingWave(),
        ];
    }

    private function poQty() {
        $getPoQty = PO_Detail::where('item_code', $this->part_no)
            ->where('item_desc_a', $this->item_desc_a)
            ->where('po_no', $this->dnHeader->po_no)
            ->value('po_qty');

        return $getPoQty;
    }

    private function qtyDelivery() {
        $getQtyOutstanding = $this->whenLoaded('dnOutstanding')->groupBy('wave')->map(function ($items) {
            return $items->pluck('qty_outstanding');
        });

        // dd($getQtyOutstanding);

        $sumQty = $this->qty_confirm + $getQtyOutstanding->flatten()->sum();

        return $sumQty;
    }

    private function dnOutstandingWave() {
        // Group dnOutstanding items by wave
        $outstandingGrouped = $this->whenLoaded('dnOutstanding')->groupBy('wave')->map(function ($items) {
            return $items->pluck('qty_outstanding');
        });

        // Format the grouped items with keys like "wave_1", "wave_2", etc.
        $outstandingFormatted = [];
        foreach ($outstandingGrouped as $wave => $items) {
            $outstandingFormatted["wave_{$wave}"] = $items; //->sum(); s
        }

        return $outstandingFormatted;
    }
}
