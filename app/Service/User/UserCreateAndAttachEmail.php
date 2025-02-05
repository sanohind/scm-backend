<?php

namespace App\Service\User;

use App\Models\Users\Email;

class UserCreateAndAttachEmail
{
    public function createEmail(string $bp_code, string $email)
    {
        try {
            // Update or create email
            $emailRecord = Email::updateOrCreate(['email' => $email]);

            // Check the attach email
            $isAttached = $emailRecord->partner()->where('bp_code', $bp_code)->exists();

            // Attach only if the relationship does not already exist
            if (! $isAttached) {
                $emailRecord->partner()->attach($bp_code);
            }

            // Attach email to the partner
            // $emailRecord->partner()->attach($bp_code);

            // Return true if the email was processed successfully
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error processing create email: cause of data request is null or {$e->getMessage()}", 403);
        }
    }
}
