<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Listing_Report;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Listing_ReportResource;

class Listing_ReportController extends Controller
{
    // View list data Listing Report
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_listingreport = Listing_Report::with('listingreport')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success Display Listing Report',
            'data' => Listing_ReportResource::collection($data_listingreport)
        ], 200);
    }

    // Store data user to database
    public function store(Request $request)
    {
        // Data input validation
        $validator = Validator::make($request->all(),[
            'bp_code' => 'required|string|max:25',
            'date' => 'required|date',
            'file' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create data
        $data_create = Listing_Report::create($validator->validated());

        // Return value
        return response()->json([
            'status' => success,
            'message' => 'Success Add Report '.$data_create->file."",
            'data' => Listing_ReportResource::collection($data_create)
        ], 201);
    }
}
