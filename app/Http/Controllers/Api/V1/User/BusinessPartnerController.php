<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Requests\User\UpdateBusinessPartnerEmailRequest;
use App\Http\Resources\User\BusinessPartnerEmailResource;
use App\Http\Resources\User\PartnerResource;
use App\Models\Users\BusinessPartner;
use App\Service\User\UserCreateAndAttachEmail;
use App\Service\User\UserDeleteAndDetachEmail;
use App\Service\User\UserGetEmail;

class BusinessPartnerController
{
    /**
     * Call service class
     * @param \App\Service\User\UserCreateAndAttachEmail $userCreateAndAttachEmail
     * @param \App\Service\User\UserGetEmail $userGetEmail
     * @param \App\Service\User\UserDeleteAndDetachEmail $userDeleteAndDetachEmail
     */
    public function __construct(
        protected UserCreateAndAttachEmail $userCreateAndAttachEmail,
        protected UserGetEmail $userGetEmail,
        protected UserDeleteAndDetachEmail $userDeleteAndDetachEmail,
    ) {}

    /**
     * Get Business Partner data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getBusinessPartner()
    {
        $users = BusinessPartner::select('bp_code', 'bp_name', 'adr_line_1')
            ->where('bp_code', 'like', 'SL%')
            ->orWhere('bp_code', 'like', 'SI%')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List  Successfully',
            'data' => PartnerResource::collection($users),
        ], 200);
    }

    /**
     * Get Business Partner Email data
     * @param mixed $bpCode
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getBussinessPartnerEmail($bpCode) {
        // Get business partner email
        $data = $this->userGetEmail->getEmail($bpCode);

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Display Partner Email Successfully',
            'data' => [
                'email' => $data
            ]
        ], 200);
    }

    /**
     * Update Business Partner Email
     * @param mixed $bpCode
     * @param \App\Http\Requests\User\UpdateBusinessPartnerEmailRequest $email
     * @return void
     */
    public function updateBusinessPartnerEmail($bpCode, UpdateBusinessPartnerEmailRequest $request) {
        // Validate request
        $data = $request->validated();

        //  add Email
        foreach ($data['email'] as $emails) {
            $this->userCreateAndAttachEmail->createEmail(
                $bpCode,
                $emails
            );
        }

        // Remove Email
         $this->userDeleteAndDetachEmail->deleteAndDetachEmail($bpCode, $data['email']);


        // Response
        return response()->json([
            'status' => true,
            'message' => 'Update Email Successful',
        ], 200);
    }
}
