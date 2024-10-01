<?php

namespace App\Http\Resources;

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
            // 'progress' =>a,
            'detail' => DN_DetailResource::collection($this->whenLoaded('dnDetail'))
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

    // private function progress(){
    //     $count_confirmed = a;
    //     $count_ = a;

    // }

}
