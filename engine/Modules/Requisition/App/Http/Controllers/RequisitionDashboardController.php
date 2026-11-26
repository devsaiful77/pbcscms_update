<?php

namespace Modules\Requisition\App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Exports\RequisitionExcelExport;
use Modules\Requisition\App\Constants\RequisitionConstants;

class RequisitionDashboardController extends Controller
{
    public function index(Request $request)
    {
        return $this->dashboard($request);
    }

    public function dashboard(Request $request)
    {
        // Date handling
        $dateFrom = $request->filled('date_from')
            ? Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $search = trim($request->search);
        $viewType = $request->view_type ?? 'table';
        $perPage = $request->qty ?? 10;

        // Get filter parameters for click-based filtering
        $filterType = $request->filter_type;  // printing, table, repair, newAsset
        $filterStatus = $request->filter_status; // status code (1,2,3,10, etc.)

        // Main query
        $query = Requisition::with([
            'printings.requItem.stock',
            'generalServices.requItem',
            'tableStationeries.requItem.stock'
        ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('requisition_number', 'LIKE', "%{$search}%")
                        ->orWhere('created_by', 'LIKE', "%{$search}%")
                        ->orWhereHas('printings.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$search}%"))
                        ->orWhereHas('tableStationeries.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$search}%"))
                        ->orWhereHas('generalServices.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$search}%"));
                });
            })
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('type', $filterType);
            })
            ->when($filterStatus !== null, function ($q) use ($filterStatus) {
                $q->where('status', $filterStatus);
            });

        $allRequisitions = $query->get();

        // Statistics using Constants
        $stats = [
            'newAsset'    => $allRequisitions->where('type', RequisitionConstants::NEWASSETITEM)->count(),
            'repair'      => $allRequisitions->where('type', RequisitionConstants::REPAIRITEM)->count(),
            'printing'    => $allRequisitions->where('type', RequisitionConstants::PRINTINGITEM)->count(),
            'nonPrinting' => $allRequisitions->where('type', RequisitionConstants::NONPRINTINGITEM)->count(),
        ];

        // Calculate quantities
        $totalPrintQty = 0;
        $totalStock = 0;
        $uniqueStockIds = [];

        $allRequisitions->where('type', RequisitionConstants::PRINTINGITEM)->each(function ($req) use (&$totalPrintQty, &$totalStock, &$uniqueStockIds) {
            foreach ($req->printings as $item) {
                $totalPrintQty += $item->quantity ?? 0;
                $stock = $item->requItem->stock ?? null;
                if ($stock && !in_array($stock->id, $uniqueStockIds)) {
                    $totalStock += $stock->quantity ?? 0;
                    $uniqueStockIds[] = $stock->id;
                }
            }
        });

        $totalTableQty = $allRequisitions->where('type', RequisitionConstants::NONPRINTINGITEM)->sum(fn($req) => $req->tableStationeries->sum('quantity'));
        $totalRepairQty = $allRequisitions->where('type', RequisitionConstants::REPAIRITEM)->sum(fn($req) => $req->generalServices->sum('quantity'));
        $totalAssetQty = $allRequisitions->where('type', RequisitionConstants::NEWASSETITEM)->sum(fn($req) => $req->generalServices->sum('quantity'));

        // Status-wise breakdown
        $getStatusCounts = function ($requisitions, $type) {
            $filtered = $requisitions->where('type', $type);

            return [
                'total' => $filtered->count(),
                'pending' => $filtered->where('status', RequisitionConstants::REQ_PENDING)->count(),
                'draft' => $filtered->where('status', RequisitionConstants::REQ_DRAFT)->count(),
                'wip' => $filtered->where('status', RequisitionConstants::REQ_WIP)->count(),
                'hold' => $filtered->where('status', RequisitionConstants::REQ_HOLD)->count(),
                'approved' => $filtered->where('status', RequisitionConstants::REQ_APPROVE)->count(),
                'closed' => $filtered->where('status', RequisitionConstants::REQ_CLOSE)->count(),
                'cancelled' => $filtered->where('status', RequisitionConstants::REQ_CANCEL)->count(),
            ];
        };

        $printingStatus = $getStatusCounts($allRequisitions, RequisitionConstants::PRINTINGITEM);
        $tableStatus = $getStatusCounts($allRequisitions, RequisitionConstants::NONPRINTINGITEM);
        $repairStatus = $getStatusCounts($allRequisitions, RequisitionConstants::REPAIRITEM);
        $newAssetStatus = $getStatusCounts($allRequisitions, RequisitionConstants::NEWASSETITEM);

        // Chart data for top products
        $productRequests = collect();
        foreach ($allRequisitions as $req) {
            foreach ($req->printings as $item) {
                $productRequests->push(['product' => $item->requItem->product_name ?? 'N/A', 'qty' => $item->quantity ?? 0]);
            }
            foreach ($req->tableStationeries as $item) {
                $productRequests->push(['product' => $item->requItem->product_name ?? 'N/A', 'qty' => $item->quantity ?? 0]);
            }
            foreach ($req->generalServices as $item) {
                $productRequests->push(['product' => $item->requItem->product_name ?? 'N/A', 'qty' => $item->quantity ?? 0]);
            }
        }
        $chartData = $productRequests->groupBy('product')->map(fn($group) => $group->sum('qty'))->sortDesc()->take(5);

        // Pagination
        $requisitions = new LengthAwarePaginator(
            $allRequisitions->forPage($request->get('page', 1), $perPage),
            $allRequisitions->count(),
            $perPage,
            $request->get('page', 1),
            ['path' => url()->current()]
        );

        return view('requisition::dashboard.index', compact(
            'stats',
            'totalPrintQty',
            'totalStock',
            'totalTableQty',
            'totalRepairQty',
            'totalAssetQty',
            'requisitions',
            'chartData',
            'viewType',
            'dateFrom',
            'dateTo',
            'search',
            'perPage',
            'printingStatus',
            'tableStatus',
            'repairStatus',
            'newAssetStatus',
            'filterType',
            'filterStatus'
        ));
    }

    /**
     * AJAX method to get filtered data without page reload
     */
    public function getFilteredData(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $filterType = $request->filter_type;
        $filterStatus = $request->filter_status;

        $query = Requisition::with([
            'printings.requItem.stock',
            'generalServices.requItem',
            'tableStationeries.requItem.stock'
        ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('type', $filterType);
            })
            ->when($filterStatus !== null && $filterStatus !== '', function ($q) use ($filterStatus) {
                $q->where('status', $filterStatus);
            });

        $requisitions = $query->get();

        // Build HTML for table rows
        $html = '';
        if ($requisitions->count() > 0) {
            $rowNumber = 1;
            foreach ($requisitions as $requisition) {
                // Printing Items
                foreach ($requisition->printings as $item) {
                    $url = route(
                        'requisition.printing-item.history',
                        encrypt($requisition->requisition_number)
                    );

                    $html .= '<tr>
                        <td>' . $rowNumber++ . '</td>
                        <td>
                            <a target="_blank" href="' . $url . '">' . $requisition->requisition_number . '</a>
                        </td>
                        <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                        <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                        <td>' . number_format($item->quantity ?? 0) . '</td>
                        <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                    </tr>';
                }
                // Table Stationery Items
                foreach ($requisition->tableStationeries as $item) {
                    $url = route(
                        'requisition.table-stationery.history',
                        encrypt($requisition->requisition_number)
                    );

                    $html .= '<tr>
                        <td>' . $rowNumber++ . '</td>
                        <td>
                            <a target="_blank" href="' . $url . '">' . $requisition->requisition_number . '</a>
                        </td>
                        <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                        <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                        <td>' . number_format($item->quantity ?? 0) . '</td>
                        <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                    </tr>';
                }
                // General Services Items
                foreach ($requisition->generalServices as $item) {
                    $url = route(
                        'requisition.general-service.history',
                        encrypt($requisition->requisition_number)
                    );

                    $html .= '<tr>
                        <td>' . $rowNumber++ . '</td>
                        <td>
                            <a target="_blank" href="' . $url . '">' . $requisition->requisition_number . '</a>
                        </td>
                        <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                        <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                        <td>' . number_format($item->quantity ?? 0) . '</td>
                        <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                    </tr>';
                }
            }
        } else {
            $html = '<tr><td colspan="6" class="text-center text-muted py-5">No requisitions found.</td></tr>';
        }

        return response()->json([
            'html' => $html,
            'total' => $requisitions->count()
        ]);
    }

    public function getFilteredDataByProduct(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $productName = $request->product_name;
        $filterType = $request->filter_type; // printingStationery, tableStationery, repair, newAsset

        $query = Requisition::with([
            'printings.requItem.stock',
            'generalServices.requItem',
            'tableStationeries.requItem.stock'
        ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->when($productName, function ($q) use ($productName) {
                $q->where(function ($sub) use ($productName) {
                    $sub->whereHas('printings.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$productName}%"))
                        ->orWhereHas('tableStationeries.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$productName}%"))
                        ->orWhereHas('generalServices.requItem', fn($q2) => $q2->where('product_name', 'LIKE', "%{$productName}%"));
                });
            })
            ->when($filterType, function ($q) use ($filterType) {
                $q->where('type', $filterType);
            });

        $requisitions = $query->get();

        // Build HTML for table rows
        $html = '';
        if ($requisitions->count() > 0) {
            $rowNumber = 1;
            foreach ($requisitions as $requisition) {
                // Printing Items
                foreach ($requisition->printings as $item) {
                    if (!$productName || stripos($item->requItem->product_name ?? '', $productName) !== false) {
                        $html .= '<tr>
                            <td class="fw-medium">' . $rowNumber++ . '</td>
                            <td>' . ($requisition->requisition_number ?? 'N/A') . '</td>
                            <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                            <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                            <td>' . number_format($item->quantity ?? 0) . '</td>
                            <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                        </tr>';
                    }
                }
                // Table Stationery Items
                foreach ($requisition->tableStationeries as $item) {
                    if (!$productName || stripos($item->requItem->product_name ?? '', $productName) !== false) {
                        $html .= '<tr>
                            <td class="fw-medium">' . $rowNumber++ . '</td>
                            <td>' . ($requisition->requisition_number ?? 'N/A') . '</td>
                            <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                            <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                            <td>' . number_format($item->quantity ?? 0) . '</td>
                            <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                        </tr>';
                    }
                }
                // General Services Items
                foreach ($requisition->generalServices as $item) {
                    if (!$productName || stripos($item->requItem->product_name ?? '', $productName) !== false) {
                        $html .= '<tr>
                            <td class="fw-medium">' . $rowNumber++ . '</td>
                            <td>' . ($requisition->requisition_number ?? 'N/A') . '</td>
                            <td>' . ($requisition->created_by ?? 'N/A') . '</td>
                            <td>' . ($item?->requItem?->product_name ?? 'N/A') . '</td>
                            <td>' . number_format($item->quantity ?? 0) . '</td>
                            <td>' . number_format($item?->requItem?->stock?->quantity ?? 0) . '</td>
                        </tr>';
                    }
                }
            }
        } else {
            $html = '<tr><td colspan="6" class="text-center text-muted py-5">No requisitions found for this product.</td></tr>';
        }

        return response()->json([
            'html' => $html,
            'total' => $requisitions->count(),
            'product_name' => $productName
        ]);
    }

    public function dashboardExport(Request $request)
    {
        $dateFrom = $request->filled('date_from')
            ? Carbon::createFromFormat('d-m-Y', $request->date_from)->startOfDay()
            : Carbon::now()->subMonth()->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::createFromFormat('d-m-Y', $request->date_to)->endOfDay()
            : Carbon::now()->endOfDay();

        $query = Requisition::with(['printings.requItem.stock', 'generalServices.requItem', 'tableStationeries.requItem.stock'])
            ->whereBetween('created_at', [$dateFrom, $dateTo]);

        $exportData = [
            'date_range' => $dateFrom->format('d-m-Y') . ' to ' . $dateTo->format('d-m-Y'),
            'export_date' => Carbon::now()->format('Y-m-d H:i:s')
        ];

        return Excel::download(
            new RequisitionExcelExport($query, $exportData, 'requisition::dashboard.dashboard-export'),
            'requisition_dashboard_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}
