<?php

namespace App\Jobs\Syncronization;

use Carbon\Carbon;
use App\Trait\ErrorLog;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\PurchaseOrder\PoHeaderErp;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\Syncronization\SyncDeleteData;
use App\Service\Syncronization\SyncDeliveryNoteData;

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
    public function handle(SyncDeliveryNoteData $syncDeliveryNoteData, SyncDeleteData $syncDeleteData): void
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

            $syncDeliveryNoteData->syncDeliveryNote($poNumber);
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
