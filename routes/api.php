<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Jobs\Syncronization\SyncDatabaseJob;
use App\Models\DeliveryNote\DnDetailDeleteErp;
use App\Models\DeliveryNote\DnHeaderDeleteErp;
use App\Models\PurchaseOrder\PoDetailDeleteErp;
use App\Models\PurchaseOrder\PoHeaderDeleteErp;
use App\Http\Controllers\Api\V1\PrintController;
use App\Http\Controllers\Api\V1\HistoryController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\User\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\Forecast\ForecastController;
use App\Http\Controllers\Api\V1\Syncronization\SyncController;
use App\Http\Controllers\Api\V1\User\BusinessPartnerController;
use App\Http\Controllers\Api\V1\DeliveryNote\DnDetailController;
use App\Http\Controllers\Api\V1\DeliveryNote\DnHeaderController;
use App\Http\Controllers\Api\V1\Subcontractor\SubcontController;
use App\Http\Controllers\Api\V1\PurchaseOrder\PoDetailController;
use App\Http\Controllers\Api\V1\PurchaseOrder\PoHeaderController;
use App\Http\Controllers\Api\V1\Syncronization\SyncManualController;
use App\Http\Controllers\Api\V1\Subcontractor\SubcontReceiveController;
use App\Http\Controllers\Api\V1\Email\EmailNotificationSupplierController;
use App\Http\Controllers\Api\V1\PerformanceReport\PerformanceReportController;

// Route Login
Route::post('/login', [AuthController::class, 'login']);

// test sync
Route::get('/testsync', function () {
    dispatch(new SyncDatabaseJob);
    $getPoHeader = PoHeaderDeleteErp::select('po_no', 'supplier_code')->whereDate('deleted_at', Carbon::today())->count();
    $getPoDetail = PoDetailDeleteErp::select('po_no', 'po_line', 'po_sequence')->whereDate('deleted_at', Carbon::today())->count();
    $getDnHeader = DnHeaderDeleteErp::select('dn_no', 'supplier_code')->whereDate('deleted_at', Carbon::today())->count();
    $getDnDetail = DnDetailDeleteErp::select('dn_no', 'dn_line')->whereDate('deleted_at', Carbon::today())->count();

    return response()->json([
        'po_head_delete' => $getPoHeader,
        'po_detail_delete' => $getPoDetail,
        'dn_head_delete' => $getDnHeader,
        'dn_detail_delete' => $getDnDetail,
    ]);
});

// test sync without job queue
Route::get('/testsync-direct', function () {
    try {
        // Test database connections
        $mysqlConnected = false;
        $sqlsrvConnected = false;
        
        try {
            DB::connection()->getPdo();
            $mysqlConnected = true;
        } catch (Exception $e) {
            $mysqlConnected = false;
        }
        
        try {
            DB::connection('sqlsrv')->getPdo();
            $sqlsrvConnected = true;
        } catch (Exception $e) {
            $sqlsrvConnected = false;
        }
        
        return response()->json([
            'mysql_connected' => $mysqlConnected,
            'sqlsrv_connected' => $sqlsrvConnected,
            'message' => 'Database connection test completed'
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

// test SQL Server connection details
Route::get('/testsync-sqlsrv', function () {
    try {
        $config = config('database.connections.sqlsrv');
        
        return response()->json([
            'sqlsrv_config' => [
                'host' => $config['host'],
                'port' => $config['port'],
                'database' => $config['database'],
                'username' => $config['username'],
                'password' => $config['password'] ? '***' : 'empty'
            ],
            'env_variables' => [
                'DB_HOST_SQLSRV' => env('DB_HOST_SQLSRV'),
                'DB_PORT_SQLSRV' => env('DB_PORT_SQLSRV'),
                'DB_DATABASE_SQLSRV' => env('DB_DATABASE_SQLSRV'),
                'DB_USERNAME_SQLSRV' => env('DB_USERNAME_SQLSRV'),
                'DB_PASSWORD_SQLSRV' => env('DB_PASSWORD_SQLSRV') ? '***' : 'empty'
            ]
        ]);
    } catch (Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
});

// test sync with local data only
Route::get('/testsync-local', function () {
    try {
        // Test local database tables
        $tables = [
            'po_header' => DB::table('po_header')->count(),
            'po_detail' => DB::table('po_detail')->count(),
            'dn_header' => DB::table('dn_header')->count(),
            'dn_detail' => DB::table('dn_detail')->count(),
            'business_partner' => DB::table('business_partner')->count(),
            'subcont_item' => DB::table('subcont_item')->count(),
        ];
        
        return response()->json([
            'status' => 'success',
            'message' => 'Local database test completed',
            'table_counts' => $tables,
            'date' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage()
        ], 500);
    }
});

// test sync simulation without job queue
Route::get('/testsync-simulation', function () {
    try {
        $startTime = microtime(true);
        
        // Simulate sync process steps
        $steps = [
            'step_1' => 'Check database connections',
            'step_2' => 'Count existing data',
            'step_3' => 'Simulate sync process',
            'step_4' => 'Generate test response'
        ];
        
        // Check connections
        $mysqlConnected = DB::connection()->getPdo() ? true : false;
        
        // Count data
        $dataCounts = [
            'po_header' => DB::table('po_header')->count(),
            'po_detail' => DB::table('po_detail')->count(),
            'dn_header' => DB::table('dn_header')->count(),
            'dn_detail' => DB::table('dn_detail')->count(),
        ];
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        
        return response()->json([
            'status' => 'success',
            'message' => 'Sync simulation completed successfully',
            'mysql_connected' => $mysqlConnected,
            'data_counts' => $dataCounts,
            'execution_time_ms' => $executionTime,
            'steps_completed' => $steps,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// test sync using controller without job queue
Route::get('/testsync-controller', function () {
    try {
        // Create controller instance manually
        $syncController = new \App\Http\Controllers\Api\V1\Syncronization\SyncController(
            app(\App\Service\Syncronization\SyncBusinessPartnerData::class),
            app(\App\Service\Syncronization\SyncPurchaseOrderData::class),
            app(\App\Service\Syncronization\SyncDeliveryNoteData::class),
            app(\App\Service\Syncronization\SyncSubcontItemData::class),
            app(\App\Service\Syncronization\SyncDeleteData::class)
        );
        
        // Call syncTest method
        $response = $syncController->syncTest();
        
        return $response;
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// test manual sync without authentication
Route::get('/testsync-manual', function (Request $request) {
    try {
        $month = $request->input('month', 8);
        $year = $request->input('year', 2025);
        
        // Create manual sync controller
        $manualController = new \App\Http\Controllers\Api\V1\Syncronization\SyncManualController();
        
        // Call syncManual method
        $response = $manualController->syncManual($request);
        
        return $response;
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// test sync manual simple
Route::get('/testsync-manual-simple', function () {
    try {
        // Test SQL Server connection for manual sync
        $sqlsrvConnected = false;
        $dataCount = 0;
        
        try {
            $sqlsrvConnected = DB::connection('sqlsrv')->getPdo() ? true : false;
            
            if ($sqlsrvConnected) {
                // Test query similar to manual sync
                $dataCount = DB::connection('sqlsrv')
                    ->table('po_header')
                    ->where('po_period', 8)
                    ->where('po_year', 2025)
                    ->count();
            }
        } catch (Exception $e) {
            $sqlsrvConnected = false;
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Manual sync test completed',
            'sqlsrv_connected' => $sqlsrvConnected,
            'data_count' => $dataCount,
            'test_period' => 'August 2025',
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// test sync with detailed information
Route::get('/testsync-detailed', function () {
    try {
        $startTime = microtime(true);
        
        // Test all sync components
        $components = [
            'business_partner' => function() {
                return DB::connection('sqlsrv')
                    ->table('business_partner')
                    ->where('bp_role_desc', 'LIKE', '%Supplier%')
                    ->count();
            },
            'po_header' => function() {
                return DB::connection('sqlsrv')
                    ->table('po_header')
                    ->whereBetween('po_period', [7, 8])
                    ->where('po_year', 2025)
                    ->count();
            },
            'po_detail' => function() {
                $poNumbers = DB::connection('sqlsrv')
                    ->table('po_header')
                    ->whereBetween('po_period', [7, 8])
                    ->where('po_year', 2025)
                    ->pluck('po_no')
                    ->toArray();
                
                return DB::connection('sqlsrv')
                    ->table('po_detail')
                    ->whereIn('po_no', $poNumbers)
                    ->count();
            },
            'dn_header' => function() {
                $poNumbers = DB::connection('sqlsrv')
                    ->table('po_header')
                    ->whereBetween('po_period', [7, 8])
                    ->where('po_year', 2025)
                    ->pluck('po_no')
                    ->toArray();
                
                return DB::connection('sqlsrv')
                    ->table('dn_header')
                    ->whereIn('po_no', $poNumbers)
                    ->count();
            },
            'dn_detail' => function() {
                $poNumbers = DB::connection('sqlsrv')
                    ->table('po_header')
                    ->whereBetween('po_period', [7, 8])
                    ->where('po_year', 2025)
                    ->pluck('po_no')
                    ->toArray();
                
                $dnNumbers = DB::connection('sqlsrv')
                    ->table('dn_header')
                    ->whereIn('po_no', $poNumbers)
                    ->pluck('no_dn')
                    ->toArray();
                
                return DB::connection('sqlsrv')
                    ->table('dn_detail')
                    ->whereIn('no_dn', $dnNumbers)
                    ->count();
            }
        ];
        
        $results = [];
        foreach ($components as $name => $callback) {
            try {
                $results[$name] = $callback();
            } catch (Exception $e) {
                $results[$name] = 'error: ' . $e->getMessage();
            }
        }
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Detailed sync test completed',
            'execution_time_ms' => $executionTime,
            'data_counts' => $results,
            'period_range' => [
                'from_month' => 7,
                'to_month' => 8,
                'year' => 2025
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// test manual sync for last month with top 10 data
Route::get('/testsync-manual-lastmonth', function () {
    try {
        $startTime = microtime(true);
        
        // Get current month and last month
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        
        // Sync Business Partner
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get();
        
        // Sync PO Header for last month
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get();
        
        // Get PO numbers for detail
        $poNumbers = $poHeaders->pluck('po_no')->toArray();
        
        // Sync PO Detail
        $poDetails = DB::connection('sqlsrv')
            ->table('po_detail')
            ->whereIn('po_no', $poNumbers)
            ->limit(10)
            ->get();
        
        // Sync DN Header
        $dnHeaders = DB::connection('sqlsrv')
            ->table('dn_header')
            ->whereIn('po_no', $poNumbers)
            ->limit(10)
            ->get();
        
        // Get DN numbers for detail
        $dnNumbers = $dnHeaders->pluck('no_dn')->toArray();
        
        // Sync DN Detail
        $dnDetails = DB::connection('sqlsrv')
            ->table('dn_detail')
            ->whereIn('no_dn', $dnNumbers)
            ->limit(10)
            ->get();
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Manual sync for last month completed',
            'execution_time_ms' => $executionTime,
            'period' => [
                'last_month' => $lastMonth,
                'current_month' => $currentMonth,
                'year' => $currentYear
            ],
            'top_10_data' => [
                'business_partners' => $businessPartners,
                'po_headers' => $poHeaders,
                'po_details' => $poDetails,
                'dn_headers' => $dnHeaders,
                'dn_details' => $dnDetails
            ],
            'counts' => [
                'business_partners' => $businessPartners->count(),
                'po_headers' => $poHeaders->count(),
                'po_details' => $poDetails->count(),
                'dn_headers' => $dnHeaders->count(),
                'dn_details' => $dnDetails->count()
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// display top 10 data in readable format
Route::get('/testsync-top10-readable', function () {
    try {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        
        // Get top 10 PO Headers for last month
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->select('po_no', 'supplier_code', 'supplier_name', 'po_date', 'po_status', 'po_currency')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get();
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->select('bp_code', 'bp_name', 'bp_role_desc', 'bp_currency', 'country')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get();
        
        // Get PO numbers for detail
        $poNumbers = $poHeaders->pluck('po_no')->toArray();
        
        // Get top 10 PO Details
        $poDetails = DB::connection('sqlsrv')
            ->table('po_detail')
            ->select('po_no', 'po_line', 'item_code', 'bp_part_name', 'po_qty', 'price', 'amount')
            ->whereIn('po_no', $poNumbers)
            ->limit(10)
            ->get();
        
        // Get top 10 DN Headers
        $dnHeaders = DB::connection('sqlsrv')
            ->table('dn_header')
            ->select('no_dn', 'po_no', 'supplier_code', 'supplier_name', 'dn_created_date', 'status_desc')
            ->whereIn('po_no', $poNumbers)
            ->limit(10)
            ->get();
        
        // Get DN numbers for detail
        $dnNumbers = $dnHeaders->pluck('no_dn')->toArray();
        
        // Get top 10 DN Details
        $dnDetails = DB::connection('sqlsrv')
            ->table('dn_detail')
            ->select('no_dn', 'dn_line', 'part_no', 'item_desc_a', 'dn_qty', 'receipt_qty', 'status_desc')
            ->whereIn('no_dn', $dnNumbers)
            ->limit(10)
            ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Top 10 data for last month',
            'period' => [
                'last_month' => $lastMonth,
                'current_month' => $currentMonth,
                'year' => $currentYear,
                'period_name' => 'July 2025'
            ],
            'summary' => [
                'total_po_headers' => $poHeaders->count(),
                'total_business_partners' => $businessPartners->count(),
                'total_po_details' => $poDetails->count(),
                'total_dn_headers' => $dnHeaders->count(),
                'total_dn_details' => $dnDetails->count()
            ],
            'top_10_data' => [
                'purchase_orders' => $poHeaders,
                'business_partners' => $businessPartners,
                'po_details' => $poDetails,
                'delivery_notes' => $dnHeaders,
                'dn_details' => $dnDetails
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// simple top 10 data display
Route::get('/testsync-top10-simple', function () {
    try {
        $lastMonth = now()->subMonth()->month;
        $currentYear = now()->year;
        
        // Get top 10 PO Headers
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get(['po_no', 'supplier_code', 'supplier_name', 'po_date', 'po_status']);
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get(['bp_code', 'bp_name', 'bp_role_desc', 'bp_currency']);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Top 10 data for last month (July 2025)',
            'period' => [
                'month' => $lastMonth,
                'year' => $currentYear,
                'period_name' => 'July 2025'
            ],
            'data' => [
                'purchase_orders' => $poHeaders,
                'business_partners' => $businessPartners
            ],
            'counts' => [
                'po_headers' => $poHeaders->count(),
                'business_partners' => $businessPartners->count()
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// display formatted top 10 data
Route::get('/testsync-top10-formatted', function () {
    try {
        $lastMonth = now()->subMonth()->month;
        $currentYear = now()->year;
        
        // Get top 10 PO Headers
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get(['po_no', 'supplier_code', 'supplier_name', 'po_date', 'po_status', 'po_currency']);
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get(['bp_code', 'bp_name', 'bp_role_desc', 'bp_currency', 'country']);
        
        // Format data for better readability
        $formattedPO = $poHeaders->map(function($po) {
            return [
                'PO Number' => $po->po_no,
                'Supplier Code' => $po->supplier_code,
                'Supplier Name' => $po->supplier_name,
                'PO Date' => $po->po_date,
                'Status' => $po->po_status,
                'Currency' => $po->po_currency
            ];
        });
        
        $formattedBP = $businessPartners->map(function($bp) {
            return [
                'BP Code' => $bp->bp_code,
                'BP Name' => $bp->bp_name,
                'Role' => $bp->bp_role_desc,
                'Currency' => $bp->bp_currency,
                'Country' => $bp->country
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'message' => 'Top 10 data for last month (July 2025) - Formatted',
            'period' => [
                'month' => $lastMonth,
                'year' => $currentYear,
                'period_name' => 'July 2025'
            ],
            'summary' => [
                'total_purchase_orders' => $poHeaders->count(),
                'total_business_partners' => $businessPartners->count(),
                'sync_period' => 'July 2025 (Last Month)'
            ],
            'formatted_data' => [
                'purchase_orders' => $formattedPO,
                'business_partners' => $formattedBP
            ],
            'raw_data' => [
                'purchase_orders' => $poHeaders,
                'business_partners' => $businessPartners
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// display top 10 data summary
Route::get('/testsync-top10-summary', function () {
    try {
        $lastMonth = now()->subMonth()->month;
        $currentYear = now()->year;
        
        // Get top 10 PO Headers
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get();
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Top 10 data summary for last month (July 2025)',
            'sync_period' => 'July 2025 (Last Month)',
            'summary' => [
                'total_purchase_orders' => $poHeaders->count(),
                'total_business_partners' => $businessPartners->count(),
                'period_month' => $lastMonth,
                'period_year' => $currentYear
            ],
            'purchase_orders' => $poHeaders,
            'business_partners' => $businessPartners,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// display top 10 data in table format
Route::get('/testsync-top10-table', function () {
    try {
        $lastMonth = now()->subMonth()->month;
        $currentYear = now()->year;
        
        // Get top 10 PO Headers
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get();
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get();
        
        // Create table format
        $poTable = [];
        foreach ($poHeaders as $index => $po) {
            $poTable[] = [
                'No' => $index + 1,
                'PO Number' => $po->po_no,
                'Supplier Code' => $po->supplier_code,
                'Supplier Name' => $po->supplier_name,
                'PO Date' => $po->po_date,
                'Status' => $po->po_status,
                'Currency' => $po->po_currency ?? 'N/A'
            ];
        }
        
        $bpTable = [];
        foreach ($businessPartners as $index => $bp) {
            $bpTable[] = [
                'No' => $index + 1,
                'BP Code' => $bp->bp_code,
                'BP Name' => $bp->bp_name,
                'Role' => $bp->bp_role_desc,
                'Currency' => $bp->bp_currency ?? 'N/A',
                'Country' => $bp->country ?? 'N/A'
            ];
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Top 10 data for last month (July 2025) - Table Format',
            'sync_period' => 'July 2025 (Last Month)',
            'summary' => [
                'total_purchase_orders' => count($poTable),
                'total_business_partners' => count($bpTable),
                'period_month' => $lastMonth,
                'period_year' => $currentYear
            ],
            'tables' => [
                'purchase_orders' => [
                    'title' => 'Top 10 Purchase Orders (July 2025)',
                    'data' => $poTable
                ],
                'business_partners' => [
                    'title' => 'Top 10 Business Partners (Suppliers)',
                    'data' => $bpTable
                ]
            ],
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// display top 10 data in HTML format
Route::get('/testsync-top10-html', function () {
    try {
        $lastMonth = now()->subMonth()->month;
        $currentYear = now()->year;
        
        // Get top 10 PO Headers
        $poHeaders = DB::connection('sqlsrv')
            ->table('po_header')
            ->where('po_period', $lastMonth)
            ->where('po_year', $currentYear)
            ->limit(10)
            ->get();
        
        // Get top 10 Business Partners
        $businessPartners = DB::connection('sqlsrv')
            ->table('business_partner')
            ->where('bp_role_desc', 'LIKE', '%Supplier%')
            ->limit(10)
            ->get();
        
        // Create HTML table
        $poHtml = '<table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">';
        $poHtml .= '<tr style="background-color: #f2f2f2;"><th>No</th><th>PO Number</th><th>Supplier Code</th><th>Supplier Name</th><th>PO Date</th><th>Status</th><th>Currency</th></tr>';
        
        foreach ($poHeaders as $index => $po) {
            $poHtml .= '<tr>';
            $poHtml .= '<td>' . ($index + 1) . '</td>';
            $poHtml .= '<td>' . ($po->po_no ?? 'N/A') . '</td>';
            $poHtml .= '<td>' . ($po->supplier_code ?? 'N/A') . '</td>';
            $poHtml .= '<td>' . ($po->supplier_name ?? 'N/A') . '</td>';
            $poHtml .= '<td>' . ($po->po_date ?? 'N/A') . '</td>';
            $poHtml .= '<td>' . ($po->po_status ?? 'N/A') . '</td>';
            $poHtml .= '<td>' . ($po->po_currency ?? 'N/A') . '</td>';
            $poHtml .= '</tr>';
        }
        $poHtml .= '</table>';
        
        $bpHtml = '<table border="1" style="border-collapse: collapse; width: 100%; margin-bottom: 20px;">';
        $bpHtml .= '<tr style="background-color: #f2f2f2;"><th>No</th><th>BP Code</th><th>BP Name</th><th>Role</th><th>Currency</th><th>Country</th></tr>';
        
        foreach ($businessPartners as $index => $bp) {
            $bpHtml .= '<tr>';
            $bpHtml .= '<td>' . ($index + 1) . '</td>';
            $bpHtml .= '<td>' . ($bp->bp_code ?? 'N/A') . '</td>';
            $bpHtml .= '<td>' . ($bp->bp_name ?? 'N/A') . '</td>';
            $bpHtml .= '<td>' . ($bp->bp_role_desc ?? 'N/A') . '</td>';
            $bpHtml .= '<td>' . ($bp->bp_currency ?? 'N/A') . '</td>';
            $bpHtml .= '<td>' . ($bp->country ?? 'N/A') . '</td>';
            $bpHtml .= '</tr>';
        }
        $bpHtml .= '</table>';
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>Sync Data Report - July 2025</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                h1 { color: #333; }
                h2 { color: #666; margin-top: 30px; }
                .summary { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
                th { background-color: #4CAF50; color: white; padding: 12px; text-align: left; }
                td { padding: 8px; border: 1px solid #ddd; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                .timestamp { color: #888; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <h1>Sync Data Report</h1>
            <div class="summary">
                <h2>Summary</h2>
                <p><strong>Period:</strong> July 2025 (Last Month)</p>
                <p><strong>Total Purchase Orders:</strong> ' . $poHeaders->count() . '</p>
                <p><strong>Total Business Partners:</strong> ' . $businessPartners->count() . '</p>
                <p><strong>Sync Date:</strong> ' . now()->format('Y-m-d H:i:s') . '</p>
            </div>
            
            <h2>Top 10 Purchase Orders (July 2025)</h2>
            ' . $poHtml . '
            
            <h2>Top 10 Business Partners (Suppliers)</h2>
            ' . $bpHtml . '
            
            <div class="timestamp">
                <p>Generated on: ' . now()->format('Y-m-d H:i:s') . '</p>
            </div>
        </body>
        </html>';
        
        return response($html, 200, ['Content-Type' => 'text/html']);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'error' => $e->getMessage(),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ], 500);
    }
});

// move email
Route::get('/move', [UserController::class, 'moveEmail']);

// route view print
Route::get('/pohview/{po_no}', [PrintController::class, 'poHeaderView']);

// print dn
Route::get('/dnhview/{no_dn}', [PrintController::class, 'dnHeaderView']);
Route::get('/dnhviewconf/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
Route::get('/dnhviewout/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);

// print label
Route::get('/lbview/{no_dn}', [PrintController::class, 'labelAllView']);
Route::get('/dnout/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
Route::get('/dnqty/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

// Route sync
Route::get('/mail-notification', [EmailNotificationSupplierController::class, 'mail']);
Route::get('/sync', [SyncController::class, 'sync']);
Route::get('/sync2', [SyncController::class, 'syncTest']);
Route::get('/copyBusinessPartner', [SyncController::class, 'copyBusinessPartner']);
Route::get('/copyPoHeader', [SyncController::class, 'copyPoHeader']);
Route::get('/copyPoDetail', [SyncController::class, 'copyPoDetail']);
Route::get('/copyDnHeader', [SyncController::class, 'copyDnHeader']);
Route::get('/copyDnDetail', [SyncController::class, 'copyDnDetail']);

// Route Super Admin
Route::middleware(['auth:sanctum', 'userRole:1'])->prefix('super-admin')->group(function () {

    // Route for show list of user
    Route::get('partner/list', [BusinessPartnerController::class, 'GetBusinessPartner']);

    // Route for show online user
    Route::get('dashboard', [DashboardController::class, 'dashboard']);

    // Route for detail active user
    Route::get('user/online', [DashboardController::class, 'detailActiveUser']);

    // Route for logout current useer active token
    Route::post('user/logout', [DashboardController::class, 'logoutByTokenId']);

    // Route for monthly login data
    Route::get('user/monthly', [DashboardController::class, 'monthlyLoginData']);

    /**
     * Route for Business Partner
     */
    // Route for get business partner
    Route::get('organization/email/{bp_code}', [BusinessPartnerController::class,'getBussinessPartnerEmail']);
    // Route for update business partner
    Route::put('organization/email/update/{bp_code}', [BusinessPartnerController::class,'updateBusinessPartnerEmail']);
    // Route for unified search business partner
    Route::get('partner/search', [BusinessPartnerController::class,'searchBusinessPartner']);
    // Route for get business partner by code (unified)
    Route::get('partner/{bp_code}', [BusinessPartnerController::class,'getBusinessPartnerByCode']);

    /**
     * Route For User
     */
    // Route for get record data
    Route::get('user/index', [UserController::class, 'getUser']);
    // Route for get email user
    Route::get('user/email/{bp_code}', [UserController::class, 'getBusinessPartnerEmail']);
    // Route for create user
    Route::post('user/store', [UserController::class, 'createUser']);
    // Route for edit user form
    Route::get('user/edit/{user}', [UserController::class, 'getUserDetail']);
    // Route for update user data
    Route::put('user/update/{user}', [UserController::class, 'updateUser']);
    // Route for update status user
    Route::put('user/update/status/{user}', [UserController::class, 'updateStatus']);
    // Route for delete user
    Route::delete('user/delete/{user}', [UserController::class, 'deleteUser']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Purchasing
Route::middleware(['auth:sanctum', 'userRole:2'])->prefix('admin-purchasing')->group(function () {
    // Route sync data
    Route::get('sync', [SyncManualController::class, 'syncManual']);

    // Route get partner list
    Route::get('partner/list', [BusinessPartnerController::class, 'GetBusinessPartner']);

    /**
     *  Route for Purchase Order
     *
     * @param  $sp_code  / supplier_code is bp_code
     */
    // Routefor get record po with specific user
    Route::get('po/index/{bp_code}', [PoHeaderController::class, 'getListPoUser']);
    // Route for show PO Detail list
    Route::get('po/detail/{po_no}', [PoDetailController::class, 'getListDetailPo']);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);
    // Route for show PO history list
    Route::get('po/history/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    /**
     * Route for Performance Report
     */
    // Route for show list of performance report
    Route::get('performance-report/index/{bp_code}', [PerformanceReportController::class, 'index'])->name('index');
    // Route for download performance report
    Route::get('performance-report/file/{filename}', [PerformanceReportController::class, 'getFile']);
    // Route for store Listing Report
    Route::post('performance-report/store', [PerformanceReportController::class, 'store']);

    /**
     * Route for Forecast
     */
    // Route for get record forecast with spesific user
    Route::get('forecast/index/{bp_code}', [ForecastController::class, 'indexPurchasing']);
    // Route for store forecast file
    Route::post('forecast/store', [ForecastController::class, 'store']);
    // Route for download forecast file
    Route::get('forecast/file/{filename}', [ForecastController::class, 'getFile']);
    // Route for delete forecast file
    Route::delete('forecast/delete/{forecast}', [ForecastController::class, 'destroy']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Warehouse
Route::middleware(['auth:sanctum', 'userRole:3'])->prefix('admin-warehouse')->group(function () {
    // Route sync data
    Route::get('sync', [SyncManualController::class, 'syncManual']);

    // Route get partner data
    Route::get('partner/list', [BusinessPartnerController::class, 'GetBusinessPartner']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index/{sp_code}', [DnHeaderController::class, 'getListDnHeaderSelected']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Admin Subcont
Route::middleware(['auth:sanctum', 'userRole:4'])->prefix('admin-subcont')->group(function () {
    // Route for get partner list
    Route::get('partner/list', [BusinessPartnerController::class, 'GetBusinessPartner']);
    // Route for dashboard admin
    Route::get('dashboard/performance-subcont/{bp_code}', [DashboardController::class, 'adminSubcontGraphic']);

    /**
     * Route for Subcontractor
     */
    // Route for get list item Erp
    Route::get('item/list/item', [SubcontController::class, 'getListItemErp']);
    // Route for get list item user
    Route::get('item/list/{bp_code}', [SubcontController::class, 'getListItem']);
    // Route for admin get all list item user based on bp_code
    Route::get('item/all-list/{bp_code}', [SubcontController::class, 'adminGetAllItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{bp_code}', [SubcontController::class, 'indexItem']);
    // Route for store subcont item
    Route::post('item/store', [SubcontController::class, 'createItem']);
    // Route for import stock subcont item
    Route::post('item/stock/initial', [SubcontController::class, 'importStockItems']);
    // Route for update subcont item
    Route::patch('item/update', [SubcontController::class, 'updateItem']);
    // Route for delete subcont item
    Route::delete('item/delete', [SubcontController::class, 'deleteItem']);
    // Route for review subcont transaction header
    Route::get('transaction-review/header/{bp_code}', [SubcontReceiveController::class, 'reviewHeader']);
    // Route for review subcont transaction detail
    Route::get('transaction-review/detail/{no_dn}', [SubcontReceiveController::class, 'reviewDetail']);
    // Route for update review subcont transaction
    Route::patch('transaction-review/update', [SubcontReceiveController::class, 'reviewUpdate']);
    // Route for get index subcont transaction
    Route::get('transaction/index/{bp_code}/{start_date}/{end_date}', [SubcontController::class, 'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class, 'createTransaction']);
    // Route for update transaction
    Route::post('transaction/edit', [SubcontController::class,'updateTransaction']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index/{sp_code}', [DnHeaderController::class, 'getListDnHeaderSelected']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Marketing
Route::middleware(['auth:sanctum', 'userRole:5'])->prefix('supplier-marketing')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Purchase Order
     */
    // Route for show list PO Header
    Route::get('po/index', [PoHeaderController::class, 'getListPoUser']);
    // Route for show list PO Detail
    Route::get('po/detail/{po_no}', [PoDetailController::class, 'getListDetailPo']);
    // Route for update list PO Header
    Route::put('po/update/{po_no}', [PoHeaderController::class, 'updateResponse']);
    // route view PO history
    Route::get('po/history', [HistoryController::class, 'poHeaderHistory']);
    // route view print PO file
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index', [DnHeaderController::class, 'getListDnUser']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}', [DnDetailController::class, 'edit']);
    // Route for update list DN Detail
    Route::put('dn/update', [DnDetailController::class, 'update']);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

    /**
     *  Route for Performance Repot
     */
    // Route for show list Perfromance Report
    Route::get('performance-report/index', [PerformanceReportController::class, 'index']);
    // Route for download Performance Report
    Route::get('performance-report/file/{filename}', [PerformanceReportController::class, 'getFile']);

    /**
     * Route for Forecast
     */
    // Route for show list Forecast
    Route::get('forecast/index', [ForecastController::class, 'indexSupplier']);
    // Route for download Forecast
    Route::get('forecast/file/{filename}', [ForecastController::class, 'getFile']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Subcont Marketing
Route::middleware(['auth:sanctum', 'userRole:6'])->prefix('supplier-subcont-marketing')->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Purchase Order
     */

    // Route for show list PO Header
    Route::get('po/index', [PoHeaderController::class, 'getListPoUser']);
    // Route for show list PO Detail
    Route::get('po/detail/{po_no}', [PoDetailController::class, 'getListDetailPo']);
    // Route for update list PO Header
    Route::put('po/update/{po_no}', [PoHeaderController::class, 'updateResponse']);
    // Route for PO history
    Route::get('po/history', [HistoryController::class, 'poHeaderHistory']);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index', [DnHeaderController::class, 'getListDnUser']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}', [DnDetailController::class, 'edit']);
    // Route for update list DN Detail
    Route::put('dn/update', [DnDetailController::class, 'update']);
    // Route for print DN
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route for print DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);

    /**
     *  Route for Performance Report
     */
    // Route for show list of Performance Report
    Route::get('performance-report/index', [PerformanceReportController::class, 'index']);
    // Route for download Performance Report
    Route::get('performance-report/file/{filename}', [PerformanceReportController::class, 'getFile']);

    /**
     *  Route for Subcontractor
     */
    // Route for get list subcont item
    Route::get('item/list', [SubcontController::class, 'getListItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{param?}', [SubcontController::class, 'indexItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class, 'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class, 'createTransaction']);

    /**
     *  Route for Forcast
     */
    // Route for get list Forecast
    Route::get('forecast/index', [ForecastController::class, 'indexSupplier']);
    // Route for download Forecast
    Route::get('forecast/file/{filename}', [ForecastController::class, 'getFile']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Warehouse
Route::middleware(['auth:sanctum', 'userRole:7'])->prefix('supplier-warehouse')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     * Route for Delivery Note
     */
    // Route for get record DN with specific user
    Route::get('dn/index', [DnHeaderController::class, 'getListDnUser']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for update list DN Detail
    Route::put('dn/update', [DnDetailController::class, 'update']);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}', [DnDetailController::class, 'edit']);
    // Route for print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // Route fo prin DN label / kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);
    // Route fo get DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Supplier Subcont
Route::middleware(['auth:sanctum', 'userRole:8'])->prefix('supplier-subcont')->group(function () {

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Graph PO And DN Yearly Data
    Route::get('yearData', [DashboardController::class, 'getYearlyData']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Subcontractor
     */
    // Route for get list subcont item
    Route::get('item/list', [SubcontController::class, 'getListItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{param?}', [SubcontController::class, 'indexItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class, 'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class, 'createTransaction']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index', [DnHeaderController::class, 'getListDnUser']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}', [DnDetailController::class, 'edit']);
    // Route for update list DN Detail
    Route::put('dn/update', [DnDetailController::class, 'update']);
    // route view DN history
    Route::get('dn/history', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    Route::get('dn/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'dnHeaderViewOutstanding']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// Route Super User
Route::middleware(['auth:sanctum', 'userRole:9'])->prefix('super-user')->group(function () {
    // Route sync data
    Route::get('sync', [SyncManualController::class, 'syncManual']);

    // Route get partner list
    Route::get('partner/list', [BusinessPartnerController::class, 'GetBusinessPartner']);

    // Route For Calender Events
    Route::get('event', [DashboardController::class, 'calenderEvents']);

    /**
     *  Route for Purchase Order

     */
    // Routefor get record po with specific user
    Route::get('po/index/{bp_code}', [PoHeaderController::class, 'getListPoUser']);
    // Route for show PO Detail list
    Route::get('po/detail/{po_no}', [PoDetailController::class, 'getListDetailPo']);
    // Route for update list PO Header
    Route::put('po/update/{po_no}', [PoHeaderController::class, 'updateResponse']);
    // Route for print PO
    Route::get('po/print/{po_no}', [PrintController::class, 'poHeaderView']);
    // Route for show PO history list
    Route::get('po/history/{bp_code}', [HistoryController::class, 'poHeaderHistory']);

    /**
     *  Route for Delivery Note
     */
    // Route for show list DN Header
    Route::get('dn/index/{sp_code}', [DnHeaderController::class, 'getListDnHeaderSelected']);
    // Route for show list DN Detail
    Route::get('dn/detail/{no_dn}', [DnDetailController::class, 'getListDetailDnUser']);
    // Route for edit list DN Detail
    Route::get('dn/edit/{dn_detail_no}', [DnDetailController::class, 'edit']);
    // Route for update list DN Detail
    Route::put('dn/update', [DnDetailController::class, 'update']);
    // route view DN history
    Route::get('dn/history/{bp_code}', [HistoryController::class, 'dnHeaderHistory']);
    // route view print DN file
    Route::get('dn/print/{no_dn}', [PrintController::class, 'dnHeaderView']);
    Route::get('dn/print/qty-confirm/{no_dn}', [PrintController::class, 'dnHeaderViewQtyConfirm']);
    // route view print DN label/ kanban
    Route::get('dn-label/print/{no_dn}', [PrintController::class, 'labelAllView']);
    Route::get('dn-label/print/outstanding/{outstanding}/{no_dn}', [PrintController::class, 'labelOutstanding']);
    Route::get('dn-label/print/qty-confirm/{no_dn}', [PrintController::class, 'labelQtyConfirm']);

    /**
     * Route for Performance Report
     */
    // Route for show list of performance report
    Route::get('performance-report/index/{bp_code}', [PerformanceReportController::class, 'index'])->name('index');
    // Route for download performance report
    Route::get('performance-report/file/{filename}', [PerformanceReportController::class, 'getFile']);
    // Route for store Listing Report
    Route::post('performance-report/store', [PerformanceReportController::class, 'store']);

    /**
     * Route for Forecast
     */
    // Route for get record forecast with spesific user
    Route::get('forecast/index/{bp_code}', [ForecastController::class, 'indexPurchasing']);
    // Route for store forecast file
    Route::post('forecast/store', [ForecastController::class, 'store']);
    // Route for download forecast file
    Route::get('forecast/file/{filename}', [ForecastController::class, 'getFile']);
    // Route for delete forecast file
    Route::delete('forecast/delete/{forecast}', [ForecastController::class, 'destroy']);

    /**
     * Route for Subcontractor
     */
    // Route for get list item Erp
    Route::get('item/list/item', [SubcontController::class, 'getListItemErp']);
    // Route for get list item user
    Route::get('item/list/{bp_code}', [SubcontController::class, 'getListItem']);
    // Route for admin get all list item user based on bp_code
    Route::get('item/all-list/{bp_code}', [SubcontController::class, 'adminGetAllItem']);
    // Route for get index subcont item (include stock)
    Route::get('item/index/{bp_code}', [SubcontController::class, 'indexItem']);
    // Route for store subcont item
    Route::post('item/store', [SubcontController::class, 'createItem']);
    // Route for import stock subcont item
    Route::post('item/stock/initial', [SubcontController::class, 'importStockItems']);
    // Route for update subcont item
    Route::patch('item/update', [SubcontController::class, 'updateItem']);
    // Route for delete subcont item
    Route::delete('item/delete', [SubcontController::class, 'deleteItem']);
    // Route for get index subcont transaction
    Route::get('transaction/index', [SubcontController::class, 'indexTrans']);
    // Route for review subcont transaction header
    Route::get('transaction-review/header/{bp_code}', [SubcontReceiveController::class, 'reviewHeader']);
    // Route for review subcont transaction detail
    Route::get('transaction-review/detail/{no_dn}', [SubcontReceiveController::class, 'reviewDetail']);
    // Route for update review subcont transaction
    Route::patch('transaction-review/update', [SubcontReceiveController::class, 'reviewUpdate']);
    // Route for get index subcont transaction
    Route::get('transaction/index/{bp_code}/{start_date}/{end_date}', [SubcontController::class, 'indexTrans']);
    // Route for store subcont transaction
    Route::post('transaction/store', [SubcontController::class, 'createTransaction']);
    // Route for update transaction
    Route::post('transaction/edit', [SubcontController::class,'updateTransaction']);

    // Feat reset password
    Route::post('change-password', [UserController::class, 'changePassword']);

    //Logout route
    Route::post('logout', [AuthController::class, 'logout']);
});

// route testing
Route::get('/listingreporttest/file/{filename}', [PerformanceReportController::class, 'getFile']);
Route::get('/forecasttest/file/{filename}', [ForecastController::class, 'getFile']);
Route::get('/patch', [SubcontController::class, 'patchOldRecord']);