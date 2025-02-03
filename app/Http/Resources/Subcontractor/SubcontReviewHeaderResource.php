<?php

namespace App\Http\Resources\Subcontractor;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SubcontReviewHeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'delivery_note' => $this->delivery_note,
            'status' => $this->status,
            'date_time' => $this->dateTime(),
            'response' => $this->response ?? 'Under Review',
        ];
    }

    // funciton concat date time

    public function dateTime()
    {
        $date = Carbon::parse($this->transaction_date)->format('Y-m-d');
        $time = Carbon::parse($this->transaction_time)->format('h:i');

        return "$date $time";
    }
}
