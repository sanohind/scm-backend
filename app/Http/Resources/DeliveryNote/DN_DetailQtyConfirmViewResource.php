<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_DetailQtyConfirmViewResource extends JsonResource
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
            'qty_confirm' => $this->qty_confirm
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
       $qty_confirm = $this->qty_confirm;

       // check if data zero
        if ($snp == 0) {
            return 0; // Or handle it differently if needed
        } elseif ($qty_confirm == 0) {
            return 0;
        }

       $total = $qty_confirm / $snp;

       return $total;
    }

    // for pass data to DN_HeaderViewResource total box
    public function valueForTotalBoxQtyConfirm()
    {
        return $this->noOfKanban();
    }
}
