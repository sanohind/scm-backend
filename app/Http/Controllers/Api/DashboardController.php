<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Psr7\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DN_Header;
use App\Models\DeliveryNote\DN_Detail;
use App\Models\PurchaseOrder\PO_Header;
use App\Http\Resources\DashboardViewResource;
use App\Models\User;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use Laravel\Sanctum\PersonalAccessToken;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
        ->whereIn('po_status', ['Open', 'open'])
        ->whereNull('response')
        ->count();

        $data_po_in_proccess = PO_Header::where('supplier_code', $sp_code)
        ->whereIn('po_status', ['In Process', 'in process', 'In Progress'])
        ->whereIn('response', ['Accepted', 'accepted'])
        ->count();

        // get data dn
        $data_dn_open = DN_Header::where('supplier_code', $sp_code)
        ->whereIn('status_desc', ['Open', 'open'])
        ->where('confirm_update_at', '=', null)
        ->count();

        $data_dn_confirmed = DN_Header::where('supplier_code', $sp_code)
        ->whereIn('status_desc', ['Open', 'open'])
        ->where('confirm_update_at', '!=', null)
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
     * Get the po and dn data from the last year.
     */
    public function getYearlyData()
    {
        // Get the authenticated user
        $user = auth()->user();
        $sp_code = $user->bp_code;
        $role_id = $user->role_id;

        // Calculate the start and end dates
        $startDate = now()->subYear()->startOfMonth();
        $endDate = now()->endOfMonth();

        // Generate an array of months within the date range
        $months = [];
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $months[] = $date->format('Y-m');
        }

        // Initialize data collections
        $po_data_closed = collect();
        $po_data_canceled = collect();
        $dn_data_confirmed = collect();
        $dn_data_overtime = collect();

        // Include PO data for roles 5 and 6
        if (in_array($role_id, [5, 6])) {
            $po_data_closed = PO_Header::where('supplier_code', $sp_code)
                ->where('po_status', 'Closed')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->get();

            $po_data_canceled = PO_Header::where('supplier_code', $sp_code)
                ->where('po_status', 'Cancelled')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->get();
        }

        // Include DN data for roles 5, 6, 7, and 8
        if (in_array($role_id, [5, 6, 7, 8])) {
            $dn_data_confirmed = DN_Header::where('supplier_code', $sp_code)
                ->where('status_desc', 'Confirmed')
                ->whereBetween('dn_created_date', [$startDate, $endDate])
                ->get();

            $dn_data_overtime = DN_Header::where('supplier_code', $sp_code)
                ->whereBetween('dn_created_date', [$startDate, $endDate])
                ->whereHas('dnDetail', function ($query) {
                    $query->whereColumn('actual_receipt_date', '>', 'plan_delivery_date');
                })
                ->get();
        }

        // Function to group data by month and count occurrences
        $groupDataByMonth = function ($data, $dateField) {
            return $data->groupBy(function ($item) use ($dateField) {
                return Carbon::parse($item->$dateField)->format('Y-m');
            })->map(function ($items) {
                return $items->count();
            });
        };

        // Group and count the data
        $po_closed_counts = $groupDataByMonth($po_data_closed, 'po_date');
        $po_cancelled_counts = $groupDataByMonth($po_data_canceled, 'po_date');
        $dn_confirmed_counts = $groupDataByMonth($dn_data_confirmed, 'dn_created_date');
        $dn_overtime_counts = $groupDataByMonth($dn_data_overtime, 'dn_created_date');

        // Prepare the final data arrays
        $po_closed_final = [];
        $po_cancelled_final = [];
        $dn_confirmed_final = [];
        $dn_overtime_final = [];

        foreach ($months as $month) {
            $po_closed_final[] = [
                'month' => $month,
                'count' => $po_closed_counts->get($month, 0),
            ];
            $po_cancelled_final[] = [
                'month' => $month,
                'count' => $po_cancelled_counts->get($month, 0),
            ];
            $dn_confirmed_final[] = [
                'month' => $month,
                'count' => $dn_confirmed_counts->get($month, 0),
            ];
            $dn_overtime_final[] = [
                'month' => $month,
                'count' => $dn_overtime_counts->get($month, 0),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Yearly Data Retrieved Successfully',
            'data' => [
                'po_closed'     => $po_closed_final,
                'po_cancelled'  => $po_cancelled_final,
                'dn_confirmed'  => $dn_confirmed_final,
                'dn_overtime'   => $dn_overtime_final,
            ],
        ]);
    }

    /**
     * Get the count of active tokens for all roles.
     */
    public function dashboard()
    {
        // Get the authenticated user
        $user = auth()->user();
        $role_id = $user->role_id;

        // Initialize data array
        $data = [];

        // Calculate the timestamp for one hour ago
        $oneHourAgo = now()->subHour();

        // Get the count of tokens created within the last hour
        $data['active_tokens'] = PersonalAccessToken::where('created_at', '>=', $oneHourAgo)->count();

        // Get the total count of users
        $data['total_users'] = User::count();

        // Get the count of active users where status is 1
        $data['active_users'] = User::where('status', 1)->count();

        // Get the count of deactivated users where status is 0
        $data['deactive_users'] = User::where('status', 0)->count();

        // Include PO statistics for roles 5 and 6
        if (in_array($role_id, [5, 6])) {
            $data['po_total'] = PO_Header::where('supplier_code', $user->bp_code)->count();
            $data['po_closed'] = PO_Header::where('supplier_code', $user->bp_code)
                ->where('po_status', 'Closed')
                ->count();
            $data['po_cancelled'] = PO_Header::where('supplier_code', $user->bp_code)
                ->where('po_status', 'Cancelled')
                ->count();
        }

        // Include DN statistics for roles 5, 6, 7, and 8
        if (in_array($role_id, [5, 6, 7, 8])) {
            $data['dn_total'] = DN_Header::where('supplier_code', $user->bp_code)->count();
            $data['dn_confirmed'] = DN_Header::where('supplier_code', $user->bp_code)
                ->where('status_desc', 'Confirmed')
                ->count();
            $data['dn_open'] = DN_Header::where('supplier_code', $user->bp_code)
                ->where('status_desc', 'Open')
                ->count();
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard Data Retrieved Successfully',
            'data' => $data,
        ]);
    }

    /**
     * Get the detail of active tokens for all roles.
     */
    public function detailActiveUser()
    {
        // Calculate the timestamp for one hour ago
        $oneHourAgo = now()->subHour();

        // Get the active tokens created within the last hour
        $active_tokens = PersonalAccessToken::where('created_at', '>=', $oneHourAgo)
            ->with('tokenable') // Ensure we load the related user
            ->get();

        // Map the active tokens to the required details
        $active_token_details = $active_tokens->map(function ($token) {
            return [
                'username'     => $token->tokenable->username,
                'name'         => $token->tokenable->name,
                'role'         => $token->tokenable->role,
                'last_login'   => $token->created_at->format('d/m/Y - H:i:s'),
                'last_update'  => $token->last_used_at ? $token->last_used_at->format('d/m/Y - H:i:s') : null,
                'id'           => $token->id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Active Token Details Retrieved Successfully',
            'data' => $active_token_details
        ]);
    }

    public function logoutByTokenId(Request $request)
    {
        // Validate the request to ensure 'token_id' is provided
        $request->validate([
            'token_id' => 'required|integer'
        ]);

        // Find the token by ID
        $token = PersonalAccessToken::find($request->token_id);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found'
            ], 404);
        }

        // Revoke the specific token
        $token->delete();

        // Logout success response
        return response()->json([
            'success' => true,
            'message' => 'Token successfully revoked'
        ], 200);
    }

    public function monthlyLoginData()
    {
        // Calculate the start date as the current date and time one month ago
        $startDate = now()->subMonth();
        // End date is the current date and time
        $endDate = now();

        // Get the tokens created within the past month from today
        $monthly_tokens = PersonalAccessToken::whereBetween('created_at', [$startDate, $endDate])
            ->with('tokenable') // Load the related user
            ->get();

        // Group the tokens by tokenable_id and count the logins for each user
        $monthly_login_data = $monthly_tokens->groupBy('tokenable_id')->map(function ($tokens, $tokenable_id) {
            $tokenable = $tokens->first()->tokenable;
            return [
                'username' => $tokenable ? $tokenable->username : 'Unknown',
                'login_count' => $tokens->count(),
            ];
        });

        // Calculate the date for the last 24 hours
        $last24Hours = now()->subDay();

        // Get the tokens created within the last 24 hours
        $daily_tokens = PersonalAccessToken::where('created_at', '>=', $last24Hours)
            ->with('tokenable') // Load the related user
            ->get();

        // Group the tokens by tokenable_id and count the logins for each user
        $daily_login_data = $daily_tokens->groupBy('tokenable_id')->map(function ($tokens, $tokenable_id) {
            $tokenable = $tokens->first()->tokenable;
            return [
                'username' => $tokenable ? $tokenable->username : 'Unknown',
                'login_count' => $tokens->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Login Data Retrieved Successfully',
            'data' => [
                'monthly' => $monthly_login_data->values(),
                'daily' => $daily_login_data->values(),
            ],
        ]);
    }

    public function calenderEvents()
    {
        // Get the authenticated user
        $user = auth()->user();
        $sp_code = $user->bp_code;
        $role_id = $user->role_id;

        // Initialize the events collection
        $events = collect();

        // For roles 5 and 6, include PO events
        if (in_array($role_id, [5, 6])) {
            // Get PO data with the required fields
            $po_events = PO_Header::where('supplier_code', $sp_code)
                ->get(['po_no', 'po_date', 'planned_receipt_date'])
                ->map(function ($po) {
                    return [
                        'title' => $po->po_no,
                        'start' => $po->po_date . ' 07:00',
                        'end'   => $po->planned_receipt_date,
                        'type'  => 'PO',
                    ];
                });
            $events = $events->merge($po_events);
        }

        // For roles 5, 6, 7, and 8, include DN events
        if (in_array($role_id, [5, 6, 7, 8])) {
            // Get DN data with the required fields
            $dn_events = DN_Header::where('supplier_code', $sp_code)
                ->get(['no_dn', 'dn_created_date', 'plan_delivery_date', 'plan_delivery_time', 'confirm_update_at', 'status_desc'])
                ->map(function ($dn) {
                    // Determine the type based on the condition
                    if (is_null($dn->confirm_update_at) && $dn->status_desc === 'Open') {
                        $type = 'DN';
                    } else {
                        $type = 'DN History';
                    }

                    return [
                        'title' => $dn->no_dn,
                        'start' => $dn->dn_created_date . ' ' . $dn->plan_delivery_time,
                        'end'   => $dn->plan_delivery_date,
                        'type'  => $type,
                    ];
                });
            $events = $events->merge($dn_events);
        }

        // Return the events as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Calendar Events Retrieved Successfully',
            'data'    => $events,
        ]);
    }
}
