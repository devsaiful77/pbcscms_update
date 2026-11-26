<?php

namespace App\Http\Controllers;

use App\AccountCategory;
use App\AccountCategoryTmp;
use App\Http\Requests\AccountCategoryRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AccountCategoryController extends Controller
{
    public $checker = false;

    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|accessAccountCategory|accountCategoryChecker']);
        parent::__construct();
    }

    public function accountCategoryChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('accountCategoryChecker')) {
            return $this->checker = true;
        }
        return false;
    }

    public function index(Request $request)
    {
        $searchDataForView = $request->all();
        $tblData = array();
        $dataObj = AccountCategory::select(
                "id", "short_id", "categ_code", "title", "ac_type", "description",
                DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),
                "status", "created_at"
            )
            ->orderBy("id", "DESC")
            ->get();

        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Account Category List";
        $title_for_layout = "Account Category List";
        $checker = $this->accountCategoryChecker();

        return view('AccountCategory.index', compact('title', 'title_for_layout', 'tblData', 'searchDataForView', 'dataObj', 'checker'));
    }

    public function add()
    {
        $checker = $this->accountCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();

        $title = "Add Account Category";
        $title_for_layout = "Add Account Category";

        return view('AccountCategory.add', compact('title', 'title_for_layout', 'id', 'dataForView', 'tmpId'));
    }

    public function store(AccountCategoryRequest $request)
    {
        $columnArr = ['categ_code', 'title', 'ac_type', 'description'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];

        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);
        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $validator = Validator::make($request->all(), [
            'categ_code' => 'required',
            'title' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (\DB::table('account_categories')->where('categ_code', $request->categ_code)->exists()) {
                $validator->errors()->add('categ_code', 'The category code has already been taken in account categories table.');
            }

            if (\DB::table('account_categories_tmp')->where('categ_code', $request->categ_code)->exists()) {
                $validator->errors()->add('categ_code', 'The category code has already been taken in account categories tmp table.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->isMethod('post')) {
            $model = new AccountCategoryTmp();
            $model->short_id = $request->short_id;
            $model->categ_code = $request->categ_code;
            $model->title = $request->title;
            $model->ac_type = $request->ac_type;
            $model->description = $request->description;
            $model->status = 2; // Pending Review
            $model->action = "Add";
            $model->form_status = 0;
            $model->created_by = Auth::id();
            $model->ip = $this->getClientIp();

            if ($model->save()) {
                flash('Account Category has been inserted For Review in Checker', 'success');
                return redirect('AccountCategory');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('AccountCategory/add');
            }
        }
    }

    public function edit(Request $req, $id = null)
    {
        $checker = $this->accountCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $tmpId = 0;
        $title = "Edit Account Category";
        $title_for_layout = 'Edit Account Category';
        $searchDataForView = $req->all();

        $dataForView = AccountCategory::where('id', $id)->first();
        if (!$dataForView) {
            abort(404, 'Account Category Not Found');
        }
        if ($dataForView->status == 0) {
            abort(403, 'Edit Not Allowed for Inactive records!');
        }

        return view('AccountCategory.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function accountCategoryTmpEdit(Request $req, $id = null)
    {
        $checker = $this->accountCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $title = "Send Back Account Category Edit";
        $title_for_layout = 'Edit Account Category';
        $searchDataForView = $req->all();
        $tmpId = $id;

        $dataForView = AccountCategoryTmp::where('id', $id)->first();
        if (!$dataForView) {
            abort(404, 'Temporary Record Not Found');
        }

        return view('AccountCategory.add', compact('title', 'title_for_layout', 'dataForView', 'searchDataForView', 'id', 'tmpId'));
    }

    public function update(AccountCategoryRequest $request, $id = null)
    {
        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;

        $columnArr = ['categ_code', 'title', 'ac_type', 'description'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];
        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);
        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }

        $tableName = $isTemp ? 'account_categories_tmp' : 'account_categories';
        $this->validate($request, [
            'categ_code' => 'required|unique:' . $tableName . ',categ_code,' . $id,
            'title' => 'required',
        ]);

        $existingRecords = $isTemp ? AccountCategoryTmp::where('id', $id)->get() : AccountCategoryTmp::where('master_id', $id)->get();

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

        if ($isTemp) {
            $model = AccountCategoryTmp::where('id', $id)->first();
        } else {
            $model = new AccountCategoryTmp();
            $update = AccountCategory::where('id', $id)->first();
            if (!$update) {
                flash('Account Category Not Found', 'danger');
                return Redirect::back();
            }
        }

        $model->short_id = $request->short_id;
        $model->categ_code = $request->categ_code;
        $model->title = $request->title;
        $model->ac_type = $request->ac_type;
        $model->description = $request->description;
        $model->status = 2; // Pending Review
        $model->form_status = 0;
        $model->action = "Edit";
        $model->master_id = $isTemp ? $model->master_id : $update->id;
        $model->created_by = Auth::id();
        $model->modified_by = Auth::id();
        $model->ip = $this->getClientIp();

        if ($model->save()) {
            flash('Account Category has been updated for review in Checker', 'success');
            return redirect('/AccountCategory' . $additionalParams);
        } else {
            flash('Failed to update Account Category, Please try again', 'danger');
            return Redirect::back();
        }
    }

    public function status($id = null, $status = "")
    {
        $checker = $this->accountCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        if ($status != 1 && $status != 0) {
            flash($status . ' is not Allowed!!!', 'danger');
            return Redirect::back();
        }

        $existingTmpRecords = AccountCategoryTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $update = AccountCategory::where('id', $id)->first();
        if (!$update) {
            flash('Record not found', 'danger');
            return Redirect::back();
        }

        $model = new AccountCategoryTmp();
        $model->short_id = $update->short_id;
        $model->categ_code = $update->categ_code;
        $model->title = $update->title;
        $model->ac_type = $update->ac_type;
        $model->description = $update->description;
        $model->status = ($status == 0) ? 0 : 1;
        $model->action = ($status == 0) ? "Inactive" : "Active";
        $model->form_status = 0;
        $model->master_id = $update->id;
        $model->created_by = Auth::id();
        $model->ip = $this->getClientIp();

        $model->save();

        if ($status == 1) {
            flash('Account Category Active Request Send to Checker', 'success');
        } elseif ($status == 0) {
            flash('Account Category Inactive Request Send to Checker', 'danger');
        }

        return Redirect::back();
    }

    public function approveAccountCategory($id)
    {
        $checker = $this->accountCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }
        DB::beginTransaction();

        try {
            $tmpData = DB::table('account_categories_tmp')->where('id', $id)->first();

            if (!$tmpData) {
                flash('Account Category not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            $categoryData = [
                'short_id'    => $tmpData->short_id,
                'categ_code'  => $tmpData->categ_code,
                'title'       => $tmpData->title,
                'ac_type'     => $tmpData->ac_type,
                'description' => $tmpData->description,
                'status'      => $tmpData->status == 2 ? 1 : $tmpData->status,
            ];

            if ($tmpData->master_id != null && $tmpData->action != "Add") {
                DB::table('account_categories')->where('id', $tmpData->master_id)->update($categoryData);
            } else {
                $categoryData['created_at'] = now();
                DB::table('account_categories')->insert($categoryData);
            }

            DB::table('account_categories_tmp')->where('id', $tmpData->id)->delete();
            DB::commit();

            if ($tmpData->status == 0) {
                flash('Account Category has been inActive.', 'danger');
            } elseif ($tmpData->status == 1) {
                flash('Account Category has been activated.', 'success');
            } else {
                flash('Account Category has been approved.', 'success');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function accountCategoryTmpList()
    {
        $checker = $this->accountCategoryChecker();
        if ($checker) {
            $isChecker = true;
            $accountCategoryTmpData = AccountCategoryTmp::whereNotIn('form_status', [7, -1])->get();
        } else {
            $isChecker = false;
            $accountCategoryTmpData = AccountCategoryTmp::whereIn('form_status', [7, -1])->get();
        }
        return view('AccountCategory.index_tmp', compact('accountCategoryTmpData', 'isChecker'));
    }

    public function checkerTableData($id)
    {
        $tmpData = AccountCategoryTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = AccountCategory::find($tmpData->master_id);
        }

        $creator = User::where('id', $tmpData->created_by)->first();
        $tmpData->created_by_name = $creator ? $creator->user_id : 'N/A';
        $tmpData->status_label = ($tmpData->status == 1) ? 'Active' : (($tmpData->status == 0) ? 'Inactive' : 'Pending');

        if (isset($oldData)) {
            $oldData->created_by_name = 'N/A';
            $oldData->action = 'Original';
            $oldData->status_label = ($oldData->status == 1) ? 'Active' : 'Inactive';
        }

        $filteredTmpData = [
            'short_id' => $tmpData->short_id,
            'categ_code' => $tmpData->categ_code,
            'title' => $tmpData->title,
            'ac_type' => $tmpData->ac_type,
            'description' => $tmpData->description,
            'status' => $tmpData->status_label,
            'action' => $tmpData->action,
            'created_by' => $tmpData->created_by_name,
        ];

        $filteredOldData = isset($oldData) ? [
            'short_id' => $oldData->short_id,
            'categ_code' => $oldData->categ_code,
            'title' => $oldData->title,
            'ac_type' => $oldData->ac_type,
            'description' => $oldData->description,
            'status' => $oldData->status_label,
            'action' => 'Original',
            'created_by' => 'N/A',
        ] : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function buttonControl($id)
    {
        $tmpData = AccountCategoryTmp::findOrFail($id);

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

    public function accountCategoryAssign($id)
    {
        $tmpData = AccountCategoryTmp::where('id', $id)->first();
        if (!$tmpData) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user() ? Auth::user()->user_id : null;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function accountCategorySendback(Request $request, $id)
    {
        $checker = $this->accountCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = AccountCategoryTmp::where('id', $id)->first();
        if (!$tmpData) {
            flash('Record not found', 'danger');
            return redirect()->back();
        }

        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function accountCategoryReject(Request $request, $id)
    {
        $checker = $this->accountCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = AccountCategoryTmp::where('id', $id)->first();
        if (!$tmpData) {
            flash('Record not found', 'danger');
            return redirect()->back();
        }

        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user() ? Auth::user()->user_id : null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }

    public function deleteTmpData($id, $table = 'account_categories_tmp')
    {
        DB::table($table)->where('id', $id)->delete();

        flash('Account Category temporary data has been deleted.', 'danger');
        return redirect()->back();
    }
}
