<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'bp_code' => $this->bp_code,
            'name' => $this->name,
            'role' => $this->role,
            'status' => $this->status,
            'username' => $this->username,
            'email' => $this->email,
            // tes waktu
            'created_at' => Carbon::Parse($this->created_at)->format('Y-m-d H:i'),
            'created_ata' => Carbon::Parse($this->created_at)->format('F'),
            // tes convert timezone
            'tes_timezone' => Carbon::now()->setTimezone('America/Chicago')->format('Y-m-d H:i')
        ];
    }
}
