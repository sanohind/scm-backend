<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\DeliveryNote\DnHeaderQtyConfirmViewResource;
use App\Http\Resources\DeliveryNote\DnHeaderQtyOutstandingViewResource;
use App\Http\Resources\DeliveryNote\DnHeaderViewResource;
use App\Http\Resources\DeliveryNote\DnLabelAllResource;
use App\Http\Resources\DeliveryNote\DnLabelResource;
use App\Http\Resources\PurchaseOrder\PoHeaderViewResource;
use App\Models\DeliveryNote\DnDetailOutstanding;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Carbon\Carbon;

class PrintController
{
    // this controller is for get the data that needed for print report
    public function poHeaderView($po_no)
    {
        //get data api to view
        $data_po = PoHeader::with('poDetail')->where('po_no', $po_no)->get();

        // print_at
        $data_update = PoHeader::where('po_no', $po_no)->first();
        $data_update->update([
            'po_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'PO Generated Successfully',
            'data' => PoHeaderViewResource::collection($data_po),
        ]);
    }

    // print dn all
    public function dnHeaderView($no_dn)
    {
        //get data api to view
        $data_dn = DnHeader::with('dnDetail', 'partner')->where('no_dn', $no_dn)->get();

        // print_at
        $data_update = DnHeader::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DnHeaderViewResource::collection($data_dn),
        ]);
    }

    // print dn based on qty confirm
    public function dnHeaderViewQtyConfirm($no_dn)
    {
        //get data api to view
        $data_dn = DnHeader::with('dnDetail', 'partner')->where('no_dn', $no_dn)->get();

        // print_at
        $data_update = DnHeader::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DnHeaderQtyConfirmViewResource::collection($data_dn),
        ]);
    }

    // print dn based on outstanding
    public function dnHeaderViewOutstanding($outstanding, $no_dn)
    {
        //get data api to view
        $data_dn = DnHeader::with(['dnDetail' => function ($query) use ($outstanding) {
            $query->with(['dnOutstanding' => function ($q) use ($outstanding) {
                $q->where('wave', $outstanding);
            }]);
        }, 'partner'])
            ->where('no_dn', $no_dn)
            ->get();

        // print_at
        $data_update = DnHeader::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'DN Generated Successfully',
            'data' => DnHeaderQtyOutstandingViewResource::collection($data_dn),
        ]);
    }

    // label / kanban all
    public function labelAllView($no_dn)
    {
        // get data
        $dn_header = DnHeader::with('dnDetail')->where('no_dn', $no_dn)->first();

        // print_at
        $data_update = DnHeader::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_label_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        // variable for store array
        $label = [];

        // initialize looping each dn_detail with relationship from dn_header
        foreach ($dn_header->dnDetail as $dn_detail) {
            // Calculate no_of_kanban = dn_qty/dn_snp
            $no_of_kanban = ceil($dn_detail->dn_qty / $dn_detail->dn_snp);

            // Generate label based of no_of_kanbana
            for ($i = 0; $i < $no_of_kanban; $i++) {
                $label[] = new DnLabelAllResource($dn_detail);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Labels generated successfully',
            'data' => $label,
        ]);
    }

    // Label / kanban based of qty confirm
    public function labelQtyConfirm($no_dn)
    {
        // get data
        $dn_header = DnHeader::with('dnDetail')->where('no_dn', $no_dn)->first();

        // print_at
        $data_update = DnHeader::where('no_dn', $no_dn)->first();
        $data_update->update([
            'dn_label_printed_at' => Carbon::now()->format('Y-m-d H:i'),
        ]);

        // Iterate over each dn_detail
        $label = [];
        foreach ($dn_header->dnDetail as $dn_detail) {
            $qty_confirm = $dn_detail->qty_confirm;
            $dn_snp = $dn_detail->dn_snp;

            // Generate labels
            while ($qty_confirm > 0) {
                $currentQuantity = $qty_confirm >= $dn_snp ? $dn_snp : $qty_confirm;
                $label[] = new DnLabelResource($dn_detail, $currentQuantity);
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
    public function labelOutstanding($outstanding, $no_dn)
    {
        // Ambil data DnDetailOutstanding dengan hubungan ke dnDetail
        $dn_detail_outstanding = DnDetailOutstanding::with('dnDetail')
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
                $label[] = new DnLabelResource($dnDetail, $currentQuantity);
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
