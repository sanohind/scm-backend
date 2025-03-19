<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\User\LoginUserRequest;
use App\Http\Resources\User\UserLoginResource;
use App\Trait\ResponseApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    /**
     * Create Authentication token
     *
     * @param  \Illuminate\Http\Request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function login(LoginUserRequest $request)
    {
        $request->validated();

        if (!Auth::attempt($request->only(['username', 'password']))) {
            return $this->returnCustomResponseApi(
                false,
                'Invalid username or password. Please try again.',
                null,
                403,
                null,
                'success'
            );
        }

        $user = Auth::user();

        if ($user->status == 0) {
            Auth::logout();

            return $this->returnResponseApi(false, 'Account is inactive.', null, 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserLoginResource($user, $token))->response()->setStatusCode(200);
    }

    /**
     * Revoke authenthication token via soft delete
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->update(['expires_at' => now()]);

        return $this->returnResponseApi(true, 'User successfully logged out.', null, 200);
    }
}
