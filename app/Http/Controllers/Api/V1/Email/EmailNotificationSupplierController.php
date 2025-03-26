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
     */
    public function mail()
    {
        $bpCode = User::where('role', 5)->pluck('bp_code');
        // dd($bpCode);

        // try {
        foreach ($bpCode as $data) {
            // dd($data);
            $poHeader = PoHeader::with('user')
                ->where('supplier_code', $data)
                ->whereIn('po_status', ['Open'])
                ->get();

            $dnHeader = DnHeader::with('partner')
                ->where('supplier_code', $data)
                ->whereIn('status_desc', ['Open'])
                ->get();

            $email = $this->userGetEmail->getEmail($data);

            foreach ($email as $data) {
                Mail::to($data)->send(new PoResponseSupplier($poHeader, $dnHeader));
            }
        }
        // } catch (\Throwable $th) {
        //     return $this->returnResponseApi(
        //         false,
        //         'Email Data Not Found & Only get user role 5. Message :' . $th->getMessage() . ' (On line ' . $th->getLine() . ')',
        //         null,
        //         500
        //     );
        // }

        // return $this->returnResponseApi(true, 'mail notification successfuly', null, 200);
    }
}
