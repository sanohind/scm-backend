<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Detail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PO_DetailResource;

class PO_DetailController extends Controller
{
    // To get PO Detail data based supplier_code
    public function index($po_no)
    {
        // Eager load the 'poHeader' relationship
        $data_podetail = PO_Detail::where('po_no', $po_no)
        ->with('poHeader')
        ->get();

        // Check if PO Header available
        if (!$data_podetail) {
            return response()->json([
                'status' => 'error',
                'message' => 'PO Number Not Found'
            ], 404);
        }

        // Check if data empty
        if ($data_podetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'PO details not found / empty'
            ], 404);
        }

        // If data isn't empty
        return response()->json([
            'success' => true,
            'message' => 'Success Display List PO Detail',
            'data' => PO_DetailResource::collection($data_podetail)
        ], 200);
    }

    // Test function to get all data
    public function indexAll()
    {
        // Eager load the 'poHeader' relationship
        $data_podetail = PO_Detail::with('poHeader')->get();

         // Check if data empty
        if ($data_podetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'PO details not found',
                'data' => []
            ], 404);
        }

        // If data isn't empty
        return response()->json([
            'success' => true,
            'message' => 'Success Display List PO Detail',
            'data' => PO_DetailResource::collection($data_podetail)
        ], 200);
    }
}
