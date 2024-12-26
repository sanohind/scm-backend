<?php

namespace App\Service\User;

use App\Http\Resources\UserEmailResource;
use App\Models\PartnerEmail;
use App\Models\PartnerLocal;

class UserGetEmail
{
    public function getEmail($bp_code) {
        $findEmail = PartnerLocal::find($bp_code);
        // dd($findEmail);
        $getEmail = $findEmail->email()->pluck('email');

        // dd($getEmail);
        return response()->json([
            'success' => true,
            'message' => 'Display List User Successfully',
            'data' => $getEmail,
        ]);
    }
}
