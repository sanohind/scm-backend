<?php

namespace App\Service\User;

use App\Models\User\Email;
use App\Models\User\PartnerLocal;

class UserDeleteAndDetachEmail
{
    public function __construct(
        protected UserGetEmail $userGetEmail,
    ) {}

    public function deleteAndDetachEmail(string $bp_code, array $email)
    {
        try {
            // get partner
            $partner = PartnerLocal::find($bp_code);

            // get current email based on bp_code
            $currentEmail = $this->userGetEmail->getEmail($bp_code)->toArray();

            // check the  array email diffrence
            // dd($currentEmail);
            // dd($email);
            $diffEmail = array_diff($currentEmail, $email);

            if (! empty($diffEmail)) {
                foreach ($diffEmail as $email) {
                    // Get email id
                    $email_id = Email::where('email', $email)->value('email_id');

                    // Detach email
                    $partner->email()->detach($email_id);

                    // Delete email
                    Email::where('email_id', $email_id)->delete();
                }
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error processing create email: cause of data request is null or {$e->getMessage()}", 403);
        }
    }
}
