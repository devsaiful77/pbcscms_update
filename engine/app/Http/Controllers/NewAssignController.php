<?php

namespace App\Http\Controllers;

use App\Unit;
use App\WForm;
use App\Comment;
use App\Reference;
use App\WFormType;
use Carbon\Carbon;
use App\Attachment;
use App\BranchCode;
use App\Complaint;
use App\IssueConfig;
use App\SubgroupInfo;
use App\IssueCheckListConfig;
use App\User;
use App\UserUnit;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Encryption\DecryptException;

class NewAssignController extends Controller
{
    public function wFormDetails(Request $request, $reference_number)
    {
        // return [
        //     $request->all(),
        //     $reference_number
        // ];

        $reference_number = $reference_number;

        $isAdminOrLogger = false;
        $reportView = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $subGroupInfoArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";

        if (!empty($request->viewFrom)) {
            $reportView = true;
        }

        if (Auth::user()->hasRole(['superadmin', 'admin'])) {
            $isAdminOrLogger = true;
        } else {
            if (Auth::user()->hasRole(['logger'])) {
                $isAdminOrLogger = true;
            }
            $unitList = Auth::user()->user_unit;
            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }
            if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                $getUnitIdArr = array();
            }
            if (!empty($getUnitId)) {
                $getUnitIdArr = explode(',', $getUnitId);
                if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                    $getUnitIdArr = array();
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

        $title = "W-Form Details";
        $title_for_layout = "W-Form Details";

        $wformModelName = new WForm;
        $dataForViewObj = $wformModelName
            ->select(
                // "w_form.*",
                "w_form.reference_number",
                "w_form.account_number",
                "w_form.acc_name",
                "w_form.mobile_number",
                "w_form.email_address",
                "w_form.customer_name",
                "w_form.time_and_ext",
                "w_form.SIF_Number",
                "w_form.customer_nid",
                "w_form.passpor_number",
                "w_form.branch_code",
                "w_form.communication",
                "w_form.segment",
                "w_form.card_status",
                "w_form.date_of_birth",
                "w_form.mask_card_no",
                "w_form.priority",
                "w_form.source",
                "w_form.tin_verified",
                "w_form.static_verified",
                "w_form.dynamic_verified",
                "w_form.notes",
                "w_form.caller_id",
                "w_form.product_type",
                "w_form.product_desc",
                "w_form.account_status",
                "w_form.acc_opening_branch",
                "w_form.w_form_type AS depricate_wform_type",
                "unit_items.name as category_name",
                "unit_items.master_id as master_id",
                "unit_items.id as main_id",
                "cb_unit_items.auto_unit_id as auto_unit_id",
                "reference.created_by",
                "reference.date",
                "reference.is_tara",
                "reference.status",
                "reference.form_status",
                "reference.access_by",
                "reference.subgroup_id",
                "reference.unit_id",
                "reference.api_status",
                "reference.iris_api_status",
                "reference.memo",
                "subgroup_info.name",
                "product_types.name as product_name",
                "unit_items.id as issue_id",
                "unit_items.name as issue_name",
                "unit_items.is_api"
            )
            ->leftJoin('reference', 'reference.reference_number', '=', 'w_form.reference_number')
            ->leftJoin('product_types', 'product_types.id', '=', 'w_form.product_type')
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.sub_group_info_id')
            ->leftJoin('unit_items', function ($join) {
                $join->on('unit_items.master_id', '=', 'w_form.w_form_type');
                $join->on('unit_items.issues_from', '=', DB::raw("'wform'"));
            })
            ->leftJoin('unit_items AS cb_unit_items', function ($join) {
                $join->on('cb_unit_items.master_id', '=', 'w_form.w_form_type');
                $join->on('cb_unit_items.issues_from', '=', DB::raw("'wform'"));
                $join->on('cb_unit_items.unit_id', '=', 'reference.unit_id');
            })

            ->where("w_form.reference_number", $reference_number);

        $dataForViewFind = $dataForViewObj->first();
        //getting the acc opening branch code for the customer
        $company_code = $dataForViewFind->branch_code ?? '';
        $acc_br_code = '';
        if (!empty($company_code)) {
            $br_code = BranchCode::select('mnemonic')
                ->where('company_code', $company_code)
                ->first();

            if (!empty($br_code)) {
                $acc_br_code = $br_code->mnemonic;
            }
        }

        $countIssueLastMonth = WForm::where('SIF_Number', $dataForViewFind->SIF_Number)->where('w_form_type', $dataForViewFind->w_form_type)->where('time_and_ext', '>=', date("d-m-Y H:i:s", strtotime(Carbon::now() . ' - 30 days')))->count();

        if ($isAdminOrLogger == false) {
            if ($reportView == false) {
                if (!empty($getUnitIdArr)) {
                    $dataForViewObj = $dataForViewObj->whereIn("reference.unit_id", [1, 2]); // only checker and maker
                }

                $dataForViewObj = $dataForViewObj->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("reference.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });
            }
        }

        $dataForViewObj = $dataForViewObj->first();
        // dd($dataForViewObj);
        $dataForView = array();
        if (!empty($dataForViewObj)) {
            $dataForView = $dataForViewObj->toArray();
        } else {
            abort(403, 'No Data Found');
        }

        $refNumber = $dataForView['reference_number'];

        $wformTypeData = array();
        $wformTypeModel = new WFormType;
        $wformTypeDataObj = $wformTypeModel
            ->select('id', 'reference_number', 'extra_field', 'check_list')
            ->where('w_form_type.reference_number', 'LIKE', $refNumber)
            ->first();
        if ($wformTypeDataObj) {
            $wformTypeData = $wformTypeDataObj->toArray();
        }
        $dataForView['w_form_type'] = $wformTypeData;

        $commentData = array();
        $commentsModel = new Comment;
        $commentDataObj = $commentsModel
            ->select(
                'comments.reference_number',
                'comments.group_id',
                'comments.subgroup_id',
                'comments.unit_id',
                'comments.user_id',
                'units.name as unit_name'
            )
            ->leftJoin('units', 'units.id', 'comments.unit_id')
            ->where('comments.reference_number', 'LIKE', $refNumber)
            ->get();
        if ($commentDataObj) {
            $commentData = $commentDataObj->toArray();
        }
        $dataForView['comment'] = $commentData;

        $commentInData = array();
        $commentsInModel = new Comment;
        $commentInDataObj = $commentsInModel
            ->select('id', 'reference_number', 'user_id', 'time', 'issendback', 'sendbacksms')
            ->where('comments.reference_number', 'LIKE', $refNumber)
            ->where('isapproved', '1')
            ->orderBy('time', 'DESC')
            ->first();
        if ($commentInDataObj) {
            $commentInData = $commentInDataObj->toArray();
        }
        $dataForView['in_date_time'] = $commentInData;

        $attachmentData = array();
        $attachmentModel = new Attachment;
        $attachmentDataObj = $attachmentModel
            ->select('id', 'file_name', 'reference_number', 'attachment_date', 'uploaded_by', 'created_at', 'updated_at')
            ->where('attachments.reference_number', 'LIKE', $refNumber)
            ->get();
        if ($attachmentDataObj) {
            $attachmentData = $attachmentDataObj->toArray();
        }
        $dataForView['w_form_attachment'] = $attachmentData;



        $unitModelName = new Unit;
        $allUnitData = $unitModelName
            ->select("id", "name")
            ->where("status", "1")
            ->pluck("name", "id")
            ->toArray();

        $dataForView['current_unit'] = "";

        $loggerCanAssign = false;

        if (!empty($dataForView['unit_id'])) {
            if (!empty($getUnitIdArr) && $isAdminOrLogger == true) {

                if (in_array($dataForView['unit_id'], $getUnitIdArr)) {
                    $loggerCanAssign = true;
                }
            }

            $dataForView['current_unit'] = (!empty($allUnitData[$dataForView['unit_id']])) ? $allUnitData[$dataForView['unit_id']] : 'N/A';
            unset($allUnitData[$dataForView['unit_id']]);
        }

        $service_request = Reference::where('reference_number', $reference_number)->first();

        $allmakers = DB::table('comments')
            ->select('comments.group_id', 'group_info.name as g_name', 'group_info.id', 'comments.unit_id', 'comments.reference_number', 'subgroup_info.name', 'comments.subgroup_id')
            ->join('group_info', 'comments.group_id', '=', 'group_info.id')
            ->join('issue_group_workflows', 'issue_group_workflows.group_info_id', '=', 'group_info.id')
            ->join('issue_workflows', 'issue_workflows.issue_workflow_id', '=', 'issue_group_workflows.issue_workflow_id')
            ->leftjoin('subgroup_info', 'comments.subgroup_id', '=', 'subgroup_info.id')
            ->where('comments.reference_number', $reference_number)
            ->where('issue_workflows.issue_id', $service_request->issue_id)
            ->where('comments.unit_id', 1)
            ->distinct('group_info.name')
            ->get();

        $row = \App\IssueWorkflow::where('issue_id', $service_request->issue_id)->first();
        $unit = \App\UserUnit::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        if ($isAdminOrLogger == false) {
            $workflow = \App\IssueGroupWorkflow::where('issue_workflow_id', $row->issue_workflow_id)->where('group_info_id', $unit->subgroup_info_id)->first();
            $lastworkflow = \App\IssueGroupWorkflow::where('issue_workflow_id', $row->issue_workflow_id)->where('is_touch_point', '<>', 1)->orderby('issue_group_workflow_id', 'desc')->first();
            $maker_user = 0;
            $checker_user = 0;
            if ($unit->unit_id == 1) {
                $maker_user = 1;
            } elseif ($unit->unit_id == 2) {
                $checker_user = 1;
            } else {
                $maker_user = 0;
                $checker_user = 0;
            }
            $last_checker = 0;
            $last_maker = 0;
            if (!empty($lastworkflow->touch_cheker) != 0) {
                $last_checker = 1;
            } else {
                $last_maker = 1;
            }
        } else {
            $maker_user = 0;
            $checker_user = 0;
            $last_checker = 0;
            $last_maker = 0;
            $workflow = [];
        }
        $issue_fields = [];
        $check_lists = [];

        $issue_data = IssueConfig::where('issue_id', $service_request->issue_id)->get();
        $check_data = IssueCheckListConfig::where('issue_id', $service_request->issue_id)->get();
        $issue_checklist_status = true;
        if (count($issue_data) == 0 && count($check_data) == 0) {
            $issue_checklist_status = false;
        }
        $cif_workflow = DB::table('cif_workflow')
            ->where('cif_workflow.issue_id', $service_request->issue_id)
            ->where('cif_workflow.status', 1)
            ->first();
        $is_exist = !empty($cif_workflow) ? 1 : 0;

        $allowedUserIds = UserUnit::where('subgroup_info_id', $service_request->sub_group_info_id)
            ->whereRaw("FIND_IN_SET(?, unit_id)", [$service_request->unit_id])
            ->pluck('user_id')
            ->toArray();

        $query = User::query()
            ->whereIn('id', $allowedUserIds)
            ->select(
                'users.id',
                'users.name',
                'users.designation',
                'users.user_id',
                'users.emp_id',
                'users.email',
                'users.mobile_no',
                'users.status',
                'users.remarks',
                'users.created_at',
                DB::raw("CASE
            WHEN users.status = 1 THEN 'Active'
            WHEN users.status = 0 THEN 'Inactive'
            WHEN users.status = -2 THEN 'Close'
            ELSE 'Invalid'
            END AS status_name")
            );

        $userData = $query->with(['roles'])
            ->get();

        $userData = $userData->toArray();
        $qd = $request->qd ?? 0;
        return view(
            'partials.w_form_edit_modal',
            compact(
                'qd',
                'title',
                'is_exist',
                'countIssueLastMonth',
                'title_for_layout',
                'dataForView',
                'allUnitData',
                'loggerCanAssign',
                'isAdminOrLogger',
                'allmakers',
                'maker_user',
                'checker_user',
                'last_checker',
                'last_maker',
                'workflow',
                'issue_fields',
                'check_lists',
                'issue_checklist_status',
                'userData',
            )
        );
    }

    public function complaintDetails(Request $request, $reference_number = "")
    {
        $reference_number = $reference_number;
        // dd($reference_number);
        // try {
        // } catch (DecryptException $e) {
        //     abort(403, 'Un-Authorize Access!!!');
        // }

        $getUnitId = "";
        $getUnitIdArr = array();
        $getSubGroupIdArr = array();
        $subGroupInfoArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";
        $getGroupId = "";
        $get_subgroup_info_id = "";

        $isAdminOrLogger = false;
        $reportView = false;

        if (!empty($request->viewFrom)) {
            $reportView = true;
        }

        if (Auth::user()->hasRole(['superadmin', 'admin'])) {
            $isAdminOrLogger = true;
        } else {
            if (Auth::user()->hasRole(['logger'])) {
                $isAdminOrLogger = true;
            }
            $unitList = Auth::user()->user_unit;
            if (!empty($unitList)) {
                $getUnitId = $unitList->unit_id;
                $getDepartmentId = $unitList->department_id;
                $getDivisionId = $unitList->division_id;
                $get_subgroup_info_id = $unitList->subgroup_info_id;
                $getGroupId = $unitList->group_info_id;
            }
            if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                $getUnitIdArr = array();
            }
            if (!empty($getUnitId)) {
                $getUnitIdArr = explode(',', $getUnitId);
                if ($getUnitId != "1,2" && $getUnitId != "2,1" && $getUnitId != "1" && $getUnitId != "2") {
                    $getUnitIdArr = array();
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

        $title = "Complaint Details";
        $title_for_layout = "Complaint Details";

        $complaintModelName = new Complaint();
        $dataForViewObj = $complaintModelName
            ->select(
                // "complaint.*",
                "complaint.reference_number",
                "complaint.account_number",
                "complaint.acc_name",
                "complaint.mobile_number",
                "complaint.email_address",
                "complaint.customer_name",
                "complaint.time_and_ext",
                "complaint.SIF_Number",
                "complaint.customer_nid",
                "complaint.passpor_number",
                "complaint.branch_code",
                "complaint.communication",
                "complaint.segment",
                "complaint.card_status",
                "complaint.product_desc",
                "complaint.acc_opening_branch",
                "complaint.account_status",
                "complaint.date_of_birth",
                "complaint.mask_card_no",
                "complaint.priority",
                "complaint.source",
                "complaint.tin_verified",
                "complaint.complaint_details",
                "complaint.caller_id",
                "complaint.product_type",
                "complaint.repeat_complaint",
                "reference.created_by",
                "reference.date",
                "reference.status",
                "reference.form_status",
                "reference.access_by",
                "reference.subgroup_id",
                "reference.unit_id",
                "reference.api_status",
                "reference.memo",
                "product_types.name as product_name",
                "cb_unit_items.auto_unit_id as auto_unit_id",
                "unit_items.name as issue_name",
                "unit_items.id as main_id",
                "unit_items.id as issue_id",
                "subgroup_info.name",
                "complaint_form_type.extra_field",
                "complaint_form_type.check_list"
            )
            ->leftJoin('reference', 'reference.reference_number', '=', 'complaint.reference_number')
            ->leftJoin('complaint_form_type', 'complaint_form_type.reference_number', '=', 'complaint.reference_number')
            ->leftJoin('subgroup_info', 'subgroup_info.id', '=', 'reference.sub_group_info_id')
            ->leftJoin('product_types', 'product_types.id', '=', 'complaint.product_type')
            ->leftJoin('unit_items', function ($join) {
                $join->on('unit_items.master_id', '=', 'complaint.complaint_type');
                $join->on('unit_items.issues_from', '=', DB::raw("'complaint'"));
            })
            ->leftJoin('unit_items AS cb_unit_items', function ($join) {
                $join->on('cb_unit_items.master_id', '=', 'complaint.complaint_type');
                $join->on('cb_unit_items.issues_from', '=', DB::raw("'complaint'"));
                $join->on('cb_unit_items.unit_id', '=', 'reference.unit_id');
            })
            // ->leftJoin('unit_items', 'unit_items.master_id', '=', 'complaint.complaint_type')
            ->where("complaint.reference_number", $reference_number);

        if ($isAdminOrLogger == false) {
            if ($reportView == false) {
                if (!empty($getUnitIdArr)) {
                    $dataForViewObj = $dataForViewObj->whereIn("reference.unit_id", $getUnitIdArr);
                }
                $dataForViewObj = $dataForViewObj->where(function ($q) use ($getUnitIdArr, $getGroupId, $get_subgroup_info_id, $getSubGroupIdArr) {
                    $q->where("reference.sub_group_info_id", $get_subgroup_info_id)
                        ->orWhereIn("reference.sub_group_info_id", $getSubGroupIdArr)
                    ;
                });
            }
        }

        $dataForViewObj = $dataForViewObj->first();

        //getting the acc opening branch code for the customer
        $company_code = $dataForViewObj->branch_code ?? '';
        $acc_br_code = '';
        if (!empty($company_code)) {
            $br_code = BranchCode::select('mnemonic')
                ->where('company_code', $company_code)
                ->first();

            if (!empty($br_code)) {
                $acc_br_code = $br_code->mnemonic;
            }
        }

        $dataForView = array();
        if (!empty($dataForViewObj)) {
            $dataForView = $dataForViewObj->toArray();
        } else {
            abort(403, 'No Data Found');
        }

        $refNumber = $dataForView['reference_number'];

        $commentData = array();
        $commentsModel = new Comment;
        $commentDataObj = $commentsModel
            ->select(
                // 'comments.*'
                'comments.reference_number',
                'comments.group_id',
                'comments.subgroup_id',
                'comments.unit_id',
                'comments.user_id',
                'units.name as unit_name'
            )
            ->leftJoin('units', 'units.id', 'comments.unit_id')
            ->where('comments.reference_number', 'LIKE', $refNumber)
            ->get();
        if ($commentDataObj) {
            $commentData = $commentDataObj->toArray();
        }
        $dataForView['comment'] = $commentData;

        $commentInData = array();
        $commentsInModel = new Comment;
        $commentInDataObj = $commentsInModel
            ->select('id', 'reference_number', 'user_id', 'time', 'issendback', 'sendbacksms')
            ->where('comments.reference_number', 'LIKE', $refNumber)
            ->where('isapproved', '1')
            ->orderBy('time', 'DESC')
            ->first();
        if ($commentInDataObj) {
            $commentInData = $commentInDataObj->toArray();
        }
        $dataForView['in_date_time'] = $commentInData;

        $attachmentData = array();
        $attachmentModel = new Attachment;
        $attachmentDataObj = $attachmentModel
            ->select('id', 'file_name', 'reference_number', 'attachment_date', 'uploaded_by', 'created_at', 'updated_at')
            ->where('attachments.reference_number', 'LIKE', $refNumber)
            ->get();
        if ($attachmentDataObj) {
            $attachmentData = $attachmentDataObj->toArray();
        }
        $dataForView['complaint_attachment'] = $attachmentData;

        $unitModelName = new Unit;
        $allUnitData = $unitModelName
            ->select("id", "name")
            ->where("status", "1")
            ->pluck("name", "id")
            ->toArray();

        $loggerCanAssign = false;

        if (!empty($dataForView['unit_id'])) {
            if (!empty($getUnitIdArr) && $isAdminOrLogger == true) {
                if (in_array($dataForView['unit_id'], $getUnitIdArr)) {
                    $loggerCanAssign = true;
                }
            }
            $dataForView['current_unit'] = (!empty($allUnitData[$dataForView['unit_id']])) ? $allUnitData[$dataForView['unit_id']] : 'N/A';
            unset($allUnitData[$dataForView['unit_id']]);
        }

        $complaint = Reference::where('reference_number', $reference_number)->first();

        $allmakers = DB::table('comments')
            ->select('comments.group_id', 'group_info.name as g_name', 'group_info.id', 'comments.unit_id', 'comments.reference_number', 'subgroup_info.name', 'comments.subgroup_id')
            ->join('group_info', 'comments.group_id', '=', 'group_info.id')
            ->join('issue_group_workflows', 'issue_group_workflows.group_info_id', '=', 'group_info.id')
            ->join('issue_workflows', 'issue_workflows.issue_workflow_id', '=', 'issue_group_workflows.issue_workflow_id')
            ->leftjoin('subgroup_info', 'comments.subgroup_id', '=', 'subgroup_info.id')
            ->where('comments.reference_number', $reference_number)
            ->where('issue_workflows.issue_id', $complaint->issue_id)
            ->where('comments.unit_id', 1)
            ->distinct('group_info.name')
            ->get();

        $row = \App\IssueWorkflow::where('issue_id', $complaint->issue_id)->first();
        $unit = \App\UserUnit::where('user_id', \Illuminate\Support\Facades\Auth::id())->first();
        if ($isAdminOrLogger == false) {
            $workflow = \App\IssueGroupWorkflow::where('issue_workflow_id', $row->issue_workflow_id)->where('group_info_id', $unit->subgroup_info_id)->first();
            $lastworkflow = \App\IssueGroupWorkflow::where('issue_workflow_id', $row->issue_workflow_id)->where('is_touch_point', '<>', 1)->orderby('issue_group_workflow_id', 'desc')->first();

            $maker_user = 0;
            $checker_user = 0;
            $unitIdArray = explode(",", $unit->unit_id);
            if (in_array(1, $unitIdArray)) {
                $maker_user = 1;
            } elseif (in_array(2, $unitIdArray)) {
                $checker_user = 1;
            } else {
                $maker_user = 0;
                $checker_user = 0;
            }
            $last_checker = 0;
            $last_maker = 0;
            if (!empty($lastworkflow->touch_cheker) != 0) {
                $last_checker = 1;
            } else {
                $last_maker = 1;
            }
        } else {
            $maker_user = 0;
            $checker_user = 0;
            $last_checker = 0;
            $last_maker = 0;
            $workflow = [];
        }

        $issue_data = IssueConfig::where('issue_id', $complaint->issue_id)->get();
        $check_data = IssueCheckListConfig::where('issue_id', $complaint->issue_id)->get();
        $issue_checklist_status = true;
        if (count($issue_data) == 0 && count($check_data) == 0) {
            $issue_checklist_status = false;
        }
        $cif_workflow = DB::table('cif_workflow')
            ->where('cif_workflow.issue_id', $complaint->issue_id)
            ->where('cif_workflow.status', 1)
            ->first();
        $is_exist = !empty($cif_workflow) ? 1 : 0;

        $allowedUserIds = UserUnit::where('subgroup_info_id', $complaint->sub_group_info_id)
            ->whereRaw("FIND_IN_SET(?, unit_id)", [$complaint->unit_id])
            ->pluck('user_id')
            ->toArray();

        $query = User::query()
            ->whereIn('id', $allowedUserIds)
            ->select(
                'users.id',
                'users.name',
                'users.designation',
                'users.user_id',
                'users.emp_id',
                'users.email',
                'users.mobile_no',
                'users.status',
                'users.remarks',
                'users.created_at',
                DB::raw("CASE
            WHEN users.status = 1 THEN 'Active'
            WHEN users.status = 0 THEN 'Inactive'
            WHEN users.status = -2 THEN 'Close'
            ELSE 'Invalid'
            END AS status_name")
            );

        $userData = $query->with(['roles'])
            ->get();

        $userData = $userData->toArray();


        $qd = $request->qd ?? 0;
        return view('partials.complaint_edit_modal', compact(
            'qd',
            'userData',
            'title',
            'is_exist',
            'title_for_layout',
            'dataForView',
            'allmakers',
            'allUnitData',
            'loggerCanAssign',
            'isAdminOrLogger',
            'workflow',
            'maker_user',
            'checker_user',
            'last_maker',
            'last_checker',
            'issue_checklist_status',
            'acc_br_code',
        ));
    }


    public function newAssign(Request $request, $reference_number = "")
    {
        $request->validate([
            'user_id' => 'required',
        ], [
            'user_id.required' => 'Please select user!',
        ]);

        $encryptedRefNo = $reference_number;
        $reference_number = decrypt($reference_number);;
        $duration_in_minutes = $request->qd;

        $user_id = $request->user_id;
        $subgroup_id = Auth::user()->user_unit->subgroup_info_id;
        $group_info_id = SubgroupInfo::find($subgroup_id);

        $referenceModelName = new Reference;
        $row = $referenceModelName->where("reference_number", $reference_number)->where('access_by', '<>', '')->first();

        if ($row) {
            flash("Ticket No: $reference_number already assigned!!!", 'danger');
            return redirect('Supports/handler');
        }
        $dataForSave = $referenceModelName->where("reference_number", $reference_number)->first();

        if ($dataForSave->subgroup_id <> $group_info_id->group_info_id || $dataForSave->sub_group_info_id <> $subgroup_id || (in_array($dataForSave->unit_id, userUnits()))) {
            flash("Ticket No: $reference_number is not available in your queue!!!", 'danger');
            return redirect('Supports/handler');
        }
        if ($dataForSave->form_status == 11) {
            flash("Ticket No: $reference_number already closed!!!", 'danger');
            return redirect('Supports/handler');
        }

        $dataForSave->form_status = 2;
        $dataForSave->access_by = $user_id;
        $dataForSave->access_date = strtotime(date('Y-m-d H:i:s'));
        if ($dataForSave->save()) {
            $this->audit(['reference_number' => $reference_number, 'unit_id' => $dataForSave->unit_id, 'group_id' => $group_info_id->group_info_id, 'user_id' => $user_id, 'action' => 'Assigned', 'comments' => '', 'duration_in_minutes' => $duration_in_minutes, 'form_load' => $request->st, 'isapproved' => '0', 'subgroup_id' => $subgroup_id]);
            flash('Successfully Assigned', 'success');
        } else {
            flash('Failed to assign', 'danger');
        }

        return back();
    }

    public function audit($params = array())
    {
        $params['duration_in_minutes'] = (!empty($params['duration_in_minutes'])) ? $params['duration_in_minutes'] : 0;

        if (!empty($params['form_load'])) {
            $formLoadTimeObj = new DateTime($params['form_load']);
            $currentTimeObj = new DateTime(date('Y-m-d H:i:s'));
            $interval = $formLoadTimeObj->diff($currentTimeObj);

            $hoursPenalty = $interval->format('%h');
            $minutesPenalty = $interval->format('%i');

            $totalPenalty = ($hoursPenalty * 60) + $minutesPenalty;

            $params['duration_in_minutes'] = $params['duration_in_minutes'] + $totalPenalty;
        }

        $commentModel = new Comment;
        $commentModel->reference_number = (!empty($params['reference_number'])) ? $params['reference_number'] : '';
        $commentModel->comments = (!empty($params['comments'])) ? $params['comments'] : '';
        $commentModel->time = strtotime(date('Y-m-d H:i:s'));
        $commentModel->user_id = (!empty($params['user_id'])) ? $params['user_id'] : 0;
        $commentModel->unit_id = (!empty($params['unit_id'])) ? $params['unit_id'] : 0;
        $commentModel->group_id = (!empty($params['group_id'])) ? $params['group_id'] : 0;
        $commentModel->action = (!empty($params['action'])) ? $params['action'] : 'INVALID';
        $commentModel->duration_in_minutes = $params['duration_in_minutes'];
        $commentModel->isapproved = (!empty($params['isapproved'])) ? $params['isapproved'] : 0;
        $commentModel->issendback = (!empty($params['issendback'])) ? $params['issendback'] : 0;
        $commentModel->subgroup_id = (!empty($params['subgroup_id'])) ? $params['subgroup_id'] : 0;
        $commentModel->ip = $this->getClientIp();
        $commentModel->save();
    }
}
