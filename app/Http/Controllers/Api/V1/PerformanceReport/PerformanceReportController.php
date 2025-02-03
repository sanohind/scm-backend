<?php

namespace App\Http\Controllers\Api\V1\PerformanceReport;

use App\Http\Controllers\Controller;
use App\Http\Resources\PerformanceReport\PerformanceReportResource;
use App\Models\PerformanceReport\PerformanceReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PerformanceReportController extends Controller
{
    // View list data Listing Report
    public function index(Request $bp_code)
    {
        $check = Auth::user()->role;
        if ($check == 5 || $check == 6 || $check == 7 || $check == 6 || $check == 8) {
            $bp_code = Auth::user()->bp_code;
        } elseif ($check == 2 || $check == 3 || $check == 4 || $check == 9) {
            // dd($request);
            $bp_code = $bp_code->bp_code;
        }
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_listingreport = PerformanceReport::with('listingreport')
            ->where('bp_code', $bp_code)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Success Display Listing Report',
            'data' => PerformanceReportResource::collection($data_listingreport),
        ], 200);
    }

    public function indexAll()
    {
        //get data api to view
        // Using eager loading request data to database for efficiency data
        //in case calling data relation
        $data_listingreport = PerformanceReport::with('listingreport')->get();

        return response()->json([
            'success' => true,
            'message' => 'Success Display Listing Report',
            'data' => PerformanceReportResource::collection($data_listingreport),
        ], 200);
    }

    // Store data user to database
    public function store(Request $request)
    {
        //validate the request data
        $request->validate([
            'bp_code' => 'required|string|max:25',
            'date' => 'required|date',
            'file' => 'required|mimes:pdf|max:5000',
        ], [
            'file.max' => 'The uploaded file exceeds the maximum allowed size of 5 MB.',
            'file.mimes' => 'The uploaded file must be a PDF.',
        ]);

        // Change file name and file path to storage
        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $filePath = $file->storeAs('public/listing_report', $fileName);

        //upload_at value declaration
        $time = Carbon::now();

        // Create data
        $data_create = PerformanceReport::updateOrCreate(
            [
                'date' => $request->input('date'),
            ],
            [
                'bp_code' => $request->input('bp_code'),
                'file' => Storage::url($filePath),
                'upload_at' => $time,
            ]
        );

        // Return value
        return response()->json([
            'status' => true,
            'message' => 'Add Performance Report Successfully '.$data_create->file,
            'data' => new PerformanceReportResource($data_create),
        ], 201);
    }

    // Get file by filename
    public function getFile($filename)
    {
        $filePath = 'public/listing_report/'.$filename;

        // Check if the file exists in the storage
        if (Storage::exists($filePath)) {
            // Return the file as a download
            return Storage::download($filePath);
        }

        // If the file doesn't exist, return a 404 response
        return response()->json(['message' => 'File not found'], 404);
    }
}
