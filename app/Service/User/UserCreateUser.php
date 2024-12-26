<?php

namespace App\Service\User;

use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreateUser
{
    public function createUser($data) {
        // business process
        $result = DB::transaction(function () use($data) {
            // Create user
            $createUser = $this->createUserProcess(
                $data['bp_code'],
                $data['name'],
                $data['role'],
                $data['status'],
                $data['username'],
                $data['password'],
            );

            // Create Email
            foreach ($data['email'] as $emails) {
                $createEmail = $this->createEmail(
                    $data['bp_code'],
                    $emails
                );
            }

            if ($createUser == true && $createEmail == true) {
                return response()->json([
                    'status' => true,
                    'message' => 'Data Successfully Stored',
                ], 200);
            }
        });

        return $result;
    }

    private function createUserProcess(string $bp_code, string $name, string $role, int $status, string $username, string $password) {
        try {
            // logic
            User::create([
                'bp_code' => $bp_code,
                'name' => $name,
                'role' => $role,
                'status' => $status,
                'username' => $username,
                'password' => Hash::make($password),
            ]);

            // for callback
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Error processing create user: cause of data request is null or {$e->getMessage()}", 403);
        }
    }

    public function createEmail(string $bp_code, string $email) {
        try {
            // Update or create email
            $emailRecord = Email::updateOrCreate(['email' => $email]);

            // Check the attach email
            $isAttached = $emailRecord->partner()->where('bp_code', $bp_code)->exists();

            // Attach only if the relationship does not already exist
            if (!$isAttached) {
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
