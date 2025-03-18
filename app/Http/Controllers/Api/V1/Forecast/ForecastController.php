<?php

namespace App\Http\Controllers\Api\V1\Forecast;

use App\Http\Requests\Forecast\StoreForecastRequest;
use App\Http\Resources\Forecast\ForecastResource;
use App\Models\Forecast\Forecast;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ForecastController
{
    /**
     * Summary of indexSupplier
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function indexSupplier(): JsonResponse
    {
        $user = Auth::user()->bp_code;

        $data = Forecast::with('user')
            ->where('bp_code', $user)
            ->orderBy('upload_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success display forecast',
            'data' => ForecastResource::collection($data),
        ]);
    }

    /**
     * Summary of indexPurchasing
     *
     * @param  mixed  $bp_code
     */
    public function indexPurchasing($bp_code): JsonResponse
    {

        $data = Forecast::with('user')
            ->where('bp_code', $bp_code)
            ->orderBy('upload_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Success display forecast',
            'data' => ForecastResource::collection($data),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreForecastRequest $request)
    {
        $request->validated();

        // Get the authenticated user and their role
        $user = auth()->user();
        $role_id = $user->role;

        // Determine the bp_code to use
        if ($role_id == 9 || $role_id == 2) {
            // Superuser can specify any bp_code from the request
            $bp_code = $request->bp_code;
        } else {
            // Other users must use their own bp_code
            $bp_code = $user->bp_code;
        }

        // Change file name and file path to storage
        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        Storage::disk('public')->putFileAs('forecast', $file, $fileName);
        $filePath = "public/forecast/$fileName";

        // Upload time value declaration
        $time = Carbon::now();

        // Create data
        $data_create = Forecast::create([
            'bp_code' => $bp_code,
            'description' => $request->description,
            'file' => Storage::url($filePath),
            'upload_at' => $time,
        ]);

        // Return value
        return response()->json([
            'status' => true,
            'message' => 'Add Forecast Successfully',
            'data' => new ForecastResource($data_create),
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Forecast $forecast)
    {
        $forecast->delete();

        return response()->json([
            'status' => true,
            'message' => 'Delete forecast successfully',
        ], 201);
    }

    // Get file by filename
    public function getFile($filename)
    {
        $filePath = "forecast/$filename";
        // dd(Storage::disk('public')->exists($filePath));
        // Check if the file exists in the storage
        if (Storage::disk('public')->exists($filePath)) {
            // Return the file as a download
            return Storage::disk('public')->download($filePath);
        }

        // If the file doesn't exist, return a 404 response
        return response()->json(['message' => 'File not found'], 404);
    }
}
