<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\User\LoginUserRequest;
use App\Models\Users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    /**
     * Create Authentication token
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        // Find the user by username
        $user = User::where('username', $request->username)->first();

        // Validate user existence and password with
        if (! Auth::attempt($request->only(['username', 'password']))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid username or password. Please try again.',
            ], 401);
        }

        // Retrieve the authenticated user
        $user = Auth::user();

        // if user status inactive
        if ($user->status == 0) {
            Auth::logout();

            return response()->json([
                'status' => false,
                'message' => 'Account is inactive',
            ], 403);
        }

        // Generate a token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return token response
        return response()->json([
            'access_token' => $token,
            'role' => $user->role,
            'bp_code' => $user->bp_code,
            'name' => $user->name,
            'token_type' => 'Bearer',
            'supplier_name' => ($user->role != 1 || 2 || 3 || 4) ? $user->partner->adr_line_1 : 'PT Sanoh Indonesia',
        ]);
    }

    /**
     * Revoke authenthication token via soft delete
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Revoke token
        $request->user()->currentAccessToken()->update(['expires_at' => now()]);

        // logout success respond
        return response()->json([
            'status' => true,
            'message' => 'User successfully logged out',
        ], 200);
    }
}
