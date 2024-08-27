<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DN_HeaderResource;

class DN_HeaderController extends Controller
{
    // View list data DNHeader
    public function index()
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
