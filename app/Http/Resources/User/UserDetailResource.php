<?php

namespace App\Http\Resources\User;

use App\Models\Users\BusinessPartner;
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

    // private function getEmail()
    // {
    //     $findEmail = BusinessPartner::find($this->bp_code);
    //     // dd($findEmail);
    //     $getEmail = $findEmail->email()->pluck('email');

    //     return $getEmail;
    // }
}
