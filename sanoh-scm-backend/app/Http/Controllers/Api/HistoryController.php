<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Header;
use App\Models\PO_Header;
use App\Http\Resources\DN_HistoryViewResource;
use App\Http\Resources\PO_HistoryViewResource;
use GuzzleHttp\Psr7\Header;

class HistoryController
{
    // this controller is for get the data that needed for history
    public function poHeaderHistory($bp_code)
    {
        // $user = Auth::user();
        // $tes = (string) $bp_code;
        // dd($tes);
        //get data api to view
        $data_po = PO_Header::with('partner')
                            ->where('supplier_code', $bp_code)
                            ->where('po_status', 'closed')
                            ->get();

        // dd($data_po);
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO',
            'data' => PO_HistoryViewResource::collection($data_po)
        ]);
    }

    public function dnHeaderHistory($bp_code)
    {
        $tes = $bp_code;
        //get data api to view
        $data_dn = DN_Header::with('poHeader')
                            ->whereHas('poHeader', function($query)  use ($tes)
                            {
                                $query->where('po_status', 'closed');
                                $query->where('supplier_code', $tes);
                            })
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN',
            'data' => DN_HistoryViewResource::collection($data_dn)
        ]);
    }
}
