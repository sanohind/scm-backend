<?php

namespace App\Http\Controllers\Api\V1\Syncronization;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnDetailErp;
use App\Models\DeliveryNote\DnHeader;
use App\Models\DeliveryNote\DnHeaderErp;
use App\Models\PurchaseOrder\PoDetail;
use App\Models\PurchaseOrder\PoDetailErp;
use App\Models\PurchaseOrder\PoHeader;
use App\Models\PurchaseOrder\PoHeaderErp;
use App\Models\Users\BusinessPartnerErp;
use App\Models\Users\BusinessPartner;
use Illuminate\Http\Request;

class SyncManualController extends Controller
{
    // Sync database based of request user (Manual)
    // with job
    // function syncManual(Request $request)
    // {

    //     // validate data
    //     $request->validate([
    //         'month' => 'required',
    //         'year' => 'required'
    //     ]);

    //     // Declare variable month and year for passing to job
    //     $month = $request->input('month');
    //     $year = $request->input('year');

    //     // Dispatch job
    //     SyncManualDatabaseJob::dispatch($month,$year);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Start Sync Data... '
    //     ],200);
    // }

    // without job
    public function syncManual(Request $request)
    {

        // validate data
        $request->validate([
            'month' => 'required',
            'year' => 'required',
        ]);

        // Declare variable month and year for passing to job
        $month = $request->input('month');
        $year = $request->input('year');

        // logic
        // Sync Logic
        set_time_limit(0);
        /*


                partner



        */
        // get data
        $sqlsrvDataPartner = BusinessPartnerErp::where('bp_role_desc', 'LIKE', '%Supplier%')
            ->where('contry', 'IDN')
            ->get();

        // copy all data from sql server
        foreach ($sqlsrvDataPartner as $data) {
            BusinessPartner::updateOrCreate(
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
        // $requestedYear = $this->year;
        // $requestedMonth = $this->month;
        // dd($requestedYear,$requestedMonth);

        $sqlsrvDataPoHeader = PoHeaderErp::where('po_period', $month)
            ->where('po_year', $year)
            ->where('po_type_desc', 'PO LOCAL')
            ->get();

        // copy all data from sql server
        $passPoNo = [];
        foreach ($sqlsrvDataPoHeader as $data) {
            $passPoNo[] = $data->po_no;

            PoHeader::updateOrCreate(
                // find the po_no
                [
                    'po_no' => $data->po_no,
                    'supplier_code' => $data->supplier_code,
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
            $sqlsrvDataPoDetail = PoDetailErp::where('po_no', $data)->get();

            // copy all data from sql server
            foreach ($sqlsrvDataPoDetail as $data) {
                PoDetail::updateOrCreate(
                    [
                        'po_no' => $data->po_no,
                        'po_line' => $data->po_line,
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
            $sqlsrvDataDnHeader = DnHeaderErp::where('po_no', $data)->get();
            // copy all data from sql server
            foreach ($sqlsrvDataDnHeader as $data) {
                $passDnNo[] = $data->no_dn;

                DnHeader::updateOrCreate(
                    [
                        'no_dn' => $data->no_dn,
                        'po_no' => $data->po_no,
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
            $sqlsrvDataDnDetail = DnDetailErp::where('no_dn', $data)->get();
            // copy all data from sql server
            foreach ($sqlsrvDataDnDetail as $data) {
                DnDetail::updateOrCreate(
                    [
                        'no_dn' => $data->no_dn,
                        'dn_line' => $data->dn_line,
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

        return response()->json([
            'status' => true,
            'message' => 'Sync Data Successfully',
        ], 200);
    }
}
