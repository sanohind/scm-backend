<?php

namespace App\Service\Syncronization;


use App\Models\Users\BusinessPartner;
use App\Models\Users\BusinessPartnerErp;

class SyncBusinessPartnerData
{
    /**
     * Sync data Business Partner erp and local
     * @return void
     */
    public function syncBusinessPartner()
    {
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
    }
}
