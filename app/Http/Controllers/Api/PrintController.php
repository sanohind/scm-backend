<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Label;
use App\Models\DN_Detail;
use App\Models\DN_Header;
use App\Models\PO_Header;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Label;
use Barryvdh\DomPDF\Facade\Pdf;
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
    // Get data for pdf
    $data_po = PO_Header::with('poDetail')
        ->where('po_no', $po_no)
        ->first();

    // Return if PO is not found
    if (!$data_po) {
        return response()->json([
            'success' => false,
            'message' => 'PO not found!',
            'po_no' => $po_no,  // Log the PO number in the response for debugging
        ], 404);
    }

    try {
        // Load the data into a resource if needed
        $value = new PO_HeaderViewResource($data_po);

        // Generate the PDF view
        $pdf = PDF::loadView('print.print-po', ['data' => $value]);

        // Stream the PDF directly to the browser
        return $pdf->stream('Purchase_Order_' . $po_no . '.pdf');
    } catch (\Exception $e) {
        // Log and handle any exceptions
        \Log::error("PDF generation error: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate PDF.',
        ], 500);
    }
}


    public function dnHeaderView($no_dn)
    {
        // Get data for pdf
        $data_dn = DN_Header::with('dnDetail')
        ->where('no_dn',$no_dn)
        ->first();

        // Return if po false/not found
        if (!$data_dn) {
            return response()->json([
                'success' => false,
                'message' => 'PO not found!',
            ], 404);
        }

        try {
            // Processed data get to resource
            $value = new DN_HeaderViewResource($data_dn);

            // For generate pdf view
            $pdf = PDF::loadView('print.print-dn', ['data' => $value]);

            // For Stream pdf view
            return $pdf->stream('Delivery_Note_' . $no_dn . '.pdf');
        } catch (\Exception $e) {

            // Exception for pdf generate error
            \Log::error("PDF generation error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
            ], 500);
        }
    }

    // label / kanban
    public function labelView($no_dn)
    {
        // get data
        $dn_header = DN_Header::with('dnDetail')->where('no_dn', $no_dn)->first();

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

        try {
            // For generate pdf view
            $pdf = PDF::loadView('print.print-label', ['data' => $label]);

            // For Stream pdf view
            return $pdf->stream('Label_' . $no_dn . '.pdf');
        } catch (\Exception $e) {

            // Exception for pdf generate error
            \Log::error("PDF generation error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF.',
            ], 500);
        }
        // create data log label
        // DN_Label::create()
    }
}
