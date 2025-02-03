<?php

namespace App\Service\Subcontractor;

use App\Models\Subcontractor\SubcontItem;
use App\Models\Subcontractor\SubcontItemErp;

class SubcontCreateItem
{
    public function __construct(protected SubcontCreateStock $subcontCreateStock) {}

    /**
     * create new data item subcont
     *
     * @param  mixed  $data
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function createItemSubcont($data)
    {
        foreach ($data['data'] as $dataItem) {

            // Get part_name and old_part_name
            $getPartName = SubcontItemErp::select('description', 'old_item')
                ->where('item', $dataItem['part_number'])
                ->first();

            // Store logic
            $createItem = SubcontItem::create([
                'bp_code' => $dataItem['bp_code'],
                'item_code' => $dataItem['part_number'],
                'item_name' => $getPartName['description'] ?? null,
                'item_old_name' => $getPartName['old_item'] ?? null,
                'status' => '1',
            ]);

            // Check stock record availability
            $this->subcontCreateStock->createAndCheckStock($dataItem['part_number'], $createItem->sub_item_id);
        }

        // Response
        return response()->json([
            'status' => true,
            'message' => 'Data Successfuly Stored',
        ], 200);
    }
}
