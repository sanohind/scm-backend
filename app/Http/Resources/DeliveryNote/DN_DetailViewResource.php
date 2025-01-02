<?php

namespace App\Http\Resources\DeliveryNote;

use App\Models\DeliveryNote\DN_Detail_Outstanding;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_DetailViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'delivery_note_detail_id' => $this->dn_detail_no,
            'dn_number' => $this->no_dn,
            'line_no' => $this->dn_line,
            'supplier_part_number' => $this->supplier_item_no,
            'internal_part_number' => $this->part_no,
            'part_name' => $this->partConcat(),
            'pcs_per_kamban' => $this->dn_snp,
            'no_of_kamban' => $this->noOfKanban(),
            'total_quantity' => $this->dn_qty,
            'box_quantity' => $this->noOfKanban(),
            'qty_confirm' => $this->sumAllQty(),
        ];
    }

    // concat description A and description B
    private function partConcat(){
        //value
        $descA = $this->item_desc_a;
        $descB = $this->item_desc_b;

        $concat = $descA.' '.$descB;

        return $concat;
    }

    // calculate no_of_kanban qty/snp
    private function noOfKanban(){
       //value
       $snp = $this->dn_snp;
       $qty = $this->dn_qty;

       // check if data zero
        if ($snp == 0) {
            return 0; // Or handle it differently if needed
        } elseif ($qty == 0) {
            return 0;
        }

       $total = ceil($qty / $snp);

       return $total;
    }

    private function sumAllQty() {
        $qtyConfirm_1 = $this->qty_confirm;
        $qtyConfirm_outstanding = DN_Detail_Outstanding::where('no_dn', $this->no_dn)
        ->where('dn_detail_no', $this->dn_detail_no)
        ->sum('qty_outstanding');

        $total = $qtyConfirm_1 + $qtyConfirm_outstanding;

        return $total;
    }

    // for pass data to DN_HeaderViewResource total box
    public function valueForTotalBox()
    {
        return $this->noOfKanban();
    }
}
