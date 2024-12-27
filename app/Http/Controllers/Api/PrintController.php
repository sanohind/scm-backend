<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\DeliveryNote\DN_HeaderQtyConfirmViewResource;
use App\Http\Resources\DeliveryNote\DN_HeaderQtyOutstandingViewResource;
use App\Models\DeliveryNote\DN_Detail;
use App\Models\DeliveryNote\DN_Detail_Outstanding;
use PDF;
// use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Label;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DN_Label;
use App\Models\DeliveryNote\DN_Header;
use App\Models\PurchaseOrder\PO_Header;
use App\Http\Resources\DeliveryNote\DN_LabelResource;
use App\Http\Resources\DeliveryNote\DN_HeaderViewResource;
use App\Http\Resources\PurchaseOrder\PO_HeaderViewResource;


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
            'message' => 'PO Generated Successfully',
            'data' => PO_HeaderViewResource::collection($data_po)
        ]);
    }

    // print dn all
    public function dnHeaderView($no_dn)
    {
        //get data api to view
        $data_dn = DN_Header::with('dnDetail','partner')->where('no_dn',$no_dn)->get();

        // print_at
        $data_update = DN_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DN_HeaderViewResource::collection($data_dn)
        ]);
    }

    // print dn based on qty confirm
    public function dnHeaderViewQtyConfirm($no_dn)
    {
        //get data api to view
        $data_dn = DN_Header::with('dnDetail','partner')->where('no_dn',$no_dn)->get();

        // print_at
        $data_update = DN_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DN_HeaderQtyConfirmViewResource::collection($data_dn)
        ]);
    }

    // print dn based on outstanding
    public function dnHeaderViewOutstanding(Request $request)
    {
        $no_dn = $request->route('no_dn');
        $outstanding = $request->route('outstanding');

        //get data api to view
        $data_dn = DN_Header::with('dnDetail','partner','dnOutstanding')
        ->where('no_dn',$no_dn)
        ->whereHas('dnOutstanding', function ($query) use($outstanding) {
            $query->where('wave', $outstanding);
        })
        ->get();

        // print_at
        $data_update = DN_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DN_HeaderQtyOutstandingViewResource::collection($data_dn)
        ]);
    }

    // label / kanban all
    public function labelAllView($no_dn)
    {
        // get data
        $dn_header = DN_Header::with('dnDetail')->where('no_dn', $no_dn)->first();

        // print_at
        $data_update = DN_Header::where('no_dn', $no_dn)->first();
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

        return response()->json([
            'success' => true,
            'message' => 'Labels generated successfully',
            'data' => $label,
        ]);
    }

    // Label / kanban based of qty confirm
    public function labelQtyConfirm($no_dn) {
        // get data
        $dn_header = DN_Header::with('dnDetail')->where('no_dn', $no_dn)->first();

        // print_at
        $data_update = DN_Header::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_label_printed_at' => Carbon::now()->format('Y-m-d H:i')
        ]);

        // variable for store array
        $label = [];

        // initialize looping each dn_detail with relationship from dn_header
        // foreach ($dn_header->dnDetail as $dn_detail) {
        //     // Calculate no_of_kanban = dn_qty/dn_snp
        //     $no_of_kanban = ceil($dn_detail->qty_confirm / $dn_detail->dn_snp);

        //     // Generate label based of no_of_kanbana
        //     for ($i = 0; $i < $no_of_kanban; $i++) {
        //         $label[] = new DN_LabelResource($dn_detail);
        //     }

        //     // if
        // }

        // Iterate over each dn_detail
        foreach ($dn_header->dnDetail as $dn_detail) {
            $qty_confirm = $dn_detail->qty_confirm;
            $dn_snp = $dn_detail->dn_snp;

            // Generate labels
            while ($qty_confirm > 0) {
                $currentQuantity = $qty_confirm >= $dn_snp ? $dn_snp : $qty_confirm;
                $label[] = new DN_LabelResource($dn_detail, $currentQuantity);
                $qty_confirm -= $currentQuantity;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Labels generated successfully',
            'data' => $label,
        ]);
    }

    // Label / kanban outstanding
    public function labelOutstanding($no_dn, $outstanding)
    {
        // Ambil data DN_Detail_Outstanding dengan hubungan ke dnDetail
        $dn_detail_outstanding = DN_Detail_Outstanding::with('dnDetail')
            ->where('wave', $outstanding)
            ->whereHas('dnDetail', function ($query) use ($no_dn) {
                $query->where('no_dn', $no_dn);
            })
            ->get();

        // Variabel untuk menyimpan label
        $label = [];

        // Iterasi setiap data dn_detail_outstanding
        foreach ($dn_detail_outstanding as $data) {
            $dnDetail = $data->dnDetail;
            $qty_outstanding = $data->qty_outstanding;
            $dn_snp = $dnDetail->dn_snp;

            // Loop untuk membuat label berdasarkan qty_outstanding
            while ($qty_outstanding > 0) {
                $currentQuantity = $qty_outstanding >= $dn_snp ? $dn_snp : $qty_outstanding;
                $label[] = new DN_LabelResource($dnDetail, $currentQuantity);
                $qty_outstanding -= $currentQuantity;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Labels $no_dn (outstanding: $outstanding) generated successfully",
            'data' => $label,
        ]);
    }
}
