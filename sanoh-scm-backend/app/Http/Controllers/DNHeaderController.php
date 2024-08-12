<?php

namespace App\Http\Controllers;

use App\Models\DNHeader;
use Illuminate\Http\Request;
use App\Http\Resources\DNHeaderResource;

class DNHeaderController extends Controller
{
    // View list data DNHeader
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_dnheader = DNHeader::with('dnheader')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN Header',
            'data' => DNHeaderResource::collection($data_dnheader)
        ]);
    }
}
