<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Label;
use App\Models\DN_Header;
use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Http\Resources\DN_LabelResource;
use App\Http\Resources\DN_HeaderViewResource;
use App\Http\Resources\PO_HeaderViewResource;

class PrintController
{
    // this controller is for get the data that needed for print report
    public function poHeaderView($po_no)
    {
        //get data api to view
        $data_po = PO_Header::with('poDetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO',
            'data' => PO_HeaderViewResource::collection($data_po)
        ]);
    }

    public function dnHeaderView($dn_no)
    {
        //get data api to view
        $data_dn = DN_Header::with('dnDetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN',
            'data' => DN_HeaderViewResource::collection($data_dn)
        ]);
    }

    public function labelView($dn_no)
    {
        //get data api to view
        $data_lb = DN_Label::with('dnDetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan Label',
            'data' => DN_LabelResource::collection($data_lb)
        ]);
    }
}
