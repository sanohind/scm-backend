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
use App\Trait\ErrorLog;

class SyncDeleteData
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ErrorLog = Monitoring debug and error on process
     */
    use ErrorLog;

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
            if (!empty($getPoHeader)) {
                foreach ($getPoHeader as $data) {
                    PoHeader::where('po_no', $data['po_no'])
                        ->where('supplier_code', $data['supplier_code'])
                        ->delete();
                }
            }

        } catch (\Throwable $th) {
            $this->syncError(
                'Delete Po Header Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
            );
        }

        // Purchase Order Detail
        try {
            // Query get deleted po_detail from ERP
            $getPoDetail = PoDetailDeleteErp::select('po_no', 'po_line', 'po_sequence')->get();

            // Conditioning and query delete po_detail
            if (!empty($getPoDetail)) {
                foreach ($getPoDetail as $data) {
                    PoDetail::where('po_no', $data['po_no'])
                        ->where('po_line', $data['po_line'])
                        ->where('po_sequence', $data['po_sequence'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            $this->syncError(
                'Delete Po Detail Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
            );
        }
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
            $getDnHeader = DnHeaderDeleteErp::select('dn_no', 'supplier_code')->get();

            // Conditioning and query delete dn_header
            if (!empty($getDnHeader)) {
                foreach ($getDnHeader as $data) {
                    DnHeader::where('no_dn', $data['dn_no'])
                        ->where('supplier_code', $data['supplier_code'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            $this->syncError(
                'Delete DN Header Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
            );
        }

        // Delivery Note Detail
        try {
            // Query get deleted dn_detail from ERP
            $getDnDetail = DnDetailDeleteErp::select('dn_no', 'dn_line')->get();

            // Conditioning and query delete dn_detail
            if (!empty($getDnDetail)) {
                foreach ($getDnDetail as $data) {
                    DnDetail::where('no_dn', $data['dn_no'])
                        ->where('dn_line', $data['dn_line'])
                        ->delete();
                }
            }
        } catch (\Throwable $th) {
            $this->syncError(
                'Delete DN Detail Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
            );
        }
    }
}
