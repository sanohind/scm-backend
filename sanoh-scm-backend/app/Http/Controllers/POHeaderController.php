<?php

namespace App\Http\Controllers;

use App\Models\POHeader;
use Illuminate\Http\Request;
use App\Http\Resources\POHeaderResource;

class POHeaderController extends Controller
{
    // View list data POHeader
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_po = POHeader::with('poheader')->get();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menampilkan List PO Header',
            'data' => POHeaderResource::collection($data_po)
        ]);
    }

    //Show edit form POHeader
    public function edit($po_header)
    {
        //variable $user to store the id of data
        $data_edit = POHeader::findOrFail($po_header);
        return new POHeaderResource($data_edit);
    }

    // Update data to database
    public function update(Request $request, POHeader $po_header)
    {
        //
        $data_edit = POHeader::findOrFail($po_header);

        // Validate the request data
        $data = $request->validate ([
            'po_no' => 'required|string|max:25'.$po_header,
            'response' => 'required|string|max:25',
            // Add other fields as necessary
        ]);

        // Update the user with the validated data
        $data_update = POHeader::update([$data]);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Merubah Status \"'.$data_update->response."\"",
            'data' => new POHeaderResource($data_update)
        ]);

    }
}
