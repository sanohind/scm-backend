<?php

namespace App\Jobs\Syncronization;

use Carbon\Carbon;
use App\Trait\ErrorLog;
use App\Models\DeliveryNote\DnDetail;
use App\Models\DeliveryNote\DnHeader;
use App\Models\DeliveryNote\DnDetailErp;
use App\Models\DeliveryNote\DnHeaderErp;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\PurchaseOrder\PoHeaderErp;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\Syncronization\SyncDeleteData;

class SyncDeliveryNoteJob implements ShouldQueue
{
    use ErrorLog, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SyncDeleteData $syncDeleteData): void
    {
        try {
            // Initialize year and period
            $actualYear = Carbon::now()->year;
            $actualPeriod = Carbon::now()->month;
            $oneYearsBefore = Carbon::now()->subYears(2)->year;
            $twoMonthBefore = Carbon::now()->subMonths(2)->month;

            if (Carbon::now()->format('H:i') >= '00:00' && Carbon::now()->format('H:i') <= '00:10') {
                // Get Purchase Order from range two year ago till now
                $poNumber = PoHeaderErp::whereBetween('po_year', [$oneYearsBefore, $actualYear])
                    ->where('po_type_desc', 'PO LOCAL')
                    ->pluck('po_no')->toArray();
            } else {
                // Get Purchase Order from range two month ago till now on this year
                $poNumber = PoHeaderErp::whereBetween('po_period', [$twoMonthBefore, $actualPeriod])
                    ->where('po_year', $actualYear)
                    ->where('po_type_desc', 'PO LOCAL')
                    ->pluck('po_no')->toArray();
            }

            // Delivery Note Header
            $dnNumber = [];
            foreach ($poNumber as $data) {
                $sqlsrvDataDnHeader = DnHeaderErp::where('po_no', $data)->get();
                // copy all data from sql server
                foreach ($sqlsrvDataDnHeader as $data) {
                    $dnNumber[] = $data->no_dn;

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

            // Delivery Note Detail
            foreach ($dnNumber as $data) {
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

            $syncDeleteData->deleteDn();
        } catch (\Throwable $th) {
            $this->syncError(
                'Sync Delivery Note Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $this->job->getJobId()
            );
        }
    }
}
