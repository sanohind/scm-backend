<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ListingReport;
use App\Http\Resources\ListingReportResource;

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
            'message' => 'Berhasil Menampilkan Listing Report',
            'data' => ListingReportResource::collection($data_listingreport)
        ]);
    }

    // Store data user to database
    public function store(Request $request)
    {
        // Data input validation
        $data = $request->validate([
            'bp_code' => 'required|string|max:25',
            'date' => 'required|date',
            'file' => 'required|string|max:255',
        ]);

        // Create data
        $data_create = ListingReport::create([$data]);

        // Return value
        return response()->json([
            'success' => true,
            'message' => 'Berhasil Menambahkan Report \"'.$data_create->file."\"",
            'data' => ListingReportResource::collection($data_create)
        ]);
    }
}
