<?php

namespace App\Service\User;

use App\Models\Users\BusinessPartner;

class UserGetEmailInternalPurchasing
{
    public function getEmailPurchasing()
    {
        $findEmail = BusinessPartner::find('SLDUMMY');
        // dd($findEmail);
        $getEmail = $findEmail->email()->pluck('email');

        // dd($getEmail);
        return $getEmail;
    }
}
