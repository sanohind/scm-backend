<?php

namespace App\Service\User;

use App\Models\Users\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class UserUpdateUser
{
    public function __construct(
        protected UserCreateAndAttachEmail $userCreateAndAttachEmail,
    ) {}

    /**
     * Update user data
     * @param int $id
     * @param string $bpCode
     * @param mixed $name
     * @param mixed $role
     * @param mixed $username
     * @param mixed $password
     * @param mixed $email
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateUser(
        int $id,
        string $bpCode,
        ?string $name,
        ?string $role,
        ?string $username,
        ?string $password,
        ?string $email,
    ) {
        // Get user
        $user = User::findOrFail($id);

        // Response user deosnt exist
        if (!$user) {
            throw new HttpResponseException(
                response()->json([
                    'status' => false,
                    'message' => 'User doesnt exist.',
                    'errors' => 'User doesnt exist.',
                ], 422)
            );
        }

        // Update user data
        $user->update([
            'bp_code' => $bpCode ?? $user->bp_code,
            'name' => $name ?? $user->name,
            'role' => $role ?? $user->role,
            'username' => $username ?? $user->username,
            'password' => ($password != null) ? Hash::make($password) : $user->password,
            'email' => $email ?? $user->email,
        ]);

        // add/attach new email to business partner code
        $this->userCreateAndAttachEmail->createEmail(
            $bpCode,
            $email
        );

        // Response
        return response()->json([
            'status' => true,
            'message' => 'User Successfully Updated',
        ], 200);
    }
}
