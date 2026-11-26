<?php

namespace App\Http\Controllers;

use App\BranchCode;
use App\BranchCodeTmp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use App\Http\Requests;
use App\Http\Requests\UnitRequest;
use App\User;

class BranchCodeController extends Controller
{
    public $checker = false;

    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|accessBranchCode|branchCodeChecker']);
        parent::__construct();
    }

    public function branchCodeChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('branchCodeChecker')) {
            return $this->checker = true;
        }
    }

    public function index(Request $request)
    {
        $branchModelName = new BranchCode();

        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();
        $dataObj = $branchModelName
            ->select("id", 'br_code', "company_code", "region", "mnemonic", "branch_name", DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"), "status")
            ->orderBy("id", "DESC")
            ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Branch List";
        $title_for_layout = "Branch List";
        $checker = $this->branchCodeChecker();

        return view('branch-code.index', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'dataObj', 'checker'));
    }
    public function add()
    {
        $checker = $this->branchCodeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();

        $title = "Add Branch Code";
        $title_for_layout = "Add Branch Code";

        return view('branch-code.add', compact('title', 'title_for_layout', 'id', 'dataForView', 'tmpId'));
    }

    public function store(Request $request)
    {

        // $charArr = ['<', '>', '"', "'", '|', '&', ';', '(', ')', '[', ']', '{', '}', '!', '@', '#', '$', '%', '^', '*', '_', '=', '+', '?', '/', ':', '~', '`', ',', '.'];
        $columnArr = ['name'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];

        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);

        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $validator = Validator::make($request->all(), [
            'branch_name' => 'required',
            'br_code' => 'required',
            'company_code' => 'required',
            'region' => 'required',
            'mnemonic' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (\DB::table('branch_codes')->where('branch_name', $request->branch_name)->exists()) {
                $validator->errors()->add('branch_name', 'The name has already been taken in branch_codes table.');
            }

            if (\DB::table('branch_code_tmps')->where('branch_name', $request->branch_name)->exists()) {
                $validator->errors()->add('branch_name', 'The name has already been taken in branch_codes tmp table.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $branchModelName = new BranchCodeTmp();

        if ($request->isMethod('post')) {

            $branchModelName->branch_name = $request->branch_name;
            $branchModelName->br_code = $request->br_code;
            $branchModelName->company_code = $request->company_code;
            $branchModelName->mnemonic = $request->mnemonic;
            $branchModelName->region = $request->region;
            $branchModelName->status = 2;
            $branchModelName->action = "Add";
            $branchModelName->created_by = Auth::user()->id;
            $branchModelName->ip = $this->getClientIp();

            if ($branchModelName->save()) {
                flash('Branch Code has been inserted For Review in Checker', 'success');
                return redirect('branchcode');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('branchcode/add');
            }
        }
    }

    public function edit($id = null, Request $req)
    {
        $checker = $this->branchCodeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $tmpId = 0;
        $branchModelName = new BranchCode();
        $title = " Edit Branch";
        $title_for_layout = 'Edit Branch';
        $searchDataForView = $req->all();


        $dataForView = $branchModelName->where([
            ['id', $id]
        ])->first();
        if ($dataForView->status == 0) {
            abort(403, 'Edit Not Allowed !!!');
        }
        return view('branch-code.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function divisionTmpEdit($id = null, Request $req)
    {
        $checker = $this->branchCodeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $branchModelName = new BranchCodeTmp;
        $title = "Send Back Branch Edit";
        $title_for_layout = 'Edit Branch';
        $searchDataForView = $req->all();
        $tmpId = $id;


        $dataForView = $branchModelName->where([
            ['id', $id]
        ])->first();

        return view('branch-code.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }


    public function update($id = null, Request $request)
    {

        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;

        $columnArr = ['branch_name'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];
        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);
        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $tableName = $isTemp ? 'branch_code_tmps' : 'branch_codes';
        $this->validate($request, [
            'branch_name' => 'required|unique:' . $tableName . ',branch_name,' . $id,
        ]);

        $existingRecords = $isTemp ? BranchCodeTmp::where('id', $id)->get() : BranchCodeTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $data = $request->all();
        $additionalParams = (!empty($data['additionalParams'])) ? $data['additionalParams'] : "";
        unset($data['additionalParams']);

        $data['modified_by'] = Auth::user()->id;

        if ($isTemp) {
            $model = BranchCodeTmp::where('id', $id)->first();
        } else {
            $model = new BranchCodeTmp();
            $update = BranchCode::where('id', $id)->first();
        }

        $model->branch_name = $request->branch_name;
        $model->br_code = $request->br_code;
        $model->company_code = $request->company_code;
        $model->mnemonic = $request->mnemonic;
        $model->region = $request->region;
        $model->description = $request->description;
        $model->status = 2;
        $model->form_status = 0;
        $model->action = "Edit";
        $model->master_id = $isTemp ? $model->master_id : $update->id;
        $model->created_by = Auth::user()->id;
        $model->ip = $this->getClientIp();

        if ($model->save()) {
            flash('Branch Code has been updated for review in Checker', 'success');
            return redirect('/branchcode' . $additionalParams);
        } else {
            flash('Failed to update Branch, Please try again', 'danger');
            return Redirect::back();
        }
    }

    public function status($id = null, $status = "")
    {
        $checker = $this->branchCodeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $branchModelName = new BranchCode();
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $existingTmpRecords = BranchCodeTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $update = $branchModelName->where([['id', $id]])->first();

        $branchModelName = new BranchCodeTmp();
        if ($status == 0) {
            $branchModelName->status = 0;
            $branchModelName->action = "Inactive";
        } else {
            $branchModelName->status = 1;
            $branchModelName->action = "Active";
        }
        $branchModelName->master_id = $update->id;
        $branchModelName->company_code = $update->company_code;
        $branchModelName->branch_name = $update->branch_name;
        $branchModelName->br_code = $update->br_code;
        $branchModelName->region = $update->region;
        $branchModelName->mnemonic = $update->mnemonic;
        $branchModelName->description = $update->description;
        $branchModelName->created_by = Auth::user()->id;
        $branchModelName->ip = $this->getClientIp();

        $branchModelName->save();

        if ($status == 1) {
            flash('Branch Active Request Send to Checker', 'success');
        } elseif ($status == 0) {
            flash('Branch Inactive Request Send to Checker', 'danger');
        }
        // return redirect('/branchcode');
        return Redirect::back();
    }


    public function approveDivision($id)
    {
        $checker = $this->branchCodeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }
        DB::beginTransaction();

        try {
            // $tmpBranch = DB::table('branch_code_tmps')->where('status', '!=', 3)->first();
            $tmpBranch = DB::table('branch_code_tmps')->where('id', $id)->first();

            if (!$tmpBranch) {
                flash('Branch not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            $branchData = [
                'company_code'        => $tmpBranch->company_code,
                'branch_name'        => $tmpBranch->branch_name,
                'br_code' => $tmpBranch->br_code,
                'region' => $tmpBranch->region,
                'mnemonic' => $tmpBranch->mnemonic,
                'status'      => $tmpBranch->status == 2 ? 1 : $tmpBranch->status,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if ($tmpBranch->master_id != null && $tmpBranch->action != "Add") {
                DB::table('branch_codes')->where('id', $tmpBranch->master_id)->update($branchData);
            } else {

                DB::table('branch_codes')->insert($branchData);
            }

            DB::table('branch_code_tmps')->where('id', $tmpBranch->id)->delete();
            DB::commit();
            if ($tmpBranch->status == 0) {
                flash('Branch Code has been inActive.', 'danger');
            } elseif ($tmpBranch->status == 1) {
                flash('Branch Code has been activated.', 'success');
            } else {
                flash('Branch Code has been approved.', 'success');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function deleteTmpData($id, $table)
    {
        $branchModelName = DB::table($table)->where('id', $id)->delete();

        if (str_ends_with($table, '_tmp') || str_ends_with($table, '_tmps')) {
            $table = preg_replace('/_tmps?$/', ' temporary', $table);
        }

        flash($table . ' data has been deleted.', 'danger');
        return redirect()->back();
    }

    public function divisionTmpList()
    {
        $checker = $this->branchCodeChecker();
        if ($checker) {
            $isChecker = true;
            $branchTmpData = BranchCodeTmp::whereNotIn('form_status', [7, -1])->get();
        } else {
            $isChecker = false;
            $branchTmpData = BranchCodeTmp::whereIn('form_status', [7, -1])->get();
        }
        return view('branch-code.index_tmp', compact('branchTmpData', 'isChecker'));
    }

    public function checkerTableData($id)
    {
        $tmpData = BranchCodeTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = BranchCode::findOrFail($tmpData->master_id);
        }

        $tmpData->branch_name = $tmpData->branch_name;
        $tmpData->description = $tmpData->description;
        $tmpData->status = $tmpData->status == 0 ? "Active" : "Inactive";
        $tmpData->action = $tmpData->action;
        $tmpData->created_by = User::where('id', $tmpData->created_by)->first()->user_id;

        if (isset($oldData)) {
            $oldData->branch_name = $oldData->branch_name;
            $oldData->description = $oldData->description;
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
            $oldData->created_by = "N/A";
        }

        $columnsToSend = ['branch_name', 'description', 'status', 'action', 'created_by'];

        $filteredTmpData = $tmpData->only($columnsToSend);

        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function buttonControl($id)
    {
        $tmpData = BranchCodeTmp::findOrFail($id);

        if ($tmpData->form_status == 0) {
            $assign_btn = true;
        } else {
            $assign_btn = false;
        }

        if ($tmpData->modified_by == Auth::user()->user_id) {
            $all_buttons = true;
        } else {
            $all_buttons = false;
        }

        $response = [
            'assign_btn' => $assign_btn,
            'all_buttons' => $all_buttons,
            'modified_by' => $tmpData->modified_by,
        ];

        return response()->json($response, 200);
    }

    public function divisionAssign($id)
    {
        $tmpData = BranchCodeTmp::where('id', $id)->first();

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function divisionSendback(Request $request, $id)
    {
        $checker = $this->branchCodeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = BranchCodeTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function divisionReject(Request $request, $id)
    {
        $checker = $this->branchCodeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = BranchCodeTmp::where('id', $id)->first();
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }

    public function branchcodeSendback(Request $request, $id)
    {
        $checker = $this->branchCodeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = BranchCodeTmp::findOrFail($id);
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }
}
