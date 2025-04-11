<?php

namespace App\Jobs\Syncronization;

use App\Trait\ErrorLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\Syncronization\SyncBusinessPartnerData;

class SyncBusinessPartnerJob implements ShouldQueue
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
    public function handle(SyncBusinessPartnerData $syncBusinessPartnerData): void
    {
        try {
            $syncBusinessPartnerData->syncBusinessPartner();
        } catch (\Throwable $th) {
            $this->syncError(
                'Sync Business Partner Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $this->job->getJobId()
            );
        }
    }
}
