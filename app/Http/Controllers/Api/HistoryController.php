<?php

namespace App\Http\Controllers\Api;

use App\Models\DeliveryNote\DN_Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseOrder\PO_Header;
use App\Http\Resources\DeliveryNote\DN_HistoryViewResource;
use App\Http\Resources\PurchaseOrder\PO_HistoryViewResource;

class HistoryController
{
    // this controller is for get the data that needed for history
    // PO History
    public function poHeaderHistory(Request $request)
    {
        $check =Auth::user()->role;
        if ($check == 5 || $check == 6 || $check == 9) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2) {
            // dd($request);
            $user = $request->bp_code;
        }

        //get data api to view
        $data_po = PO_Header::with('partner','poDetail')
                            ->where('supplier_code', $user)
                            ->whereIn('po_status', ['Closed','closed','close','Cancelled','cancelled','cancel'])
                            ->get();

        // dd($data_po);
        return response()->json([
            'success' => true,
            'message' => 'Display List PO History Successfully',
            'data' => PO_HistoryViewResource::collection($data_po)
        ]);
    }

    // DN History
    public function dnHeaderHistory(Request $request)
    {
        $check =Auth::user()->role;
        if ($check == 5 || $check == 6 || $check == 7 || $check == 8 || $check == 9) {
            $user = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 3 || $check == 4) {
            // dd($request);
            $user = $request->bp_code;
        }
        //get data api to view
        $data_dn = DN_Header::with('poHeader','dnDetail')
        ->where('supplier_code', $user)
        ->orderBy('plan_delivery_date', 'desc')
        ->whereIn('status_desc', ['Closed','closed','close','Confirmed','confirmed'])
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN History Successfully',
            'data' => DN_HistoryViewResource::collection($data_dn)
        ]);
    }
}
