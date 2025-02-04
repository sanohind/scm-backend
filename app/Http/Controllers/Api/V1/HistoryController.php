<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\DeliveryNote\DnHistoryViewResource;
use App\Http\Resources\PurchaseOrder\PoHistoryViewResource;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController
{
    // this controller is for get the data that needed for history
    // PO History
    public function poHeaderHistory(Request $request)
    {
        $check = Auth::user()->role;
        if ($check == 5 || $check == 6) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 9) {
            // dd($request);
            $user = $request->bp_code;
        }

        //get data api to view
        $data_po = PoHeader::with('partner', 'poDetail')
            ->where('supplier_code', $user)
            ->whereIn('po_status', ['Closed', 'closed', 'close', 'Cancelled', 'cancelled', 'cancel'])
            ->get();

        // dd($data_po);
        return response()->json([
            'success' => true,
            'message' => 'Display List PO History Successfully',
            'data' => PoHistoryViewResource::collection($data_po),
        ]);
    }

    // DN History
    public function dnHeaderHistory(Request $request)
    {
        $check = Auth::user()->role;
        if ($check == 5 || $check == 6 || $check == 7 || $check == 8) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 3 || $check == 4 || $check == 9) {
            // dd($request);
            $user = $request->bp_code;
        }
        //get data api to view
        $data_dn = DnHeader::with('poHeader', 'dnDetail')
            ->where('supplier_code', $user)
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
