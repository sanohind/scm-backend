<?php

namespace App\Http\Controllers\Api;


use App\Models\DN_Detail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DN_DetailResource;
use App\Models\DN_Header;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class DN_DetailController extends Controller
{
    // View list data DNDetail
    public function index($no_dn)
    {
        $data_dndetail = DN_Detail::where('no_dn', $no_dn)->get();

        if ($data_dndetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'DN details not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Display List DN Detail',
            'data' => DN_DetailResource::collection($data_dndetail)
        ], 200);
    }

    //test
    public function indexAll()
    {
        // Fetch PO details based on the provided po_no
        $data_podetail = DN_Detail::with('dnHeader')->get();

        if ($data_podetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'PO details not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Display List PO Detail',
            'data' => DN_DetailResource::collection($data_podetail)
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
    public function update(Request $request)
    {
        try {
            // Dump the request data to inspect the incoming data
            // dd($request->all()); // Uncomment this for debugging if needed

            // Validate request data
            $data = $request->validate([
                'no_dn' => 'required|string',
                'updates' => 'required|array',
                'updates.*.dn_detail_no' => 'required|integer|exists:dn_detail,dn_detail_no',
                'updates.*.qty_confirm' => 'required|integer|min:0',
            ]);

            // Dump the validated data to verify
            // dd($data); // Uncomment this for debugging if needed

            // Update DN_Header with current timestamp
            $update_header = DN_Header::where('no_dn', $data['no_dn'])->first();

            if ($update_header) {
                $time = Carbon::now()->format('Y-m-d H:i:s'); // Correct datetime format
                $update_header->update([
                    'confirm_update_at' => $time,
                ]);
            } else {
                return response()->json(['error' => 'Header not found for: ' . $data['no_dn']], 404);
            }

            // Update DN_Detail records
            foreach ($data['updates'] as $update) {
                $record = DN_Detail::where('dn_detail_no', $update['dn_detail_no'])->first();

                if ($record) {
                    $record->update([
                        'qty_confirm' => $update['qty_confirm'],
                    ]);
                } else {
                    // Handle the case where the record is not found
                    return response()->json(['error' => 'DN Detail not found for: ' . $update['dn_detail_no']], 404);
                }
            }

            return response()->json(['message' => 'DN details updated successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $errors = $e->errors();
            return response()->json(['error' => 'Validation failed', 'details' => $errors], 422);

        } catch (\Exception $e) {
            // Handle other types of exceptions
            return response()->json(['error' => 'An unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }
}
