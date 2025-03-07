<?php

namespace App\Http\Resources\DeliveryNote;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DnDetailListResource extends JsonResource
{
    public function __construct(
        protected $dataHeader,
        protected $dataDetail,
        protected $planDeliveryDate,
        protected $confirmation
    ) {}

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($this->confirmation);
        return [
            'no_dn' => $this->dataHeader->no_dn,
            'po_no' => $this->dataHeader->po_no,
            'plan_delivery_date' => $this->planDeliveryDate,
            'confirm_update_at' => $this->dataHeader->confirm_update_at,
            'confirm_at' => $this->confirmation,
            'detail' => DnDetailResource::collection($this->dataDetail),
        ];
    }
}
