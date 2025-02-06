<?php

namespace App\Service\Syncronization;

use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnDetailDeleteErp;
use App\Models\DeliveryNote\DnHeader;
use App\Models\DeliveryNote\DnHeaderDeleteErp;
use App\Models\PurchaseOrder\PoDetail;
use App\Models\PurchaseOrder\PoDetailDeleteErp;
use App\Models\PurchaseOrder\PoHeader;
use App\Models\PurchaseOrder\PoHeaderDeleteErp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SyncDeleteData
{
    /**
     * Delete Purchase Order if in ERP was deleted
     * @return void
     */
    public function deletePo()
    {
        // Purchase Order Header
        try {
            // Query get deleted po_header from ERP
            $getPoHeader = PoHeaderDeleteErp::select('po_no', 'supplier_code')->get();

            // Conditioning and query delete po_header
            if (! empty($getPoHeader)) {
                foreach ($getPoHeader as $data) {
                    PoHeader::where('po_no', $data['po_no'])
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
            $getPoDetail = PoDetailDeleteErp::select('po_no', 'po_line', 'po_sequence')->get();

            // Conditioning and query delete po_detail
            if (! empty($getPoDetail)) {
                foreach ($getPoDetail as $data) {
                    PoDetail::where('po_no', $data['po_no'])
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

    /**
     * Delete Delivery Note if in ERP was deleted
     * @return void
     */
    public function deleteDn()
    {
        // Delivery Note Header
        try {
            // Query get deleted dn_header from ERP
            $getDnHeader = DnHeaderDeleteErp::select('dn_no', 'plan_delivery_date', 'supplier_code')->get();

            // Conditioning and query delete dn_header
            if (! empty($getDnHeader)) {
                foreach ($getDnHeader as $data) {
                    // format date & time
                    $formatPlanDeliveryDate = Carbon::parse($data['plan_delivery_date'])->format('Y-m-d');
                    $formatPlanDeliverytime = Carbon::parse($data['plan_delivery_date'])->format('H:i:s');
                    DnHeader::where('no_dn', $data['dn_no'])
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
            $getDnDetail = DnDetailDeleteErp::select('dn_no', 'dn_line', 'order_origin', 'no_order', 'order_set', 'order_line', 'order_seq')->get();

            // Conditioning and query delete dn_detail
            if (! empty($getDnDetail)) {
                foreach ($getDnDetail as $data) {
                    DnDetail::where('no_dn', $data['dn_no'])
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
    }
}
