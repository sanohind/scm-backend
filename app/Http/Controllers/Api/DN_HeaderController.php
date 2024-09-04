<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DN_HeaderResource;

class DN_HeaderController extends Controller
{
    public function index($po_no)
    {
        // Eager load the 'podetail' relationship
        $data_po = DN_Header::where('po_no', $po_no)->with('poHeader')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Success Display List PO Header',
            'data' => DN_HeaderResource::collection($data_po)
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
            'message' => 'Success Display List DN Header',
            'data' => DN_HeaderResource::collection($data_dnheader)
        ], 200);
    }
}
