<?php

namespace App\Service\Syncronization;

class SyncPurchaseOrderData
{
    public function syncPurchaseOrder() {
        /*

                po header



        */
        //year and period
        $actualYear = Carbon::now()->year;
        $actualPeriod = Carbon::now()->month;
        // dd($actualYear,$actualPeriod);

        $sqlsrvDataPoHeader = PO_Header_ERP::where('po_period', $actualPeriod)
            ->where('po_year', $actualYear)
            ->where('po_type_desc', 'PO LOCAL')
            ->get();


        // copy all data from sql server
        $passPoNo = [];
        foreach ($sqlsrvDataPoHeader as $data) {
            $passPoNo[] = $data->po_no;

            PO_Header::updateOrCreate(
                // find the po_no
                [
                    'po_no' => $data->po_no,
                    'supplier_code' => $data->supplier_code
                ],
                // update data
                [
                    'supplier_name' => $data->supplier_name,
                    'po_date' => $data->po_date,
                    'po_year' => $data->po_year,
                    'po_period' => $data->po_period,
                    'po_status' => $data->po_status,
                    'reference_1' => $data->reference_1,
                    'reference_2' => $data->reference_2,
                    'attn_name' => $data->attn_name,
                    'po_currency' => $data->po_currency,
                    'po_type_desc' => $data->po_type_desc,
                    'pr_no' => $data->pr_no,
                    'planned_receipt_date' => $data->planned_receipt_date,
                    'payment_term' => $data->payment_term,
                    'po_origin' => $data->po_origin,
                    'po_revision_no' => $data->po_revision_no,
                    'po_revision_date' => $data->po_revision_date,
                ]
            );
        }

        // return po_no

        return $passPoNo;
    }
}
