<?php

namespace App\Http\Controllers\Api;

use App\Models\Partner;
use App\Models\PartnerLocal;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index(){
        $users = PartnerLocal::all();

        return view('index', compact('users'));
    }

    // function for copy the data from sql server to mysql
    public function copy(){
        // get all data from sql server
        $sqlsrvData = Partner::all();

        // copy all data from sql server
        foreach ($sqlsrvData as $data) {
            PartnerLocal::create([
                'bp_code' => $data->bp_code,
                'bp_name' => $data->bp_name,
                'bp_status_desc' => $data->bp_status_desc,
                'bp_currency' => $data->bp_currency,
                'country' => $data->country,
                'adr_line_1' => $data->adr_line_1,
                'adr_line_2' => $data->adr_line_2,
                'adr_line_3' => $data->adr_line_3,
                'adr_line_4' => $data->adr_line_4,
                'bp_phone' => $data->bp_phone,
                'bp_fax' => $data->bp_fax,
            ]);
        }

        return response()->json(['message' => 'Data successfuly copied']);
    }
}
