<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Psr7\Header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DN_Header;
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
        ->where('response', [null])
        ->count();

        $data_po_in_proccess = PO_Header::where('supplier_code', $sp_code)
        ->whereIn('po_status', ['In Process', 'in process', 'In Progress'])
        ->whereIn('response', ['Accepted', 'accepted'])
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
     * Get the po and dn data from the last year.
     */
    public function getYearlyData()
    {
        // Calculate the start date as the first day of the current month one year ago
        $startDate = now()->subYear()->startOfMonth();

        // Calculate the end date as the last day of the current month
        $endDate = now()->endOfMonth();

        // Generate an array of months within the date range
        $months = [];
        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $months[] = $date->format('Y-m');
        }

        // Get the PO data that is Closed within the last year
        $po_data_closed = PO_Header::where('po_status', 'Closed')
            ->whereBetween('po_date', [$startDate, $endDate])
            ->get();

        // Get the PO data that is Cancelled within the last year
        $po_data_canceled = PO_Header::where('po_status', 'Cancelled')
            ->whereBetween('po_date', [$startDate, $endDate])
            ->get();

        // Get the DN data that is Confirmed within the last year
        $dn_data_confirmed = DN_Header::where('status_desc', 'Confirmed')
            ->whereBetween('dn_created_date', [$startDate, $endDate])
            ->get();

        // Get the DN data that is Cancelled within the last year
        $dn_data_canceled = DN_Header::where('status_desc', 'Cancelled')
            ->whereBetween('dn_created_date', [$startDate, $endDate])
            ->get();

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
        $dn_cancelled_counts = $groupDataByMonth($dn_data_canceled, 'dn_created_date');

        // Prepare the final data arrays, ensuring each month is represented
        $po_closed_final = [];
        $po_cancelled_final = [];
        $dn_confirmed_final = [];
        $dn_cancelled_final = [];

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
            $dn_cancelled_final[] = [
                'month' => $month,
                'count' => $dn_cancelled_counts->get($month, 0),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Yearly Data Retrieved Successfully',
            'data' => [
                'po_closed' => $po_closed_final,
                'po_cancelled' => $po_cancelled_final,
                'dn_confirmed' => $dn_confirmed_final,
                'dn_cancelled' => $dn_cancelled_final,
            ],
        ]);
    }

    /**
     * Get the count of active tokens for all roles.
     */
    public function dashboard()
    {
        // Calculate the timestamp for one hour ago
        $oneHourAgo = now()->subHour();

        // Get the count of tokens created within the last hour
        $active_tokens_count = PersonalAccessToken::where('created_at', '>=', $oneHourAgo)
        ->count();

        // Get the total count of users
        $total_users_count = User::count();

        // Get the count of active users where status is 1
        $active_users_count = User::where('status', 1)->count();

        // Get the count of deactive users where status is 0
        $deactive_users_count = User::where('status', 0)->count();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard Data Retrieved Successfully',
            'data' => [
                'active_tokens'   => $active_tokens_count,
                'total_users'     => $total_users_count,
                'active_users'    => $active_users_count,
                'deactive_users'  => $deactive_users_count,
            ]
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
}
