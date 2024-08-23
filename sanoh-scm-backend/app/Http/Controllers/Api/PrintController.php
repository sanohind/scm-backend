<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Header;
use App\Http\Resources\PO_HeaderViewResource;
use App\Models\DN_Header;
use App\Http\Resources\DN_HeaderViewResource;
use Illuminate\Http\Request;
use GuzzleHttp\Psr7\Header;

class PrintController
{
    // this controller is for get the data that needed for print report
    public function poHeaderView($po_no)
    {
        //get data api to view
        $data_po = PO_Header::with('poDetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List User',
            'data' => PO_HeaderViewResource::collection($data_po)
        ]);
    }

    public function dnHeaderView($dn_no)
    {
        //get data api to view
        $data_dn = DN_Header::with('dnDetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List User',
            'data' => DN_HeaderViewResource::collection($data_dn)
        ]);
    }

    public function labelView()
    {

    }
}
