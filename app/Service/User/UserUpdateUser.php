<?php

namespace App\Service\User;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserUpdateUser
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected UserCreateUser $userCreateUser,
    )
    {}

    public function updateUser($data, $user) {

        $isUserExist =  User::findOrFail($user);

        if (!$isUserExist) {
            throw new \Exception("User doesnt exist", 403);
        }


        // Prepare the update data
        $updateData = [
            'bp_code' => $data['bp_code'],
            'name' => $data['name'],
            'role' => $data['role'],
        ];

        if (!empty($data['username'])) {
            $updateData['username'] = $data['username']; // Remove username if not provided
        }

        // Only hash and include the password if provided
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Update the user instance
        $isUserExist->update($updateData);

        // Attach email
        foreach ($data['email'] as $emails) {
            $this->userCreateUser->createEmail($data['bp_code'], $emails);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data Successfully Updated',
        ], 200);
    }
}
