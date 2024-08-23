<?php

namespace App\Http\Controllers\Api;

use App\Models\DN_Detail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DN_DetailResource;

class DN_DetailController extends Controller
{
    // View list data DNDetail
    public function index($no_dn)
    {
        $data_dndetail = DN_Detail::where('no_dn', $no_dn)->get();

        if ($data_dndetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'DN details not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Display List DN Detail',
            'data' => DN_DetailResource::collection($data_dndetail)
        ], 200);
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

        if (!$dn_detail) {
            return response()->json([
                'status' => 'error',
                'message' => 'DN Detail Not Found'
            ], 404);
        }

        $rules = [
            'dn_detail_no' => 'required|string|max:25',
            'qty_confirm' => 'required|integer',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validate Fail',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update the record with the validated data
        $dn_detail->update($validator->validated());

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Success Edit Quantity ' . $dn_detail->qty_confirm . '',
            'data' => new DN_DetailResource($dn_detail)
        ], 200);
    }
}
