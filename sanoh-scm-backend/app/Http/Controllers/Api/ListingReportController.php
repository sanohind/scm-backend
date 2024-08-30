<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ListingReport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ListingReportResource;
use Carbon\Carbon;

class ListingReportController extends Controller
{
    // View list data Listing Report
    public function index()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_listingreport = ListingReport::with('listingreport')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success Display Listing Report',
            'data' => ListingReportResource::collection($data_listingreport)
        ], 200);
    }

    // Store data user to database
    public function store(Request $request)
    {
        //add datetime
        $request->merge(['upload_at' => Carbon::now()]);

        $rules =[
            'bp_code' => 'required|string|max:25',
            'date' => 'required|date',
            'file' => 'required|string|max:255',
            'upload_at' => 'required'
        ];
        // Data input validation
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create data
        $data_create = ListingReport::create($validator->validated());

        // Return value
        return response()->json([
            'status' => true,
            'message' => 'Success Add Report '.$data_create->file."",
            'data' => new ListingReportResource($data_create)
        ], 201);
    }
}
