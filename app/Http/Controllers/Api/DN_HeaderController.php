<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DN_HeaderResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class DN_HeaderController extends Controller
{
    public function index()
    {
        $sp_code = Auth::user()->bp_code;

        // dd($sp_code);

        // Eager load the 'podetail' relationship
        $data_po = DN_Header::with('poHeader','dnDetail')
        ->orderBy('plan_delivery_date', 'desc')
        ->where('supplier_code', $sp_code)
        ->whereNotIn('status_desc', ['Closed','closed','close','Confirmed','confirmed'])
        ->whereHas('poHeader', function ($query){
            $query->whereNotIn('po_status', ['Closed','closed','close','Confirmed','confirmed']);
        })
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Display List DN Header Successfully',
            'data' => DN_HeaderResource::collection($data_po)
        ], 200);
    }

    public function indexWarehouse($sp_code)
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        $data_dnheader = DN_Header::with('poHeader','dnDetail')
        ->whereNotIn('status_desc', ['Closed','closed','close','Confirmed','confirmed'])
        ->orderBy('plan_delivery_date', 'desc')
        ->where('supplier_code', $sp_code)
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN Header Successfully',
            'data' => DN_HeaderResource::collection($data_dnheader)
        ], 200);
    }

    //test
    // View list data DNHeader
    public function indexAll()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_dnheader = DN_Header::with('poHeader')->get();

        return response()->json([
            'success' => true,
            'message' => 'Display List DN Header Successfully',
            'data' => DN_HeaderResource::collection($data_dnheader)
        ], 200);
    }
}
