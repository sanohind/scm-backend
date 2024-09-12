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
        $data = $request->validate([
            'no_dn' => 'required|string',
            'updates' => 'required|array',
            'updates.*.dn_detail_no' => 'required|integer|exists:dn_detail,dn_detail_no',
            'updates.*.qty_confirm' => 'required|integer|min:0',
        ]);

        // update data DN_header column confirm_update_at
        $data_header= $data['no_dn'];

        $update_header = DN_Header::where('no_dn', $data_header)->first();

        if($update_header){
            $time = Carbon::now()->format('Ymd H:i');
            $update_header->update([
                'confirm_update_at' => $time
            ]);
        }

        // update multiple data DN_Detail column qty_confirm
        $data_detail = $data['updates'];

        foreach ($data_detail as $update) {
            // Find the record to update
            $record = DN_Detail::where('dn_detail_no', $update['dn_detail_no'])->with('dnHeader')->first();
            // Update the record
            $record->update([
                'qty_confirm' => $update['qty_confirm'],
            ]);

        }

        return response()->json(['message' => 'DN details updated successfully']);
    }
}
