<?php

namespace App\Http\Controllers\Api\V1\PurchaseOrder;

use App\Http\Resources\PurchaseOrder\PoDetailListResource;
use App\Trait\ResponseApi;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder\PoDetail;
use App\Http\Resources\PurchaseOrder\PoDetailResource;

class PoDetailController extends Controller
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    public function getListDetailPo($po_no)
    {
        $poDetailData = PoDetail::with('poHeader')
            ->where('po_no', $po_no)
            ->orderBy('planned_receipt_date', 'asc')
            ->get();

        if (!$poDetailData) {
            return $this->returnCustomResponseApi('error', 'PO Detail Not Found', null, 404);
        }

        return $this->returnResponseApi(
            true,
            'Display List PO Detail Successfully',
            new PoDetailListResource($poDetailData),
            200
        );
    }

    // Test function to get all data
    public function indexAll()
    {
        $poDetailData = PoDetail::with('poHeader')->get();

        if ($poDetailData->isEmpty()) {
            return $this->returnResponseApi(false, 'PO details not found', null, 404);
        }

        return $this->returnResponseApi(
            true,
            'Display List PO Detail Successfully',
            PoDetailResource::collection($poDetailData),
            200
        );
    }
}
