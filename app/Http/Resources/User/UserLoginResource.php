<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    // Initialize variable
    public static $wrap = false;
    protected $token;
    protected $user;

    // Constructor
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'access_token' => $this->whenNotNull($this->token),
            'role' => $this->user->role ?? null,
            'bp_code' => $this->user->bp_code ?? null,
            'name' => $this->user->name ?? null,
            'token_type' => 'Bearer',
            'supplier_name' => (! in_array($this->user->role, [1, 2, 3, 4])) ? $this->user->partner->adr_line_1 : 'PT Sanoh Indonesia',
        ];
    }
}
