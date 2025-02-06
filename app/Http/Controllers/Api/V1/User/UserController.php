<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserDetailResource;
use App\Http\Resources\User\UserResource;
use App\Models\Users\User;
use App\Service\User\UserCreateAndAttachEmail;
use App\Service\User\UserCreateUser;
use App\Service\User\UserGetEmail;
use App\Service\User\UserUpdateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController
{
    /**
     * Call service class
     * @param \App\Service\User\UserCreateUser $userCreateUser
     * @param \App\Service\User\UserUpdateUser $userUpdateUser
     * @param \App\Service\User\UserGetEmail $userGetEmail
     * @param \App\Service\User\UserCreateAndAttachEmail $userCreateAndAttachEmail
     */
    public function __construct(
        protected UserCreateUser $userCreateUser,
        protected UserUpdateUser $userUpdateUser,
        protected UserGetEmail $userGetEmail,
        protected UserCreateAndAttachEmail $userCreateAndAttachEmail,
    ) {
    }

    /**
     * Get all user data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getUser()
    {
        // Get user data
        $dataUser = User::orderby('user_id', 'asc')
            ->with('partner')
            ->get();

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Display User Data Successfully',
            'data' => UserResource::collection($dataUser),
        ], 200);
    }

    /**
     * Get list Business Partner Email
     * @param mixed $bpCode
     */
    public function getBusinessPartnerEmail($bpCode)
    {
        // Get Business Partner Email
        $this->userGetEmail->getEmail($bpCode);

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Display Business Partner Email Successfully',
        ], 200);
    }

    /**
     * Create/store new User
     * @param \App\Http\Requests\User\StoreUserRequest $request
     */
    public function createUser(StoreUserRequest $request)
    {
        // Validate request
        $request->validated();

        // Create new user
        $this->userCreateUser->createUser(
            $request->bp_code,
            $request->name,
            $request->role,
            $request->status,
            $request->username,
            $request->password,
            $request->email,
        );

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Create User Successfully',
        ], 200);
    }

    /**
     * Get User detail data
     * @param mixed $user
     * @return UserDetailResource
     */
    public function getUserDetail($user)
    {
        // Get User detail
        $data_edit = User::findOrFail($user);

        // Return
        return new UserDetailResource($data_edit);
    }

    /**
     * Update user data
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @param mixed $user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateUser(UpdateUserRequest $request, $id)
    {
        // Validate User
        $request->validated();

        // Update User
        $this->userUpdateUser->updateUser(
            $id,
            $request->bp_code,
            $request->name,
            $request->role,
            $request->username,
            $request->password,
            $request->email,
        );

        // Response
        return response()->json([
            'status' => true,
            'message' => 'User Successfully Updated',
        ], 200);
    }

    /**
     * Update status User active/inactive
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $user)
    {
        // Find user
        $data_edit = User::findOrFail($user);

        // Fail find user
        if (!$user) {
            return response()->json([
                'success' => false,
                'errors' => 'User Not Found',
            ], 404);
        }

        // Data input rules
        $rules = [
            'status' => 'required|string|max:25',
        ];

        // Validator instance
        $validator = Validator::make($request->all(), $rules);

        // Check validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Update Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update the user with validated data
        $validatedData = $validator->validated();

        // Update the user instance
        $data_edit->update($validatedData);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Data User "' . $data_edit->username . '" Successfully Updated',
            'data' => new UserResource($data_edit),
        ]);
    }

    /**
     * Move/migrate main email user from table user to table email
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function moveEmail()
    {
        $data = User::whereNotNull('email')->select('bp_code', 'email')->get();

        foreach ($data as $user) {
            $this->userCreateAndAttachEmail->createEmail($user->bp_code, $user->email);
        }

        return response()->json([
            'data' => 'Move Email Success',
        ]);
    }

    /**
     * Delete User
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function deleteUser($id)
    {
        $getUser = User::findOrFail($id);

        $getUser->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully',
        ]);
    }
}
