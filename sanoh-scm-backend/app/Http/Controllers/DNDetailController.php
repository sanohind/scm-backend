<?php

namespace App\Http\Controllers;

use App\Models\DNDetail;
use Illuminate\Http\Request;
use App\Http\Resources\DNDetailResource;

class DNDetailController extends Controller
{
    // View list data DNDetail
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_dndetail = DNDetail::with('dndetail')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN Detail',
            'data' => DNDetailResource::collection($data_dndetail)
        ]);
    }

    //Show edit form POHeader
    public function edit($dn_detail)
    {
        //variable $user to store the id of data
        $data_edit = DNDetail::findOrFail($dn_detail);
        return new DNDetailResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, DNDetail $dn_detail)
    {
        //
        $data_edit = DNDetail::findOrFail($dn_detail);

        // Validate the request data
        $data = $request->validate ([
            'dn_detail_no' => 'required|integer'.$dn_detail,
            'qty_confirm' => 'required|integer',
            // Add other fields as necessary
        ]);

        // Update the user with the validated data
        $data_update = DNDetail::update([$data]);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Merubah Status \"'.$data_update->qty_confirm."\"",
            'data' => new DNDetailResource($data_update)
        ]);

    }
}
