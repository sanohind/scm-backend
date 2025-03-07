<?php

namespace App\Http\Controllers\Api\V1\DeliveryNote;

use App\Trait\ResponseApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DnHeader;
use App\Http\Resources\DeliveryNote\DnHeaderResource;

class DnHeaderController extends Controller
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    /**
     * Get list dn header user
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListDnUser()
    {
        $bpCode = Auth::user()->bp_code;

        $dnHeaderData = DnHeader::with('poHeader', 'dnDetail')
            ->orderBy('plan_delivery_date', 'desc')
            ->where('supplier_code', $bpCode)
            ->whereNotIn(
                'status_desc',
                ['Closed', 'closed', 'close', 'Confirmed', 'confirmed']
            )
            ->whereHas('poHeader', function ($query) {
                $query->whereNotIn('po_status', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed']);
            })
            ->get();

        return $this->returnCustomResponseApi(
            'success',
            'Display List DN Header Successfully',
            DnHeaderResource::collection($dnHeaderData),
            200
        );
    }

    /**
     * Get list selected supplier dn header
     * @param mixed $bpCode
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListDnHeaderSelected($bpCode)
    {
        $dnHeaderData = DnHeader::with('poHeader', 'dnDetail')
            ->where('supplier_code', $bpCode)
            ->whereNotIn('status_desc', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed'])
            ->orderBy('plan_delivery_date', 'desc')
            ->get();

        return $this->returnResponseApi(
            true,
            'Display List DN Header Successfully',
            DnHeaderResource::collection($dnHeaderData),
            200
        );
    }

    /**
     * Get all dn header
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexAll()
    {
        $dnHeaderData = DnHeader::with('poHeader')->get();

        return $this->returnResponseApi(true,'Display List DN Header Successfully',DnHeaderResource::collection($dnHeaderData),200);
    }
}
