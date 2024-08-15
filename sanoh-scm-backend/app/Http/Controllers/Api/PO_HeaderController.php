<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PO_HeaderResource;

class PO_HeaderController extends Controller
{
    public function index()
    {
        $data_po = PO_Header::all();
        return response()->json([
            'status' => 'success',
            'message' => 'PO berhasil diambil',
            'data' => PO_HeaderResource::collection($data_po)
        ], 200);
    }

    // Function Store
    /* public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'po_no' => 'required|string|max:255',
            'status' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $po_header = POHeaderModel::create($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'PO berhasil dibuat',
            'data' => new POHeaderResource($po_header)
        ], 201);
    } */

    public function update(Request $request, $po_no)
    {
        $po_header = PO_Header::find($po_no);

        if (!$po_header) {
            return response()->json([
                'status' => 'error',
                'message' => 'PO tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'response' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $po_header->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'PO berhasil diperbarui',
            'data' => new PO_HeaderResource($po_header)
        ], 200);
    }
}
