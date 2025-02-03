<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DnHistoryViewResource extends JsonResource
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
            'packing_slip' => $this->packing_slip,
        ];
    }

    // concat plan date and plan time
    private function planConcat()
    {
        //value
        // Convert and format date and time to strings
        $dateString = date('Y-m-d', strtotime($this->plan_delivery_date));
        $timeString = date('H:i', strtotime($this->plan_delivery_time));

        $concat = "$dateString $timeString";

        return $concat; //dd($concat);
    }

    // concat receipt date and plan time
    private function receiptConcat()
    {
        //value
        // Convert and format date and time to strings
        $dnDetail = $this->dnDetail->first();

        $date = $dnDetail->actual_receipt_date ?? null;
        $time = $dnDetail->actual_receipt_time ?? null;

        if ($date == null && $time == null || $date == null && $time != null || $date != null && $time == null) {
            $concat = null;
        } else {
            $dateString = date('Y-m-d', strtotime($dnDetail->actual_receipt_date));
            $timeString = date('H:i', strtotime($dnDetail->actual_receipt_time));

            $concat = "$dateString $timeString";
        }

        return $concat; //dd($concat);
    }
}
