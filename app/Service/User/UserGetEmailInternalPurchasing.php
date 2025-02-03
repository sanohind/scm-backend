<?php

namespace App\Service\User;

use App\Models\User\PartnerLocal;

class UserGetEmailInternalPurchasing
{
    public function getEmailPurchasing()
    {
        $findEmail = PartnerLocal::find('SLDUMMY');
        // dd($findEmail);
        $getEmail = $findEmail->email()->pluck('email');

        // dd($getEmail);
        return $getEmail;
    }
}
