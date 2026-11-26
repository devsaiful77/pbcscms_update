<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\Role;
use App\Unit;
use App\User;
use App\UserTmp;
use App\Division;
use App\UserUnit;
use App\GroupInfo;
use App\Department;
use App\SubgroupInfo;
use App\Enum\AccessApp;
use App\Exports\UsersExport;
use App\Exports\UserTmpExport;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\OutgoingEMAIL;
use App\OutgoingSMS;
use App\SMSEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RequUsersController extends Controller
{
    protected $modelName;
    protected $modelNameTmp;
    public $checker = false;

    public function __construct()
    {
        $this->modelName = new User;
        $this->modelNameTmp = new UserTmp();
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|accessRequUsers|accessRequUsersChecker']);
    }

    public function accessRequUsersChecker()
    {
        if (Auth::check() && Auth::user()->hasPermissionTo('accessRequUsersChecker')) {
            return $this->checker = true;
        }
    }
    public function index(Request $request)
    {
        $dataForView = array();
        $searchDataForView = array();
        $userData = array();

        $selectedValue = $request->get('search_value');
        $selectedStatus = $request->get('status');
        $selectedUnit = $request->get('unit_id');
        $statusForSearch = 1;

        if ($selectedStatus == 2) {
            $statusForSearch = 0;
        }

        $userModel = new User;
        $userDataObj = $userModel
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
                DB::raw("CASE WHEN users.status = 1 THEN 'Active' WHEN users.status = 0 THEN 'Inactive' WHEN users.status = -2 THEN 'Close' ELSE 'Invalid' END AS status_name"),
                DB::raw('MAX(log_users.log_in_at) as last_login_time')
            )
            ->leftJoin('log_users', 'log_users.user_id', '=', 'users.id')
            ->groupBy('users.id')
            ->with([
                'roles' => function ($q) {
                    $q->where('roles.module', AccessApp::Requisition);
                },
                'user_unit' => function ($q) {
                    $q->where('user_units.module', AccessApp::Requisition);
                }
            ]);

        if (!empty($selectedUnit)) {
            $userDataObj = $userDataObj
                ->leftJoin('user_units', 'user_units.user_id', 'users.id')
                ->where(function ($q) use ($selectedUnit) {
                    $q
                        ->where('user_units.unit_id', $selectedUnit)
                        ->orWhere('user_units.unit_id', 'LIKE', '%,' . $selectedUnit)
                        ->orWhere('user_units.unit_id', 'LIKE', $selectedUnit . ',%')
                        ->orWhere('user_units.unit_id', 'LIKE', '%,' . $selectedUnit . ',%');
                });
        }

        if (!empty($selectedValue)) {
            $searchDataForView['search_value'] = $selectedValue;
            $userDataObj = $userDataObj
                ->where(function ($q) use ($selectedValue) {
                    $q
                        ->where('users.user_id', 'LIKE', '%' . $selectedValue . '%')
                        ->orWhere('users.emp_id', 'LIKE', '%' . $selectedValue . '%')
                        ->orWhere('users.mobile_no', 'LIKE', '%' . $selectedValue . '%')
                        ->orWhere('users.name', 'LIKE', '%' . $selectedValue . '%')
                        ->orWhere('users.email', 'LIKE', '%' . $selectedValue . '%');
                });
        }

        $searchDataForView['status'] = $selectedStatus;
        $searchDataForView['unit_id'] = $selectedUnit;
        $searchDataForView['search_value'] = $selectedValue;

        $isDownload = $request->get('download');

        if ($isDownload) {
            $userData = $userDataObj->get();
            $fileName = 'user_details_report_' . date('dmYhis') . '.xlsx';
            return Excel::download(new UsersExport($userData), $fileName);
        }

        $userDataObj = $userDataObj->orderBy("users.id", "DESC")->paginate(PAGINATION_NUMBER);

        if (!empty($userDataObj)) {
            $userData = $userDataObj->toArray();
        }

        $userId = (!empty(Auth::user()->id)) ? Auth::user()->id : 0;

        $roleModelName = new Role;
        $allRoleData = $roleModelName
            ->select('id', 'display_name', 'module')
            ->where('name', '<>', 'superadmin')
            ->where('module', AccessApp::Requisition)
            ->orderBy('display_name', 'ASC')
            ->pluck('display_name', 'id')
            ->toArray();

        $unitModelName = new Unit;
        $allUnitData = $unitModelName
            ->select('id', 'name', 'module')
            ->where('status', 1)
            ->where('module', AccessApp::Requisition)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id', 'subgroup_info_id', 'group_info_id')
            ->toArray();

        $home_menu_icon = "fa fa-user";
        $title = "User List";
        $title_for_layout = 'User List';
        $checker = $this->accessRequUsersChecker();

        // dd($userData);
        return view('requisition::RequUsers/users', compact(
            "userData",
            "dataForView",
            "searchDataForView",
            "selectedValue",
            "selectedStatus",
            "selectedUnit",
            "title_for_layout",
            "title",
            "userDataObj",
            "userId",
            "home_menu_icon",
            "allRoleData",
            "allUnitData",
            'checker'
        ));
    }

    public function add()
    {
        $checker = $this->accessRequUsersChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $uploadError = 0;
        $id = 0;
        $uploadErrorMsg = "";
        $dataForView = array();
        $dataForViewUser = array();
        $userInfo = array();



        $title = "Add User";
        $title_for_layout = 'Add User';
        $home_menu_icon = "fa fa-user";

        $roleModelName = new Role;
        $allRoleData = $roleModelName
            ->select('id', 'display_name')
            ->where('name', '<>', 'superadmin')
            ->where('module', AccessApp::Requisition)
            ->where(function ($q) {
                $q->where('is_user', 0)->orWhereNull('is_user');
            })
            ->orderBy('display_name', 'ASC')
            ->pluck('display_name', 'id')
            ->toArray();
        $currentRoleId = 0;
        // add unit setup code
        $divisionModelName = new Division;
        $allDivisionData = $divisionModelName
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $departmentModelName = new Department;
        $allDepartmentData = $departmentModelName
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $groupModelName = new GroupInfo();
        $allGroupData = $groupModelName
            ->select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $subgroupModelName = new SubgroupInfo();
        $allSubgroupData = $subgroupModelName
            ->select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        return view('requisition::RequUsers/add', compact("uploadErrorMsg", "dataForViewUser", "id", "uploadError", "dataForView", "allRoleData", "currentRoleId", "title", "title_for_layout", "home_menu_icon", 'userInfo', 'dataForView', 'allDivisionData', 'allDepartmentData', 'allGroupData', 'allSubgroupData'));
    }

    public function store(UserRequest $request)
    {

        $existingTmpRecords = UserTmp::where('user_id', $request->user_id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        DB::beginTransaction();

        try {
            $data = $request->all();
            Log::info('Request data for store:', $data);

            $existingTmpRecords = UserTmp::where('user_id', $request->user_id)->get();

            Log::info('Existing temp records:', $existingTmpRecords->toArray());

            $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
                return $record->form_status != 7 && $record->form_status != -1;
            });
            Log::info('Has invalid record: ' . $hasInvalidRecord);

            if ($hasInvalidRecord) {
                flash('User already exists, please wait for Checker Approval', 'danger');
                return Redirect::back();
            }

            // Create a new temporary record
            $this->modelNameTmp->user_id = $request->user_id;
            $this->modelNameTmp->emp_id = $request->emp_id;
            $this->modelNameTmp->name = $request->name;
            $this->modelNameTmp->designation = $request->designation;
            $this->modelNameTmp->email = $request->email;
            $this->modelNameTmp->mobile_no = $request->mobile_no;
            $this->modelNameTmp->remarks = $request->remarks;
            $this->modelNameTmp->ip = $this->getClientIp();
            $this->modelNameTmp->status = $request->status;
            $this->modelNameTmp->role_id = $request->role_id ?? 0;
            $this->modelNameTmp->action = "Add";
            $this->modelNameTmp->master_id = null;
            $this->modelNameTmp->form_status = 0;
            $this->modelNameTmp->module = AccessApp::Requisition;
            $this->modelNameTmp->created_by = auth()->user()->user_id;
            $this->modelNameTmp->modified_by = "";
            $this->modelNameTmp->comments = "Created by " . auth()->user()->name;

            Log::info('Temporary model before save:', $this->modelNameTmp->toArray());

            // Save the new record
            if (!$this->modelNameTmp->save()) {
                throw new \Exception('Failed to save the new user record.');
            }
            $newRecordId = $this->modelNameTmp->id;
            $userId = $this->modelNameTmp->user_id;
            $name = $this->modelNameTmp->name;

            Log::info('Temporary record created successfully');

            DB::table('user_units_tmp')->where('user_id', $request->user_id)->where('module', AccessApp::Requisition)->delete();
            DB::table('model_has_roles_tmp')->where('model_id', $request->user_id)->where('module', AccessApp::Requisition)->delete();

            if ($request->role_id > 0) {
                DB::table('model_has_roles_tmp')->insert([
                    'role_id' => $request->role_id,
                    'model_type' => 'App\User',
                    'model_id' => $request->user_id,
                    'module' => AccessApp::Requisition,
                ]);
            } else {
                $unitIds = $request->unit_id ?? [];
                // if(count($unitIds) > 0){
                switch ($request['type']) {
                    case 'Subgroup':
                        $this->handleSubgroup($newRecordId, $request, $unitIds, $userId); // No $id for a new user
                        break;

                    case 'Group':
                        $this->handleGroup($newRecordId, $request, $unitIds, $userId); // No $id for a new user
                        break;

                    case 'Department':
                        $this->handleDepartment($newRecordId, $request, $unitIds, $userId); // No $id for a new user
                        break;

                    case 'Division':
                        $this->handleDivision($newRecordId, $request, $unitIds, $userId); // No $id for a new user
                        break;

                    default:
                        // throw new \Exception('Invalid type provided.');
                }

                // }
            }

            if (!empty($request->get('oldDataForAudit'))) {
                $audit = new Audit();
                $audit->auditable_type = 'App\User';
                $audit->user_type = 'App\User';
                $audit->user_id = Auth::id();
                $audit->event = 'create';
                $audit->auditable_id = $this->modelNameTmp->id;
                $audit->old_values = json_decode($request->get('oldDataForAudit'));
                $audit->save();
            }

            // Commit the transaction
            DB::commit();
            flash('User has been created successfully', 'success');
            return redirect('/requisition/users' . $request->get('additionalParams'));
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollback();
            Log::error('User creation failed: ' . $e->getMessage());
            flash('Failed to create the user, please try again.', 'danger');
            return Redirect::back();
        }
    }

    public function edit($id = null, Request $req)
    {
        $checker = $this->accessRequUsersChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            toast()->error('Internal Error Occurs!!!', 'Danger');
            return Redirect::back();
        }
        $dataForViewUser = $this->modelName
            ->with([
                'roles'
            ])
            ->where('id', $id)
            ->first();

        $roleModelName = new Role;
        $allRoleData = "";
        $uploadErrorMsg = "";
        $uploadError = "";

        if (Auth::user()->roles->first()->id == 12) {

            $allRoleData = $roleModelName
                ->select('id', 'display_name')
                ->where('name', '<>', 'superadmin')
                ->where('name', '<>', 'admin')
                ->where(function ($q) {
                    $q->where('is_user', 0)->orWhereNull('is_user');
                })
                ->orderBy('display_name', 'ASC')
                ->pluck('display_name', 'id')
                ->toArray();
        } else {
            $allRoleData = $roleModelName
                ->select('id', 'display_name')
                ->where('name', '<>', 'superadmin')
                ->where('module', AccessApp::Requisition)
                ->where(function ($q) {
                    $q->where('is_user', 0)->orWhereNull('is_user');
                })
                ->orderBy('display_name', 'ASC')
                ->pluck('display_name', 'id')
                ->toArray();
        }
        // prd(Auth::user()->role_user->role_id);

        $currentRoleId = (count($dataForViewUser->roles) >= 1) ? $dataForViewUser->roles->first()->id : 0;

        // add unit setup code
        $divisionModelName = new Division;
        $allDivisionData = $divisionModelName
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $departmentModelName = new Department;
        $allDepartmentData = $departmentModelName
            ->select('id', 'name')
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $groupModelName = new GroupInfo();
        $allGroupData = $groupModelName
            ->select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $subgroupModelName = new SubgroupInfo();
        $allSubgroupData = $subgroupModelName
            ->select('id', 'name')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->pluck('name', 'id')
            ->toArray();

        $userInfo = array();
        $userInfoObj = $dataForViewUser;
        // $userInfoObj = $this->modelName->where('id', $id)->first();
        if (empty($userInfoObj)) {
            abort(403, 'Edit Not possible!!!');
        } else {
            $userInfo = $userInfoObj->toArray();
        }


        $userUnitModel = new UserUnit;
        $dataForView = $userUnitModel->where([
            ['user_id', $id]
        ])->where('module', AccessApp::Requisition)->first();

        $home_menu_icon = "fa fa-user";
        $title = "Edit User";
        $title_for_layout = 'Edit User';
        return view('requisition::RequUsers/add', compact("uploadErrorMsg", "id", "uploadError", "dataForView", "allRoleData", "currentRoleId", "title", "title_for_layout", "home_menu_icon", 'userInfo', 'dataForView', 'allDivisionData', 'allDepartmentData', 'allGroupData', 'allSubgroupData'));
    }

    public function update($id = null, UserRequest $request)
    {
        $existingTmpRecords = UserTmp::where('user_id', $request->user_id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            Log::info('Has invalid record: ' . $hasInvalidRecord);
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        DB::beginTransaction();

        try {
            try {
                $id = decrypt($id);
                Log::info('Decrypted ID: ' . $id);
            } catch (DecryptException $e) {
                Log::error('Decryption failed: ' . $e->getMessage());
                return abort(403, 'Unauthorized Access');
            }

            $data = $request->all();
            Log::info('Request data:', $data);

            $update = $this->modelName->find($id);
            Log::info('Fetched main record:', $update->toArray());

            $this->modelNameTmp->user_id = $update->user_id;
            $this->modelNameTmp->emp_id = $request->emp_id;
            $this->modelNameTmp->name = $request->name;
            $this->modelNameTmp->designation = $request->designation;
            $this->modelNameTmp->email = $request->email;
            $this->modelNameTmp->mobile_no = $request->mobile_no;
            $this->modelNameTmp->remarks = $request->remarks;
            $this->modelNameTmp->ip = $this->getClientIp();
            $this->modelNameTmp->status = $request->status;
            $this->modelNameTmp->role_id = $request->role_id ?? 0;
            $this->modelNameTmp->action = "Edit";
            $this->modelNameTmp->master_id = $update->id;
            $this->modelNameTmp->form_status = 0;
            $this->modelNameTmp->created_by = auth()->user()->user_id;
            $this->modelNameTmp->modified_by = "";
            $this->modelNameTmp->module = AccessApp::Requisition;
            $this->modelNameTmp->comments = "Edit by " . auth()->user()->name;

            Log::info('Temporary model before save:', $this->modelNameTmp->toArray());

            if (!$this->modelNameTmp->save()) {
                throw new \Exception('Failed to save the user record.');
            }

            Log::info('Temporary record saved successfully');


            DB::table('user_units_tmp')->where('user_id', $id)->where('module', AccessApp::Requisition)->delete();
            DB::table('model_has_roles_tmp')->where('model_id', $id)->where('module', AccessApp::Requisition)->delete();
            if ($request->role_id > 0) {
                DB::table('model_has_roles_tmp')->insert([
                    'role_id' => $request->role_id,
                    'model_type' => 'App\User',
                    'model_id' => $update->user_id,
                    'module' => AccessApp::Requisition
                ]);
            } else {
                $unitIds = $request->unit_id ?? [];
                // if(count($unitIds) > 0){
                switch ($request['type']) {
                    case 'Subgroup':
                        $this->handleSubgroup($id, $request, $unitIds, $update->user_id);
                        break;

                    case 'Group':
                        $this->handleGroup($id, $request, $unitIds, $update->user_id);
                        break;

                    case 'Department':
                        $this->handleDepartment($id, $request, $unitIds, $update->user_id);
                        break;

                    case 'Division':
                        $this->handleDivision($id, $request, $unitIds, $update->user_id);
                        break;

                    default:
                        // throw new \Exception('Invalid type provided.');
                }
                // }
            }

            if (!empty($request->get('oldDataForAudit'))) {
                $audit = new Audit();
                $audit->auditable_type = 'App\User';
                $audit->user_type = 'App\User';
                $audit->user_id = Auth::id();
                $audit->event = 'Modify';
                $audit->auditable_id = $id;
                $audit->old_values = json_decode($request->get('oldDataForAudit'));
                $audit->save();
            }

            DB::commit();
            flash('User has been updated successfully', 'success');
            return redirect('/requisition/users' . $request->get('additionalParams'));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('User update failed: ' . $e->getMessage());
            flash('Failed to update the user, please try again.', 'danger');
            return Redirect::back();
        }
    }

    public function approve($id)
    {
        $checker = $this->accessRequUsersChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        DB::beginTransaction();

        try {
            Log::debug('Approval process started for UserTmp ID: ' . $id);

            $userTmp = UserTmp::where('status', '!=', 3)
                ->whereNotIn('form_status', [7, -1])
                ->where('id', $id)
                ->first();

            if (!$userTmp) {
                Log::debug('UserTmp not found or not approved yet. UserTmp ID: ' . $id);
                flash('User not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            Log::debug('UserTmp found: ', $userTmp->toArray());

            $userData = [
                'user_id'     => $userTmp->user_id,
                'name'        => $userTmp->name,
                'designation' => $userTmp->designation,
                'email'       => $userTmp->email,
                'emp_id'       => $userTmp->emp_id,
                'remarks'       => $userTmp->remarks,
                'password'    => Hash::make(1),
                'mobile_no'   => $userTmp->mobile_no,
                'ip'          => $this->getClientIp(),
                'status'      => $userTmp->status,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            Log::debug('User data prepared: ', $userData);

            // Check if updating or adding a new user
            if ($userTmp->master_id != null && $userTmp->action != "Add") {
                $existingUser = User::find($userTmp->master_id);
                $supp_status = "Creation";

                if ($existingUser) {
                    $userData['c_maker'] =  $userTmp->created_by;
                    $userData['c_checker'] = auth()->user()->id;
                    $userData['cm_date'] = now();
                    $userData['cc_date'] = now();

                    Log::debug('Existing user found: ', $existingUser->toArray());

                    $this->storeAudit('App\User', $existingUser->id, $existingUser->toArray(), $userData);

                    //$existingUser->syncRoles($userTmp->role_id);
                    DB::table('users')->where('id', $userTmp->master_id)->update($userData);
                    $userIdForUnit = $userTmp->master_id;

                    Log::debug('Existing user updated: User ID ' . $userIdForUnit);
                }
            } else {
                $userData['f_maker'] = $userTmp->created_by;
                $userData['f_checker'] = auth()->user()->id;
                $userData['fm_date'] = now();
                $userData['fc_date'] = now();
                $newUserId = DB::table('users')->insertGetId($userData);
                $newUser = User::find($newUserId);
                $supp_status = "Modification";
                if ($newUser) {
                    Log::debug('New user created: User ID ' . $newUserId);
                    $newUser->syncRoles($userTmp->role_id);
                }
                $userIdForUnit = $newUserId;
            }

            // Check and delete existing unit if found
            $unitExist = DB::table('user_units')->where('user_id', $userIdForUnit)->first();
            if ($unitExist) {
                Log::debug('Existing unit found: ', (array)$unitExist);
                $this->storeAudit('App\UserUnit', $unitExist->id, (array)$unitExist, null);

                DB::table('user_units')->where('user_id', $userIdForUnit)->where('module', AccessApp::Requisition)->delete();
                Log::debug('Existing unit deleted for User ID ' . $userIdForUnit);
            }

            // Insert new unit from user_units_tmp
            $unitTmpExist = DB::table('user_units_tmp')->where('user_id', $userTmp->user_id)->where('module', AccessApp::Requisition)->first();
            if ($unitTmpExist) {
                DB::table('user_units')->insert([
                    'user_id' => $userIdForUnit,
                    'unit_id' => $unitTmpExist->unit_id,
                    'is_email_allow' => $unitTmpExist->is_email_allow ?? 0,
                    'is_unit_head' => $unitTmpExist->is_unit_head ?? 0,
                    'subgroup_info_id' => $unitTmpExist->subgroup_info_id,
                    'is_group_info_head' => $unitTmpExist->is_group_info_head,
                    'group_info_id' => $unitTmpExist->group_info_id,
                    'department_id' => $unitTmpExist->department_id,
                    'is_department_head' => $unitTmpExist->is_department_head,
                    'division_id' => $unitTmpExist->division_id,
                    'is_division_head' => $unitTmpExist->is_division_head,
                    'module' => AccessApp::Requisition,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::debug('New unit inserted for User ID ' . $userIdForUnit);
            }

            // Check and delete existing roles
            $MHRole = DB::table('model_has_roles')->where('model_id', $userIdForUnit)->first();
            if ($MHRole) {
                Log::debug('Existing role found: ', (array)$MHRole);
                $this->storeAudit('App\ModelHasRole', $MHRole->model_id, (array)$MHRole, null);

                DB::table('model_has_roles')->where('model_id', $userIdForUnit)->where('module', AccessApp::Requisition)->delete();
                Log::debug('Existing roles deleted for User ID ' . $userIdForUnit);
            }

            // Insert new roles from model_has_roles_tmp
            $MHRoleTmp = DB::table('model_has_roles_tmp')->where('model_id', $userTmp->user_id)->where('module', AccessApp::Requisition)->get();
            // dd($userTmp);
            Log::alert('test1 '. json_encode($MHRoleTmp) . " === " . json_encode($unitTmpExist));

            if (count($MHRoleTmp) > 0) {
                foreach ($MHRoleTmp as $roleTmp) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleTmp->role_id,
                        'model_type' => 'App\User',
                        'model_id' => $userIdForUnit,
                        'module' => AccessApp::Requisition,
                    ]);
                }
                Log::debug('New roles inserted for User ID ' . $userIdForUnit);
            }else{
                if(!$MHRoleTmp->isEmpty()) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $MHRoleTmp->role_id,
                        'model_type' => 'App\User',
                        'model_id' => $userIdForUnit,
                        'module' => AccessApp::Requisition,
                    ]);
                }
            }

            // Delete temporary data
            $userTmp->delete();
            DB::table('user_units_tmp')->where('user_id', $userTmp->user_id)->where('module', AccessApp::Requisition)->delete();
            DB::table('model_has_roles_tmp')->where('model_id', $userTmp->user_id)->where('module', AccessApp::Requisition)->delete();

            Log::debug('Temporary data deleted for UserTmp ID ' . $userTmp->user_id);

            DB::commit();

            $outgoingSMSMessage = $this->outgoingSMSEmail(  $userTmp->name, $userTmp->user_id);

            if (!empty($outgoingSMSMessage['sms'])) {
                $this->sendSMS($userTmp->mobile_no, $outgoingSMSMessage['sms'], '0000', $supp_status);
            }

            if (!empty($outgoingSMSMessage['mail'])) {
                if (!empty($userTmp->email)) {
                    $this->sendEMAIL($userTmp->email, $outgoingSMSMessage['mail'], '0000', $supp_status);
                }
            }

            // flash('User has been approved and updated successfully', 'success');

            if ($userTmp->status == 0) {
                flash('User has been inactivated.', 'danger');
            } elseif ($userTmp->status == 1) {
                flash('User has been activated.', 'success');
            } elseif ($userTmp->status == -2) {
                flash('User has been closed.', 'success');
            } else {
                flash('User has been approved.', 'success');
            }

            return redirect('/requisition/users/action-queue-list');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Approval failed: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
            flash('Failed to approve the user, please try again.', 'danger');
            return Redirect::back();
        }
    }

    protected function handleSubgroup($id, $request, $unitIds, $userId)
    {
        $this->validate($request, [
            'subgroup_info_id' => 'required',
            'unit_id' => 'required',
            'unit_id.*' => 'required',
        ]);



        if ($request->subgroup_info_id == 380) {
            $unitIds = array_filter($unitIds, function ($value) {
                return $value !== '1';
            });
        }

        foreach ($unitIds as $unit_id) {
            DB::table('model_has_roles_tmp')->insert([
                'role_id' => $unit_id,
                'model_type' => 'App\User',
                'model_id' => $userId,
                'module' => AccessApp::Requisition,
            ]);
        }

        $data = [
            'user_id' => $userId,
            'subgroup_info_id' => $request->subgroup_info_id,
            'is_email_allow' => $request->is_email_allow,
            'unit_id' => !empty($unitIds) ? implode(',', $unitIds) : null,
            'is_unit_head' => $request->is_unit_head ?? 0,
            'ip' => $this->getClientIp(),
            'action' => 'Assign',
            'master_id' => $id,
            'form_status' => 0,
            'module' => AccessApp::Requisition,
            'created_by' => Auth::user()->user_id,
        ];

        DB::table('user_units_tmp')->insert($data);
    }

    protected function handleGroup($id, $request, $unitIds, $userId)
    {
        $groupData = DB::table('units')->where([['is_head', 1], ['status', 1]])->first();
        if (!empty($groupData)) {
            DB::table('model_has_roles_tmp')->insert([
                'role_id' => $groupData->id,
                'model_type' => 'App\User',
                'model_id' => $userId,
                'module' => AccessApp::Requisition,
            ]);
        }

        $data = [
            'user_id' => $userId,
            'group_info_id' => $request->group_info_id,
            'is_email_allow' => $request->is_email_allow,
            'unit_id' => 6,
            'is_group_info_head' => $request->group_head ? $request->group_head : 0,
            'ip' => $this->getClientIp(),
            'action' => 'Assign',
            'master_id' => $id,
            'form_status' => 0,
            'module' => AccessApp::Requisition,
            'created_by'=> Auth::user()->user_id,
        ];

        DB::table('user_units_tmp')->insert($data);
    }


    protected function handleDepartment($id, $request, $unitIds, $userId)
    {
        $departmentData = DB::table('units')->where([['is_head', 2], ['status', 1]])->first();
        if (!empty($departmentData)) {
            DB::table('model_has_roles_tmp')->insert([
                'role_id' => $departmentData->id,
                'model_type' => 'App\User',
                'model_id' => $userId,
                'module' => AccessApp::Requisition,
            ]);
        }


        $data = [
            'user_id' => $userId,
            'department_id' => $request->department_id,
            'is_email_allow' => $request->is_email_allow,
            'unit_id' => 7,
            'is_department_head' => $request->department_head ? $request->department_head : 0,
            'ip' => $this->getClientIp(),
            'action' => 'Assign',
            'master_id' => $id,
            'form_status' => 0,
            'module' => AccessApp::Requisition,
            'created_by'=> Auth::user()->user_id,
        ];

        DB::table('user_units_tmp')->insert($data);
    }

    protected function handleDivision($id, $request, $unitIds, $userId)
    {
        $divisionData = DB::table('units')->where([['is_head', 3], ['status', 1]])->first();
        if (!empty($divisionData)) {
            DB::table('model_has_roles_tmp')->insert([
                'role_id' => $divisionData->id,
                'model_type' => 'App\User',
                'model_id' => $userId,
                'module' => AccessApp::Requisition,
            ]);
        }


        $data = [
            'user_id' => $userId,
            'division_id' => $request->division_id,
            'is_email_allow' => $request->is_email_allow,
            'unit_id' => 11,
            'is_division_head' => $request->division_head ? $request->division_head : 0,
            'ip' => $this->getClientIp(),
            'action' => 'Assign',
            'master_id' => $id,
            'form_status' => 0,
            'module' => AccessApp::Requisition,
            'created_by'=> Auth::user()->user_id,
        ];

        DB::table('user_units_tmp')->insert($data);
    }

    public function userTmpList(Request $request)
    {
        $checker = $this->accessRequUsersChecker();

        if ($checker) {
            $tmpData = DB::table('users_tmp')
                ->leftJoin('user_units_tmp', 'users_tmp.user_id', '=', 'user_units_tmp.user_id')
                ->leftJoin('roles', 'users_tmp.role_id', '=', 'roles.id')
                ->leftJoin('units', 'user_units_tmp.unit_id', '=', 'units.id')
                ->leftJoin('subgroup_info', 'user_units_tmp.subgroup_info_id', '=', 'subgroup_info.id')
                ->leftJoin('group_info', 'user_units_tmp.group_info_id', '=', 'group_info.id')
                ->leftJoin('departments', 'user_units_tmp.department_id', '=', 'departments.id')
                ->leftJoin('divisions', 'user_units_tmp.division_id', '=', 'divisions.id')
                ->whereNotIn('users_tmp.form_status', [7, -1])
                ->where('users_tmp.module', AccessApp::Requisition)
                ->select(
                    'users_tmp.id as uid',
                    'users_tmp.user_id',
                    'users_tmp.emp_id',
                    'users_tmp.name',
                    'users_tmp.action as uaction',
                    'users_tmp.designation',
                    'users_tmp.email',
                    'users_tmp.mobile_no',
                    'units.name as unit_name',
                    DB::raw("
                        CASE
                            WHEN subgroup_info.name IS NOT NULL THEN subgroup_info.name
                            WHEN group_info.name IS NOT NULL THEN group_info.name
                            WHEN departments.name IS NOT NULL THEN departments.name
                            WHEN divisions.name IS NOT NULL THEN divisions.name
                            ELSE ''
                        END as group_name
                    "),
                    'roles.display_name as role_name',
                    'users_tmp.status as ustatus',
                    'users_tmp.form_status as u_form_status',
                    'users_tmp.comments as ucomments',
                    'users_tmp.created_by as uCreated_by'
                )
                ->get();

        } else {
            $tmpData = DB::table('users_tmp')
                ->leftJoin('user_units_tmp', 'users_tmp.user_id', '=', 'user_units_tmp.user_id')
                ->leftJoin('roles', 'users_tmp.role_id', '=', 'roles.id')
                ->leftJoin('units', 'user_units_tmp.unit_id', '=', 'units.id')
                ->leftJoin('subgroup_info', 'user_units_tmp.subgroup_info_id', '=', 'subgroup_info.id')
                ->leftJoin('group_info', 'user_units_tmp.group_info_id', '=', 'group_info.id')
                ->leftJoin('departments', 'user_units_tmp.department_id', '=', 'departments.id')
                ->leftJoin('divisions', 'user_units_tmp.division_id', '=', 'divisions.id')
                ->whereIn('users_tmp.form_status', [7, -1])
                ->select(
                    'users_tmp.id as uid',
                    'users_tmp.user_id',
                    'users_tmp.emp_id',
                    'users_tmp.name',
                    'users_tmp.action as uaction',
                    'users_tmp.designation',
                    'users_tmp.email',
                    'users_tmp.mobile_no',
                    'units.name as unit_name',
                    DB::raw("
                        CASE
                            WHEN subgroup_info.name IS NOT NULL THEN subgroup_info.name
                            WHEN group_info.name IS NOT NULL THEN group_info.name
                            WHEN departments.name IS NOT NULL THEN departments.name
                            WHEN divisions.name IS NOT NULL THEN divisions.name
                            ELSE ''
                        END as group_name
                    "),
                    'roles.display_name as role_name',
                    'users_tmp.status as ustatus',
                    'users_tmp.form_status as u_form_status',
                    'users_tmp.comments as ucomments',
                    'users_tmp.created_by as uCreated_by'
                )
                ->get();

        }

        $isDownload = $request->get('download');
        if ($isDownload) {
            $fileName = 'user_tmp_list_' . date('dmYhis') . '.xlsx';
            return Excel::download(new UserTmpExport($tmpData), $fileName);
        }

        return view('requisition::RequUsers/users_tmp',compact('tmpData', 'checker'));
    }

    public function checkerTableData($id)
    {
        $tmpData = UserTmp::with('roleName')->findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = User::findOrFail($tmpData->master_id);
        }

        $tmpData->name = $tmpData->name ?? null;
        $tmpData->designation = $tmpData->designation ?? null;
        $tmpData->user_id = $tmpData->user_id ?? null;
        $tmpData->emp_id = $tmpData->emp_id ?? null;
        $tmpData->email = $tmpData->email ?? null;
        $tmpData->role = $tmpData->roleName->display_name ?? 'N/A';
        $tmpData->mobile_no = $tmpData->mobile_no ?? null;
        $tmpData->status = $tmpData->status == 1 ? "Active" : "Inactive";

        if (isset($oldData)) {
            $oldData->name = $oldData->name ?? null;
            $oldData->designation = $oldData->designation ?? null;
            $oldData->user_id = $oldData->user_id ?? null;
            $oldData->emp_id = $oldData->emp_id ?? null;
            $oldData->email = $oldData->email ?? null;
            // $oldData->role = $oldData->getRoleNames()->first() ?? 'N/A';
            $oldData->role = $oldData->roles->first()->display_name ?? 'N/A';
            $oldData->mobile_no = $oldData->mobile_no ?? null;
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
        }
        $columnsToSend = ['name', 'designation', 'user_id', 'emp_id', 'email', 'role', 'mobile_no', 'status'];

        $filteredTmpData = $tmpData->only($columnsToSend);
        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function buttonControl($id){
        $tmpData = UserTmp::findOrFail($id);

        if($tmpData->form_status == 0){
            $assign_btn = true;
        }else{
            $assign_btn = false;
        }

        if($tmpData->modified_by == Auth::user()->user_id){
            $all_buttons = true;
        }else{
            $all_buttons = false;
        }

        $response = [
            'assign_btn' => $assign_btn,
            'all_buttons' => $all_buttons,
            'modified_by' => $tmpData->modified_by,
        ];

        return response()->json($response, 200);
    }

    public function userAssign($id)
    {
        $tmpData = UserTmp::where('id', $id)->first();

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function userSendback(Request $request, $id)
    {
        $checker = $this->accessRequUsersChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = UserTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        if ($tmpData) {
            DB::table('user_units_tmp')->where('user_id', $tmpData->user_id)->delete();
            DB::table('model_has_roles_tmp')->where('model_id', $tmpData->user_id)->delete();
        }

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function userReject(Request $request, $id)
    {
        $checker = $this->accessRequUsersChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = UserTmp::where('id', $id)->first();
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        if ($tmpData) {
            DB::table('user_units_tmp')->where('user_id', $tmpData->user_id)->delete();
            DB::table('model_has_roles_tmp')->where('model_id', $tmpData->user_id)->delete();
        }

        flash('Rejected !!!', 'danger');
        return redirect()->back();
    }

    protected function storeAudit($auditableType, $auditableId, $oldValues, $newValues)
    {
        $audit = new Audit();
        $audit->auditable_type = $auditableType;
        $audit->auditable_id = $auditableId;
        $audit->user_type = 'App\User';
        $audit->user_id = Auth::id();
        $audit->event = 'update';
        $audit->old_values = json_encode($oldValues);
        $audit->new_values = json_encode($newValues);
        $audit->save();
    }

    public function outgoingSMSEmail($name = "", $user_id = "")
    {
        $msg = array();
        $sms = "";
        $mail = "";

        $smsEmailModel = new SMSEmail();
        $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();

        if (!empty($smsEmailData)) {
            $sms = $smsEmailData['user_notify_sms'];
            $mail = $smsEmailData['user_notify_email'];
        }

        if (!empty($sms)) {
            $sms = str_replace("{user_name}", $name, $sms);
            $sms = str_replace("{user_id}", $user_id, $sms);
            $msg['sms'] = $sms;
        }

        if (!empty($mail)) {
            $mail = str_replace("{user_name}", $name, $mail);
            $mail = str_replace("{user_id}", $user_id, $mail);
            $msg['mail'] = $mail;
        }


        return $msg;
    }

    public function sendSMS($mobile_no, $msg, $ref_no = "", $supp_status = NULL)
    {

        date_default_timezone_set('Asia/Dhaka');
        $savedtime = date("Y-m-d H:i:s");
        $mobile_no_1 = str_replace("+88(00)", "+88", $mobile_no);
        $mnumber = formatMobileNumber($mobile_no_1);
        if ($mnumber != "") {
            if (is_numeric($mnumber) && strlen($mnumber) == 14) {
                $outgoingSMSModel = new OutgoingSMS();
                $outgoingSMSModel->sentSMSid = 0;
                $outgoingSMSModel->message = $msg;
                $outgoingSMSModel->savetime = $savedtime;
                $outgoingSMSModel->senttime = '';
                $outgoingSMSModel->status = '3';
                $outgoingSMSModel->support_status = $supp_status;
                $outgoingSMSModel->mobileNo = $mnumber;
                $outgoingSMSModel->reference_number = $ref_no;
                $outgoingSMSModel->save();
            }
        }
    }


    public function sendEMAIL($email_address, $mail, $ref_no = "", $supp_status = NULL)
    {
        date_default_timezone_set('Asia/Dhaka');
        $savedtime = date("Y-m-d H:i:s");
        if ($mail != "") {
            $outgoingEMAILModel = new OutgoingEMAIL();
            $outgoingEMAILModel->subject = 'User Notification';
            $outgoingEMAILModel->body = $mail;
            $outgoingEMAILModel->savetime = $savedtime;
            $outgoingEMAILModel->senttime = '';
            $outgoingEMAILModel->status = '3';
            $outgoingEMAILModel->support_status = $supp_status;
            $outgoingEMAILModel->email_address = $email_address;
            $outgoingEMAILModel->reference_number = $ref_no;
            $outgoingEMAILModel->save();
        }
    }

}
