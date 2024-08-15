<?php

namespace App\Http\Controllers;

use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Http\Resources\PO_HeaderResource;

class PO_HeaderController extends Controller
{
    // View list data POHeader
    public function index()
    {
        // Ensure the relationship is correct or remove if not used
        $data_po = PO_Header::with('poheader')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO Header',
            'data' => PO_HeaderResource::collection($data_po)
        ]);
    }

    // Show edit form POHeader
    public function edit($po_no)
    {
        // Ensure the ID exists in the database
        $data_edit = PO_Header::findOrFail($po_no);
        return new PO_HeaderResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, $po_no)
    {
        $po_header = PO_Header::findOrFail($po_no);
        // Validate the request data
        $data = $request->validate([
            'response' => 'required|string|max:25',
            // Add other fields as necessary
        ]);

        // Update the model with the validated data
        $po_header->update($data);

        // Return updated model data
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Merubah Status ' . $po_header->response,
            'data' => new PO_HeaderResource($po_header)
        ]);
    }
}
