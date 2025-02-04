<?php

namespace App\Http\Controllers\Api\V1\DeliveryNote;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeliveryNote\DnHeaderResource;
use App\Models\DeliveryNote\DnHeader;
use Illuminate\Support\Facades\Auth;

class DnHeaderController extends Controller
{
    public function index()
    {
        $sp_code = Auth::user()->bp_code;

        // dd($sp_code);

        // Eager load the 'podetail' relationship
        $data_po = DnHeader::with('poHeader', 'dnDetail')
            ->orderBy('plan_delivery_date', 'desc')
            ->where('supplier_code', $sp_code)
            ->whereNotIn(
                'status_desc', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed'])
            ->whereHas('poHeader', function ($query) {

                $query->whereNotIn('po_status', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed']);
            })

            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Display List DN Header Successfully',
            'data' => DnHeaderResource::collection($data_po),
        ], 200);
    }

    public function indexWarehouse($sp_code)
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        $data_dnheader = DnHeader::with('poHeader', 'dnDetail')
            ->whereNotIn('status_desc', ['Closed', 'closed', 'close', 'Confirmed', 'confirmed'])
            ->orderBy('plan_delivery_date', 'desc')
            ->where('supplier_code', $sp_code)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN Header Successfully',
            'data' => DnHeaderResource::collection($data_dnheader),
        ], 200);
    }

    //test
    // View list data DNHeader
    public function indexAll()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_dnheader = DnHeader::with('poHeader')->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN Header Successfully',
            'data' => DnHeaderResource::collection($data_dnheader),
        ], 200);
    }
}
