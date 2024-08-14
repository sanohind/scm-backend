<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
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
            'password' => $this->password,
            'email' => $this->email,
        ];
    }
}
