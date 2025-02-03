<?php

namespace App\Service\SyncData;

use App\Models\DeliveryNote\DN_Detail;
use App\Models\DeliveryNote\DN_Detail_Delete_ERP;
use App\Models\DeliveryNote\DN_Header;
use App\Models\DeliveryNote\DN_Header_Delete_ERP;
use App\Models\PurchaseOrder\PO_Detail;
use App\Models\PurchaseOrder\PO_Detail_Delete_ERP;
use App\Models\PurchaseOrder\PO_Header;
use App\Models\PurchaseOrder\PO_Header_Delete_ERP;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncDeleteData
{
    public function deletePo()
    {
        // Purchase Order Header
        try {
            // Query get deleted po_header from ERP
            $getPoHeader = PO_Header_Delete_ERP::select('po_no', 'supplier_code')->get();

            // Conditioning and query delete po_header
            if (! empty($getPoHeader)) {
                foreach ($getPoHeader as $data) {
                    PO_Header::where('po_no', $data['po_no'])
                        ->where('supplier_code', $data['supplier_code'])
                        ->delete();
                }
            }

        } catch (\Throwable $th) {
            Log::debug("$th");
        }

        // Purchase Order Detail
        try {
            // Query get deleted po_detail from ERP
            $getPoDetail = PO_Detail_Delete_ERP::select('po_no', 'po_line', 'po_sequence')->get();

            // Conditioning and query delete po_detail
            if (! empty($getPoDetail)) {
                foreach ($getPoDetail as $data) {
                    PO_Detail::where('po_no', $data['po_no'])
                        ->where('po_line', $data['po_line'])
                        ->where('po_sequence', $data['po_sequence'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug("$th");
        }

        // return "Delete Purchase Order Successful";
    }

    public function deleteDn()
    {
        // Delivery Note Header
        try {
            // Query get deleted dn_header from ERP
            $getDnHeader = DN_Header_Delete_ERP::select('dn_no', 'plan_delivery_date', 'supplier_code')->get();

            // Conditioning and query delete dn_header
            if (! empty($getDnHeader)) {
                foreach ($getDnHeader as $data) {
                    // format date & time
                    $formatPlanDeliveryDate = Carbon::parse($data['plan_delivery_date'])->format('Y-m-d');
                    $formatPlanDeliverytime = Carbon::parse($data['plan_delivery_date'])->format('H:i:s');
                    DN_Header::where('no_dn', $data['dn_no'])
                        ->where('plan_delivery_date', $formatPlanDeliveryDate)
                        ->where('plan_delivery_time', $formatPlanDeliverytime)
                        ->where('supplier_code', $data['supplier_code'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            Log::debug("$th");
        }

        // Delivery Note Detail
        try {
            // Query get deleted dn_detail from ERP
            $getDnDetail = DN_Detail_Delete_ERP::select('dn_no', 'dn_line', 'order_origin', 'no_order', 'order_set', 'order_line', 'order_seq')->get();

            // Conditioning and query delete dn_detail
            if (! empty($getDnDetail)) {
                foreach ($getDnDetail as $data) {
                    DN_Detail::where('no_dn', $data['dn_no'])
                        ->where('dn_line', $data['dn_line'])
                        ->where('order_origin', $data['order_origin'])
                        ->where('no_order', $data['no_order'])
                        ->where('order_set', $data['order_set'])
                        ->where('order_line', $data['order_line'])
                        ->where('order_seq', $data['order_seq'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            Log::debug("$th");
        }

        // return "Delete Delivery Note Successful";
    }
}
