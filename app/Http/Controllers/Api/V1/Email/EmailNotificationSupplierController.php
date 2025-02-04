<?php

namespace App\Http\Controllers\Api\V1\Email;

use App\Mail\PoResponseSupplier;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use App\Models\User\User;
use App\Service\User\UserGetEmail;
use Illuminate\Support\Facades\Mail;

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
        $user = User::where('role', 5)->get(['bp_code', 'email']);

        // Get PO open based of bp_code
        foreach ($user as $data) {

            $po_header = PoHeader::with('user')
                ->where('supplier_code', $data->bp_code)
                ->whereIn('po_status', ['Open'])
                ->get();

            $dn_header = DnHeader::with('partner')
                ->where('supplier_code', $data->bp_code)
                ->whereIn('status_desc', ['Open'])
                ->get();

            // Store/format the return value of po_header into collection map function
            $collection1 = $po_header->map(function ($data) {
                $user = $data->user;

                return [
                    'bp_code' => $user ? $user->bp_code : 'User Data Not Found',
                    'email' => $user ? $user->email : 'Data Email Data Not Found',
                    'po_no' => $data->po_no,
                ];
            });

            // Store/format the return value of po_header into collection map function
            $collection2 = $dn_header->map(function ($data) {
                $user = $data->user;

                return [
                    'bp_code' => $user ? $user->bp_code : 'User Data Not Found',
                    'email' => $user ? $user->email : 'Data Email Data Not Found',
                    'no_dn' => $data->no_dn,
                ];
            });
        }

        // Mail notification
        try {
            $email = $this->userGetEmail->getEmail($data->bp_code);

            foreach ($email as $data) {
                Mail::to($data)->send(new PoResponseSupplier($collection1, $collection2));
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Email Data Not Found & Only get user role 5. Message :'.$th->getMessage().' (On line '.$th->getLine().')',
            ], 500);
        }

        return response()->json(['message' => 'mail notification successfuly']);
    }
}
