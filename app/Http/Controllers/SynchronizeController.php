<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor\SubcontItemConnectionErp;
use App\Models\Subcontractor\SubcontItemErp;
use App\Service\Syncronization\SyncBusinessPartnerData;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncDeliveryNoteData;
use App\Service\Syncronization\SyncPurchaseOrderData;
use App\Service\Syncronization\SyncSubcontItemData;
use Carbon\Carbon;
use App\Models\Partner;
use App\Models\PartnerLocal;
use App\Models\DeliveryNote\DN_Detail;
use App\Models\DeliveryNote\DN_Header;
use App\Models\PurchaseOrder\PO_Detail;
use App\Models\PurchaseOrder\PO_Header;
use App\Models\DeliveryNote\DN_Detail_ERP;
use App\Models\DeliveryNote\DN_Header_ERP;
use App\Models\PurchaseOrder\PO_Detail_ERP;
use App\Models\PurchaseOrder\PO_Header_ERP;



class SynchronizeController
{
    // function for copy the data from sql server to mysql
    public function synctes()
    {
        set_time_limit(0);
        /**
         *  get item subcont erp
         */
        $sqlsrvDataSubcontItem = SubcontItemConnectionErp::where('item_group', 'WBL1')->get();
        foreach ($sqlsrvDataSubcontItem as $data) {
            SubcontItemErp::updateOrCreate(
                // find the item
                ['item' => $data->item],

                //update data
                [
                    'description' => $data->description,
                    'item_group' => $data->item_group,
                    'group_desc' => $data->group_desc,
                    'material' => $data->material,
                    'old_item' => $data->old_item,
                    'unit' => $data->unit,
                    'div_code' => $data->div_code,
                    'divisi' => $data->divisi,
                    'model' => $data->model,
                    ]
            );
        }

        /*


                partner



        */
        // get data
        $sqlsrvDataPartner = Partner::where('bp_role_desc', 'LIKE','%Supplier%')
        ->where('contry', 'IDN')
        ->get();

        // copy all data from sql server
        foreach ($sqlsrvDataPartner as $data) {
            PartnerLocal::updateOrCreate(
                // find the bp_code
                ['bp_code' => $data->bp_code],
                //update data
                [
                    'bp_name' => $data->bp_name,
                    'bp_status_desc' => $data->bp_status_desc,
                    'bp_role' => $data->bp_role,
                    'bp_role_desc' => $data->bp_role_desc,
                    'bp_currency' => $data->bp_currency,
                    'country' => $data->contry,
                    'adr_line_1' => $data->adr_line_1,
                    'adr_line_2' => $data->adr_line_2,
                    'adr_line_3' => $data->adr_line_3,
                    'adr_line_4' => $data->adr_line_4,
                    'bp_phone' => $data->bp_phone,
                    'bp_fax' => $data->bp_fax,
                ]
            );
        }

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

        /*

                po detail


        */
        foreach ($passPoNo as $data) {
            $sqlsrvDataPoDetail = PO_Detail_ERP::where('po_no', $data)->get();

            // copy all data from sql server
            foreach ($sqlsrvDataPoDetail as $data) {
                PO_Detail::updateOrCreate(
                    [
                        'po_no' => $data->po_no,
                        'po_line' => $data->po_line
                    ],
                    [
                        'po_sequence' => $data->po_sequence,
                        'item_code' => $data->item_code,
                        'code_item_type' => $data->code_item_type,
                        'bp_part_no' => $data->bp_part_no,
                        'bp_part_name' => $data->bp_part_name,
                        'item_desc_a' => $data->item_desc_a,
                        'item_desc_b' => $data->item_desc_b,
                        'planned_receipt_date' => $data->planned_receipt_date,
                        'po_qty' => $data->po_qty,
                        'receipt_qty' => $data->receipt_qty,
                        'invoice_qty' => $data->invoice_qty,
                        'purchase_unit' => $data->purchase_unit,
                        'price' => $data->price,
                        'amount' => $data->amount,
                    ]
                );
            }
        }

        /*

                dn header



        */
        // dd($passPoNo);
        $passDnNo = [];
        foreach ($passPoNo as $data) {
            $sqlsrvDataDnHeader = DN_Header_ERP::where('po_no', $data)->get();
            // copy all data from sql server
            foreach ($sqlsrvDataDnHeader as $data) {
                $passDnNo[] = $data->no_dn;

                DN_Header::updateOrCreate(
                    [
                        'no_dn' => $data->no_dn,
                        'po_no' => $data->po_no
                    ],
                    [
                        'supplier_code' => $data->supplier_code,
                        'supplier_name' => $data->supplier_name,
                        'dn_created_date' => $data->dn_create_date,
                        'dn_year' => $data->dn_year,
                        'dn_period' => $data->dn_period,
                        'plan_delivery_date' => $data->plan_delivery_date,
                        'plan_delivery_time' => $data->plan_delivery_time,
                        'status_desc' => $data->status_desc,
                        'packing_slip' => $data->packing_slip,
                    ]
                );
            }
        }

        /*


                dn detail


        */
        foreach ($passDnNo as $data) {
            $sqlsrvDataDnDetail = DN_Detail_ERP::where('no_dn', $data)->get();
            // copy all data from sql server
            foreach ($sqlsrvDataDnDetail as $data) {
                DN_Detail::updateOrCreate(
                    [
                        'no_dn' => $data->no_dn,
                        'dn_line' => $data->dn_line
                    ],
                    [
                        'order_origin' => $data->order_origin,
                        'plan_delivery_date' => $data->plan_delivery_date,
                        'plan_delivery_time' => $data->plan_delivery_time,
                        'actual_receipt_date' => $data->actual_receipt_date,
                        'actual_receipt_time' => $data->actual_receipt_time,
                        'no_order' => $data->no_order,
                        'order_set' => $data->order_set,
                        'order_line' => $data->order_line,
                        'order_seq' => $data->order_seq,
                        'part_no' => $data->part_no,
                        'supplier_item_no' => $data->supplier_item_no,
                        'item_desc_a' => $data->item_desc_a,
                        'item_desc_b' => $data->item_desc_b,
                        'lot_number' => $data->lot_number,
                        'dn_qty' => $data->dn_qty,
                        'receipt_qty' => $data->receipt_qty,
                        'dn_unit' => $data->dn_unit,
                        'dn_snp' => $data->dn_snp,
                        'reference' => $data->reference,
                        'status_desc' => $data->status_desc,
                    ]
                );
            }
        }

        return response()->json(['message' => 'Sync Data successfuly ']);
    }

    public function __construct(
        protected SyncBusinessPartnerData $syncBusinessPartnerData,
        protected SyncPurchaseOrderData $syncPurchaseOrderData,
        protected SyncDeliveryNoteData $syncDeliveryNoteData,
        protected SyncSubcontItemData $syncSubcontItemData,
        protected SyncDeleteData $syncDeleteData,
    ) {}

    public function sync(){
        try {
            set_time_limit(0);
            // sync data
            $this->syncBusinessPartnerData->syncBussinessPartner(); // Business Partner

            $this->syncSubcontItemData->syncSubcontItem(); // Subcont Item

            $purchaseOrder = $this->syncPurchaseOrderData->syncPurchaseOrder(); // Purchase Order *note: must return array


            if (!empty($purchaseOrder)) {
                $this->syncDeliveryNoteData->syncDeliveryNote($purchaseOrder); // Delivery Note

                // delete data

                $this->syncDeleteData->deletePo(); // Delete Purchase Order

                $this->syncDeleteData->deleteDn(); // Delete Delivery Note

            }

            return response()->json([
                "message" => "Sync Data Successfuly",
            ]);

        } catch (\Throwable $th) {
            // throw $th;
            \Log::error("$th");
        }
    }
}
