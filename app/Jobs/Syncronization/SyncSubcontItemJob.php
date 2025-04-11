<?php

namespace App\Jobs\Syncronization;

use App\Trait\ErrorLog;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\Syncronization\SyncSubcontItemData;

class SyncSubcontItemJob implements ShouldQueue
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
    public function handle(SyncSubcontItemData $syncSubcontItemData): void
    {
        try {
            $syncSubcontItemData->syncSubcontItem();
        } catch (\Throwable $th) {
            $this->syncError(
                'Sync Subcont Item Failed',
                $th->getMessage(),
                $th->getFile(),
                $th->getLine(),
                $this->job->getJobId()
            );
        }
    }
}
