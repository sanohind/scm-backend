<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Users\User;
use Illuminate\Http\Request;
use App\Models\Users\BusinessPartner;
use Illuminate\Support\Facades\Auth;
use App\Models\DeliveryNote\DnHeader;
use App\Models\PurchaseOrder\PoHeader;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\Subcontractor\SubcontTransaction;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Service\User\BusinessPartnerUnifiedService;

class DashboardController
{
    /**
     * List of service used
     */
    public function __construct(
        protected BusinessPartnerUnifiedService $businessPartnerUnifiedService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get bp_code by auth
        $sp_code = Auth::user()->bp_code;

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($sp_code);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$sp_code];
        }

        // get data po
        $data_po_active = PoHeader::whereIn('supplier_code', $supplierCodes)
            ->whereIn('po_status', ['Open', 'open'])
            ->whereNull('response')
            ->count();

        $data_po_in_proccess = PoHeader::whereIn('supplier_code', $supplierCodes)
            ->whereIn('po_status', ['In Process', 'in process', 'In Progress'])
            ->whereIn('response', ['Accepted', 'accepted'])
            ->count();

        // get data dn
        $data_dn_open = DnHeader::whereIn('supplier_code', $supplierCodes)
            ->whereIn('status_desc', ['Open', 'open'])
            ->where('confirm_update_at', '=', null)
            ->count();

        $data_dn_confirmed = DnHeader::whereIn('supplier_code', $supplierCodes)
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
                'dn_confirmed' => $data_dn_confirmed,
            ],
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
        $role_id = $user->role;

        // Get all related bp_codes (parent and children)
        $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($sp_code);
        $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

        // If no related codes found, use the original bp_code
        if (empty($supplierCodes)) {
            $supplierCodes = [$sp_code];
        }

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
        $po_data_accepted = collect();
        $po_data_declined = collect();
        $dn_data_confirmed = collect();
        $dn_data_overtime = collect();
        $dn_data_advance = collect(); // Added for advance data

        // Include PO data for roles 5 and 6
        if (in_array($role_id, [5, 6])) {
            $po_data_accepted = PoHeader::whereIn('supplier_code', $supplierCodes)
                ->where('response', 'Accepted')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->get();

            $po_data_declined = PoHeader::whereIn('supplier_code', $supplierCodes)
                ->where('response', 'Declined')
                ->whereBetween('po_date', [$startDate, $endDate])
                ->get();
        }

        // Include DN data for roles 5 and 6
        if (in_array($role_id, [5, 6])) {
            $dn_data_confirmed = DnHeader::whereIn('supplier_code', $supplierCodes)
                ->where('status_desc', 'Confirmed')
                ->whereBetween('dn_created_date', [$startDate, $endDate])
                ->get();

            $dn_data_overtime = DnHeader::whereIn('supplier_code', $supplierCodes)
                ->where('status_desc', 'Confirmed')
                ->where('plan_delivery_date', '<', 'dn_created_date')
                ->whereBetween('dn_created_date', [$startDate, $endDate])
                ->get();

            $dn_data_advance = DnHeader::whereIn('supplier_code', $supplierCodes)
                ->where('status_desc', 'Confirmed')
                ->where('plan_delivery_date', '>', 'dn_created_date')
                ->whereBetween('dn_created_date', [$startDate, $endDate])
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
        $po_accepted_counts = $groupDataByMonth($po_data_accepted, 'po_date');
        $po_declined_counts = $groupDataByMonth($po_data_declined, 'po_date');
        $dn_confirmed_counts = $groupDataByMonth($dn_data_confirmed, 'dn_created_date');
        $dn_overtime_counts = $groupDataByMonth($dn_data_overtime, 'dn_created_date');
        $dn_advance_counts = $groupDataByMonth($dn_data_advance, 'dn_created_date'); // Added for advance data

        // Prepare the final data arrays
        $po_accepted_final = [];
        $po_declined_final = [];
        $dn_confirmed_final = [];
        $dn_overtime_final = [];
        $dn_advance_final = []; // Added for advance data

        foreach ($months as $month) {
            $po_accepted_final[] = [
                'month' => $month,
                'count' => $po_accepted_counts->get($month, 0),
            ];
            $po_declined_final[] = [
                'month' => $month,
                'count' => $po_declined_counts->get($month, 0),
            ];
            $dn_confirmed_final[] = [
                'month' => $month,
                'count' => $dn_confirmed_counts->get($month, 0),
            ];
            $dn_overtime_final[] = [
                'month' => $month,
                'count' => $dn_overtime_counts->get($month, 0),
            ];
            $dn_advance_final[] = [
                'month' => $month,
                'count' => $dn_advance_counts->get($month, 0),
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Yearly Data Retrieved Successfully',
            'data' => [
                'po_accepted' => $po_accepted_final,
                'po_declined' => $po_declined_final,
                'dn_confirmed' => $dn_confirmed_final,
                'dn_overtime' => $dn_overtime_final,
                'dn_advance' => $dn_advance_final, // Included in response
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
        $role_id = $user->role;

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
            // Get all related bp_codes (parent and children)
            $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($user->bp_code);
            $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

            // If no related codes found, use the original bp_code
            if (empty($supplierCodes)) {
                $supplierCodes = [$user->bp_code];
            }

            $data['po_total'] = PoHeader::whereIn('supplier_code', $supplierCodes)->count();
            $data['po_closed'] = PoHeader::whereIn('supplier_code', $supplierCodes)
                ->where('po_status', 'Closed')
                ->count();
            $data['po_cancelled'] = PoHeader::whereIn('supplier_code', $supplierCodes)
                ->where('po_status', 'Cancelled')
                ->count();
        }

        // Include DN statistics for roles 5, 6, 7, and 8
        if (in_array($role_id, [5, 6, 7, 8])) {
            // Get all related bp_codes (parent and children)
            $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($user->bp_code);
            $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

            // If no related codes found, use the original bp_code
            if (empty($supplierCodes)) {
                $supplierCodes = [$user->bp_code];
            }

            $data['dn_total'] = DnHeader::whereIn('supplier_code', $supplierCodes)->count();
            $data['dn_confirmed'] = DnHeader::whereIn('supplier_code', $supplierCodes)
                ->where('status_desc', 'Confirmed')
                ->count();
            $data['dn_open'] = DnHeader::whereIn('supplier_code', $supplierCodes)
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
            ->whereNull('expires_at')
            ->get();

        // Map the active tokens to the required details
        $active_token_details = $active_tokens->map(function ($token) {
            return [
                'username' => $token->tokenable->username,
                'name' => $token->tokenable->name,
                'role' => $token->tokenable->role,
                'last_login' => $token->created_at->format('d/m/Y - H:i:s'),
                'last_update' => $token->last_used_at ? $token->last_used_at->format('d/m/Y - H:i:s') : null,
                'id' => $token->id,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Active Token Details Retrieved Successfully',
            'data' => $active_token_details,
        ]);
    }

    public function logoutByTokenId(Request $request)
    {
        // Validate the request to ensure 'token_id' is provided
        $request->validate([
            'token_id' => 'required|integer',
        ]);

        // Find the token by ID
        $token = PersonalAccessToken::find($request->token_id);

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token not found',
            ], 404);
        }

        // Revoke the specific token
        $token->delete();

        // Logout success response
        return response()->json([
            'success' => true,
            'message' => 'Token successfully revoked',
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
        $monthly_login_data = $monthly_tokens->groupBy('tokenable_id')->map(function ($tokens) {
            $tokenable = $tokens->sortByDesc('tokenable_type')->first()->tokenable;

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
        $daily_login_data = $daily_tokens->groupBy('tokenable_id')->map(function ($tokens) {
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
        $role_id = $user->role;

        // Initialize the events collection
        $events = collect();

        // Calculate date range: 30 days before today
        $startDate = now()->subDays(30)->startOfDay();
        $endDate = now()->endOfDay(); // Today

        // Check if the user is a superuser (Role 9)
        if ($role_id == 9) {
            // Superuser: Include PO events where po_date is within 30 days before today
            $po_events = PoHeader::whereBetween('po_date', [$startDate, $endDate])
                ->get(['po_no', 'po_date', 'planned_receipt_date', 'supplier_code'])
                ->map(function ($po) {
                    return [
                        'title' => $po->po_no,
                        'start' => $po->po_date.' 07:00',
                        'end' => $po->planned_receipt_date,
                        'type' => 'PO',
                        'bp_code' => $po->supplier_code, // Include bp_code
                    ];
                });
            $events = $events->merge($po_events);

            // Superuser: Include DN events where dn_created_date is within 30 days before today
            $dn_events = DnHeader::whereBetween('dn_created_date', [$startDate, $endDate])
                ->get([
                    'no_dn',
                    'dn_created_date',
                    'plan_delivery_date',
                    'plan_delivery_time',
                    'confirm_update_at',
                    'status_desc',
                    'supplier_code',
                ])
                ->map(function ($dn) {
                    // Determine the type based on the condition
                    $type = (is_null($dn->confirm_update_at) && $dn->status_desc === 'Open') ? 'DN' : 'DN History';

                    return [
                        'title' => $dn->no_dn,
                        'start' => $dn->dn_created_date.' '.$dn->plan_delivery_time,
                        'end' => $dn->plan_delivery_date,
                        'type' => $type,
                        'bp_code' => $dn->supplier_code, // Include bp_code
                    ];
                });
            $events = $events->merge($dn_events);
        } else {
            // Get all related bp_codes (parent and children)
            $relatedBpCodes = $this->businessPartnerUnifiedService->getRelatedBusinessPartners($sp_code);
            $supplierCodes = $relatedBpCodes->pluck('bp_code')->toArray();

            // If no related codes found, use the original bp_code
            if (empty($supplierCodes)) {
                $supplierCodes = [$sp_code];
            }

            // For roles 5 and 6, include PO events
            if (in_array($role_id, [5, 6])) {
                // Get PO data filtered by user's bp_code (unified)
                $po_events = PoHeader::whereIn('supplier_code', $supplierCodes)
                    ->get(['po_no', 'po_date', 'planned_receipt_date'])
                    ->map(function ($po) {
                        return [
                            'title' => $po->po_no,
                            'start' => $po->po_date.' 07:00',
                            'end' => $po->planned_receipt_date,
                            'type' => 'PO',
                        ];
                    });
                $events = $events->merge($po_events);
            }

            // For roles 5, 6, 7, and 8, include DN events
            if (in_array($role_id, [5, 6, 7, 8])) {
                // Get DN data filtered by user's bp_code (unified)
                $dn_events = DnHeader::whereIn('supplier_code', $supplierCodes)
                    ->get([
                        'no_dn',
                        'dn_created_date',
                        'plan_delivery_date',
                        'plan_delivery_time',
                        'confirm_update_at',
                        'status_desc',
                    ])
                    ->map(function ($dn) {
                        // Determine the type based on the condition
                        $type = (is_null($dn->confirm_update_at) && $dn->status_desc === 'Open') ? 'DN' : 'DN History';

                        return [
                            'title' => $dn->no_dn,
                            'start' => $dn->dn_created_date.' '.$dn->plan_delivery_time,
                            'end' => $dn->plan_delivery_date,
                            'type' => $type,
                        ];
                    });
                $events = $events->merge($dn_events);
            }
        }

        // Return the events as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Calendar Events Retrieved Successfully',
            'data' => $events,
        ]);
    }

    // function to get subcont activity admin
    public function adminSubcontGraphic(string $bp_code)
    {
        $checkBpCode = BusinessPartner::where('bp_code', $bp_code)->exists();

        if ($checkBpCode == false) {
            // Response
            throw new HttpResponseException(
                response()->json([
                    'status' => false,
                    'message' => 'Bp_code not found.',
                ], 422)
            );
        }
        /*
         * note: balikin tiga response (transaksi: Incoming,Process,Outgoing) dengan urutan berdasarkan 11 hari sebelum hari ini.
         * buat for untuk mengambil 11 hari kebelakang.
         * test apakah array map dapat mengubah key indexnya.
         */
        // initialize variable

        // this variable iterate to get 11 days back, reversed
        $getDate = array_map(fn ($i) => Carbon::now()->subDays($i)->format('Y-m-d'), range(11, 0));

        // fresh
        $getDataFreshIncoming = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Incoming')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Incoming')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        $getDataFreshProcess = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Process')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Process')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        $getDataFreshOutgoing = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Outgoing')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Outgoing')
                ->where('status', 'Fresh')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        // replating
        $getDataReplatingIncoming = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Incoming')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Incoming')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        $getDataReplatingProcess = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Process')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Process')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        $getDataReplatingOutgoing = array_map(
            fn ($date) => SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Outgoing')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ok')
            +
            SubcontTransaction::where('transaction_date', $date)
                ->where('transaction_type', 'Outgoing')
                ->where('status', 'Replating')
                ->whereHas('subItem', function ($query) use ($bp_code) {
                    $query->where('bp_code', $bp_code);
                })
                ->sum('qty_ng'),
            iterator_to_array($getDate)
        );

        return response()->json([
            'success' => true,
            'message' => 'Subcont Activity Data Retrieved Successfully',
            'data' => [
                'fresh_incoming' => $getDataFreshIncoming,
                'fresh_process' => $getDataFreshProcess,
                'fresh_outgoing' => $getDataFreshOutgoing,
                'replating_incoming' => $getDataReplatingIncoming,
                'replating_process' => $getDataReplatingProcess,
                'replating_outgoing' => $getDataReplatingOutgoing,
            ],
        ]);

        // return response()->json([
        //     "fresh" => [
        //         "Incoming" => $getDataFreshIncoming,
        //         "Process" => $getDataFreshProcess,
        //         "Outgoing" => $getDataFreshOutgoing,
        //     ],
        //     "replating" =>[
        //         "Incoming" =>$getDataReplatingIncoming,
        //         "Process" => $getDataReplatingProcess,
        //         "Outgoing" => $getDataReplatingOutgoing,
        //     ]
        // ]);
        // return $tes1A;
        // dd($tes);
        // dd($months);
        // dd($startDate);
        // dd($endDate);
        // $getSubcontData = SubcontTransaction::where("",);
    }
}
