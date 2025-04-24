<?php

namespace App\Service\Syncronization;

use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnDetailErp;
use App\Models\DeliveryNote\DnHeader;
use App\Models\DeliveryNote\DnHeaderErp;
use Log;

class SyncDeliveryNoteData
{
    /**
     * Sync Delivery Note Header and Detail
     * @param array $poNumber
     * @return void
     */
    public function syncDeliveryNote(array $poNo)
    {
        $collect1 = collect();
        foreach (array_chunk($poNo, 2000) as $chunk) {
            $result = DnHeaderErp::whereIn('po_no', $chunk)->get();
            $collect1 = $collect1->merge($result);
        }
        Log::info("DN Header Count: " . $collect1->count());

        $dnNo = $collect1->pluck('no_dn')->toArray();
        $collect2 = collect();
        foreach (array_chunk($dnNo, 2000) as $chunk2) {
            $result2 = DnDetailErp::whereIn('no_dn', $chunk2)->get();
            $collect2 = $collect2->merge($result2);
        }
        Log::info("DN Detail Count: " . $collect2->count());

        foreach ($collect1 as $data) {
            DnHeader::updateOrCreate(
                [
                    'no_dn' => $data['no_dn'],
                    'po_no' => $data['po_no'],
                ],
                [
                    'supplier_code' => $data['supplier_code'],
                    'supplier_name' => $data['supplier_name'],
                    'dn_created_date' => $data['dn_create_date'],
                    'dn_year' => $data['dn_year'],
                    'dn_period' => $data['dn_period'],
                    'plan_delivery_date' => $data['plan_delivery_date'],
                    'plan_delivery_time' => $data['plan_delivery_time'],
                    'status_desc' => $data['status_desc'],
                    'packing_slip' => $data['packing_slip'],
                ]
            );
        }

        foreach ($collect2 as $data) {
            DnDetail::updateOrCreate(
                [
                    'no_dn' => $data['no_dn'],
                    'dn_line' => $data['dn_line'],
                ],
                [
                    'order_origin' => $data['order_origin'],
                    'plan_delivery_date' => $data['plan_delivery_date'],
                    'plan_delivery_time' => $data['plan_delivery_time'],
                    'actual_receipt_date' => $data['actual_receipt_date'],
                    'actual_receipt_time' => $data['actual_receipt_time'],
                    'no_order' => $data['no_order'],
                    'order_set' => $data['order_set'],
                    'order_line' => $data['order_line'],
                    'order_seq' => $data['order_seq'],
                    'part_no' => $data['part_no'],
                    'supplier_item_no' => $data['supplier_item_no'],
                    'item_desc_a' => $data['item_desc_a'],
                    'item_desc_b' => $data['item_desc_b'],
                    'lot_number' => $data['lot_number'],
                    'dn_qty' => $data['dn_qty'],
                    'receipt_qty' => $data['receipt_qty'],
                    'dn_unit' => $data['dn_unit'],
                    'dn_snp' => $data['dn_snp'],
                    'reference' => $data['reference'],
                    'status_desc' => $data['status_desc'],
                ]
            );
        }


        // $dnNumber = [];
        // foreach ($poNumber as $data) {
        //     $sqlsrvDataDnHeader = DnHeaderErp::where('po_no', $data)->get();
        //     // copy all data from sql server
        //     foreach ($sqlsrvDataDnHeader as $data) {
        //         $dnNumber[] = $data->no_dn;

        //         DnHeader::updateOrCreate(
        //             [
        //                 'no_dn' => $data->no_dn,
        //                 'po_no' => $data->po_no,
        //             ],
        //             [
        //                 'supplier_code' => $data->supplier_code,
        //                 'supplier_name' => $data->supplier_name,
        //                 'dn_created_date' => $data->dn_create_date,
        //                 'dn_year' => $data->dn_year,
        //                 'dn_period' => $data->dn_period,
        //                 'plan_delivery_date' => $data->plan_delivery_date,
        //                 'plan_delivery_time' => $data->plan_delivery_time,
        //                 'status_desc' => $data->status_desc,
        //                 'packing_slip' => $data->packing_slip,
        //             ]
        //         );
        //     }
        // }

        // // Delivery Note Deltail
        // foreach ($dnNumber as $data) {
        //     $sqlsrvDataDnDetail = DnDetailErp::where('no_dn', $data)->get();
        //     // copy all data from sql server
        //     foreach ($sqlsrvDataDnDetail as $data) {
        //         DnDetail::updateOrCreate(
        //             [
        //                 'no_dn' => $data->no_dn,
        //                 'dn_line' => $data->dn_line,
        //             ],
        //             [
        //                 'order_origin' => $data->order_origin,
        //                 'plan_delivery_date' => $data->plan_delivery_date,
        //                 'plan_delivery_time' => $data->plan_delivery_time,
        //                 'actual_receipt_date' => $data->actual_receipt_date,
        //                 'actual_receipt_time' => $data->actual_receipt_time,
        //                 'no_order' => $data->no_order,
        //                 'order_set' => $data->order_set,
        //                 'order_line' => $data->order_line,
        //                 'order_seq' => $data->order_seq,
        //                 'part_no' => $data->part_no,
        //                 'supplier_item_no' => $data->supplier_item_no,
        //                 'item_desc_a' => $data->item_desc_a,
        //                 'item_desc_b' => $data->item_desc_b,
        //                 'lot_number' => $data->lot_number,
        //                 'dn_qty' => $data->dn_qty,
        //                 'receipt_qty' => $data->receipt_qty,
        //                 'dn_unit' => $data->dn_unit,
        //                 'dn_snp' => $data->dn_snp,
        //                 'reference' => $data->reference,
        //                 'status_desc' => $data->status_desc,
        //             ]
        //         );
        //     }
        // }
    }
}
