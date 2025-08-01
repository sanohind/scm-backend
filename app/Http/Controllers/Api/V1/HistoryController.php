<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\DeliveryNote\DnHistoryViewResource;
use App\Http\Resources\PurchaseOrder\PoHistoryViewResource;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use App\Service\User\BusinessPartnerUnifiedService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController
{
    /**
     * List of service used
     */
    public function __construct(
        protected BusinessPartnerUnifiedService $businessPartnerUnifiedService
    ) {
    }

    // this controller is for get the data that needed for history
    // PO History (unified search)
    public function poHeaderHistory(Request $request)
    {
        $check = Auth::user()->role;
        if ($check == 5 || $check == 6) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 9) {
            // dd($request);
            $user = $request->bp_code;
        }

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($user);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$user];
        }

        //get data api to view
        $data_po = PoHeader::with('partner', 'poDetail')
            ->whereIn('supplier_code', $supplierCodes)
            ->whereIn('po_status', ['Closed', 'closed', 'close', 'Cancelled', 'cancelled', 'cancel'])
            ->get();

        // dd($data_po);
        return response()->json([
            'success' => true,
            'message' => 'Display List PO History Successfully',
            'data' => PoHistoryViewResource::collection($data_po),
        ]);
    }

    // DN History (unified search)
    public function dnHeaderHistory(Request $request)
    {
        $check = Auth::user()->role;
        if ($check == 5 || $check == 6 || $check == 7 || $check == 8) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 3 || $check == 4 || $check == 9) {
            // dd($request);
            $user = $request->bp_code;
        }

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($user);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$user];
        }

        //get data api to view
        $data_dn = DnHeader::with('poHeader', 'dnDetail')
            ->whereIn('supplier_code', $supplierCodes)
            ->orderBy('plan_delivery_date', 'desc')
            ->whereIn('status_desc', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed'])
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN History Successfully',
            'data' => DnHistoryViewResource::collection($data_dn),
        ]);
    }
}
