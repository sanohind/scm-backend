<?php

namespace App\Service\User;

use App\Models\Users\BusinessPartner;

class UserGetEmail
{
    public function getEmail($bp_code)
    {
        $findEmail = BusinessPartner::find($bp_code);
        // dd($findEmail);
        $getEmail = $findEmail->email()->pluck('email');

        // dd($getEmail);
        return $getEmail;
    }
}
