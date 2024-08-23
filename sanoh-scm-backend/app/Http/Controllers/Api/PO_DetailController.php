<?php

namespace App\Http\Controllers\Api;

use App\Models\PO_Detail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\PO_DetailResource;

class PO_DetailController extends Controller
{
    // View list data PODetail
    public function index($po_no)
    {
        // Fetch PO details based on the provided po_no
        $data_podetail = PO_Detail::where('po_no', $po_no)->get();

        if ($data_podetail->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'PO details not found',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Success Display List PO Detail',
            'data' => PO_DetailResource::collection($data_podetail)
        ], 200);
    }
}
