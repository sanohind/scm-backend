<?php

namespace App\Http\Controllers\Api\V1\Email;

use App\Models\Users\User;
use App\Trait\ResponseApi;
use App\Mail\PoResponseSupplier;
use App\Service\User\UserGetEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;

class EmailNotificationSupplierController
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    /**
     * Call service class
     * @param \App\Service\User\UserGetEmail $userGetEmail
     */
    public function __construct(
        protected UserGetEmail $userGetEmail,
    ) {
    }

    /**
     * Mail to all user role supplier email
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function mail()
    {
        $user = User::where('role', 5)->get(['bp_code', 'email']);
        foreach ($user as $data) {
            $poHeader = PoHeader::with('user')
                ->where('supplier_code', $data->bp_code)
                ->whereIn('po_status', ['Open'])
                ->get();

            $dnHeader = DnHeader::with('partner')
                ->where('supplier_code', $data->bp_code)
                ->whereIn('status_desc', ['Open'])
                ->get();
        }

        try {
            $email = $this->userGetEmail->getEmail($data->bp_code);

            foreach ($email as $data) {
                Mail::to($data)->send(new PoResponseSupplier($poHeader, $dnHeader));
            }
        } catch (\Throwable $th) {
            return $this->returnResponseApi(
                false,
                'Email Data Not Found & Only get user role 5. Message :' . $th->getMessage() . ' (On line ' . $th->getLine() . ')',
                null,
                500
            );
        }

        return $this->returnResponseApi(true, 'mail notification successfuly', null, 200);
    }
}
