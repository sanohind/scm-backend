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
        // Get data with eager loading of any relationships
        $data_dndetail = DNDetail::all(); // Adjust if you have relationships to load

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN Detail',
            'data' => DNDetailResource::collection($data_dndetail)
        ]);
    }

    // Show edit form DNDetail
    public function edit($dn_detail_no)
    {
        // Find the record by id
        $data_edit = DNDetail::findOrFail($dn_detail_no);
        return new DNDetailResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, $dn_detail_no)
    {
        // Find the record by id
        $dn_detail = DNDetail::findOrFail($dn_detail_no);

        // Validate the request data
        $data = $request->validate([
            'dn_detail_no' => 'required|string|max:25',
            'qty_confirm' => 'required|integer',
            // Add other fields as necessary
        ]);

        // Update the record with the validated data
        $dn_detail->update($data);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Merubah Status ' . $dn_detail->qty_confirm . '',
            'data' => new DNDetailResource($dn_detail)
        ]);
    }
}
