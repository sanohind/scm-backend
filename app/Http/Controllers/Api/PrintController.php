<?php

namespace App\Http\Controllers\Api;

use PDF;
// use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use App\Models\DN_Label;
use App\Models\DN_Detail;
use App\Models\DN_Header;
use App\Models\PO_Header;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Label;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DN_LabelResource;
use App\Http\Resources\DN_DetailViewResource;
use App\Http\Resources\DN_HeaderViewResource;
use App\Http\Resources\PO_HeaderViewResource;


class PrintController
{
    // this controller is for get the data that needed for print report
    public function poHeaderView($po_no)
    {
        //get data api to view
        $data_po = PO_Header::with('poDetail')->where('po_no',$po_no)->get();

        // print_at
        $data_update = PO_Header::where('po_no', $po_no)->first();
        $data_update->update([
            'po_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO',
            'data' => PO_HeaderViewResource::collection($data_po)
        ]);
    }

    public function dnHeaderView($no_dn)
    {
        //get data api to view
        $data_dn = DN_Header::with('dnDetail')->where('no_dn',$no_dn)->first();

        // print_at
        $data_update = PO_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN',
            'data' => DN_HeaderViewResource::collection($data_dn)
        ]);
    }

    // label / kanban
    public function labelView($no_dn)
    {
        // get data
        $dn_header = DN_Header::with('dnDetail')->where('no_dn', $no_dn)->first();

        // print_at
        $data_update = PO_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_label_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        // variable for store array
        $label = [];

        // initialize looping each dn_detail with relationship from dn_header
        foreach ($dn_header->dnDetail as $dn_detail) {
            // Calculate no_of_kanban = dn_qty/dn_snp
            $no_of_kanban = ceil($dn_detail->dn_qty / $dn_detail->dn_snp);

            // Generate label based of no_of_kanbana
            for ($i = 0; $i < $no_of_kanban; $i++) {
                $label[] = new DN_LabelResource($dn_detail);
            }
        }

        // create data log label
        // DN_Label::create()

        return response()->json([
            'success' => true,
            'message' => 'Labels generated successfully',
            'data' => $label,
        ]);
    }
}
