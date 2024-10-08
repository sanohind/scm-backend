<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_HistoryViewResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'dn_number' => $this->no_dn,
            'po_number' => $this->po_no,
            'dn_status' => $this->status_desc,
            'po_status' => $this->poHeader->po_status,
            'send_date' => $this->planConcat(),
            'receive_date' => $this->receiptConcat(),
            'no_packing_slip' => $this->packing_slip,
            'detail' => DN_DetailViewResource::collection($this->whenLoaded('dnDetail'))
        ];
    }

    // concat plan date and plan time
    private function planConcat(){
        //value
        // Convert and format date and time to strings
        $dateString = date('Y-m-d', strtotime($this->plan_delivery_date));
        $timeString = date('H:i', strtotime($this->plan_delivery_time));

        $concat = "$dateString $timeString";

        return $concat;//dd($concat);
    }

    // concat receipt date and plan time
    private function receiptConcat(){
        //value
        // Convert and format date and time to strings
        $dateString = date('Y-m-d', $this->actual_receipt_date);
        $timeString = date('H:i', $this->actual_receipt_time);

        $concat = "$dateString $timeString";

        return $concat;//dd($concat);
        }
}
