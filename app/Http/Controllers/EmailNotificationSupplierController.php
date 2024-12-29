<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Service\User\UserGetEmail;
use Illuminate\Http\Request;
use App\Mail\PoResponseSupplier;
use Illuminate\Support\Facades\Mail;
use App\Models\DeliveryNote\DN_Header;
use App\Models\PurchaseOrder\PO_Header;

class EmailNotificationSupplierController
{
    public function __construct(
        protected UserGetEmail $userGetEmail,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function mail()
    {
        $user = User::where('role', 5)->get(['bp_code','email']);

        // Get PO open based of bp_code
        foreach ($user as $data) {

            $po_header = PO_Header::with('user')
            ->where('supplier_code', $data->bp_code)
            ->whereIn('po_status', ['Open'])
            ->get();

            $dn_header = DN_Header::with('partner')
            ->where('supplier_code', $data->bp_code)
            ->whereIn('status_desc', ['Open'])
            ->get();

            // Store/format the return value of po_header into collection map function
            $collection1 = $po_header->map(function ($data) {
                $user = $data->user;

                return [
                    'bp_code' => $user ? $user->bp_code : 'User Data Not Found',
                    'email' => $user ? $user->email : 'Data Email Data Not Found',
                    'po_no' => $data->po_no
                ];
            });

            // Store/format the return value of po_header into collection map function
            $collection2 = $dn_header->map(function ($data) {
                $user = $data->user;

                return [
                    'bp_code' => $user ? $user->bp_code : 'User Data Not Found',
                    'email' => $user ? $user->email : 'Data Email Data Not Found',
                    'no_dn' => $data->no_dn
                ];
            });
        }

        // Mail notification
        $email = $this->userGetEmail->getEmail($data->bp_code);

        foreach ($email as $data) {
            Mail::to($data)->send(new PoResponseSupplier($collection1,$collection2));
        }

        return response()->json(['message' => 'mail notification successfuly']);
    }
}
