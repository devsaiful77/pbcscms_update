<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\TreasuryRequest;
use App\TreasuryBondTmp;
use App\TreasuryBond;
use App\User;

class TreasuryBondController extends Controller
{
    public $checker = false;

    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|accessTreasuryBond|accessTreasuryBondChecker']);
        parent::__construct();
    }

    public function accessTreasuryBondChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('accessTreasuryBondChecker')) {
            return $this->checker = true;
        }
    }

    public function index(Request $request)
    {
        $divisionModelName = new TreasuryBond();

        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();
        $dataObj = $divisionModelName
            ->select("id", "name", "week", "count", DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"), "status")
            ->orderBy("id", "DESC")
            ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Treasury Bond List";
        $title_for_layout = "Treasury Bond List";
        $checker = $this->accessTreasuryBondChecker();

        return view('TreasuryBond.index', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'dataObj', 'checker'));
    }
    public function add()
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();

        $title = "Add Treasury";
        $title_for_layout = "Add Treasury";

        return view('TreasuryBond.add', compact('title', 'title_for_layout', 'id', 'dataForView', 'tmpId'));
    }

    public function store(TreasuryRequest $request)
    {

        // $charArr = ['<', '>', '"', "'", '|', '&', ';', '(', ')', '[', ']', '{', '}', '!', '@', '#', '$', '%', '^', '*', '_', '=', '+', '?', '/', ':', '~', '`', ',', '.'];
        $columnArr = ['name'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];

        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);

        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (\DB::table('treasury_bonds')->where('name', $request->name)->exists()) {
                $validator->errors()->add('name', 'The name has already been taken in treasury bond table.');
            }

            if (\DB::table('treasury_bond_tmp')->where('name', $request->name)->exists()) {
                $validator->errors()->add('name', 'The name has already been taken in treasury bond tmp table.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $divisionModelName = new TreasuryBondTmp();

        if ($request->isMethod('post')) {

            $divisionModelName->name = $request->name;
            $divisionModelName->week = $request->week;
            $divisionModelName->count = $request->count;
            $divisionModelName->status = 2;
            $divisionModelName->action = "Add";
            $divisionModelName->created_by = Auth::user()->id;
            $divisionModelName->ip = $this->getClientIp();

            if ($divisionModelName->save()) {
                flash('Treasury has been inserted For Review in Checker', 'success');
                return redirect('treasury-bond');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('treasury-bond/add');
            }
        }
    }

    public function edit($id = null, Request $req)
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $tmpId = 0;
        $divisionModelName = new TreasuryBond;
        $title = " Edit Treasury Bond";
        $title_for_layout = 'Edit Treasury Bond';
        $searchDataForView = $req->all();


        $dataForView = $divisionModelName->where([
            ['id', $id]
        ])->first();
        if ($dataForView->status == 0) {
            abort(403, 'Edit Not Allowed !!!');
        }
        return view('TreasuryBond.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function divisionTmpEdit($id = null, Request $req)
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $divisionModelName = new TreasuryBondTmp;
        $title = "Send Back Treasury Bond Edit";
        $title_for_layout = 'Edit Treasury Bond';
        $searchDataForView = $req->all();
        $tmpId = $id;


        $dataForView = $divisionModelName->where([
            ['id', $id]
        ])->first();

        return view('TreasuryBond.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }


    public function update($id = null, TreasuryRequest $request)
    {

        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;

        $columnArr = ['name'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];
        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);
        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $tableName = $isTemp ? 'treasury_bond_tmp' : 'divisions';
        $this->validate($request, [
            'name' => 'required|unique:' . $tableName . ',name,' . $id,
        ]);

        $existingRecords = $isTemp ? TreasuryBondTmp::where('id', $id)->get() : TreasuryBondTmp::where('master_id', $id)->get();

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
            $model = TreasuryBondTmp::where('id', $id)->first();
        } else {
            $model = new TreasuryBondTmp();
            $update = TreasuryBond::where('id', $id)->first();
        }

        $model->name = $request->name;
        $model->week = $request->week;
        $model->count = $request->count;
        $model->status = 2;
        $model->form_status = 0;
        $model->action = "Edit";
        $model->master_id = $isTemp ? $model->master_id : $update->id;
        $model->created_by = Auth::user()->id;
        $model->ip = $this->getClientIp();

        if ($model->save()) {
            flash('Treasury Bond has been updated for review in Checker', 'success');
            return redirect('/treasury-bond' . $additionalParams);
        } else {
            flash('Failed to update Treasury Bond, Please try again', 'danger');
            return Redirect::back();
        }
    }

    public function status($id = null, $status = "")
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $divisionModelName = new TreasuryBond;
        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $existingTmpRecords = TreasuryBondTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $update = $divisionModelName->where([['id', $id]])->first();

        $divisionModelName = new TreasuryBondTmp();
        if ($status == 0) {
            $divisionModelName->status = 0;
            $divisionModelName->action = "Inactive";
        } else {
            $divisionModelName->status = 1;
            $divisionModelName->action = "Active";
        }
        $divisionModelName->master_id = $update->id;
        $divisionModelName->name = $update->name;
        $divisionModelName->week = $update->week;
        $divisionModelName->count = $update->count;
        $divisionModelName->created_by = Auth::user()->id;
        $divisionModelName->ip = $this->getClientIp();

        $divisionModelName->save();

        if ($status == 1) {
            flash('Treasury Bond Active Request Send to Checker', 'success');
        } elseif ($status == 0) {
            flash('Treasury Bond Inactive Request Send to Checker', 'danger');
        }
        return Redirect::back();
    }


    public function approveDivision($id)
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }
        DB::beginTransaction();

        try {
            $tmpDivision = DB::table('treasury_bond_tmp')->where('id', $id)->first();

            if (!$tmpDivision) {
                flash('Treasury not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            $divisionData = [
                'name'        => $tmpDivision->name,
                'week'    => $tmpDivision->week,
                'count'  => $tmpDivision->count,
                'status'      => $tmpDivision->status == 2 ? 1 : $tmpDivision->status,
                'created_by'  => Auth::user()->id,
                'ip'          => $this->getClientIp(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if ($tmpDivision->master_id != null && $tmpDivision->action != "Add") {
                DB::table('treasury_bonds')->where('id', $tmpDivision->master_id)->update($divisionData);
            } else {

                DB::table('treasury_bonds')->insert($divisionData);
            }

            DB::table('treasury_bond_tmp')->where('id', $tmpDivision->id)->delete();
            DB::commit();
            if ($tmpDivision->status == 0) {
                flash('Treasury Bond has been inActive.', 'danger');
            } elseif ($tmpDivision->status == 1) {
                flash('Treasury Bond has been activated.', 'success');
            } else {
                flash('Treasury Bond has been approved.', 'success');
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
        $divisionModelName = DB::table($table)->where('id', $id)->delete();

        if (str_ends_with($table, '_tmp') || str_ends_with($table, '_tmps')) {
            $table = preg_replace('/_tmps?$/', ' temporary', $table);
        }

        flash($table . ' data has been deleted.', 'danger');
        return redirect()->back();
    }

    public function divisionTmpList()
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker) {
            $isChecker = true;
            $divisionTmpData = TreasuryBondTmp::whereNotIn('form_status', [7, -1])->get();
        } else {
            $isChecker = false;
            $divisionTmpData = TreasuryBondTmp::whereIn('form_status', [7, -1])->get();
        }
        return view('TreasuryBond.index_tmp', compact('divisionTmpData', 'isChecker'));
    }

    public function checkerTableData($id)
    {
        $tmpData = TreasuryBondTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = TreasuryBond::findOrFail($tmpData->master_id);
        }

        $tmpData->name = $tmpData->name;
        $tmpData->week = $tmpData->week;
        $tmpData->count = $tmpData->count;
        $tmpData->status = $tmpData->status == 0 ? "Active" : "Inactive";
        $tmpData->action = $tmpData->action;
        $tmpData->created_by = User::where('id', $tmpData->created_by)->first()->user_id;

        if (isset($oldData)) {
            $oldData->name = $oldData->name;
            $oldData->week = $oldData->week;
            $oldData->count = $oldData->count;
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
            $oldData->created_by = "N/A";
        }

        $columnsToSend = ['name', 'week', 'count', 'status', 'action', 'created_by'];

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
        $tmpData = TreasuryBondTmp::findOrFail($id);

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
        $tmpData = TreasuryBondTmp::where('id', $id)->first();

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
        $checker = $this->accessTreasuryBondChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = TreasuryBondTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function divisionReject(Request $request, $id)
    {
        $checker = $this->accessTreasuryBondChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = TreasuryBondTmp::where('id', $id)->first();
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }
}
