<?php

namespace App\Http\Controllers\Api\V1\DeliveryNote;

use App\Trait\ResponseApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DnHeader;
use App\Http\Resources\DeliveryNote\DnHeaderResource;
use App\Service\User\BusinessPartnerUnifiedService;

class DnHeaderController extends Controller
{
    /**
     * -------TRAIT---------
     * Mandatory:
     * 1. ResponseApi = Response api should use ResponseApi trait template
     */
    use ResponseApi;

    /**
     * List of service used
     */
    public function __construct(
        protected BusinessPartnerUnifiedService $businessPartnerUnifiedService
    ) {
    }

    /**
     * Get list dn header user (unified search)
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListDnUser()
    {
        $bpCode = Auth::user()->bp_code;

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($bpCode);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$bpCode];
        }

        $dnHeaderData = DnHeader::with('poHeader', 'dnDetail')
            ->orderBy('plan_delivery_date', 'desc')
            ->whereIn('supplier_code', $supplierCodes)
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
     * Get list selected supplier dn header (unified search)
     * @param mixed $bpCode
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function getListDnHeaderSelected($bpCode)
    {
        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($bpCode);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$bpCode];
        }

        $dnHeaderData = DnHeader::with('poHeader', 'dnDetail')
            ->whereIn('supplier_code', $supplierCodes)
            ->whereNotIn('status_desc', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed'])
            ->whereHas('poHeader', function ($query) {
                $query->whereNotIn('po_status', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed']);
            })
            ->orderBy('plan_delivery_date', 'desc')
            ->get();

        return $this->returnCustomResponseApi(
            true,
            'Display List DN Header Successfully',
            DnHeaderResource::collection($dnHeaderData),
            200,
            null,
            'success'
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
