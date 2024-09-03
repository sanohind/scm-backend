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
        //validate the request data
        $request->validate([
            'bp_code' => 'required|string|max:25',
            'date' => 'required|date',
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,xls|max:10048', // Acceptable file formats
        ]);

        // Change file name and file path to storage
        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('listing_report',$fileName);

        //upload_at value declaration
        $time = Carbon::now();

        // Create data
        $data_create = ListingReport::create([
            'bp_code' => $request->input('bp_code'),
            'date' => $request->input('date'),
            'file' => Storage::url($filePath),
            'upload_at' => $time,
        ]);

        // Return value
        return response()->json([
            'status' => true,
            'message' => 'Success Add Report ' . $data_create->file,
            'data' => new ListingReportResource($data_create)
        ], 201);
    }

    // Get file by filename
    public function getFile($filename)
    {
        $filePath = 'listing_report/' . $filename;

        if (Storage::exists($filePath)) {
            // Return the publicly accessible URL
            return response()->json([
                'status' => true,
                'url' => Storage::url($filePath)
            ], 200);
        }

        // If file doesn't exist, return a 404 response
        return response()->json(['message' => 'File not found'], 404);
    }

}
