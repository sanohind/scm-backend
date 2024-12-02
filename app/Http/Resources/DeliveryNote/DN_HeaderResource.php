<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DN_HeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'no_dn' => $this->no_dn,
            'po_no' => $this->po_no,
            'dn_created_date'=> $this->dn_created_date,
            'plan_delivery_date' => $this->planConcat(),
            'status_desc' => $this->status_desc,
            'confirm_update_at' => $this->confirm_update_at,
            'progress' =>$this->progress(),
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

    // Function for check Dn progress when confirmed in warehouse
    private function progress(){
        $count_total = $this->dnDetail->count();
        $count_confirmed = 0;

        foreach ($this->dnDetail as $data) {
            $dn_qty = $data->dn_qty;
            $recipt_qty = $data->receipt_qty;

            $check_qty = $dn_qty - $recipt_qty;

            if($check_qty == 0){
                $count_confirmed++;
            }
        }

        return  "$count_confirmed / $count_total";

    }

}
