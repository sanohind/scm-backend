<?php

namespace App\Service\Syncronization;

use App\Models\Subcontractor\SubcontItemErp;
use App\Models\Subcontractor\SubcontItemConnectionErp;

class SyncSubcontItemData
{
    public function syncSubcontItem()
    {
        // Get item subcont
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
    }
}
