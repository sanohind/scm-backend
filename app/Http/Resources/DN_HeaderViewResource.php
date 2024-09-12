<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_HeaderViewResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'dn_number' => $this->no_dn,
            'po_number' => $this->po_no,
            'supplier_name' => $this->supplier_name,
            'supplier_code' => $this->supplier_code,
            'planned_receipt_date' => $this->planConcat(),
            // 'actual_received_date' => $this->planConcat(), kosongkan karna ditulis tangan
            'total_box' => $this->dnDetail->
            sum(
                function ($detail) {
                    return (new DN_DetailViewResource($detail))->valueForTotalBox();
                }
            ),
            'confirm_update_at' => $this->confirm_update_at,
            'printed_date' => $this->dn_printed_at,
            'detail' => DN_DetailViewResource::collection($this->whenLoaded('dnDetail'))
        ];
    }

    // concat plan date and plan time
    private function planConcat(){
    //value
    // Convert and format date and time to strings
    $dateString = date('Y-m-d', strtotime($this->plan_delivery_date));
    $timeString = date('H:i', strtotime($this->plan_delivery_time));

    $concat = $dateString . ' ' . $timeString;

    return $concat;//dd($concat);
    }
}
