<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;
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
    // Add datetime
    $request->merge(['upload_at' => Carbon::now()]);

    $rules = [
        'bp_code' => 'required|string|max:25',
        'date' => 'required|date',
        'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,xls|max:10048', // Acceptable file formats
        'upload_at' => 'required'
    ];

    // Data input validation
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    // Manually move the file to the storage path
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();

        // Define the storage path
        $storagePath = storage_path('app/public/listing_report');

        // Ensure the directory exists
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        // Move the file manually
        $file->move($storagePath, $filename);

        // Get the relative path to store in the database
        $filePath = 'listing_report/' . $filename;

        // Merge the file path into the request
        $request->merge(['file' => $filePath]);
    }

    // Create data
    $data_create = ListingReport::create($validator->validated());

    // Return value
    return response()->json([
        'status' => true,
        'message' => 'Success Add Report ' . $data_create->file,
        'data' => new ListingReportResource($data_create)
    ], 201);
}
}
