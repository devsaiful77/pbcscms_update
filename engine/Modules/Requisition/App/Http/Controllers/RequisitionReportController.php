<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\SubgroupInfo;
use App\Exports\ExcelExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Requisition\App\Models\Region;
use Modules\Requisition\App\Models\Vendor;
use Modules\Requisition\App\Models\Requisition;
use Modules\Requisition\App\Constants\RequisitionConstants;
use Modules\Requisition\App\Exports\RequisitionExcelExport;
use Modules\Requisition\App\Models\RequisitionItem;

class RequisitionReportController extends Controller
{
    public function index(Request $request)
    {
        $msc = microtime(true);

        $searchDataForView = array();
        $searchDataForView['report_type'] = $request->report_type;
        $searchDataForView['requisition_number'] = $request->requisition_number;
        $searchDataForView['user_id'] = $request->user_id;
        $searchDataForView['subgroup_id'] = array_filter($request->subgroup_id ?? []);
        $searchDataForView['mobile_number'] = $request->mobile_number;
        $searchDataForView['requisition_type'] = array_filter($request->requisition_type ?? []);
        $searchDataForView['date_from'] = $request->date_from;
        $searchDataForView['date_to'] = $request->date_to;
        $searchDataForView['requisition_status'] = array_filter($request->requisition_status ?? []);
        $searchDataForView['region_id'] = array_filter($request->region_id ?? []);
        $searchDataForView['vendor_id'] = array_filter($request->vendor_id ?? []);
        $searchDataForView['delivery_status'] = array_filter($request->delivery_status ?? []);
        $searchDataForView['item_id'] = array_filter($request->item_id ?? []);

        $searchDataForView = xss_cleaner($searchDataForView);

        $unitList = Auth::user()->user_unit;
        $userGroup = $unitList->group_info_id ?? null;
        $userSubGroup = $unitList->subgroup_info_id ?? null;
        $userRegions = $unitList->region_id ?? null;

        $reportType = array(
            'gen_report' => 'General Report',
            'handler_report' => 'Handler Report',
            'history_report' => 'History Report',
            'product_quantity' => 'Product Quantity'
        );

        if (!empty($unitList)) {
            if (!empty($unitList->unit_id)) {
                $units = explode(',', $unitList->unit_id);
                if (in_array('25', $units)) {
                    unset($reportType['gen_report']);
                }
            }
        }

        $requisitionType = array(
            RequisitionConstants::PRINTINGITEM => 'Printing Stationery',
            RequisitionConstants::NONPRINTINGITEM => 'Non-Printing Stationery',
            RequisitionConstants::REPAIRITEM => 'Repair & Maintenance',
            RequisitionConstants::NEWASSETITEM => 'New Asset',
        );

        $requisitionStatus = array(
            RequisitionConstants::REQ_DRAFT => 'Draft',
            RequisitionConstants::REQ_PENDING => 'Pending',
            RequisitionConstants::REQ_WIP => 'WIP',
            RequisitionConstants::REQ_APPROVE => 'Approved',
            RequisitionConstants::REQ_CLOSE => 'Closed',
            RequisitionConstants::REQ_CANCEL => 'Cancelled',
        );

        $regions = Region::select('id', 'name')->get();
        $vendors = Vendor::select('id', 'name')->where('status', 1)->get();
        $subGroups = SubgroupInfo::select('id', 'name')->where('is_active', 1)->get();
        $items = RequisitionItem::select('id', 'product_code', 'product_name')->where('status', 1)->get();

        $customError = 0;
        $dataObj = null;

        //general report
        if ($searchDataForView['report_type'] == "gen_report") {
            if ($this->hasSearchFilters($searchDataForView)) {
                $dataObj = Requisition::baseReportQuery();
            }
        }

        if ($searchDataForView['report_type'] == "history_report") {
            if ($this->hasSearchFilters($searchDataForView)) {
                $dataObj = Requisition::baseHistoryReportQuery();
            }
        }

        //product quantity report
        if ($searchDataForView['report_type'] == "product_quantity") {
            if ($this->hasSearchFilters($searchDataForView)) {
                $dataObj = Requisition::baseProductQtyReportQuery();
            }
        }

        //handler report
        if ($searchDataForView['report_type'] == "handler_report") {
            $userUnits = explode(',', $unitList->unit_id);
            $dataObj = Requisition::baseReportQuery();

            if (in_array(25, $userUnits)) {
                $dataObj = $dataObj->where('requisition.source_unit_id', 25);
                $dataObj = $dataObj->where('requisition.source_sub_group_info_id', $userSubGroup);
                $dataObj = $dataObj->whereIn(
                    "requisition.status",
                    [
                        RequisitionConstants::REQ_PENDING,
                        RequisitionConstants::REQ_WIP,
                        RequisitionConstants::REQ_DRAFT
                    ]
                );
            } elseif (in_array(53, $userUnits)) {
                if (!empty($userRegions)) {
                    $userRegions = explode(',', $userRegions);
                    $dataObj = $dataObj->whereIn('requisition.region_id', $userRegions);
                } else {
                    $dataObj = $dataObj->where('requisition.sub_group_info_id', $userSubGroup);
                }
                $dataObj = $dataObj->where('requisition.unit_id', 53);
                $dataObj = $dataObj->whereNotIn(
                    "requisition.status",
                    [
                        RequisitionConstants::REQ_CLOSE,
                        RequisitionConstants::REQ_DRAFT,
                        RequisitionConstants::REQ_APPROVE,
                        RequisitionConstants::REQ_CANCEL
                    ]
                );
            } elseif (in_array(54, $userUnits)) {
                if (!empty($userRegions)) {
                    $userRegions = explode(',', $userRegions);
                    $dataObj = $dataObj->whereIn('requisition.region_id', $userRegions);
                } else {
                    $dataObj = $dataObj->where('requisition.sub_group_info_id', $userSubGroup);
                }
                $dataObj = $dataObj->where('requisition.unit_id', 54);
                $dataObj = $dataObj->whereNotIn(
                    "requisition.status",
                    [
                        RequisitionConstants::REQ_CLOSE,
                        RequisitionConstants::REQ_DRAFT,
                        RequisitionConstants::REQ_APPROVE,
                        RequisitionConstants::REQ_CANCEL
                    ]
                );
            }
        }


        if (!empty($searchDataForView['requisition_number'])) {
            $dataObj = $dataObj->where('requisition.requisition_number', $searchDataForView['requisition_number']);
        }

        if (!empty($searchDataForView['user_id'])) {
            $dataObj = $dataObj->where('requisition.created_by', $searchDataForView['user_id']);
        }

        if (!empty($searchDataForView['mobile_number'])) {
            $dataObj = $dataObj->where('requisition.i_phone', $searchDataForView['mobile_number']);
        }

        if (!empty($searchDataForView['requisition_status'])) {
            $dataObj = $dataObj->whereIN('requisition.status', $searchDataForView['requisition_status']);
        }

        if (!empty($searchDataForView['requisition_type'])) {
            $dataObj = $dataObj->whereIn('requisition.type', $searchDataForView['requisition_type']);
        }

        if (!empty($searchDataForView['subgroup_id'])) {
            $dataObj = $dataObj->whereIn('requisition.source_sub_group_info_id', $searchDataForView['subgroup_id']);
        }

        if (!empty($searchDataForView['region_id'])) {
            $dataObj = $dataObj->whereIn('requisition.region_id', $searchDataForView['region_id']);
        }

        if (!empty($searchDataForView['vendor_id'])) {
            $dataObj = $dataObj->whereRaw(
                implode(' OR ', array_map(fn($id) => "FIND_IN_SET($id, requisition.vendors_id)", $searchDataForView['vendor_id']))
            );
        }

        if ($searchDataForView['report_type'] == "gen_report") {
            if (!empty($searchDataForView['item_id'])) {
                $dataObj = $dataObj->where(function ($query) use ($searchDataForView) {
                    $query->where('gs_products.id', $searchDataForView['item_id'])
                        ->orWhere('np_products.id', $searchDataForView['item_id'])
                        ->orWhere('p_products.id', $searchDataForView['item_id']);
                });
            }
        }

        if ($searchDataForView['report_type'] == "history_report" && !empty($searchDataForView['item_id'])) {
            $itemIds = $searchDataForView['item_id'];
            if (!is_array($itemIds)) {
                $itemIds = [$itemIds];
            }

            $dataObj = $dataObj->where(function ($query) use ($itemIds) {
                foreach ($itemIds as $id) {
                    if (!empty($id)) {
                        $query->orWhereRaw("FIND_IN_SET(?, gs_sub.gs_product_ids)", [$id])
                            ->orWhereRaw("FIND_IN_SET(?, np_sub.np_product_ids)", [$id])
                            ->orWhereRaw("FIND_IN_SET(?, p_sub.rp_product_ids)", [$id]);
                    }
                }
            });
        }

        if ($searchDataForView['report_type'] == "product_quantity" && !empty($searchDataForView['item_id'])) {
            if (!empty($searchDataForView['item_id'])) {
                $itemIds = $searchDataForView['item_id'];
                if (!is_array($itemIds)) {
                    $itemIds = [$itemIds];
                }

                $dataObj->whereIn('combined_items.product_id', $itemIds);
            }
        }

        if (!empty($searchDataForView['delivery_status'])) {
            if (in_array('delivered', $searchDataForView['delivery_status']) && !in_array('not_delivered', $searchDataForView['delivery_status'])) {
                $dataObj = $dataObj->whereNotNull('requisition.receive_date');
            } elseif (in_array('not_delivered', $searchDataForView['delivery_status']) && !in_array('delivered', $searchDataForView['delivery_status'])) {
                $dataObj = $dataObj->whereNull('requisition.receive_date');
            } elseif (in_array('delivered', $searchDataForView['delivery_status']) && in_array('not_delivered', $searchDataForView['delivery_status'])) {
                // both selected, no filter needed
            }
        }
        if ($searchDataForView['report_type'] == "product_quantity") {

            if ($this->hasSearchFilters($searchDataForView)) {
                if (!empty($searchDataForView['date_from']) || !empty($searchDataForView['date_to'])) {

                    $reference_date_col = "combined_items.date";

                    if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                        $start = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                        $end   = strtotime($searchDataForView['date_from'] . ' 23:59:59');

                        $dataObj = $dataObj->where($reference_date_col, '>=', $start)
                            ->where($reference_date_col, '<=', $end);
                    }

                    if (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                        $start = strtotime($searchDataForView['date_to'] . ' 00:00:00');
                        $end   = strtotime($searchDataForView['date_to'] . ' 23:59:59');

                        $dataObj = $dataObj->where($reference_date_col, '>=', $start)
                            ->where($reference_date_col, '<=', $end);
                    }

                    if (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
                        $start = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                        $end   = strtotime($searchDataForView['date_to'] . ' 23:59:59');

                        $dataObj = $dataObj->whereBetween($reference_date_col, [$start, $end]);
                    }
                }

                if (!empty($searchDataForView['item_id'])) {
                    $itemIds = $searchDataForView['item_id'];
                    if (!is_array($itemIds)) {
                        $itemIds = [$itemIds];
                    }
                    $dataObj = $dataObj->whereIn('combined_items.product_id', $itemIds);
                }
            }
        } else {
            // DATE FILTERS
            if (!empty($searchDataForView['date_from']) || !empty($searchDataForView['date_to'])) {

                $reference_date_col = "requisition.date";

                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    // Only date_from
                    $start = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $end   = strtotime($searchDataForView['date_from'] . ' 23:59:59');

                    $dataObj = $dataObj->where($reference_date_col, '>=', $start)
                        ->where($reference_date_col, '<=', $end);
                }

                if (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    // Only date_to
                    $start = strtotime($searchDataForView['date_to'] . ' 00:00:00');
                    $end   = strtotime($searchDataForView['date_to'] . ' 23:59:59');

                    $dataObj = $dataObj->where($reference_date_col, '>=', $start)
                        ->where($reference_date_col, '<=', $end);
                }

                if (!empty($searchDataForView['date_from']) && !empty($searchDataForView['date_to'])) {
                    // Both
                    $start = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $end   = strtotime($searchDataForView['date_to'] . ' 23:59:59');

                    $dataObj = $dataObj->whereBetween($reference_date_col, [$start, $end]);
                }
            }
        }

        // dd($dataObj->get()->toArray());
        if (!empty($dataObj)) {

            if (empty($request->export)) {
                $dataObj = $dataObj->paginate(PAGINATION_NUMBER);
                // dd($dataObj->toArray());
            } else {
                if ($searchDataForView['report_type'] == "gen_report") {
                    $fileName = 'requisition_general_report_' . date('d_m_Y_H_i_s') . '.xlsx';
                    $viewName = 'requisition::reports.exports.general_report';
                } elseif ($searchDataForView['report_type'] == "handler_report") {
                    $fileName = 'requisition_handler_report_' . date('d_m_Y_H_i_s') . '.xlsx';
                    $viewName = 'requisition::reports.exports.handler_report';
                } elseif ($searchDataForView['report_type'] == "history_report") {
                    $fileName = 'requisition_history_report_' . date('d_m_Y_H_i_s') . '.xlsx';
                    $viewName = 'requisition::reports.exports.history_report';
                } elseif ($searchDataForView['report_type'] == "product_quantity") {
                    $fileName = 'requisition_product_quantity_report_' . date('d_m_Y_H_i_s') . '.xlsx';
                    $viewName = 'requisition::reports.exports.product_qty_report';
                }

                ob_end_clean();
                ob_start();

                return Excel::download(new RequisitionExcelExport($dataObj, $searchDataForView, $viewName), $fileName);
            }
        } else {
            if (!empty($request->report_type)) {
                $customError = 1;
            }
        }

        $msc = microtime(true) - $msc;
        $processing_time = number_format($msc, 2);

        return view('requisition::reports.index', compact(
            'reportType',
            'processing_time',
            'searchDataForView',
            'requisitionType',
            'requisitionStatus',
            'subGroups',
            'customError',
            'dataObj',
            'regions',
            'vendors',
            'items',
        ));
    }

    private function hasSearchFilters($search)
    {
        return !empty($search['requisition_number'])
            || !empty($search['user_id'])
            || !empty($search['mobile_number'])
            || !empty($search['requisition_status'])
            || !empty($search['requisition_type'])
            || !empty($search['subgroup_id'])
            || !empty($search['region_id'])
            || !empty($search['item_id'])
            || !empty($search['delivery_status'])
            || !empty($search['date_from'])
            || !empty($search['date_to']);
    }
}
