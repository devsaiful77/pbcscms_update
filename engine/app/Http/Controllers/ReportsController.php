<?php

namespace App\Http\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Requests;

use App\Source;
use App\ProductType;
use App\UnitChild;
use App\UnitItem;
use App\Department;
use App\WForm;
use App\Complaint;
use App\NonCustomer;
use App\SubgroupInfo;


use App\Services\UtilService;

use App\Exports\ExcelExport;

use Excel;
use Carbon;

class ReportsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        // $this->middleware('ability:superadmin|admin,accessReport|true');
        $this->middleware('role_or_permission:superadmin|admin|accessReport|true');
    }

    public function index(Request $request)
    {

        // dd($request->all());
        $msc = microtime(true);
        // DB::enableQueryLog();
        // prd($request->all());
        $title = "Reports";
        $title_for_layout = 'Reports';
        $home_menu_icon = "fa fa-file-text-o";

        $searchDataForView = array();
        $searchDataForView['report_type'] = $request->report_type;
        $searchDataForView['reference_number'] = $request->reference_number;
        $searchDataForView['account_no'] = $request->account_no;
        $searchDataForView['user_id'] = $request->user_id;
        $searchDataForView['curr_user_id'] = $request->curr_user_id;
        $searchDataForView['department_id'] = $request->department_id;
        $searchDataForView['branch_id'] = $request->branch_id;
        $searchDataForView['mobile_number'] = $request->mobile_number;
        $searchDataForView['product_type'] = $request->product_type;
        $searchDataForView['source'] = $request->source;
        $searchDataForView['form_type'] = (!empty($request->form_type)) ? $request->form_type : "wform";
        $searchDataForView['date_from'] = $request->date_from;
        $searchDataForView['date_to'] = $request->date_to;
        $searchDataForView['date_of_birth'] = $request->date_of_birth;
        $searchDataForView['is_justified'] = $request->is_justified;
        $searchDataForView['escalation'] = $request->escalation;
        $searchDataForView['status'] = $request->status;
        $searchDataForView['dms_status'] = $request->dms_status;
        $searchDataForView['apiStatus'] = $request->apiStatus;
        $searchDataForView['SIF_Number'] = $request->SIF_Number;
        $searchDataForView['date_type'] = (!empty($request->date_type)) ? $request->date_type : 'create_date';
        // dd($searchDataForView['date_type']);
        //$searchDataForView['date_type']     = 'create_date' ;
        $searchDataForView['form_category'] = $request->form_category;
        $searchDataForView['export'] = $request->export;

        $searchDataForView = xss_cleaner($searchDataForView);

        if ($searchDataForView['report_type'] == 'usrws_lg_rprt' || $searchDataForView['report_type'] == 'dptws_lg_rprt') {
            $searchDataForView['curr_user_id'] = "";
        } else {
            $searchDataForView['user_id'] = "";
        }

        $updateAPIStatus = array(
            '88' => 'All Success',
            '55' => 'Finacle Success',
            '44' => 'CardPro Success',
            '77' => 'Finacle Failed',
            '66' => 'CardPro Failed',
        );

        $reportType = array(
            'gen_report' => 'General Report',
            // 'dms_report' => 'DMS Report',
            // 'usrws_lg_rprt' => 'User Wise Log Report',
            // 'hstry_report' => 'History Report',
            'sendback_report' => 'Sentback By Others',
            'complaint_closing_report' => 'Complaint Closing',
            'ci_send_back_report' => 'Send-back to customer (CI)',
            'ci_send_back_submit_report' => 'customer re-submit to bank (CI)',
            'ci_summary_report' => 'CI Summary Report',
            'api_update_report' => 'API Update Report',
        );

        $formType = array(
            'wform' => 'Service Request',
            'complaint' => 'Complaint',
            'noncustomer' => 'Non Customer'
        );

        $sourceModelName = new Source;
        $allSourceData = $sourceModelName
            ->select('id', 'source_name')
            ->orderBy('id', 'ASC')
            ->pluck('source_name', 'source_name')
            ->toArray();

        $productTypeModelName = new ProductType;
        $allProductTypeData = $productTypeModelName
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $departmentModelName = new Department;
        $allDepartmentData = $departmentModelName
            ->select("id", "name")
            ->where("status", "1")
            ->orderBy('name', 'ASC')
            ->pluck("name", "id")
            ->toArray();

        $branchModelName = new SubgroupInfo;
        $allBranchData = $branchModelName
            ->select("id", "name")
            ->where("is_active", "1")
            ->orderBy('name', 'ASC')
            ->pluck("name", "id")
            ->toArray();

        $unitItemModelName = new UnitItem;
        $allWformUnitData = $unitItemModelName
            ->select("master_id", "name")
            ->where("status", "1")
            ->where("issues_from", "wform")
            ->orderBy('name', 'ASC')
            ->pluck("name", "master_id")
            ->toArray();

        $unitItemModelName = new UnitItem;
        $allComplaintUnitData = $unitItemModelName
            ->select("master_id", "name")
            ->where("status", "1")
            ->where("issues_from", "complaint")
            ->orderBy('name', 'ASC')
            ->pluck("name", "master_id")
            ->toArray();

        $allStatus = array(
            "99" => "Pending",
            "2" => "Wip",
            // "-1" => "Reject",
            "10" => "Hold",
            "11" => "Close",
            "12" => "Resolved",
            // "" => "All"
        );
        $dmsStatus = array(
            "0" => "Retry Once",
            "1" => "Success",
            "-1" => "Failed",
        );

        //$searchDataForView['issueTypeExcel'] = 0;

        $customError = 0;
        $isAdmin = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $subGroupInfoArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";

        // dd($request->all());

        $isUserAllGet = false;
        if (!empty($searchDataForView['reference_number']) || !empty($searchDataForView['account_no']) || !empty($searchDataForView['user_id']) || !empty($searchDataForView['mobile_number']) || !empty($searchDataForView['SIF_Number'])) {
            $isUserAllGet = true;
        }

        if (Auth::user()->hasRole(['superadmin', 'admin', 'user', 'audit'])) {
            $isAdmin = true;
            $reportType['dptws_lg_rprt'] = 'Department Wise Log Report'; //Inject Department wise log report only for admin
            $reportType['branch_wise_lg_report'] = "Branch Wise Log Report";
        } elseif ($isUserAllGet == true) {
            $reportType['hstry_report'] = 'History Report';
            if ($searchDataForView['report_type'] == 'api_update_report' && (Auth::user()->hasRole(['CEX_ADMIN']) || Auth::user()->user_unit->subgroup_info_id == 16 || Auth::user()->user_unit->subgroup_info_id == 124)) {
                $unitList = [];
            } else {
                $unitList = Auth::user()->user_unit;
            }
            if (!empty($unitList)) {
                $get_subgroup_info_id = $unitList->subgroup_info_id;
            }

            if (!empty($get_subgroup_info_id)) {
                $reportType['hndlr_report'] = 'Handler Report';
            } else {
                $reportType['dptws_lg_rprt'] = 'Department Wise Log Report';
                $reportType['branch_wise_lg_report'] = "Branch Wise Log Report";
            }

            /* Forcing user to get all data after search by specific input field */
        } else {
            $reportType['hstry_report'] = 'History Report';
            if ($searchDataForView['report_type'] == 'api_update_report' && (Auth::user()->hasRole(['CEX_ADMIN']) || Auth::user()->user_unit->subgroup_info_id == 16 || Auth::user()->user_unit->subgroup_info_id == 124)) {
                $unitList = [];
            } else {
                $unitList = Auth::user()->user_unit;
            }
            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }

            if (!empty($get_subgroup_info_id)) {
                $reportType['hndlr_report'] = 'Handler Report';
            } else {
                $reportType['dptws_lg_rprt'] = 'Department Wise Log Report';
                $reportType['branch_wise_lg_report'] = "Branch Wise Log Report";
            }

            if (!empty($getUnitId)) {
                $getUnitIdArr = explode(',', $getUnitId);
                if ($searchDataForView['report_type'] == "hndlr_report") {
                    if (in_array(1, userUnits()) || in_array(2, userUnits())) {
                        //
                    }
                    if (in_array(3, userUnits()) || in_array(4, userUnits()) || in_array(5, userUnits())) {
                        $getUnitIdArr = array(1, 2, 3, 4, 5);
                    }
                } else {
                    if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                        $getUnitIdArr = array();
                    }
                }
                /*$getGroup = SubgroupInfo::find($get_subgroup_info_id); $getGroupId=''; if(!empty($getGroup)){$getGroupId=$getGroup->group_info_id; }*/
            }
            if (!empty($get_subgroup_info_id)) {
                $subGroupInfoModel = new SubgroupInfo;
                if ($searchDataForView['report_type'] == "hndlr_report") {
                    $subGroupInfoArr = $subGroupInfoModel->where('id', $get_subgroup_info_id)->pluck('id', 'id');
                } else {
                    $subGroupInfoArr = $subGroupInfoModel->where('group_info_id', $getGroupId)->pluck('id', 'id');
                }
                if (!empty($subGroupInfoArr)) {
                    $getSubGroupIdArr = $subGroupInfoArr->toArray();;
                }
            }
            if (!empty($getDepartmentId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('department_id', $getDepartmentId)->pluck('id', 'id');
                if (!empty($subGroupInfoArr)) {
                    $tmpGetSubGroupIdArr = $subGroupInfoArr->toArray();
                    $getSubGroupIdArr = array_merge($getSubGroupIdArr, $tmpGetSubGroupIdArr);
                }
            }

            /* if (!empty($getDepartmentId)) {$unitChildModel = new UnitChild; $unitChildArr = $unitChildModel->where('department_id',$getDepartmentId)->pluck('unit_id','unit_id')->toArray(); if (!empty($unitChildArr)) {$getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr); } } */ /*if (!empty($getDivisionId)) {$unitChildModel = new UnitChild; $unitChildArr = $unitChildModel ->select('unit_childs.unit_id') ->leftJoin('departments','departments.id','unit_childs.department_id') ->where('departments.division_id',$getDivisionId) ->pluck('unit_childs.unit_id','unit_childs.unit_id') ->toArray(); if (!empty($unitChildArr)) {$getUnitIdArr = array_merge($getUnitIdArr,$unitChildArr); } }*/
        }

        if (!empty($searchDataForView['department_id']) && $searchDataForView['department_id'] != 'all') {
            $subGroupInfoModel = new SubgroupInfo;
            $subGroupInfoArr = $subGroupInfoModel->where('department_id', $searchDataForView['department_id'])->pluck('id', 'id');
            if (!empty($subGroupInfoArr)) {
                $tmpGetSubGroupIdArr = $subGroupInfoArr->toArray();
                $getSubGroupIdArr = array_merge($getSubGroupIdArr, $tmpGetSubGroupIdArr);
            }
        }
        // searchDataForView['department_id']
        //prd($getSubGroupIdArr);
        $label = array();
        $modelName = array();
        $dataObj = array();
        $dataForView = array();

        $label['searchedFrom'] = "W-Form";
        $label['tableName'] = "w_form";

        // if (!empty($request->all())) {
        if ($searchDataForView['report_type'] == "hndlr_report" || (!empty($searchDataForView['reference_number']) || !empty($searchDataForView['account_no']) || !empty($searchDataForView['mobile_number']) || !empty($searchDataForView['SIF_Number']) || !empty($searchDataForView['date_from']) || !empty($searchDataForView['date_to']))) {
            if (!empty($searchDataForView['form_category']) && count($searchDataForView['form_category']) == 1) {
                $searchDataForView['issueTypeExcel'] = 1;
            } else {
                $searchDataForView['issueTypeExcel'] = 0;
            }
            if ($searchDataForView['form_type'] == 'complaint') {
                $label['searchedFrom'] = "Complaint";
                $label['tableName'] = "complaint";

                $modelName = new Complaint;
                $dataObj = $modelName
                    ->select(
                        "complaint.*",
                        "complaint.product_type as product_type_ext",
                        "complaint.complaint_type as form_type",
                        DB::raw("CASE WHEN complaint.is_justified = 1 THEN 'Yes' WHEN complaint.is_justified = 0 THEN 'No' ELSE 'N/A' END AS is_justified_name"),
                        "product_types.name as product_type_name",
                        "unit_items.name as category_name"
                        // , "reference.reference_number"
                        ,
                        "reference.created_by",
                        "reference.date",
                        "reference.status",
                        "reference.form_status",
                        "reference.api_status",
                        "reference.failed_api",
                        "reference.access_by",
                        "reference.access_date",
                        "subgroup_info.name",
                        "reference.unit_id",
                        "user_subgroup_infos.subgroup_name AS source_maker",
                        "issue_workflows.flow_type",
                        "issue_group_workflows.sla_maker",
                        "issue_group_workflows.sla_checker",
                        "issue_workflows.complain_sla_time",
                        DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME"),
                        DB::raw("FROM_UNIXTIME(reference.access_date,'%Y-%m-%d %r') AS last_access_dt")
                    )
                    ->leftJoin('reference', 'reference.reference_number', '=', 'complaint.reference_number')
                    ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.sub_group_info_id')
                    ->leftJoin('product_types', 'product_types.id', '=', 'complaint.product_type')
                    ->leftJoin('user_subgroup_infos', 'user_subgroup_infos.username', '=', 'reference.created_by')
                    ->leftJoin('unit_items', function ($join) {
                        $join->on('unit_items.master_id', '=', 'complaint.complaint_type');
                        $join->on('unit_items.issues_from', '=', DB::raw("'complaint'"));
                    })
                    ->leftJoin('group_info', 'group_info.id', '=', 'reference.subgroup_id')
                    ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                    ->leftJoin('issue_group_workflows', function ($join) {
                        $join->on('issue_group_workflows.group_info_id', '=', 'group_info.id');
                        $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                    });
            } elseif ($searchDataForView['form_type'] == 'noncustomer') {
                $label['searchedFrom'] = "Non Customer";
                $label['tableName'] = "non_customers";


                $modelName = new NonCustomer;
                $dataObj = $modelName
                    ->select(
                        "non_customers.*",
                        "non_customers.customer_email AS email_address"
                        // , "reference.reference_number"
                        ,
                        "reference.created_by as created_by_name",
                        "reference.date",
                        "reference.status as ref_status",
                        "reference.form_status",
                        "reference.api_status",
                        "reference.failed_api",
                        "reference.access_by",
                        "reference.access_date",
                        "subgroup_info.name",
                        "reference.unit_id",
                        "issue_workflows.flow_type",
                        "issue_group_workflows.sla_maker",
                        "issue_group_workflows.sla_checker",
                        "user_subgroup_infos.subgroup_name AS source_maker",
                        DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME"),
                        DB::raw("FROM_UNIXTIME(reference.access_date,'%Y-%m-%d %r') AS last_access_dt")
                    )
                    ->leftJoin('reference', 'reference.reference_number', '=', 'non_customers.reference_number')
                    ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.sub_group_info_id')
                    ->leftJoin('user_subgroup_infos', 'user_subgroup_infos.username', '=', 'reference.created_by')
                    ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                    ->leftJoin('issue_group_workflows', function ($join) {
                        $join->on('issue_group_workflows.group_info_id', '=', 'subgroup_info.group_info_id');
                        $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                    });
            } else {
                $modelName = new WForm;
                $dataObj = $modelName
                    ->select(
                        "w_form.*",
                        "w_form.product_type as product_type_ext",
                        "w_form.w_form_type as form_type",
                        "product_types.name as product_type_name",
                        "unit_items.name as category_name"
                        //, "reference.reference_number"
                        ,
                        "reference.created_by",
                        "reference.date",
                        "reference.status",
                        "reference.form_status",
                        "reference.api_status",
                        "reference.failed_api",
                        "reference.access_by",
                        "reference.access_date",
                        "subgroup_info.name",
                        "reference.unit_id",
                        "user_subgroup_infos.subgroup_name AS source_maker",
                        "issue_workflows.flow_type",
                        "issue_group_workflows.sla_maker",
                        "issue_group_workflows.sla_checker",
                        DB::raw("FROM_UNIXTIME(reference.date,'%d-%m-%Y') AS UNXTIME"),
                        DB::raw("FROM_UNIXTIME(reference.access_date,'%d-%m-%Y %r') AS last_access_dt")
                    );

                $dataObj = $dataObj
                    ->leftJoin('reference', 'reference.reference_number', '=', 'w_form.reference_number')
                    ->leftJoin('user_subgroup_infos', 'user_subgroup_infos.username', '=', 'reference.created_by')
                    ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.sub_group_info_id')
                    ->leftJoin('product_types', 'product_types.id', '=', 'w_form.product_type')
                    ->leftJoin('unit_items', function ($join) {
                        $join->on('unit_items.master_id', '=', 'w_form.w_form_type');
                        $join->on('unit_items.issues_from', '=', DB::raw("'wform'"));
                    })
                    ->leftJoin('group_info', 'group_info.id', '=', 'reference.subgroup_id')
                    ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                    ->leftJoin('issue_group_workflows', function ($join) {
                        $join->on('issue_group_workflows.group_info_id', '=', 'group_info.id');
                        $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                    });
            }

            if ($searchDataForView['report_type'] == "hndlr_report" || $searchDataForView['report_type'] == "gen_report") {
                if ($searchDataForView['form_type'] == 'complaint') {
                    $dataObj = $dataObj->addSelect('complaint_form_type.extra_field');

                    $dataObj = $dataObj->leftJoin('complaint_form_type', 'complaint_form_type.reference_number', '=', 'reference.reference_number');
                } elseif ($searchDataForView['form_type'] == 'noncustomer') {
                    /*********** N/A *****************/
                } else {
                    $dataObj = $dataObj->addSelect('w_form_type.extra_field');
                    $dataObj = $dataObj->leftJoin('w_form_type', 'w_form_type.reference_number', '=', 'reference.reference_number');
                }
            }

            /*Zihad 6-4-2023*/
            if ($searchDataForView['report_type'] == "dms_report") {
                $dataObj = $dataObj->addSelect('dms_attach_mapping.attachment_id');
                $dataObj = $dataObj->join('dms_attach_mapping', 'dms_attach_mapping.reference_number', '=', 'reference.reference_number');
                if ($searchDataForView["dms_status"] != null) {
                    $dataObj = $dataObj->where('dms_attach_mapping.status', '=', $searchDataForView['dms_status']);
                }
                $dataObj = $dataObj->addSelect('attachments.file_name');
                $dataObj = $dataObj->leftJoin('attachments', 'attachments.id', 'dms_attach_mapping.attachment_id');
                /* Mahdi 24 May 2023 */
                $reference_date_col = "reference.date";
                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    $dataObj = $dataObj
                        ->where($reference_date_col, ">=", strtotime($searchDataForView['date_from'] . ' 00:00:00'))
                        ->where($reference_date_col, "<=", strtotime($searchDataForView['date_from'] . ' 23:59:59'));
                    $searchDataForView['date_to'] = date('d-m-Y', strtotime($searchDataForView['date_from']));
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    $dataObj = $dataObj
                        ->where($reference_date_col, ">=", strtotime($searchDataForView['date_to'] . ' 00:00:00'))
                        ->where($reference_date_col, "<=", strtotime($searchDataForView['date_to'] . ' 23:59:59'));
                    $searchDataForView['date_from'] = date('d-m-Y', strtotime($searchDataForView['date_to']));
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    $dataObj = $dataObj->where(function ($q) use ($searchDataForView, $reference_date_col) {
                        $q->where($reference_date_col, ">=", strtotime($searchDataForView['date_from'] . ' 00:00:00'))
                            ->where($reference_date_col, "<=", strtotime($searchDataForView['date_to'] . ' 23:59:59'));
                    });
                }
            }

            if (!empty($searchDataForView['product_type'])) {
                if ($label['tableName'] != 'non_customers') {
                    $dataObj = $dataObj->where($label['tableName'] . '.product_type', $searchDataForView['product_type']);
                }
            }

            if ($searchDataForView['form_type'] == 'complaint') {
                if (!empty($searchDataForView['form_category'])) {
                    $advSearch['form_category'] = $searchDataForView['form_category'];
                    $advSearch['table_name'] = 'complaint.complaint_type';
                    $dataObj = $dataObj->where(function ($q) use ($advSearch) {
                        $q->whereIn($advSearch['table_name'], $advSearch['form_category']);
                    });
                    // $dataObj = $dataObj->whereIn('complaint.complaint_type',$searchDataForView['form_category']);
                }
                if (!empty($searchDataForView['account_no'])) {
                    $dataObj = $dataObj->where(function ($query) use ($searchDataForView) {
                        $query->where('complaint.account_number', $searchDataForView['account_no'])
                            ->orWhere('complaint.SIF_Number', $searchDataForView['account_no']);
                    });
                }
                if (!empty($searchDataForView['mobile_number'])) {
                    $dataObj = $dataObj->where('complaint.mobile_number', "like", '%' . $searchDataForView['mobile_number'] . '%');
                }
                if (!empty($searchDataForView['SIF_Number'])) {
                    $dataObj = $dataObj->where('complaint.SIF_Number', $searchDataForView['SIF_Number']);
                }
                if (!empty($searchDataForView['source'])) {
                    $dataObj = $dataObj->where('complaint.source', "like", '%' . $searchDataForView['source'] . '%');
                }
                if (isset($searchDataForView['is_justified'])) {
                    if ($searchDataForView['is_justified'] != "") {
                        $dataObj = $dataObj->where('complaint.is_justified', $searchDataForView['is_justified']);
                    }
                }

                if (!empty($searchDataForView['date_of_birth'])) {
                    $dataObj = $dataObj->where('complaint.date_of_birth', date('Y-m-d', strtotime($searchDataForView['date_of_birth'])));
                }
            } elseif ($searchDataForView['form_type'] == 'wform') {
                if (!empty($searchDataForView['form_category'])) {
                    $advSearch['form_category'] = $searchDataForView['form_category'];
                    $advSearch['table_name'] = 'w_form.w_form_type';
                    $dataObj = $dataObj->where(function ($q) use ($advSearch) {
                        $q->whereIn($advSearch['table_name'], $advSearch['form_category']);
                    });
                    // $dataObj = $dataObj->whereIn('w_form.w_form_type',$searchDataForView['form_category']);
                }
                if (!empty($searchDataForView['account_no'])) {
                    $dataObj = $dataObj->where(function ($query) use ($searchDataForView) {
                        $query->where('w_form.account_number', $searchDataForView['account_no'])
                            ->orWhere('w_form.SIF_number', $searchDataForView['account_no']);
                    });
                }
                if (!empty($searchDataForView['mobile_number'])) {
                    $dataObj = $dataObj->where('w_form.mobile_number', "like", '%' . $searchDataForView['mobile_number'] . '%');
                }
                if (!empty($searchDataForView['SIF_Number'])) {
                    $dataObj = $dataObj->where('w_form.SIF_Number', $searchDataForView['SIF_Number']);
                }
                if (!empty($searchDataForView['source'])) {
                    $dataObj = $dataObj->where('w_form.source', "like", '%' . $searchDataForView['source'] . '%');
                }

                if (!empty($searchDataForView['date_of_birth'])) {
                    $dataObj = $dataObj->where('w_form.date_of_birth', date('Y-m-d', strtotime($searchDataForView['date_of_birth'])));
                }
            }
            $reference_date_col = "";

            $reference_date_col = "reference.date";
            $comments_date_col = "comments.time";
            if ($searchDataForView['report_type'] == 'hndlr_report') {
                $searchDataForView['date_type'] = 'create_date';
            }
            // strtotime("3 October 2005")
            if ($searchDataForView['report_type'] != "hndlr_report" && $searchDataForView['report_type'] != 'sendback_report' && $searchDataForView['report_type'] != 'ci_send_back_report' && $searchDataForView['report_type'] != 'ci_send_back_submit_report' && $searchDataForView['report_type'] != 'ci_summary_report' && $searchDataForView['date_type'] == 'create_date' && $isUserAllGet == false) {
                if (!empty($searchDataForView['curr_user_id'])) {
                    $dataObj = $dataObj->where('reference.created_by', $searchDataForView['curr_user_id']);
                }
            }
            if ($searchDataForView['report_type'] != "hndlr_report" && $searchDataForView['report_type'] != "dms_report" && $searchDataForView['report_type'] != 'sendback_report' && $searchDataForView['report_type'] != 'ci_send_back_report' && $searchDataForView['report_type'] != 'ci_send_back_submit_report' && $searchDataForView['report_type'] != 'ci_summary_report' && $searchDataForView['date_type'] == 'create_date' && $isUserAllGet == false) {
                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    /*
                    $dataObj = $dataObj
                        ->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_from'])))
                        ->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_from'])));
                    */
                    $dataObj = $dataObj
                        ->where($reference_date_col, ">=", strtotime($searchDataForView['date_from'] . ' 00:00:00'))
                        ->where($reference_date_col, "<=", strtotime($searchDataForView['date_from'] . ' 23:59:59'));

                    $searchDataForView['date_to'] = date('d-m-Y', strtotime($searchDataForView['date_from']));
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    /*
                    $dataObj = $dataObj
                        ->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_to'])))
                        ->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
                    */
                    $dataObj = $dataObj
                        ->where($reference_date_col, ">=", strtotime($searchDataForView['date_to'] . ' 00:00:00'))
                        ->where($reference_date_col, "<=", strtotime($searchDataForView['date_to'] . ' 23:59:59'));

                    $searchDataForView['date_from'] = date('d-m-Y', strtotime($searchDataForView['date_to']));
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    /*
                    $dataObj = $dataObj->where(function ($q) use ($searchDataForView, $reference_date_col, $comments_date_col) {
                        $q->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_from'])))
                            ->where(DB::raw("FROM_UNIXTIME($reference_date_col,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
                    });
                    */
                    $dataObj = $dataObj->where(function ($q) use ($searchDataForView, $reference_date_col, $comments_date_col) {
                        $q->where($reference_date_col, ">=", strtotime($searchDataForView['date_from'] . ' 00:00:00'))
                            ->where($reference_date_col, "<=", strtotime($searchDataForView['date_to'] . ' 23:59:59'));
                    });
                } else {
                    // $dataObj = $dataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),">=", date('Y-m-d'));
                }
            }

            if (!empty($searchDataForView['reference_number'])) {
                $dataObj = $dataObj->where('reference.reference_number', $searchDataForView['reference_number']);
            }
            if (!empty($searchDataForView['user_id'])) {
                $dataObj = $dataObj->where('reference.created_by', $searchDataForView['user_id']);
            }
            if (!empty($getSubGroupIdArr) && $searchDataForView['report_type'] != "hndlr_report") {
                $dataObj = $dataObj->join('comments', 'reference.reference_number', '=', 'comments.reference_number');
                $dataObj = $dataObj->groupBy('reference.reference_number');
                $dataObj = $dataObj->whereIn('comments.subgroup_id', $getSubGroupIdArr);
            }

            if ($searchDataForView['report_type'] == 'sendback_report') {
                $commentWhere = ' 1';

                if (!empty($getUnitIdArr)) {
                    $getUnitIdArrStr = implode(',', $getUnitIdArr);
                    $commentWhere .= ' AND comments.unit_id in (' . $getUnitIdArrStr . ')';
                }
                if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                    $getSubGroupIdArrStr = implode(',', $getSubGroupIdArr);
                    if ((!empty($getSubGroupIdArr) && !empty($get_subgroup_info_id))) {
                        $commentWhere .= ' AND (comments.subgroup_id=' . $get_subgroup_info_id . ' OR comments.subgroup_id IN (' . $getSubGroupIdArrStr . '))';
                    } elseif (!empty($getSubGroupIdArr)) {
                        $commentWhere .= ' AND comments.subgroup_id IN (' . $getSubGroupIdArrStr . ')';
                    } elseif (!empty($get_subgroup_info_id)) {
                        $commentWhere .= ' AND comments.subgroup_id=' . $get_subgroup_info_id;
                    }
                }
                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $commentWhere .= " AND comments.time >= '$dateFromUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ";
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND comments.time <= '$dateToUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND ( FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ) ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND ( comments.time >= '$dateFromUnix' AND comments.time <= '$dateToUnix' ) ";
                }

                if (!empty($searchDataForView['status'])) {
                    if (in_array(99, $searchDataForView['status'])) {
                        array_push($searchDataForView['status'], 0);
                    }
                    $dataObj = $dataObj->whereIn('reference.form_status', $searchDataForView['status']);
                }

                $commentWhere .= ' AND comments.issendback = 1';

                $commentsRefData = Comment::select('reference_number')
                    ->whereRaw($commentWhere)
                    ->pluck('reference_number', 'reference_number');
                $dataObj = $dataObj->whereIn('reference.reference_number', $commentsRefData);
                /*$dataObj = $dataObj->whereRaw(
                    "reference.reference_number IN ( SELECT comments.reference_number FROM comments WHERE $commentWhere )");*/
            } elseif ($searchDataForView['report_type'] == "complaint_closing_report") {
                $dataObj = $dataObj->leftJoin('complaint_closings', 'complaint.reference_number', '=', 'complaint_closings.reference_number')
                    ->addSelect(
                        "complaint_closings.natureofcomp",
                        "complaint_closings.amountinvoled",
                        "complaint_closings.rootcause",
                        "complaint_closings.actiontaken",
                        "complaint_closings.fi_id"
                    )
                    ->where('reference.form_status', 11);
            }

            // CI Part Reports
            elseif ($searchDataForView['report_type'] == 'ci_send_back_submit_report') {
                $dataObj = $dataObj->where('reference.created_by', 'CI');
                // $dataObj = $dataObj->where('reference.form_status', 7); Sentback Ticket re-submitted
                $commentWhere = '';

                if (!empty($getUnitIdArr)) {
                    $getUnitIdArrStr = implode(',', $getUnitIdArr);
                    $getUnitIdArrStr = "1";
                    $commentWhere .= ' comments.unit_id in (' . $getUnitIdArrStr . ') AND';
                }

                $commentWhere .= " comments.subgroup_id = 0 AND comments.group_id = 0";
                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $commentWhere .= " AND comments.time >= '$dateFromUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND comments.time <= '$dateToUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND ( comments.time >= '$dateFromUnix' AND comments.time <= '$dateToUnix' ) ";
                }

                if (!empty($searchDataForView['status'])) {
                    if (in_array(99, $searchDataForView['status'])) {
                        array_push($searchDataForView['status'], 0);
                    }
                    $dataObj = $dataObj->whereIn('reference.form_status', $searchDataForView['status']);
                }

                $commentWhere .= "AND comments.issendback = 0";

                $commentsRefData = Comment::select('reference_number')
                    ->whereRaw($commentWhere)
                    ->pluck('reference_number', 'reference_number');
                $dataObj = $dataObj->whereIn('reference.reference_number', $commentsRefData);
            } elseif ($searchDataForView['report_type'] == 'ci_send_back_report') {
                $dataObj = $dataObj->where('reference.created_by', 'CI');
                $dataObj = $dataObj->where('reference.form_status', 7);
                $commentWhere = ' 1';

                if (!empty($getUnitIdArr)) {
                    $getUnitIdArrStr = implode(',', $getUnitIdArr);
                    $getUnitIdArrStr = "1";
                    $commentWhere .= ' AND comments.unit_id in (' . $getUnitIdArrStr . ')';
                }

                // dd($getUnitIdArr, $getUnitIdArrStr, $commentWhere);
                if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                    $getSubGroupIdArrStr = implode(',', $getSubGroupIdArr);
                    if ((!empty($getSubGroupIdArr) && !empty($get_subgroup_info_id))) {
                        $commentWhere .= ' AND (comments.subgroup_id=' . $get_subgroup_info_id . ' OR comments.subgroup_id IN (' . $getSubGroupIdArrStr . '))';
                    } elseif (!empty($getSubGroupIdArr)) {
                        $commentWhere .= ' AND comments.subgroup_id IN (' . $getSubGroupIdArrStr . ')';
                    } elseif (!empty($get_subgroup_info_id)) {
                        $commentWhere .= ' AND comments.subgroup_id=' . $get_subgroup_info_id;
                    }
                }
                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $commentWhere .= " AND comments.time >= '$dateFromUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ";
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND comments.time <= '$dateToUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND ( FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ) ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND ( comments.time >= '$dateFromUnix' AND comments.time <= '$dateToUnix' ) ";
                }

                if (!empty($searchDataForView['status'])) {
                    if (in_array(99, $searchDataForView['status'])) {
                        array_push($searchDataForView['status'], 0);
                    }
                    $dataObj = $dataObj->whereIn('reference.form_status', $searchDataForView['status']);
                }

                /* $commentWhere .= ' AND comments.issendback = 1';*/

                $commentsRefData = Comment::select('reference_number')
                    ->whereRaw($commentWhere)
                    ->pluck('reference_number', 'reference_number');
                $dataObj = $dataObj->whereIn('reference.reference_number', $commentsRefData);
                /*$dataObj = $dataObj->whereRaw(
                    "reference.reference_number IN ( SELECT comments.reference_number FROM comments WHERE $commentWhere )");*/
            } elseif ($searchDataForView['report_type'] == 'ci_summary_report') {
                $dataObj = $dataObj->where('reference.created_by', 'CI');
                $dataObj = $dataObj->addSelect('w_form_type.extra_field');
                $dataObj = $dataObj->leftJoin('w_form_type', 'w_form_type.reference_number', '=', 'reference.reference_number');
            } elseif ($searchDataForView['report_type'] == 'api_update_report') {
            } elseif ($searchDataForView['report_type'] == 'branch_wise_lg_report') {
                if (!empty($searchDataForView['branch_id'])) {
                    $dataObj = $dataObj->where('reference.sub_group_info_id', $searchDataForView['branch_id']);
                }
            } else {
                if (!empty($searchDataForView['status'])) {
                    if (in_array(99, $searchDataForView['status'])) {
                        array_push($searchDataForView['status'], 0);
                    }
                    $dataObj = $dataObj->whereIn('reference.form_status', $searchDataForView['status']);
                }
            }

            if ($isAdmin == false && $isUserAllGet == false && ($searchDataForView['date_type'] != 'action_date')) {
                if ($searchDataForView['report_type'] == "hndlr_report") {
                    $dataObj = $dataObj->where(function ($q) use ($get_subgroup_info_id, $getUnitIdArr) {
                        $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                            ->whereIn("reference.unit_id", $getUnitIdArr)
                            ->where("reference.form_status", "<>", 11)
                            ->where("reference.form_status", "<>", -7);
                    });
                }
            }
            if ($searchDataForView['report_type'] == 'api_update_report') {
                $dataObj = $dataObj->addSelect('cif_request_response.requested_by as updated_by', 'cif_request_response.status_msg', 'cif_request_response.type as cif_type');
                $dataObj = $dataObj->leftJoin('cif_request_response', 'reference.reference_number', '=', 'cif_request_response.reference_number');
                $dataObj = $dataObj->where("cif_request_response.type", "=", '2');
                $dataObj = $dataObj->groupBy('reference.reference_number');
                $dataObj = $dataObj->addSelect('w_form_type.extra_field');
                $dataObj = $dataObj->leftJoin('w_form_type', 'w_form_type.reference_number', '=', 'reference.reference_number');

                if (!empty($searchDataForView['apiStatus'])) {
                    if (in_array(88, $searchDataForView['apiStatus'])) {
                        /*  All Success  */
                        $dataObj = $dataObj->where("reference.api_status", "=", 1);
                    } elseif (in_array(55, $searchDataForView['apiStatus'])) {
                        /*  Finacle UAT API = 8 Success  */
                        $dataObj = $dataObj->where("reference.failed_api", "not like", '%8%');
                    } elseif (in_array(44, $searchDataForView['apiStatus'])) {
                        /*  CardPro UAT API = 13,14 Success  */
                        $dataObj = $dataObj->where("reference.failed_api", "not like", '%13%');
                        $dataObj = $dataObj->orWhere("reference.failed_api", "not like", '%14%');
                    } elseif (in_array(77, $searchDataForView['apiStatus'])) {
                        /*  Finacle UAT API = 8 Failed  */
                        $dataObj = $dataObj->where("reference.failed_api", "like", '%8%');
                    } elseif (in_array(66, $searchDataForView['apiStatus'])) {
                        /*  CardPro UAT API = 13,14 Failed  */
                        $dataObj = $dataObj->where("reference.failed_api", "like", '%13%');
                        $dataObj = $dataObj->orWhere("reference.failed_api", "like", '%14%');
                    }
                }
                if (!empty($searchDataForView['user_id'])) {
                    $dataObj = $dataObj->where('cif_request_response.requested_by', $searchDataForView['user_id']);
                }
                if (!empty($searchDataForView['reference_number'])) {
                    $dataObj = $dataObj->where('reference.reference_number', $searchDataForView['reference_number']);
                }
                if (!empty($searchDataForView['account_no'])) {
                    $dataObj = $dataObj->where('w_form.account_number', $searchDataForView['account_no']);
                }
                if (!empty($searchDataForView['mobile_number'])) {
                    $dataObj = $dataObj->where('w_form.mobile_number', "like", '%' . $searchDataForView['mobile_number'] . '%');
                }
                if (!empty($searchDataForView['SIF_Number'])) {
                    $dataObj = $dataObj->where('w_form.SIF_Number', $searchDataForView['SIF_Number']);
                }
                if (!empty($searchDataForView['source'])) {
                    $dataObj = $dataObj->where('w_form.source', "like", '%' . $searchDataForView['source'] . '%');
                }
            }
            if ($searchDataForView['report_type'] != 'hndlr_report' && $searchDataForView['report_type'] != 'complaint_closing_report' && $searchDataForView['report_type'] != 'dms_report' && $isUserAllGet == false) {
                $commentWhere = ' 1';
                if (!empty($searchDataForView['curr_user_id'])) {
                    $commentWhere .= " AND comments.user_id='" . $searchDataForView['curr_user_id'] . "'";
                }
                if (!empty($getUnitIdArr)) {
                    $getUnitIdArrStr = implode(',', $getUnitIdArr);
                    $commentWhere .= ' AND comments.unit_id in (' . $getUnitIdArrStr . ')';
                }
                if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                    $getSubGroupIdArrStr = implode(',', $getSubGroupIdArr);
                    if ((!empty($getSubGroupIdArr) && !empty($get_subgroup_info_id))) {
                        $commentWhere .= ' AND (comments.subgroup_id=' . $get_subgroup_info_id . ' OR comments.subgroup_id IN (' . $getSubGroupIdArrStr . '))';
                    } elseif (!empty($getSubGroupIdArr)) {
                        $commentWhere .= ' AND comments.subgroup_id IN (' . $getSubGroupIdArrStr . ')';
                    } elseif (!empty($get_subgroup_info_id)) {
                        $commentWhere .= ' AND comments.subgroup_id=' . $get_subgroup_info_id;
                    }
                }

                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $commentWhere .= " AND comments.time >= '$dateFromUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ";
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND comments.time <= '$dateToUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND ( FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ) ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND ( comments.time >= '$dateFromUnix' AND comments.time <= '$dateToUnix' ) ";
                }
                // dd("asjl",$dataObj->get());

                // $dataObj = $dataObj->whereRaw(
                //     "reference.reference_number IN ( SELECT comments.reference_number FROM comments WHERE $commentWhere )");
                // dd($dataObj->get());
                $commentsRefData = Comment::select('reference_number')
                    ->whereRaw($commentWhere)
                    ->pluck('reference_number', 'reference_number');
                $dataObj = $dataObj->whereIn('reference.reference_number', $commentsRefData);
            } elseif ($searchDataForView['report_type'] != 'hndlr_report' && $searchDataForView['report_type'] == 'complaint_closing_report' && $searchDataForView['report_type'] != 'dms_report' && $isUserAllGet == false) {
                $commentWhere = ' 1';
                if (!empty($searchDataForView['curr_user_id'])) {
                    $commentWhere .= " AND comments.user_id='" . $searchDataForView['curr_user_id'] . "'";
                }
                if (!empty($getUnitIdArr)) {
                    $getUnitIdArrStr = implode(',', $getUnitIdArr);
                    $commentWhere .= ' AND comments.unit_id in (' . $getUnitIdArrStr . ')';
                }
                if (!empty($getSubGroupIdArr) || !empty($get_subgroup_info_id)) {
                    $getSubGroupIdArrStr = implode(',', $getSubGroupIdArr);
                    if ((!empty($getSubGroupIdArr) && !empty($get_subgroup_info_id))) {
                        $commentWhere .= ' AND (comments.subgroup_id=' . $get_subgroup_info_id . ' OR comments.subgroup_id IN (' . $getSubGroupIdArrStr . '))';
                    } elseif (!empty($getSubGroupIdArr)) {
                        $commentWhere .= ' AND comments.subgroup_id IN (' . $getSubGroupIdArrStr . ')';
                    } elseif (!empty($get_subgroup_info_id)) {
                        $commentWhere .= ' AND comments.subgroup_id=' . $get_subgroup_info_id;
                    }
                }

                if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $commentWhere .= " AND comments.time >= '$dateFromUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ";
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND comments.time <= '$dateToUnix' ";
                } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                    // $dateFromUnix = date('Y-m-d', strtotime($searchDataForView['date_from']));
                    // $dateToUnix = date('Y-m-d', strtotime($searchDataForView['date_to']));
                    // $commentWhere .= " AND ( FROM_UNIXTIME(comments.time,'%Y-%m-%d') >= '$dateFromUnix' AND FROM_UNIXTIME(comments.time,'%Y-%m-%d') <= '$dateToUnix' ) ";
                    $dateFromUnix = strtotime($searchDataForView['date_from'] . ' 00:00:00');
                    $dateToUnix = strtotime($searchDataForView['date_to'] . ' 23:59:59');
                    $commentWhere .= " AND ( comments.time >= '$dateFromUnix' AND comments.time <= '$dateToUnix' ) ";
                }
                // dd("asjl",$dataObj->get());

                // $dataObj = $dataObj->whereRaw(
                //     "reference.reference_number IN ( SELECT comments.reference_number FROM comments WHERE $commentWhere )");
                // dd($dataObj->get());
                // $commentsRefData = Comment::select('reference_number')
                //     ->whereRaw($commentWhere)
                //     ->pluck('reference_number','reference_number');
                // $dataObj = $dataObj->whereIn('reference.reference_number', $commentsRefData);

            }



            // $dataObj = $dataObj->groupBy("reference.reference_number");
            // $dataObj = $dataObj->orderBy("reference.reference_number","DESC");
            if (!empty($dataObj)) {
                // dd($dataObj->get());
                if (empty($request->export)) {
                    // ini_set('memory_limit', '1024M');
                    $dataObj = $dataObj->where('reference.form_status', '<>', -7)
                        ->paginate(PAGINATION_NUMBER);
                    //->get();
                } else {

                    /*$dataObj = $dataObj->chunk(200, function ($dataForExcel) use ($searchDataForView,$fileName) {
                        foreach ($dataForExcel as $key=> $data) {
                            $dataArr = $data->toArray();
                            $formatedArr =  $this->genReportExcel($dataArr,$searchDataForView);
                            if ($key == 0) {
                                $dataStr = implode(',',$formatedArr[0]);
                                $dataStr = $dataStr.'\n';
                            }
                            $dataStr = implode(',',$formatedArr[1]);
                            $dataStr = $dataStr.'\n';

                            Storage::disk('local')->append($fileName, $dataStr);
                        }
                    });*/


                    // fclose($dataFile);

                    // excel_gen_report.blade
                    // return (new ExcelExport($dataObj))->download('invoices.xlsx');
                    // 'Reports.export.excel_gen_report'

                    // branch_wise_lg_report
                    // dptws_lg_rprt

                    if (empty($searchDataForView['report_type']) || ($searchDataForView['report_type'] == 'gen_report')) {
                        $fileName = "General-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_gen_report';
                        // } elseif ($searchDataForView['report_type'] == 'usrws_lg_rprt') {
                        // $viewName = 'Reports.export.excel_usrws_lg_rprt';
                    } elseif ($searchDataForView['report_type'] == 'hstry_report') {
                        $fileName = "History-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_hstry_report';
                    } elseif ($searchDataForView['report_type'] == 'complaint_closing_report') {
                        $fileName = "Complaint-Closing-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_report_compclose';
                    } elseif ($searchDataForView['report_type'] == 'sendback_report') {
                        $fileName = "Sendback-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_sendback_report';
                    } elseif ($searchDataForView['report_type'] == 'ci_send_back_report') {
                        $fileName = "Send-back-to-customer-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_ci_send_back_report';
                    } elseif ($searchDataForView['report_type'] == 'ci_send_back_submit_report') {
                        $fileName = "Customer-re-submit-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_ci_send_back_submit_report';
                    } elseif ($searchDataForView['report_type'] == 'ci_summary_report') {
                        $fileName = "CI-Summary-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_ci_summary_report';
                    } elseif ($searchDataForView['report_type'] == 'dms_report') {
                        $fileName = "DMS-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_dms_report';
                    } elseif ($searchDataForView['report_type'] == 'dptws_lg_rprt') {
                        $fileName = "Dept-wise-log-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_usrws_lg_rprt';
                    } elseif ($searchDataForView['report_type'] == 'branch_wise_lg_report') {
                        $fileName = "Branch-wise-log-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_usrws_lg_rprt';
                    } elseif ($searchDataForView['report_type'] == 'hndlr_report') {
                        $fileName = "Handler-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_hndlr_report';
                    } elseif ($searchDataForView['report_type'] == 'api_update_report') {
                        $fileName = "API-Update-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_api_update_report';
                    } else {
                        $fileName = "General-Report-" . date("Y-m-d_His") . ".xlsx";
                        $viewName = 'Reports.export.excel_gen_report';
                    }

                    ob_end_clean(); // this
                    ob_start(); // and this
                    // ini_set('memory_limit', '1024M');
                    // dd($dataObj->get());
                    $dataObj = $dataObj->where('reference.form_status', '<>', -7);
                    if ($searchDataForView['report_type'] == 'hstry_report') {
                        $dataObj = $dataObj->limit(20000);
                    }

                    // dd($dataObj, $searchDataForView, $viewName, $fileName);

                    return Excel::download(new ExcelExport($dataObj, $searchDataForView, $viewName), $fileName);
                    // (new ExcelExport($dataObj,$searchDataForView,$viewName))->queue($fileName);

                    // return redirect('Reports');
                }
            }
        } else {
            if (!empty($request->report_type)) {
                $customError = 1;
            }
        }

        if (!empty($dataObj)) {
            $dataForView = $dataObj->toArray();
        }
        $settingsData = DB::table('settings')->first();

        $workingHours = DB::table('working_hours')->first();
        $workingHours = json_decode(json_encode($workingHours), true);

        if (!empty($workingHours['office_from'])) {
            $dataForView['office_from'] = $workingHours['office_from'];
            $dataForView['office_from_str'] = substr($dataForView['office_from'], 0, 2) . ':' . substr($dataForView['office_from'], 2, 2) . ':' . substr($dataForView['office_from'], 4, 2);
        } else {
            $dataForView['office_from'] = '100000';
            $dataForView['office_from_str'] = '10:00:00';
        }
        if (!empty($workingHours['office_to'])) {
            $dataForView['office_to'] = $workingHours['office_to'];
            $dataForView['office_to_str'] = substr($dataForView['office_to'], 0, 2) . ':' . substr($dataForView['office_to'], 2, 2) . ':' . substr($dataForView['office_to'], 4, 2);
        } else {
            $dataForView['office_to'] = '180000';
            $dataForView['office_to_str'] = '18:00:00';
        }
        // pr(userUnits());
        // pr($getUnitIdArr);
        // pr($get_subgroup_info_id);
        // prd($getSubGroupIdArr);
        // $query = DB::getQueryLog();
        // pr($query);
        // prd($get_subgroup_info_id);
        // prd($dataForView);
        $msc = microtime(true) - $msc;
        $processing_time = number_format($msc, 2);

        if (Auth::user()->hasRole(['CEX_ADMIN', 'user'])) {
            unset($reportType['hstry_report']);
            unset($reportType['dptws_lg_rprt']);
            unset($reportType['ci_send_back_report']);
            unset($reportType['ci_send_back_submit_report']);
            unset($reportType['ci_summary_report']);
            unset($reportType['api_update_report']);
        }

        return view('Reports.index', compact(
            'title',
            'title_for_layout',
            'updateAPIStatus',
            'home_menu_icon',
            'label',
            'allSourceData',
            'allDepartmentData',
            'allBranchData',
            'allProductTypeData',
            'allStatus',
            'dmsStatus',
            'searchDataForView',
            'dataObj',
            'dataForView',
            'allWformUnitData',
            'allComplaintUnitData',
            'reportType',
            'formType',
            'isAdmin',
            'isUserAllGet',
            'customError',
            'settingsData',
            'processing_time'
        ));
    }

    public function workingStatusReport(Request $request)
    {
        $viewer = DB::table('comments')
            ->select('action', DB::raw('count(*) as total'))
            ->groupBy('action')
            ->get()->toArray();

        // $viewer = array_column($commentList, 'action');
        // dd($viewer);

        $title = "Reports";
        $title_for_layout = 'Reports';
        return view('Reports.workingStatus', compact('title', 'title_for_layout', 'viewer')); //->with('viewer',json_encode($viewer,JSON_NUMERIC_CHECK))
    }

    public function complainPendingReport(Request $request)
    {
        $jsonData = array(
            array("Pending", 20.42),
            array("InProgress", 14.66),
            array("OnHold", 2.09),
            array("Complete", 62.30),
            array("Deferred", 0.00),
            array("Cancelled", 0.52),
        );
        $title = "complainPendingReports";
        $title_for_layout = 'complainPendingReports';
        return view('Reports.complainPendingReport', compact('title', 'title_for_layout', 'jsonData'));
    }

    public function dashboardReport(Request $request)
    {
        $jsonData = array(
            array("Pending", 20.42),
            array("InProgress", 14.66),
            array("OnHold", 2.09),
            array("Complete", 62.30),
            array("Deferred", 0.00),
            array("Cancelled", 0.52),
        );
        $title = "complainPendingReports";
        $title_for_layout = 'complainPendingReports';
        return view('Reports.dashboardReport', compact('title', 'title_for_layout', 'jsonData'));
    }

    public function genReportExcel($data = array(), $searchDataForView = array())
    {
        $returnArr = array();

        $getCommentInfo = getCommentsActionTimeAndCloseComment($data['reference_number']);
        $data['action_time'] = $getCommentInfo['action_time'];
        $data['close_comments'] = $getCommentInfo['close_comments'];

        $stime = date('Y-m-d H:i:s', (int)$data['date']);
        $etime = ($data['form_status'] == 11) ? date('Y-m-d H:i:s', (int)$data['access_date']) : date('Y-m-d H:i:s');

        $utilServices = new UtilService();

        $duration = $utilServices->queueDurationCalculator($stime, $etime);
        $smsEmailStatus = $utilServices->getMailSMSStatus($data['reference_number']);

        $form_status = $data['form_status'];

        if ($form_status == 8 || $form_status == 0 || $form_status == null) {
            $status = "Pending";
        } elseif ($form_status == 2) {
            $status = "Wip";
        } else if ($form_status == 11) {
            $status = "Close";
        } else if ($form_status == 10) {
            $status = "Hold";
        } else {
            $status = "Wip";
        }

        $returnArr[0][] = 'Ticket Number';
        $returnArr[0][] = 'Card / Acc Number';
        $returnArr[0][] = 'Customer Name';
        $returnArr[0][] = 'Product Type';
        if ($searchDataForView["form_type"] == 'wform') {
            $returnArr[0][] = 'Service Request Type';
        } else {
            $returnArr[0][] = 'Complaint Type';
        }

        $returnArr[0][] = 'Maker';
        $returnArr[0][] = 'Action Date';
        $returnArr[0][] = 'Action Time';
        $returnArr[0][] = 'Current Position';
        $returnArr[0][] = 'TaT(H:M:S)';
        $returnArr[0][] = 'Segment Code';
        $returnArr[0][] = 'Priority';

        if ($searchDataForView["form_type"] == 'complaint'):
            $returnArr[0][] = 'Is Justified';
        endif;

        $returnArr[0][] = 'Status';
        $returnArr[0][] = 'Close Date';
        $returnArr[0][] = 'Send SMS?';
        $returnArr[0][] = 'Send Mail?';
        $returnArr[0][] = 'Card / Acc Number';
        $returnArr[0][] = 'Customer Name';
        $returnArr[0][] = 'Mobile Number';
        $returnArr[0][] = 'Customer Email';
        $returnArr[0][] = 'Product Type';
        $returnArr[0][] = 'Time &amp; Ext';
        $returnArr[0][] = 'CIF Number';
        $returnArr[0][] = 'Customer DOB';
        $returnArr[0][] = 'Segment Code';

        if ($searchDataForView["form_type"] == 'wform'):
            $returnArr[0][] = 'Notes';
            $returnArr[0][] = 'Service Request Type';
        elseif ($searchDataForView["form_type"] == 'complaint'):
            $returnArr[0][] = 'Complaint Details';
            $returnArr[0][] = 'Complaint Type';
        endif;

        $returnArr[0][] = 'Close Comments';
        $returnArr[0][] = 'AF1';
        $returnArr[0][] = 'AF2';
        $returnArr[0][] = 'AF3';
        $returnArr[0][] = 'AF4';
        $returnArr[0][] = 'AF5';
        $returnArr[0][] = 'AF6';
        $returnArr[0][] = 'AF7';
        $returnArr[0][] = 'AF8';
        $returnArr[0][] = 'AF9';
        $returnArr[0][] = 'AF10';
        $returnArr[0][] = 'AF11';
        $returnArr[0][] = 'AF12';
        $returnArr[0][] = 'AF13';


        $returnArr[1][] = $data['reference_number'];

        if ($searchDataForView["form_type"] == 'wform' || $searchDataForView["form_type"] == 'complaint') {
            $returnArr[1][] = $data['account_number'];
        } else {
            $returnArr[1][] = 'N/A';
        }

        $returnArr[1][] = htmlentities($data['customer_name']);

        if ($searchDataForView["form_type"] != 'noncustomer') {
            $returnArr[1][] = (!empty($data['product_type'])) ? htmlentities($data['product_type']) : htmlentities($data['product_type_ext']);
            $returnArr[1][] = (!empty($data['category_name'])) ? htmlentities($data['category_name']) : htmlentities($data['form_type']);
        } else {
            $returnArr[1][] = 'N/A';
            $returnArr[1][] = 'N/A';
        }

        $returnArr[1][] = htmlentities($data['created_by']) . ' ' . htmlentities($data['source_maker']);

        $returnArr[1][] = \Carbon\Carbon::createFromTimestamp($data['action_time'])->format('d-m-Y');
        $returnArr[1][] = \Carbon\Carbon::createFromTimestamp($data['action_time'])->format('H:i:s');

        if ($data['form_status'] == 11):
            $makerCheckerLebel = ($data['unit_id'] == 1) ? 'Maker' : 'Checker';
            $returnArr[1][] = 'Form Close at' . htmlentities($data['name']) . ' ' . '[' . $makerCheckerLebel . ']';
        else:
            $makerCheckerLebel = ($data['unit_id'] == 1) ? 'Maker' : 'Checker';
            $returnArr[1][] = htmlentities($data['name']) . ' ' . '[' . $makerCheckerLebel . ']';
        endif;

        $returnArr[1][] = $duration;
        if ($searchDataForView["form_type"] != 'noncustomer') {
            $returnArr[1][] = htmlentities($data['segment']);
            $returnArr[1][] = htmlentities($data['priority']);
            if ($searchDataForView["form_type"] == 'complaint') {
                $returnArr[1][] = htmlentities($data['is_justified_name']);
            }
        } else {
            $returnArr[1][] = '';
            $returnArr[1][] = '';
        }

        $returnArr[1][] = htmlentities($status);
        $returnArr[1][] = ($data['form_status'] == 11) ? $data['last_access_dt'] : '';
        $returnArr[1][] = $smsEmailStatus['is_send_sms'];
        $returnArr[1][] = $smsEmailStatus['is_send_mail'];

        if ($searchDataForView["form_type"] == 'wform' || $searchDataForView["form_type"] == 'complaint') {
            $returnArr[1][] = $data['account_number'];
        } else {
            $returnArr[1][] = 'N/A';
        }
        $returnArr[1][] = htmlentities($data['customer_name']);
        $returnArr[1][] = htmlentities($data['mobile_number']);
        $returnArr[1][] = htmlentities($data['email_address']);
        if ($searchDataForView["form_type"] != 'noncustomer') {
            $returnArr[1][] = (!empty($data['product_type'])) ? $data['product_type'] : $data['product_type_ext'];
        } else {
            $returnArr[1][] = 'N/A';
        }

        $returnArr[1][] = $data['time_and_ext'];
        $returnArr[1][] = $data['SIF_Number'];
        $returnArr[1][] = $data['date_of_birth'];

        if ($searchDataForView["form_type"] != 'noncustomer'):
            $returnArr[1][] = htmlentities($data['segment']);
        else:
            $returnArr[1][] = '';
        endif;

        if ($searchDataForView["form_type"] == 'wform'):
            $returnArr[1][] = $data['notes'];
            $returnArr[1][] = (!empty($data['category_name'])) ? $data['category_name'] : $data['form_type'];
        elseif ($searchDataForView["form_type"] == 'complaint'):
            $returnArr[1][] = $data['complaint_details'];
            $returnArr[1][] = (!empty($data['category_name'])) ? $data['category_name'] : $data['form_type'];
        else:
            $returnArr[1][] = '';
            $returnArr[1][] = '';
        endif;

        if ($data['form_status'] == 11):
            $returnArr[1][] = (!empty($data['close_comments'])) ? $data['close_comments'] : '';
        else:
            $returnArr[1][] = '';
        endif;

        $extraField = $data['extra_field'];
        $extraFieldArr = array();
        if (!empty($extraField)) {
            $extraFieldArr = json_decode($extraField, true);
        }

        if (!empty($extraFieldArr)):
            foreach ($extraFieldArr as $extFld):
                $returnArr[1][] = key($extFld) . ':' . current($extFld);
            endforeach;
        endif;

        return $returnArr;
    }
}
