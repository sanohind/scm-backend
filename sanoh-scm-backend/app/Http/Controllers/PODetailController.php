<?php

namespace App\Http\Controllers;

use App\Models\PODetail;
use Illuminate\Http\Request;
use App\Http\Resources\PODetailResource;

class PODetailController extends Controller
{
    // View list data PODetail
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_podetail = PODetail::with('podetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO Detail',
            'data' => PODetailResource::collection($data_podetail)
        ]);
    }
}
