<?php

namespace App\Http\Controllers;

use App\Models\DN_Detail;
use Illuminate\Http\Request;
use App\Http\Resources\DN_DetailResource;

class DN_DetailController extends Controller
{
    // View list data DNDetail
    public function index()
    {
        // Get data with eager loading of any relationships
        $data_dndetail = DN_Detail::all(); // Adjust if you have relationships to load

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List DN Detail',
            'data' => DN_DetailResource::collection($data_dndetail)
        ]);
    }

    // Show edit form DNDetail
    public function edit($dn_detail_no)
    {
        // Find the record by id
        $data_edit = DN_Detail::findOrFail($dn_detail_no);
        return new DN_DetailResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, $dn_detail_no)
    {
        // Find the record by id
        $dn_detail = DN_Detail::findOrFail($dn_detail_no);

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
            'data' => new DN_DetailResource($dn_detail)
        ]);
    }
}
