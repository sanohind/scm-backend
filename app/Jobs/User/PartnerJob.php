<?php

namespace App\Jobs\User;

use App\Models\User\Partner;
use App\Models\User\PartnerLocal;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PartnerJob implements ShouldQueue
{
    use Queueable;

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
    public function handle(): void
    {
        // get all data from sql server
        $sqlsrvData = Partner::All();

        // copy all data from sql server
        foreach ($sqlsrvData as $data) {
            PartnerLocal::updateOrCreate([
                'bp_code' => $data->bp_code,
                'bp_name' => $data->bp_name,
                'bp_status_desc' => $data->bp_status_desc,
                'bp_currency' => $data->bp_currency,
                'country' => $data->country,
                'adr_line_1' => $data->adr_line_1,
                'adr_line_2' => $data->adr_line_2,
                'adr_line_3' => $data->adr_line_3,
                'adr_line_4' => $data->adr_line_4,
                'bp_phone' => $data->bp_phone,
                'bp_fax' => $data->bp_fax,
            ]);
        }

        // return response()->json(['message' => 'Data business_partner successfuly copied']);
    }
}
