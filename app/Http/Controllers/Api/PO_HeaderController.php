<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PO_HeaderResource;
use Carbon\Carbon;

class PO_HeaderController
{
    // To get PO Header data based supplier_code
    public function index($sp_code)
    {
        // Eager load the 'poDetail' relationship
        $data_po = PO_Header::where('supplier_code', $sp_code)
            ->orderBy('po_date', 'desc')
            ->whereNotIn('po_status', ['Closed', 'closed', 'close', 'Cancelled', 'cancelled', 'cancel'])
            ->with('poDetail')->get();

        // Check if user available
        if (!$data_po) {
            return response()->json([
                'status' => 'error',
                'message' => 'User Not Found'
            ], 404);
        }

        // Check if data empty
        if ($data_po->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'PO Header data not found / empty / all PO data is Closed',
                'data' => []
            ], 404);
        }

        // If data isn't empty
        return response()->json([
            'status' => true,
            'message' => 'Success Display List PO Header',
            'data' => PO_HeaderResource::collection($data_po)
        ], 200);
    }

    // Test to get all po header data
    public function indexAll()
    {
        // Eager load the 'podetail' relationship
        $data_po = PO_Header::with('poDetail')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Display List PO Header Successfully',
            'data' => PO_HeaderResource::collection($data_po)
        ], 200);
    }

    // For update response column in po header
    public function update(Request $request, $po_no)
    {
        $po_header = PO_Header::with('poDetail')->find($po_no);

        // Check if PO header not found
        if (!$po_header) {
            return response()->json([
                'status' => 'error',
                'message' => 'PO Header Not Found'
            ], 404);
        }

        // Rules data request
        $rules = [
            'response' => 'required|string|max:25',
        ];

        // Rules based on response value
        switch ($request->response) {
            // Value Accepted
            case 'Accepted':
                // No rules return
                break;

            // Value Declined
            case 'Declined':
                // dd($request->input('reason'));
                $rules['reason'] = 'required|string|max:255';
                break;

                default:
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid response value.'
                ], 400);
            }

        // Message if data request error or invalid
        $messages = [
            'response.required' => 'The response field is required.',
            'response.string' => 'The response must be a string.',
            'response.max' => 'The response cannot be longer than 25 characters.',
            'reason.required' => 'The reason field is required.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason cannot be longer than 255 characters.',
        ];

        // Validator to check the rules isn't violated
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validator fail
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validate Fail',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update coloumn based of value requested
        // If Accepted
        if ($request->response == "Accepted") {
            $po_header->update([
                'response' => $request->input('response'),
                'accept_at' => Carbon::now()->format('Y-m-d H:i')
            ]);
        }
        // If Decline
        elseif ($request->response == "Declined") {
            // dd($request->input('reason'));
            $po_header->update([
                'response' => $request->input('response'),
                'reason' => $request->input('reason'),
                'decline_at' => Carbon::now()->format('Y-m-d H:i')
            ]);
        }

        // Return respond
        return response()->json([
            'status' => 'success',
            'message' => 'PO Edited Successfully',
            'data' => new PO_HeaderResource($po_header)
        ], 200);
    }
}
