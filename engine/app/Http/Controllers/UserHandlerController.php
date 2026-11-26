<?php

/******************************************
 **** This items change For Production ****
 ******************************************

1   ALl CI Group ID chnage To 185


 ****************************************/

namespace App\Http\Controllers;


use App\WForm;
use App\Complaint;
use App\WorkingDay;
use App\NonCustomer;
use App\SubgroupInfo;
use App\Enum\IssueTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserHandlerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|admin|supportExecutive'])->only('handler');

    }

    /************ User Handler Queue Group **********/
    public function userHandler(Request $request)
    {

        $dataForView = array();
        $searchDataForView = $request->all();
        $searchDataForView['reference_number'] = $request->ref_number;
        $searchDataForView['account_number'] = $request->account_number;
        $searchDataForView['date_from'] = $request->date_from;
        $searchDataForView['date_to'] = $request->date_to;
        $searchDataForView['service_category'] = $request->service_category;
        $searchDataForView['service_type'] = $request->service_type;
        $searchDataForView['logger'] = $request->logger;

        if ((!empty($request->active_tab))) {
            $searchDataForView['active_tab'] = $request->active_tab;
        } else {
            $searchDataForView['active_tab'] = 'wform';
        }

        $searchDataForView['cmmn_pgntion'] = (!empty($request->cmmn_pgntion)) ? $request->cmmn_pgntion : 15;
        $searchDataForView['cmmn_search'] = (!empty($request->cmmn_pgntion)) ? $request->cmmn_search : '';

        $tblData = array();

        $wFormData = array();
        $wFormDataObj = array();
        $complaintData = array();
        $complaintDataObj = array();
        $nonCustomerData = array();
        $nonCustomerDataObj = array();

        $unitDeptCondition = array();

        $isAdmin = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";


        $title = "Handler / Queue";
        $title_for_layout = "Handler / Queue";
        if (empty(Auth::user()->user_unit)) {
            $wFormData = array();
            $wFormDataObj = array();
            $complaintData = array();
            $complaintDataObj = array();
            $nonCustomerData = array();
            $workingDays = array();
            $dataForView = array();
            $settingsData = array();
            return view('Supports.handler', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'wFormData', 'complaintData', 'nonCustomerData', 'wFormDataObj', 'complaintDataObj', 'workingDays', 'dataForView', 'settingsData'));
        }
        $mostOldDate = "";

        $todayDate = date('Y-m-d');

        if (Auth::user()->hasRole(['superadmin', 'admin'])) {
            $isAdmin = true;
        } else {
            $unitList = Auth::user()->user_unit;

            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }
            if (!empty($getUnitId)) {
                $getUnitIdArr = explode(',', $getUnitId);
                if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                    if ($get_subgroup_info_id == 386) {
                        $getUnitIdArr = $getUnitIdArr;
                    } else {
                        $getUnitIdArr = array();
                    }
                }
            }
            if (!empty($getGroupId)) {
                $subGroupInfoModel = new SubgroupInfo;
                $subGroupInfoArr = $subGroupInfoModel->where('group_info_id', $getGroupId)->pluck('id', 'id');
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


        }

        $accessUserId = Auth::user()->user_id ?? '';

        if ($searchDataForView['active_tab'] == 'wform') {
            $wformModelName = new WForm;
            $wFormDataObj = $wformModelName
                ->select(
                    "w_form.account_number",
                    "w_form.customer_name",
                    "w_form.product_type",
                    "w_form.time_and_ext",
                    "w_form.reference_number",
                    "w_form.priority",
                    "w_form.product_type as product_type_ext",
                    "w_form.SIF_Number as customer_id",
                    "product_types.name as product_type",
                    "unit_items.name as category_name",
                    "reference.unit_id",
                    "reference.created_by",
                    "reference.segment_priority",
                    "reference.date",
                    "reference.status",
                    "reference.form_status",
                    "reference.access_by",
                    "reference.access_date",
                    "group_info.id as group_info_id",
                    "issue_workflows.flow_type",
                    "issue_group_workflows.sla_maker",
                    "issue_group_workflows.sla_checker",
                    DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")
                )
                ->leftJoin('reference', 'reference.reference_number', '=', 'w_form.reference_number')
                ->leftJoin('group_info', 'group_info.id', '=', 'reference.subgroup_id')
                ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                ->leftJoin('issue_group_workflows', function ($join) {
                    $join->on('issue_group_workflows.group_info_id', '=', 'group_info.id');
                    $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                })

                ->leftJoin('product_types', 'product_types.id', '=', 'w_form.product_type')
                ->leftJoin('unit_items', function ($join) {
                    $join->on('unit_items.master_id', '=', 'w_form.w_form_type');
                    $join->on('unit_items.issues_from', '=', DB::raw("'wform'"));
                })
                ->where('reference.access_by', $accessUserId);

            if (!empty($searchDataForView['reference_number'])) {
                $wFormDataObj = $wFormDataObj->where("w_form.reference_number", $searchDataForView['reference_number']);
            }
            if (!empty($searchDataForView['account_number'])) {
                $wFormDataObj = $wFormDataObj->where("w_form.account_number", $searchDataForView['account_number']);
            }
            if (!empty($searchDataForView['service_type'])) {
                $wFormDataObj = $wFormDataObj->where("reference.issue_id", $searchDataForView['service_type']);
            }
            if (!empty($searchDataForView['logger'])) {
                $wFormDataObj = $wFormDataObj->where("reference.created_by", $searchDataForView['logger']);
            }


            if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                $wFormDataObj = $wFormDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_from'])));
            } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                $wFormDataObj = $wFormDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
            } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {
                $wFormDataObj = $wFormDataObj->where(function ($q) use ($searchDataForView) {
                    $q->where(
                        DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),
                        ">=",
                        date('Y-m-d', strtotime($searchDataForView['date_from']))
                    )
                        ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
                });
            }


            if (!empty($searchDataForView['cmmn_search'])) {
                $statusNumb = array();
                if ($searchDataForView['cmmn_search'] == 'Pending') {
                    $statusNumb = [8, 0, NULL];
                } elseif ($searchDataForView['cmmn_search'] == 'Close') {
                    $statusNumb = [11];
                } elseif ($searchDataForView['cmmn_search'] == 'Reject') {
                    $statusNumb = [-1];
                } elseif ($searchDataForView['cmmn_search'] == 'Hold') {
                    $statusNumb = [10];
                } elseif ($searchDataForView['cmmn_search'] == 'Wip') {
                    $statusNumb = [1, 2, 3, 4, 5, 6, 7, 9];
                }

                $commentWhere = " comments.user_id='" . $searchDataForView['cmmn_search'] . "'";
                if ($isAdmin == false) {
                    if (!empty($searchDataForView['curr_user_id'])) {
                        $commentWhere .= ' AND comments.user_id=' . $searchDataForView['curr_user_id'];
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
                }
                $wFormDataObj = $wFormDataObj
                    ->where(function ($query) use ($searchDataForView, $statusNumb, $commentWhere) {
                        $query
                            ->where("reference.reference_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("w_form.account_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("w_form.customer_name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("product_types.name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("unit_items.name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("w_form.time_and_ext", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhereIn("reference.form_status", $statusNumb)
                            ->orWhere("reference.created_by", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("reference.access_by", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("w_form.SIF_Number", "like", '%' . $searchDataForView['cmmn_search'] . '%');

                    });
            }

            if ($isAdmin == false) {
                if (!empty($getUnitIdArr)) {
                    $wFormDataObj = $wFormDataObj->whereIn("reference.unit_id", $getUnitIdArr);
                }
                $wFormDataObj = $wFormDataObj->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("reference.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });
            }

            $wFormDataObj = $wFormDataObj->where("reference.form_status", "<>", -7);
            $wFormDataObj = $wFormDataObj->where("reference.form_status", "<>", 11);
            if (!empty($_GET['orderby'])) {
                $orderByArr = explode('-', $_GET['orderby']);
                $orderName = (!empty($orderByArr[0])) ? $orderByArr[0] : 'DESC';
                if ($orderByArr[1] == "reference.date=w_form.time_and_ext") {
                    $orderByRefArr = explode('=', $orderByArr[1]);
                    $columnsNameW = (!empty($orderByRefArr[1])) ? $orderByRefArr[1] : 'reference.reference_number';
                    $columnsName = (!empty($orderByRefArr[0])) ? $orderByRefArr[0] : 'reference.reference_number';
                    $wFormDataObj = $wFormDataObj->orderBy("reference.segment_priority", "DESC");
                    $wFormDataObj = $wFormDataObj->orderBy($columnsName, $orderName);
                    $wFormDataObj = $wFormDataObj->orderBy($columnsNameW, $orderName);
                } else {
                    $columnsName = (!empty($orderByArr[1])) ? $orderByArr[1] : 'reference.reference_number';
                    $wFormDataObj = $wFormDataObj->orderBy("reference.segment_priority", "DESC");
                    $wFormDataObj = $wFormDataObj->orderBy($columnsName, $orderName);
                }

            } else {

                $wFormDataObj = $wFormDataObj->orderBy("reference.segment_priority", "DESC")
                    ->orderBy("reference.date", "ASC");
            }

            $wFormDataObj = $wFormDataObj
                ->paginate($searchDataForView['cmmn_pgntion']);

            if (!empty($wFormDataObj)) {
                $wFormData = $wFormDataObj->toArray();
                $lastRecord = end($wFormData['data']);

                if (!empty($lastRecord)) {
                    $mostOldDate = $lastRecord['UNXTIME'];
                }
            }
        } elseif ($searchDataForView['active_tab'] == 'complaint') {
            $complaintModelName = new Complaint;
            $complaintDataObj = $complaintModelName
                ->select(

                    "complaint.account_number",
                    "complaint.customer_name",
                    "complaint.product_type",
                    "complaint.time_and_ext",
                    "complaint.reference_number",
                    "complaint.priority",
                    "complaint.product_type as product_type_ext",
                    "complaint.SIF_Number as customer_id",
                    "product_types.name as product_type",
                    "unit_items.name as issue_name",
                    "reference.unit_id",
                    "reference.created_by",
                    "reference.date",
                    "reference.segment_priority",
                    "reference.status",
                    "reference.segment_priority",
                    "reference.form_status",
                    "reference.access_by",
                    "reference.access_date",
                    "group_info.id as group_info_id",
                    "issue_workflows.flow_type",
                    "issue_group_workflows.sla_maker",
                    "issue_group_workflows.sla_checker",
                    "issue_workflows.complain_sla_time",
                    DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")

                )
                ->leftJoin('reference', 'reference.reference_number', '=', 'complaint.reference_number')
                ->leftJoin('group_info', 'group_info.id', '=', 'reference.subgroup_id')
                ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                ->leftJoin('issue_group_workflows', function ($join) {
                    $join->on('issue_group_workflows.group_info_id', '=', 'group_info.id');
                    $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                })

                ->leftJoin('product_types', 'product_types.id', '=', 'complaint.product_type')
                ->leftJoin('unit_items', function ($join) {
                    $join->on('unit_items.master_id', '=', 'complaint.complaint_type');
                    $join->on('unit_items.issues_from', '=', DB::raw("'complaint'"));
                })
                ->where('reference.access_by', $accessUserId);

            if (!empty($searchDataForView['reference_number'])) {
                $complaintDataObj = $complaintDataObj->where("complaint.reference_number", $searchDataForView['reference_number']);
            }
            if (!empty($searchDataForView['account_number'])) {
                $complaintDataObj = $complaintDataObj->where("complaint.account_number", $searchDataForView['account_number']);
            }
            if (!empty($searchDataForView['service_type'])) {
                $complaintDataObj = $complaintDataObj->where("reference.issue_id", $searchDataForView['service_type']);
            }
            if (!empty($searchDataForView['logger'])) {
                $complaintDataObj = $complaintDataObj->where("reference.created_by", $searchDataForView['logger']);
            }


            if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                $complaintDataObj = $complaintDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_from'])));
            } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                $complaintDataObj = $complaintDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
            } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {

                $complaintDataObj = $complaintDataObj->where(function ($q) use ($searchDataForView) {
                    $q->where(
                        DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),
                        ">=",
                        date('Y-m-d', strtotime($searchDataForView['date_from']))
                    )
                        ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
                });
            } else {

            }

            if (!empty($searchDataForView['cmmn_search'])) {

                $statusNumb = array();
                if ($searchDataForView['cmmn_search'] == 'Pending') {
                    $statusNumb = [8, 0, NULL];
                } elseif ($searchDataForView['cmmn_search'] == 'Close') {
                    $statusNumb = [11];
                } elseif ($searchDataForView['cmmn_search'] == 'Reject') {
                    $statusNumb = [-1];
                } elseif ($searchDataForView['cmmn_search'] == 'Hold') {
                    $statusNumb = [10];
                } elseif ($searchDataForView['cmmn_search'] == 'Wip') {
                    $statusNumb = [1, 2, 3, 4, 5, 6, 7, 9];
                }

                $commentWhere = " comments.user_id='" . $searchDataForView['cmmn_search'] . "'";
                if ($isAdmin == false) {
                    if (!empty($searchDataForView['curr_user_id'])) {
                        $commentWhere .= ' AND comments.user_id=' . $searchDataForView['curr_user_id'];
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
                }

                $complaintDataObj = $complaintDataObj
                    ->where(function ($query) use ($searchDataForView, $statusNumb, $commentWhere) {
                        $query
                            ->where("reference.reference_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("complaint.account_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("complaint.customer_name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("product_types.name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("unit_items.name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("complaint.time_and_ext", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhereIn("reference.form_status", $statusNumb)
                            ->orWhere("reference.created_by", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("reference.access_by", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("complaint.SIF_Number", "like", '%' . $searchDataForView['cmmn_search'] . '%');

                    });
            }

            if ($isAdmin == false) {
                if (!empty($getUnitIdArr)) {
                    $complaintDataObj = $complaintDataObj->whereIn("reference.unit_id", $getUnitIdArr);
                }
                $complaintDataObj = $complaintDataObj->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("reference.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });
            }


            $complaintDataObj = $complaintDataObj->where("reference.form_status", "<>", -7);
            $complaintDataObj = $complaintDataObj->where("reference.form_status", "<>", 11);

            if (!empty($_GET['orderby'])) {
                $orderByArr = explode('-', $_GET['orderby']);
                $orderName = (!empty($orderByArr[0])) ? $orderByArr[0] : 'DESC';

                if ($orderByArr[1] == "reference.date=complaint.time_and_ext") {
                    $orderByRefArr = explode('=', $orderByArr[1]);
                    $columnsNameC = (!empty($orderByRefArr[1])) ? $orderByRefArr[1] : 'reference.reference_number';
                    $columnsName = (!empty($orderByRefArr[0])) ? $orderByRefArr[0] : 'reference.reference_number';
                    $complaintDataObj = $complaintDataObj->orderBy("reference.segment_priority", "DESC");
                    $complaintDataObj = $complaintDataObj->orderBy($columnsName, $orderName);
                    $complaintDataObj = $complaintDataObj->orderBy($columnsNameC, $orderName);
                } else {
                    $columnsName = (!empty($orderByArr[1])) ? $orderByArr[1] : 'reference.reference_number';
                    $complaintDataObj = $complaintDataObj->orderBy("reference.segment_priority", "DESC");
                    $complaintDataObj = $complaintDataObj->orderBy($columnsName, $orderName);
                }
            } else {
                $complaintDataObj = $complaintDataObj->orderBy("reference.segment_priority", "DESC")
                    ->orderBy("reference.date", "ASC");
            }

            $complaintDataObj = $complaintDataObj
                ->paginate($searchDataForView['cmmn_pgntion']);


            if (!empty($complaintDataObj)) {
                $complaintData = $complaintDataObj->toArray();

                $lastRecord = end($complaintData['data']);
                if (!empty($lastRecord)) {
                    $mostOldDate = $lastRecord['UNXTIME'];
                }
            }
        } elseif ($searchDataForView['active_tab'] == IssueTypeEnum::NON_CUSTOMER) {
            $nonCustomerModelName = new NonCustomer();
            $nonCustomerDataObj = $nonCustomerModelName
                ->select(
                    "non_customers.customer_name",
                    "non_customers.mobile_number",
                    "non_customers.customer_email",
                    "non_customers.customer_dob",
                    "non_customers.customer_profession",
                    "non_customers.time_and_ext",
                    "non_customers.reference_number",
                    "reference.unit_id",
                    "reference.created_by",
                    "reference.date",
                    "reference.status",
                    "reference.form_status",
                    "reference.access_by",
                    "reference.access_date",
                    "subgroup_info.group_info_id",
                    "issue_workflows.flow_type",
                    "issue_group_workflows.sla_maker",
                    "issue_group_workflows.sla_checker",
                    "profession.name as customer_profession",
                    DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")

                )

                ->leftJoin('reference', 'reference.reference_number', '=', 'non_customers.reference_number')
                ->leftJoin('profession', 'profession.id', '=', 'non_customers.customer_profession')
                ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.subgroup_id')
                ->leftJoin('issue_workflows', 'issue_workflows.issue_id', '=', 'reference.issue_id')
                ->leftJoin('issue_group_workflows', function ($join) {
                    $join->on('issue_group_workflows.group_info_id', '=', 'subgroup_info.group_info_id');
                    $join->on('issue_group_workflows.issue_workflow_id', '=', 'issue_workflows.issue_workflow_id');
                })
                ->where('reference.access_by', $accessUserId);



            if (!empty($searchDataForView['reference_number'])) {
                $nonCustomerDataObj = $nonCustomerDataObj->where("non_customers.reference_number", $searchDataForView['reference_number']);
            }

            if (!empty($searchDataForView['logger'])) {
                $nonCustomerDataObj = $nonCustomerDataObj->where("reference.created_by", $searchDataForView['logger']);
            }


            if (!empty($searchDataForView['date_from']) && empty($searchDataForView['date_to'])) {
                $nonCustomerDataObj = $nonCustomerDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), ">=", date('Y-m-d', strtotime($searchDataForView['date_from'])));
            } elseif (!empty($searchDataForView['date_to']) && empty($searchDataForView['date_from'])) {
                $nonCustomerDataObj = $nonCustomerDataObj->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
            } elseif (!empty($searchDataForView['date_to']) && !empty($searchDataForView['date_from'])) {

                $nonCustomerDataObj = $nonCustomerDataObj->where(function ($q) use ($searchDataForView) {
                    $q->where(
                        DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"),
                        ">=",
                        date('Y-m-d', strtotime($searchDataForView['date_from']))
                    )
                        ->where(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "<=", date('Y-m-d', strtotime($searchDataForView['date_to'])));
                });
            } else {

            }

            if (!empty($searchDataForView['cmmn_search'])) {
                $statusNumb = array();
                if ($searchDataForView['cmmn_search'] == 'Pending') {
                    $statusNumb = [8, 0, NULL];
                } elseif ($searchDataForView['cmmn_search'] == 'Close') {
                    $statusNumb = [11];
                } elseif ($searchDataForView['cmmn_search'] == 'Reject') {
                    $statusNumb = [-1];
                } elseif ($searchDataForView['cmmn_search'] == 'Hold') {
                    $statusNumb = [10];
                } elseif ($searchDataForView['cmmn_search'] == 'Wip') {
                    $statusNumb = [1, 2, 3, 4, 5, 6, 7, 9];
                }

                $commentWhere = " comments.user_id='" . $searchDataForView['cmmn_search'] . "'";
                if ($isAdmin == false) {
                    if (!empty($searchDataForView['curr_user_id'])) {
                        $commentWhere .= ' AND comments.user_id=' . $searchDataForView['curr_user_id'];
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
                }

                $nonCustomerDataObj = $nonCustomerDataObj
                    ->where(function ($query) use ($searchDataForView, $statusNumb, $commentWhere) {
                        $query
                            ->where("reference.reference_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.customer_name", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.mobile_number", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.customer_email", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.customer_dob", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.customer_profession", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere(DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d')"), "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("non_customers.time_and_ext", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhereIn("reference.form_status", $statusNumb)
                            ->orWhere("reference.created_by", "like", '%' . $searchDataForView['cmmn_search'] . '%')
                            ->orWhere("reference.access_by", "like", '%' . $searchDataForView['cmmn_search'] . '%');
                    });
            }


            if ($isAdmin == false) {
                if (!empty($getUnitIdArr)) {
                    $nonCustomerDataObj = $nonCustomerDataObj->whereIn("reference.unit_id", $getUnitIdArr);
                }
                $nonCustomerDataObj = $nonCustomerDataObj->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("reference.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });
            }

            $nonCustomerDataObj = $nonCustomerDataObj->where("reference.form_status", "<>", -7);
            $nonCustomerDataObj = $nonCustomerDataObj->where("reference.form_status", "<>", 11);

            if (!empty($_GET['orderby'])) {
                $orderByArr = explode('-', $_GET['orderby']);
                $orderName = (!empty($orderByArr[0])) ? $orderByArr[0] : 'DESC';
                $columnsName = (!empty($orderByArr[1])) ? $orderByArr[1] : 'reference.reference_number';

                $nonCustomerDataObj = $nonCustomerDataObj->orderBy($columnsName, $orderName);
            } else {
                $nonCustomerDataObj = $nonCustomerDataObj->orderBy("reference.date", "ASC");
            }

            $nonCustomerDataObj = $nonCustomerDataObj
                ->paginate($searchDataForView['cmmn_pgntion']);


            if (!empty($nonCustomerDataObj)) {
                $nonCustomerData = $nonCustomerDataObj->toArray();

                $lastRecord = end($nonCustomerData['data']);

                if (!empty($lastRecord)) {
                    $mostOldDate = $lastRecord['UNXTIME'];
                }
            }
        }


        $workingDays = array();
        if (!empty($mostOldDate)) {
            $workingDayModel = new WorkingDay;
            $workingDayDataObj = $workingDayModel->where([['dates', '>=', $mostOldDate], ['status', 1]])->pluck('dates', 'dates');
            if (!empty($workingDayDataObj)) {
                $workingDays = $workingDayDataObj->toArray();
            }
        }

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

        $settingsData = DB::table('settings')->first();


        return view('Supports.handler', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'wFormData', 'complaintData', 'nonCustomerData', 'wFormDataObj', 'complaintDataObj', 'workingDays', 'dataForView', 'settingsData'));
    }
}
