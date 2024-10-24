<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'bp_code' => $this->bp_code,
            'bp_name' => $this->checkNullName(),
        ];
    }

    private function checkNullName(){
        $adr_name = $this->adr_line_1;
        $bp_name = $this->bp_name;

        $retVal = ($adr_name == null) ? $bp_name : $adr_name ;

        return $retVal;
    }
}
