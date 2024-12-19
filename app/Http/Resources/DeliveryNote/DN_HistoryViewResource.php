<?php

namespace App\Http\Resources\DeliveryNote;

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
            'dn_number' => $this->no_dn ?? 'No data',
            'po_number' => $this->po_no ?? 'No data',
            'dn_status' => $this->status_desc ?? 'No data',
            'po_status' => $this->poHeader->po_status ?? 'No data',
            'send_date' => $this->planConcat() ?? 'No data',
            'receive_date' => $this->receiptConcat() ?? 'No data',
            'packing_slip' => $this->packing_slip ?? 'No data',
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
        $dnDetail = $this->dnDetail->first();

        $dateString = date('Y-m-d', strtotime($dnDetail->actual_receipt_date));
        $timeString = date('H:i', strtotime($dnDetail->actual_receipt_time));

        $concat = "$dateString $timeString";

        return $concat;//dd($concat);
        }
}
