<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\PartnerLocal;
use Illuminate\Http\Request;

class SynchronizeController extends Controller
{
    // Copy data ERP bussiness_partner
    // function for copy the data from sql server to mysql
    public function copyBusinessPartner(){
        // get all data from sql server
        $sqlsrvData = Partner::where('bp_status_desc','Active')->get();

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

    // Copy data ERP po_header
    // function for copy the data from sql server to mysql
    public function copyPoHeader(){
        // get all data from sql server
        $sqlsrvData = PO_Header_ERP::where('period',8)->get();

        // copy all data from sql server
        foreach ($sqlsrvData as $data) {
            PO_Header::create([
                'po_no',
                'bp_code',
                'supplier_code',
                'supplier_name',
                'po_type_desc',
                'po_date',
                'po_year',
                'po_period',
                'po_status',
                'references_1',
                'references_2',
                'attn_name',
                'po_currency',
                'pr_no',
                'planned_receipt_date',
                'payment_term',
                'po_origin',
                'po_revision_no',
                'po_revision_date',
                'response',
            ]);
        }

        return response()->json(['message' => 'Data successfuly copied']);
    }

    // Copy data ERP bussiness_partner
    // function for copy the data from sql server to mysql
    public function copyPoDetail(){
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

    // Copy data ERP bussiness_partner
    // function for copy the data from sql server to mysql
    public function copyDnHeader(){
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

    // Copy data ERP bussiness_partner
    // function for copy the data from sql server to mysql
    public function copyDnDetail(){
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
