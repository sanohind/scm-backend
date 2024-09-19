<?php

namespace App\Http\Controllers;

use App\Jobs\SyncManualDatabaseJob;
use Illuminate\Http\Request;

class SynchronizeManualController extends Controller
{
    // Sync database based of request user (Manual)
    function syncManual(Request $request)
    {

        // validate data
        $request->validate([
            'month' => 'required',
            'year' => 'required'
        ]);

        // Declare variable month and year for passing to job
        $month = $request->input('month');
        $year = $request->input('year');

        // Dispatch job
        SyncManualDatabaseJob::dispatch($month,$year);

        return response()->json([
            'status' => true,
            'message' => 'Start Sync Data... '
        ],200);
    }
}
