<?php

namespace App\Service\User;

use App\Models\User\PartnerLocal;

class UserGetEmail
{
    public function getEmail($bp_code)
    {
        $findEmail = PartnerLocal::find($bp_code);
        // dd($findEmail);
        $getEmail = $findEmail->email()->pluck('email');

        // dd($getEmail);
        return $getEmail;
    }
}
