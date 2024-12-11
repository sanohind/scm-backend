<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Psr7\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DN_Header;
use App\Models\PurchaseOrder\PO_Header;
use App\Http\Resources\DashboardViewResource;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Laravel\Sanctum\PersonalAccessToken;

class DashboardController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get bp_code by auth
        $sp_code = Auth::user()->bp_code;

        // get data po
        $data_po_active = PO_Header::where('supplier_code', $sp_code)
        ->whereIn('po_status', ['Sent', 'sent'])
        ->count();

        $data_po_in_proccess = PO_Header::where('supplier_code', $sp_code)
        ->whereIn('po_status', ['In Process', 'in process', 'In Progress'])
        ->count();

        // get data dn
        $data_dn_open = DN_Header::where('supplier_code', $sp_code)
        ->whereIn('status_desc', ['Open', 'open'])
        ->count();

        $data_dn_confirmed = DN_Header::where('supplier_code', $sp_code)
        ->whereIn('status_desc', ['Confirmed', 'confirmed'])
        ->count();

        // dd($data_po_in_proccess);

        return response()->json([
            'success' => true,
            'message' => 'Display Dashboard Successfully',
            'data' => [
                'po_active' => $data_po_active,
                'po_in_progress' => $data_po_in_proccess,
                'dn_active' => $data_dn_open,
                'dn_confirmed'=> $data_dn_confirmed
            ]
        ]);
    }

    /**
     * Get the count of active tokens for all roles.
     */
    public function onlineUser()
    {
        // Calculate the timestamp for one hour ago
        $oneHourAgo = now()->subHour();

        // Get the count of tokens created within the last hour
        $active_tokens_count = PersonalAccessToken::where('created_at', '>=', $oneHourAgo)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Active Tokens Retrieved Successfully',
            'data' => [
                'active_tokens' => $active_tokens_count
            ]
        ]);
    }
}
