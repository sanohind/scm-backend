<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Mail\PoResponseSupplier;
use Illuminate\Support\Facades\Mail;

class TestMailController extends Controller
{
    //
    function mail() {
        // Get data User
        $user = User::where('role', 1)->get(['bp_code','email']);

        // Get PO open based of bp_code
        foreach ($user as $data) {

            $po_header = PO_Header::with('user')
            ->where('supplier_code', $data->bp_code)
            ->whereIn('po_status', ['Sent','Open'])
            ->get();

            // Store/format the return value of po_header into collection map function
            $collection = $po_header->map(function ($data) {
                $user = $data->user;

                return [
                    'bp_code' => $user ? $user->bp_code : 'User Data Not Found',
                    'email' => $user ? $user->email : 'Data Email Data Not Found',
                    'po_no' => $data ? $data->po_no : 'PO Data Not Found',
                ];
            });


            // dd($collection);


            Mail::to($data->email)->send(new PoResponseSupplier( po_header: $collection));
        }


        return response()->json(['data' => 'berhasil']);
    }


}
