<?php

namespace App\Service\User;

use App\Models\Users\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserCreateUser
{
    /**
     * Call service class
     * @param \App\Service\User\UserCreateAndAttachEmail $userCreateAndAttachEmail
     */
    public function __construct(
        protected UserCreateAndAttachEmail $userCreateAndAttachEmail,
    ) {}

    /**
     * Create new user and add/attach new email to business partner code
     * @param string $bpCode
     * @param string $name
     * @param string $role
     * @param string $status
     * @param string $username
     * @param string $password
     * @param string $email
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createUser(
        string $bpCode,
        string $name,
        string $role,
        string $status = "1",
        string $username,
        string $password,
        string $email,
    ) {
        // Create user
        User::create([
            'bp_code' => $bpCode,
            'name' => $name,
            'role' => $role,
            'status' => $status,
            'username' => $username,
            'password' => Hash::make($password),
            'email' => $email,
        ]);

        // add/attach new email to business partner code
        $this->userCreateAndAttachEmail->createEmail(
            $bpCode,
            $email
        );

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Data Successfully Stored',
        ], 200);
    }
}
