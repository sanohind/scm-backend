<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PO_HeaderResource;

class PO_HeaderController
{
    public function index($sp_code)
    {
        // Eager load the 'podetail' relationship
        $data_po = PO_Header::where('supplier_code', $sp_code)->with('poDetail')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Success Display List PO Header',
            'data' => PO_HeaderResource::collection($data_po)
        ], 200);
    }

    public function update(Request $request, $po_no)
    {
        $po_header = PO_Header::with('poDetail')->find($po_no);

        if (!$po_header) {
            return response()->json([
                'status' => 'error',
                'message' => 'PO Not Found'
            ], 404);
        }

        $rules = [
            'response' => 'required|string|max:25',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validate Fail',
                'errors' => $validator->errors()
            ], 422);
        }

        $po_header->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'PO Edited Success',
            'data' => new PO_HeaderResource($po_header)
        ], 200);
    }
}
