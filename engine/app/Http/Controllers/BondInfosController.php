<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\BondInformation;
use App\BondInfoCategory;
use App\BondInformationTmp;

use App\BondInfoCategoryTmp;
use App\BondInfoSubCategory;
use Illuminate\Http\Request;
use App\Http\Requests\UnitRequest;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BondInfoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\BondInfoSubCatRequest;

class BondInfosController extends Controller
{
    public $checker = false;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|admin|accessBondUpload|accessBondInfoCategoryChecker']);
        parent::__construct();
    }

    public function accessBondInfoCategoryChecker(){
        if (Auth::check() && Auth::user()->hasPermissionTo('accessBondInfoCategoryChecker')) {
            return $this->checker = true;
        }
        else{
            return $this->checker;
        }
    }

    public function categoryIndex(Request $request)
    {
        // http_build_query($searchDataForView)
        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();

        $bondinfoCatModel = new BondInfoCategory;
        $dataObj = $bondinfoCatModel
                        ->select(
                            "binfo_catsubcats.id",
                            "binfo_catsubcats.parent_id",
                            "binfo_catsubcats.name",
                            "binfo2.name AS category_name",
                            "binfo_catsubcats.description",
                            DB::raw("CASE WHEN binfo_catsubcats.status = 1 THEN 'Active' WHEN binfo_catsubcats.status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),
                            "binfo_catsubcats.status"
                        )
                        ->leftJoin("binfo_catsubcats AS binfo2","binfo2.id","binfo_catsubcats.parent_id")
                        ->orderByRaw("CASE WHEN binfo_catsubcats.parent_id = 0 THEN binfo_catsubcats.id ELSE binfo_catsubcats.parent_id END, binfo_catsubcats.parent_id, binfo_catsubcats.id")
                        ->get();

        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Product Info Category";
        $title_for_layout = "Product Info Category";
        $checker = $this->accessBondInfoCategoryChecker();
        return view('BondInfo.category_index',compact('title','title_for_layout','tblData','searchDataForView','dataObj','checker'));
    }
    public function categoryAdd(Request $request)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();

        $title = "Add Product Info Category";
        $title_for_layout = "Add Product Info Category";

        return view('BondInfo.category_add',compact('title','title_for_layout','id','dataForView', 'tmpId'));
    }

    // public function categoryStore(UnitRequest $request)
    // {
    //     if ($request->isMethod('post')) {

    //         $bondinfoCatModel = new BondInfoCategory;
    //         $bondinfoCatModel->name = $request->name;
    //         $bondinfoCatModel->description = $request->description;
    //         $bondinfoCatModel->status = 1;
    //         $bondinfoCatModel->created_by = Auth::user()->id;
    //         $bondinfoCatModel->ip = $request->ip();
    //         if ($bondinfoCatModel->save()) {
    //             /* End of Saving Associate Data */
    //             flash('Product Info Category has been inserted successfully', 'success');
    //             return redirect('bond-info/category');
    //         } else {
    //             flash('Failed to insert data', 'danger');
    //             return redirect('bond-info/category/add');
    //         }
    //     }
    // }
    public function categoryStore(UnitRequest $request)
    {
        if ($request->isMethod('post')) {


            // $departmentModelName = new DepartmentTmp();
            // $bondinfoCatModel->name = $request->name;
            // $bondinfoCatModel->description = $request->description;
            // $bondinfoCatModel->status = 1;
            // $bondinfoCatModel->created_by = Auth::user()->id;
            // $bondinfoCatModel->ip = $request->ip();
            // if ($bondinfoCatModel->save()) {
            //     /* End of Saving Associate Data */
            //     flash('Product Info Category has been inserted successfully', 'success');
            //     return redirect('bond-info/category');
            // } else {
            //     flash('Failed to insert data', 'danger');
            //     return redirect('bond-info/category/add');
            // }
            $tmpBondinfoCatModel = new BondInfoCategoryTmp;
            $tmpBondinfoCatModel->name = $request->name;
            $tmpBondinfoCatModel->description = $request->description;
            $tmpBondinfoCatModel->status = 1;
            $tmpBondinfoCatModel->action = "Add";
            $tmpBondinfoCatModel->created_by = Auth::user()->id;
            $tmpBondinfoCatModel->ip = $request->ip();
            if ($tmpBondinfoCatModel->save()) {
                /* End of Saving Associate Data */
                flash('Product Info Category has been inserted successfully, please wait for Checker Approval', 'success');
                return redirect('bond-info/category');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('bond-info/category/add');
            }
        }
    }

    public function categoryEdit($id=NULL)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $tmpId = 0;
        $bondinfoCatModel = new BondInfoCategory;
        $title = " Edit Product Info Category";
        $title_for_layout = 'Edit Product Info Category';
        $dataForView = $bondinfoCatModel->where('id', $id)->first();
        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }
        return view('BondInfo.category_edit', compact('title', 'title_for_layout', 'dataForView','id', 'tmpId'));
    }

    public function bondInfocategoryTmpEdit($id = null, Request $req)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $modelName = new BondInfoCategoryTmp;
        $title = "Send Back Product Info Category Edit";
        $title_for_layout = 'Edit Product Info Category';
        $searchDataForView = $req->all();
        $tmpId = $id;

        $dataForView = $modelName->where
                                            ([
                                                ['id', $id]
                                            ])->first();

        return view('BondInfo.category_edit', compact('title', 'title_for_layout', 'dataForView', 'id', 'tmpId'));
    }

    public function categoryUpdate($id = null, UnitRequest $request)
    {
        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;
        $tableName = $isTemp ? 'binfo_catsubcats_tmp' : 'binfo_catsubcats';
        $data = $request->all();

        $additionalParams = (!empty($request->additionalParams)) ? $request->additionalParams : "";

        $existingRecords = $isTemp ? BondInfoCategoryTmp::where('id', $id)->get() : BondInfoCategoryTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        if ($isTemp) {
            $tmpBondinfoCatModel = BondInfoCategoryTmp::where('id', $id)->first();
        }else{
            $tmpBondinfoCatModel = new BondInfoCategoryTmp();
            $update = BondInfoCategory::where('id', $id)->first();
        }

        $tmpBondinfoCatModel->name = $request->name;
        $tmpBondinfoCatModel->description = $request->description;
        $tmpBondinfoCatModel->updated_by = Auth::user()->id;
        $tmpBondinfoCatModel->ip = $request->ip();
        $tmpBondinfoCatModel->status = 2;
        $tmpBondinfoCatModel->form_status = 0;
        $tmpBondinfoCatModel->action = "Edit";
        $tmpBondinfoCatModel->master_id = $isTemp ? $tmpBondinfoCatModel->master_id : $update->id;
        $tmpBondinfoCatModel->modified_by = Auth::user()->id;
        $tmpBondinfoCatModel->created_by = Auth::user()->id;

        if ($tmpBondinfoCatModel->save()) {
            flash('Product Info Category has been updated successfully. Wait for checker Approval.', 'success');
            return redirect('/bond-info/category'.$additionalParams);
        } else {
            flash('Failed to update Product Info Category, Please try again', 'danger');
        }
    }

    public function categoryStatus($id = null)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $existingTmpRecords = BondInfoCategoryTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }


        $bondinfoCatModel = new BondInfoCategory;

        $dataForSave = $bondinfoCatModel->where('id', $id)->first();

        $tmpBondinfoCatModel = new BondInfoCategoryTmp;

        if ($dataForSave->status == 0) {
            // dd($dataForSave->status);
            $tmpBondinfoCatModel->status = 1;
            $tmpBondinfoCatModel->action = "Active";
        } else {
            // dd($dataForSave->status);
            $tmpBondinfoCatModel->status = 0;
            $tmpBondinfoCatModel->action = "Inactive";
        }

        $tmpBondinfoCatModel->name = $dataForSave->name;
        $tmpBondinfoCatModel->description = $dataForSave->description;
        $tmpBondinfoCatModel->updated_by = Auth::user()->id;
        $tmpBondinfoCatModel->ip = $dataForSave->ip;
        $tmpBondinfoCatModel->master_id = $dataForSave->id;
        $tmpBondinfoCatModel->modified_by = Auth::user()->id;
        $tmpBondinfoCatModel->created_by = Auth::user()->id;

        $tmpBondinfoCatModel->save();

        if ($tmpBondinfoCatModel->status == 1) {
            flash('Product info Category is Active. Wait for checker Approval.', 'success');
        } elseif ($tmpBondinfoCatModel->status == 0) {
            flash('Product info category is Inactive. Wait for checker Approval.', 'danger');
        }
        return Redirect::back();
    }

    public function approveCategory($id)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        DB::beginTransaction();

        try {
            $bondInfoCategoryTmp = DB::table('binfo_catsubcats_tmp')->where('id', $id)->where('status', '!=', 3)->first();
            // $tmpDepartment = DB::table('divisions_tmp')->where('id', $id)->where('status', 0)->first();
            if (!$bondInfoCategoryTmp) {
                flash('Category not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            $bondInfoCategory = [
                'name'        => $bondInfoCategoryTmp->name,
                'description' => $bondInfoCategoryTmp->description,
                'status'      => $bondInfoCategoryTmp->status,
                'updated_by'        => $bondInfoCategoryTmp->updated_by,
                'ip' => $bondInfoCategoryTmp->ip,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
            // Check if parent_id exists in the request, and add it if it does
            if ($bondInfoCategoryTmp->parent_id) {
                $bondInfoCategory['parent_id'] = $bondInfoCategoryTmp->parent_id;
            }

            if($bondInfoCategoryTmp->master_id != null && $bondInfoCategoryTmp->action != "Add"){
                DB::table('binfo_catsubcats')->where('id', $bondInfoCategoryTmp->master_id)->update($bondInfoCategory);
            }else{
                DB::table('binfo_catsubcats')->insert($bondInfoCategory);
            }

            DB::table('binfo_catsubcats_tmp')->where('id', $bondInfoCategoryTmp->id)->delete();
            DB::commit();
            flash('Category Credential has been approved.', 'success');

            // if($bondInfoCategoryTmp->status == 0){
            //     flash('Category has been inActive.', 'danger');
            // }elseif($bondInfoCategoryTmp->status == 1){
            //     flash('Category has been activated.', 'success');
            // }else{
            //     flash('Category has been approved.', 'success');
            // }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function bondInfosTmpList()
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            $isChecker = true;
            $tmpData = BondInfoCategoryTmp::whereNotIn('form_status', [7, -1])->get();
        }else{
            $isChecker = false;
            $tmpData = BondInfoCategoryTmp::whereIn('form_status', [7, -1])->get();
        }

        return view('BondInfo.index_category_tmp',compact('tmpData', 'isChecker'));
    }

    public function bondInfosAssign($id)
    {
        $tmpData = BondInfoCategoryTmp::where('id', $id)->first();

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function bondInfosSendback(Request $request, $id)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = BondInfoCategoryTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function bondInfosReject(Request $request, $id)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = BondInfoCategoryTmp::where('id', $id)->first();
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Rejected !!!', 'danger');
        return redirect()->back();
    }


    public function buttonCategoryControl($id){
        $tmpData = BondInfoCategoryTmp::findOrFail($id);

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

    public function checkerCategoryTableData($id)
    {
        $tmpData = BondInfoCategoryTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = BondInfoCategory::findOrFail($tmpData->master_id);
        }
        // Recursive function to get parent name
        function getParentName($parentId) {
            if ($parentId != 0) {
                $parentData = BondInfoCategory::find($parentId);
                // dd($parentData);
                return $parentData ? $parentData->name : null;
            }
            return null; // If parent_id is 0, return null or a base value
        }
        // Handle the tmpData (new data)
        if ($tmpData->parent_id != 0) {
            $tmpData->parent = getParentName($tmpData->parent_id); // Replace parent_id with parent name
        } else {
            $tmpData->parent = '-'; // Custom text for no parent
        }

        $tmpData->name = $tmpData->name ?? null;
        $tmpData->description = $tmpData->description;
        $tmpData->status = $tmpData->status == 1 ? "Active" : "Inactive";

        if (isset($oldData))
        {
            if ($oldData->parent_id != 0) {
                $oldData->parent = getParentName($oldData->parent_id); // Replace parent_id with parent name
            } else {
                $oldData->parent = '-'; // Custom text for no parent
            }
            $oldData->name = $oldData->name ?? null;
            $oldData->description = $oldData->description ?? "N/A";
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
        }

        $columnsToSend = ['parent','name','description','status'];

        $filteredTmpData = $tmpData->only($columnsToSend);

        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }




    public function subCategoryAdd(Request $request)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();

        $title = "Add Product Info Sub Category";
        $title_for_layout = "Add Product Info Sub Category";

        $bondinfoCatModel = new BondInfoCategory;
        $bondInfoList = $bondinfoCatModel
                        ->select("id","name")
                        ->where("parent_id",0)
                        ->pluck("name","id")
                        ->toArray();
        return view('BondInfo.subcategory_add',compact('title','title_for_layout','id','dataForView','bondInfoList', 'tmpId'));
    }


    public function subCategoryStore(BondInfoSubCatRequest $request)
    {
        if ($request->isMethod('post')) {

            // $bondinfoCatModel = new BondInfoCategory;

            $tmpBondinfoCatModel = new BondInfoCategoryTmp;
            $tmpBondinfoCatModel->name = $request->name;
            $tmpBondinfoCatModel->parent_id = $request->parent_id;
            $tmpBondinfoCatModel->description = $request->description;
            $tmpBondinfoCatModel->status = 1;
            $tmpBondinfoCatModel->action = "Add";
            $tmpBondinfoCatModel->created_by = Auth::user()->id;
            $tmpBondinfoCatModel->ip = $request->ip();
            if ($tmpBondinfoCatModel->save()) {
                /* End of Saving Associate Data */
                flash('Product Info Sub Category has been inserted successfully, please wait for Checker Approval', 'success');
                return redirect('bond-info/category');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('bond-info/category/add');
            }
        }
    }


    public function subCategoryEdit($id=NULL)
    {
        $checker = $this->accessBondInfoCategoryChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $tmpId = 0;
        $bondinfoCatModel = new BondInfoCategory;
        $title = " Edit Product Info Category";
        $title_for_layout = 'Edit Product Info Category';
        $dataForView = $bondinfoCatModel->where('id', $id)->first();
        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }

        $bondinfoCatModel = new BondInfoCategory;
        $bondInfoList = $bondinfoCatModel
                        ->select("id","name")
                        ->where("parent_id",0)
                        ->pluck("name","id")
                        ->toArray();

        return view('BondInfo.subcategory_edit', compact('title', 'title_for_layout', 'dataForView','id','bondInfoList', 'tmpId'));
    }

    public function subCategoryUpdate($id = null, UnitRequest $request)
    {
        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;
        $tableName = $isTemp ? 'binfo_catsubcats_tmp' : 'binfo_catsubcats';
        $data = $request->all();

        $additionalParams = (!empty($request->additionalParams)) ? $request->additionalParams : "";

        $existingRecords = $isTemp ? BondInfoCategoryTmp::where('id', $id)->get() : BondInfoCategoryTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        if ($isTemp) {
            $tmpBondinfoCatModel = BondInfoCategoryTmp::where('id', $id)->first();
        }else{
            $tmpBondinfoCatModel = new BondInfoCategoryTmp();
            $update = BondInfoCategory::where('id', $id)->first();
        }

        // $bondinfoCatModel = new BondInfoCategory;
        // $update = $bondinfoCatModel->where('id', $id)->first();

        // $existingTmpRecords = BondInfoCategoryTmp::where('master_id', $id)->get();

        // $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
        //     return $record->form_status != 7 && $record->form_status != -1;
        // });

        // if ($hasInvalidRecord) {
        //     flash('An entry already exists, please wait for Checker Approval', 'danger');
        //     return Redirect::back();
        // }

        // if ($existingTmpRecord) {
        //     flash('An entry already exists, please wait for Checker Approval', 'danger');
        //     return Redirect::back();
        // }
        // $additionalParams = (!empty($request->additionalParams)) ? $request->additionalParams : "";


        // dd($update);

        // if ($update->status == 0) {
        //     flash('Edit Not Allowed !!!', 'danger');
        //     return redirect('bond-info/category');
        // }


        // $tmpBondinfoCatModel = new BondInfoCategoryTmp;
        $tmpBondinfoCatModel->parent_id = $request->parent_id;
        $tmpBondinfoCatModel->name = $request->name;
        $tmpBondinfoCatModel->description = $request->description;
        // $tmpBondinfoCatModel->status = 1;
        $tmpBondinfoCatModel->status = 2;
        $tmpBondinfoCatModel->form_status = 0;
        $tmpBondinfoCatModel->updated_by = Auth::user()->id;
        $tmpBondinfoCatModel->ip = $request->ip();
        $tmpBondinfoCatModel->action = "Edit";
        $tmpBondinfoCatModel->master_id = $isTemp ? $tmpBondinfoCatModel->master_id : $update->id;
        $tmpBondinfoCatModel->modified_by = Auth::user()->id;
        $tmpBondinfoCatModel->created_by = Auth::user()->id;

        if ($tmpBondinfoCatModel->save()) {
            flash('Product Info Category has been updated successfully', 'success');
            return redirect('/bond-info/category'.$additionalParams);
        } else {
            flash('Failed to update Product Info Category, Please try again', 'danger');
        }
    }

    public function accessBondUploadChecker(){
        if (Auth::check() && Auth::user()->hasPermissionTo('accessBondUploadChecker')) {
            return $this->checker = true;
        }
        else{
            return $this->checker;
        }
    }

    public function bondInfoList()
    {
        $dataObj = array();
        $tblData = array();
        $searchDataForView = array();

        $bondinfoModel = new BondInformation;
        $dataObj = $bondinfoModel
                        ->select(
                            "bond_informations.id",
                            "bond_informations.binfo_category_id",
                            "bond_informations.binfo_subcategory_id",
                            "bond_informations.title",
                            "bond_informations.description",
                            "bond_informations.file_name",
                            "binfo_cat.name AS category_name",
                            "binfo_subcat.name AS subcategory_name",
                            DB::raw("CASE WHEN bond_informations.status = 1 THEN 'Active' WHEN bond_informations.status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),
                            "bond_informations.status"
                        )
                        ->leftJoin("binfo_catsubcats AS binfo_cat","binfo_cat.id","bond_informations.binfo_category_id")
                        ->leftJoin("binfo_catsubcats AS binfo_subcat","binfo_subcat.id","bond_informations.binfo_subcategory_id");

        if (Auth::user()->hasRole(['superadmin','admin'])) {

        } else {
            $dataObj = $dataObj->where("bond_informations.status",1);
        }

        $dataObj = $dataObj
                        ->orderByRaw("CASE WHEN binfo_cat.parent_id = 0 THEN binfo_cat.id ELSE binfo_cat.parent_id END, binfo_cat.parent_id, binfo_cat.id")
                        ->get();

        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }
        $title = "Product Information";
        $title_for_layout = "Product Information";
        $checker = $this->accessBondUploadChecker();
        if (Auth::user()->hasRole(['superadmin,admin']) || Auth::user()->can('accessBondUpload')) {
            return view('BondInfo.bond_information_list',compact('title','title_for_layout','tblData','searchDataForView','dataObj','checker'));
        } else {
            return view('BondInfo.bond_information_gen_list',compact('title','title_for_layout','tblData','searchDataForView','dataObj'));
        }

    }

    public function bondInfoUpload(Request $request)
{
    $dataForView = array();
    $tmpId = 0;

    // Main categories
    $bondInfoList = BondInfoCategory::where("parent_id", 0)
                    ->pluck("name", "id")
                    ->toArray();

    // Subcategories grouped by parent_id
    $bondInfoSubCatList = BondInfoCategory::where("parent_id", "<>", 0)
                    ->select("id", "parent_id", "name")
                    ->get()
                    ->groupBy("parent_id")
                    ->map(function ($items) {
                        return $items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                            ];
                        })->toArray();
                    })
                    ->toArray();  // Convert to array to be JSON-encoded in Blade

    $title = "Product Information Upload";
    $title_for_layout = "Product Information Upload";

    return view('BondInfo.bond_information_upload', compact('title', 'title_for_layout', 'dataForView', 'bondInfoList', 'bondInfoSubCatList', 'tmpId'));
}


    public function bondInfoUploadSubmit(BondInfoRequest $request)
    {
        if($request->isMethod('post')){
            $bondInfoTmp = new BondInformationTmp();
            $bondInfoTmp->binfo_category_id = $request->binfo_category_id;
            $bondInfoTmp->binfo_subcategory_id = $request->binfo_subcategory_id;
            $bondInfoTmp->title = $request->title;
            $bondInfoTmp->description = $request->description;
            $bondInfoTmp->status = 2;
            $bondInfoTmp->action = "Add";
            $bondInfoTmp->created_by = Auth::user()->id;
            $bondInfoTmp->ip = $request->ip();
            if(!empty($request->file('file_name'))){
                $file = $request->file('file_name');
                $fileContent = file_get_contents($file->getRealPath());
                $bondInfoTmp->file = $fileContent;
                $bondInfoTmp->file_name = $file->getClientOriginalName();
                $bondInfoTmp->file_mime = $file->getMimeType();
            }

            if($bondInfoTmp->save()){
                flash('Product information successfully uploaded and sent to checker for approval.', 'success');
                return redirect('bond-info/list');
            }else{
                flash('Failed to insert data', 'danger');
                return redirect('bond-info/upload');
            }
        }
        // if ($request->isMethod('post')) {

        //     $bondinfoModel = new BondInformation;
        //     $bondinfoModel->binfo_category_id = $request->binfo_category_id;
        //     $bondinfoModel->binfo_subcategory_id = $request->binfo_subcategory_id;
        //     $bondinfoModel->title = $request->title;
        //     $bondinfoModel->description = $request->description;
        //     $bondinfoModel->status = 1;
        //     $bondinfoModel->created_by = Auth::user()->id;
        //     $bondinfoModel->ip = $request->ip();

        //     /* Document Upload Process */
        //     $docDestPath = 'public/attachments/bond_information';

        //     $key = 0;

        //     if (!empty($request->file('file_name'))) {
        //         $extension = $request->file('file_name')->getClientOriginalExtension();
        //         $origin_name = pathinfo($request->file('file_name')->getClientOriginalName(), PATHINFO_FILENAME);
        //         $origin_name = str_replace(' ', '_', $origin_name);
        //         $origin_name = substr($origin_name, 0,20);

        //         $titleForFile = strtolower($request->title);
        //         $titleForFile = str_replace(' ','_',$titleForFile);
        //         $fileName = $titleForFile."_attach_bb_".round(microtime(true) * 10).'.'. $extension;

        //         $request->file('file_name')->move($docDestPath, $fileName);

        //         $bondinfoModel->file_name = $fileName;

        //         // $fileContent = File::get($request->file('file_name')->getRealPath());
        //         // Storage::disk('custom_storage')->put($fileName, $fileContent);
        //     }

        //     if ($bondinfoModel->save()) {
        //         /* End of Saving Associate Data */
        //         flash('Product Info have been uploaded successfully', 'success');
        //         return redirect('bond-info/list');
        //     } else {
        //         flash('Failed to insert data', 'danger');
        //         return redirect('bond-info/upload');
        //     }
        // }
    }

    private function processFile($file){
        /* Document Upload Process */
        $docDestPath = 'public/attachments/bond_information';
        $main_file = $file;
        $fileContent = $file->file;
        $tempFilePath = tempnam(sys_get_temp_dir(), 'file_');
        file_put_contents($tempFilePath, $fileContent);
        $file = new \Illuminate\Http\UploadedFile(
            $tempFilePath,
            $file->file_name,
            $file->file_mime,
            null,
            true
        );

        $extension = $file->getClientOriginalExtension();
        $titleForFile = $main_file->title;
        $titleForFile = str_replace(' ', '_', $titleForFile);
        $fileName = $titleForFile . "_attach_bb_" . round(microtime(true) * 10) . $extension;
        $file->move($docDestPath, $fileName);

        $array = [
            'extension' => $extension,
            'titleForFile' => $titleForFile,
            'fileName' => $fileName,
        ];

        return $array;
    }

    public function previewTmpFile($id){
        $tmpData = BondInformationTmp::findOrFail($id);
        $imageBlob = $tmpData->file;

        $filename = $tmpData->file_name;
        $mimeType = $tmpData->file_mime;

        if (!preg_match('/\.\w+$/', $filename)) {
            $extension = $this->getExtensionFromMime($mimeType);
            $filename .= $extension;
        }

        return response($imageBlob)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Content-Length', strlen($imageBlob));
        }

    private function getExtensionFromMime($mimeType)
    {
        $mimeMap = [
            'image/png' => '.png',
            'image/jpeg' => '.jpg',
            'image/gif' => '.gif',
            'application/pdf' => '.pdf',
        ];

        return $mimeMap[$mimeType] ?? '';
    }

    public function status($id = null, $status = "")
    {
        $checker = $this->accessBondUploadChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $bondInfo = new BondInformation();
        if ($status != 1 && $status != 0) {
            flash($status.' is not Allowed!!!','danger');
            return Redirect::back();
        }

        $existingTmpRecords = BondInformationTmp::where('master_id', $id)->get();

        $hasInvalidRecord = $existingTmpRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });

        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
            return Redirect::back();
        }

        $update = $bondInfo->where([['id', $id]])->first();

        $bondInfoTmp = new BondInformationTmp();
        if ($status == 0){
            $bondInfoTmp->status = 0;
            $bondInfoTmp->action = "Inactive";
        }else{
            $bondInfoTmp->status = 1;
            $bondInfoTmp->action = "Active";
        }
        $bondInfoTmp->master_id = $update->id;
        $bondInfoTmp->binfo_category_id = $update->binfo_category_id;
        $bondInfoTmp->binfo_subcategory_id = $update->binfo_subcategory_id;
        $bondInfoTmp->title = $update->title;
        $bondInfoTmp->description = $update->description;
        $bondInfoTmp->file_name = $update->file_name;
        $bondInfoTmp->created_by = Auth::user()->id;
        $bondInfoTmp->updated_by = Auth::user()->id;
        $bondInfoTmp->ip = $this->getClientIp();

        $bondInfoTmp->save();

        if ($status == 1) {
            flash('Product info Active Request Send to Checker', 'success');
        } elseif ($status == 0) {
            flash('Product info Inactive Request Send to Checker', 'danger');
        }
        // return redirect('/Divisions');
        return Redirect::back();
    }

    public function approveBondInfoUpload($id){

        $checker = $this->accessBondUploadChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        DB::beginTransaction();

        try {
            // $bondInfoTmp = DB::table('bond_information_tmps')->where('status', '!=', 3)->first();
            // $bondInfoTmp = DB::table('bond_information_tmps'')->where('id', $id)->where('status', 0)->first();
            $bondInfoTmp = BondInformationTmp::findOrFail($id);

            if (!$bondInfoTmp) {
                flash('Bond info not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            if($bondInfoTmp->file != null){
                $processFile = $this->processFile($bondInfoTmp);
            }
            $bondInfoData = [
                'binfo_category_id'    => $bondInfoTmp->binfo_category_id,
                'binfo_subcategory_id' => $bondInfoTmp->binfo_subcategory_id,
                'title'                => $bondInfoTmp->title,
                'description'          => $bondInfoTmp->description,
                'status'               => $bondInfoTmp->status == 2 ? 1: $bondInfoTmp->status,
                'created_by'           => $bondInfoTmp->created_by,
                'updated_by'           => isset($bondInfoTmp->updated_by) ? $bondInfoTmp->updated_by : "",
                'ip'                   => $bondInfoTmp->ip,
                'file_name'            => isset($processFile['fileName']) ? $processFile['fileName'] : $bondInfoTmp->file_name,
                'created_at'           => now(),
                'updated_at'           => now(), //now()->subSeconds(3600);
            ];

            if($bondInfoTmp->master_id != null && $bondInfoTmp->action != "Add"){
                DB::table('bond_informations')->where('id', $bondInfoTmp->master_id)->update($bondInfoData);
            }else{
                DB::table('bond_informations')->insert($bondInfoData);
            }

            DB::table('bond_information_tmps')->where('id', $bondInfoTmp->id)->delete();
            DB::commit();

            if($bondInfoTmp->status == 0){
                flash('Product info has been inActive.', 'danger');
            }elseif($bondInfoTmp->status == 1){
                flash('Product info has been activated.', 'success');
            }else{
                flash('Product info has been approved.', 'success');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function bondInfoUploadEdit($id = NULL)
    {
        $tmpId = 0;
        $dataForView = array();
        $bondinfoModel = new BondInformation;
        $dataObj = $bondinfoModel
                        ->select(
                            "bond_informations.id",
                            "bond_informations.binfo_category_id",
                            "bond_informations.binfo_subcategory_id",
                            "bond_informations.title",
                            "bond_informations.description",
                            "bond_informations.file_name",
                            "bond_informations.status"
                        )
                        ->where('bond_informations.id', $id)
                        ->first();
    
        if (!empty($dataObj)) {
            $dataForView = $dataObj->toArray();
        }
    
        $bondinfoCatModel = new BondInfoCategory;
        $bondInfoList = $bondinfoCatModel
                        ->select("id", "name")
                        ->where("parent_id", 0)
                        ->pluck("name", "id")
                        ->toArray();
    
        // Assuming bondInfoSubCatList is already grouped by parent_id (category_id)
        $bondInfoSubCatList = $bondinfoCatModel
                                ->select("id", "parent_id", "name")
                                ->where("parent_id", "<>", 0)
                                ->get()
                                ->groupBy("parent_id")
                                ->toArray();  // Ensure it's an array for easy JS parsing

                                // dd($bondInfoSubCatList);
    
        $title = "Product Information Edit";
        $title_for_layout = "Product Information Edit";
    
        return view('BondInfo.bond_information_upload_edit', compact('title', 'title_for_layout', 'dataForView', 'bondInfoList', 'bondInfoSubCatList', 'id', 'tmpId'));
    }
    


    public function bondInfoCatTmpEdit($id=NULL, Request $req)
    {
        $dataForView = array();
        $departmentModelName = new BondInfoCategoryTmp;
        $title = "Send Back Product Info Category Edit";
        $title_for_layout = 'Edit Product Info Category';
        $searchDataForView = $req->all();
        $tmpId = $id;
        $dataForView = $departmentModelName->where('id', $id)->first();

        $bondinfoCatModel = new BondInfoCategory;
        $bondInfoList = $bondinfoCatModel
                        ->select("id","name")
                        ->where("parent_id",0)
                        ->pluck("name","id")
                        ->toArray();

        $bondinfoSubCatModel = new BondInfoCategory;
        $bondInfoSubCatList = $bondinfoCatModel
                        ->select("id","parent_id","name")
                        ->where("parent_id","<>",0)
                        ->get()
                        ->groupBy("parent_id")
                        ->toJson();

        $title = "Product Information Edit";
        $title_for_layout = "Product Information Edit";

        return view('BondInfo.subcategory_edit', compact('title', 'title_for_layout', 'dataForView','bondInfoList','bondInfoSubCatList','id', 'tmpId'));
    }

    public function bondInfoUploadTmpEdit($id=NULL, Request $req)
    {
        $dataForView = array();
        $departmentModelName = new BondInformationTmp;
        $title = "Send Back Product Info Edit";
        $title_for_layout = 'Edit Product Info';
        $searchDataForView = $req->all();
        $tmpId = $id;

        $dataForView = $departmentModelName->where('id', $id)->first();

        // $dataForView = array();
        // $tmpId = $id;
        // $bondinfoModel = new BondInformationTmp;
        // $dataObj = $bondinfoModel
        //                 ->select(
        //                     "bond_informations.id",
        //                     "bond_informations.binfo_category_id",
        //                     "bond_informations.binfo_subcategory_id",
        //                     "bond_informations.title",
        //                     "bond_informations.description",
        //                     "bond_informations.file_name",
        //                     "bond_informations.status"
        //                 )
        //                 ->where('bond_informations.id',$id)
        //                 ->first();

        // if (!empty($dataObj)) {
        //     $dataForView = $dataObj->toArray();
        // }

        $bondinfoCatModel = new BondInfoCategory;
        $bondInfoList = $bondinfoCatModel
                        ->select("id","name")
                        ->where("parent_id",0)
                        ->pluck("name","id")
                        ->toArray();

        $bondinfoSubCatModel = new BondInfoCategory;
        $bondInfoSubCatList = $bondinfoCatModel
                        ->select("id","parent_id","name")
                        ->where("parent_id","<>",0)
                        ->get()
                        ->groupBy("parent_id")
                        ->toJson();

                        // dd($bondInfoSubCatList);

        $title = "Product Information Edit";
        $title_for_layout = "Product Information Edit";

        return view('BondInfo.bond_information_upload_edit', compact('title', 'title_for_layout', 'dataForView','bondInfoList','bondInfoSubCatList','id', 'tmpId'));
    }

    public function bondInfoUploadUpdate($id=NULL, BondInfoRequest $request)
    {
        if($request->isMethod('post')){

            $isTemp = !empty($request->tmpId) && $request->tmpId != 0;
            // dd($isTemp);
            $data = $request->all();
            $existingRecords = $isTemp ? BondInformationTmp::where('id', $id)->get() : BondInformationTmp::where('master_id', $id)->get();
            $hasInvalidRecord = $existingRecords->contains(function ($record) {
                return $record->form_status != 7 && $record->form_status != -1;
            });
            if ($hasInvalidRecord) {
                flash('An entry already exists, please wait for Checker Approval', 'danger');
                return Redirect::back();
            }


            if ($isTemp) {
                // dd("1");
                $bondInfoTmp = BondInformationTmp::where('id', $id)->first();
                // dd($bondInfoTmp);
            }else{
                // dd("11");
                $bondInfoTmp = new BondInformationTmp();
                $update = BondInformation::where('id', $id)->first();
            }
            $bondInfoTmp->binfo_category_id = $request->binfo_category_id;
            $bondInfoTmp->binfo_subcategory_id = $request->binfo_subcategory_id;
            $bondInfoTmp->title = $request->title;
            $bondInfoTmp->description = $request->description;
            $bondInfoTmp->status = 2;
            $bondInfoTmp->form_status = 0;
            $bondInfoTmp->action = "Edit";
            // dd($bondInfoTmp->master_id);
            $bondInfoTmp->master_id = $isTemp ? $bondInfoTmp->master_id : $update->id;
            // $bondInfoTmp->master_id = $update->id;
            $bondInfoTmp->updated_by = Auth::user()->id;
            $bondInfoTmp->created_by = Auth::user()->id;
            $bondInfoTmp->ip = $this->getClientIp();
            if(!empty($request->file('file_name'))){
                $file = $request->file('file_name');
                $fileContent = file_get_contents($file->getRealPath());
                $bondInfoTmp->file = $fileContent;
                $bondInfoTmp->file_name = $file->getClientOriginalName();
                $bondInfoTmp->file_mime = $file->getMimeType();
            }
            $bondInfoTmp->file_name = $isTemp ? $bondInfoTmp->file_name : $update->file_name;
            // $bondInfoTmp->file_name = $update->file_name;

            if($bondInfoTmp->save()){
                flash('Product info has been updated For Review in Checker', 'success');
                return redirect('bond-info/list');
            }else{
                flash('Failed to update data', 'danger');
                return redirect('bond-info/upload-edit/'.$id);
            }
        }

        // if ($request->isMethod('post')) {

        //     $bondinfoModel = new BondInformation;

        //     $update = $bondinfoModel->where('id',$id)->first();

        //     $update->binfo_category_id = $request->binfo_category_id;
        //     $update->binfo_subcategory_id = $request->binfo_subcategory_id;
        //     $update->title = $request->title;
        //     $update->description = $request->description;
        //     $update->status = 1;
        //     $update->updated_by = Auth::user()->id;
        //     $update->ip = $request->ip();

        //     /* Document Upload Process */
        //     $docDestPath = 'public/attachments/bond_information';

        //     if (!empty($request->file('file_name'))) {
        //         $extension = $request->file('file_name')->getClientOriginalExtension();
        //         $origin_name = pathinfo($request->file('file_name')->getClientOriginalName(), PATHINFO_FILENAME);
        //         $origin_name = str_replace(' ', '_', $origin_name);
        //         $origin_name = substr($origin_name, 0,20);

        //         $titleForFile = strtolower($request->title);
        //         $titleForFile = str_replace(' ','_',$titleForFile);
        //         $fileName = $titleForFile."_attach_bb_".round(microtime(true) * 10).'.'. $extension;

        //         $request->file('file_name')->move($docDestPath, $fileName);

        //         $update->file_name = $fileName;

        //         // $fileContent = File::get($request->file('file_name')->getRealPath());
        //         // Storage::disk('custom_storage')->put($fileName, $fileContent);
        //     }

        //     if ($update->save()) {
        //         /* End of Saving Associate Data */
        //         flash('Product Info have been updated successfully', 'success');
        //         return redirect('bond-info/list');
        //     } else {
        //         flash('Failed to insert data', 'danger');
        //         return redirect('bond-info/upload-edit/'.$id);
        //     }
        // }
    }

    public function bondInfoStatus($id = null)
    {
        $bondinfoModel = new BondInformation;

        $dataForSave = $bondinfoModel->where('id', $id)->first();
        if ($dataForSave->status == 0) {
            $dataForSave->status = 1;
        } else {
            $dataForSave->status = 0;

        }
        $dataForSave->save();

        if ($dataForSave->status == 1) {
            flash('Product info has been Active', 'success');
        } elseif ($dataForSave->status == 0) {
            flash('Product info has been Inactive', 'danger');
        }
        return Redirect::back();
    }

    public function productInfoQueueList(){
        $checker = $this->accessBondUploadChecker();
        if ($checker) {
            $isChecker = true;
            $productInfoTmp = BondInformationTmp::with(['category', 'subCategory'])->whereNotIn('form_status', [7, -1])->get();
        }else{
            $isChecker = false;
            $productInfoTmp = BondInformationTmp::with(['category', 'subCategory'])->whereIn('form_status', [7, -1])->get();

        }
            return view('BondInfo.index_tmp',compact('productInfoTmp', 'isChecker'));
    }

    public function productInfoAssign($id){
        $tmpData = BondInformationTmp::where('id', $id)->first();

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
    }

    public function productInfoSendback(Request $request, $id){
        $checker = $this->accessBondUploadChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = BondInformationTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }

    public function productInfoReject(Request $request, $id){
        $checker = $this->accessBondUploadChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        $tmpData = BondInformationTmp::findOrFail($id);
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Request rejected!!', 'danger');
        return Redirect::back();
    }
    public function buttonControl($id){
        $tmpData = BondInformationTmp::findOrFail($id);

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
    public function checkerTableData($id)
    {
        $tmpData = BondInformationTmp::with(['category', 'subCategory'])->findOrFail($id);
        if (!empty($tmpData->master_id)) {
            $oldData = BondInformation::with(['category', 'subCategory'])->findOrFail($tmpData->master_id);
        }
        $tmpData->binfo_category_id = $tmpData->category->name ?? 'N/A';
        $tmpData->binfo_subcategory_id = $tmpData->subCategory->name ?? 'N/A';
        $tmpData->title = $tmpData->title ?? 'N/A';
        $tmpData->description = $tmpData->description ?? 'N/A';
        $tmpData->file_name = $tmpData->file_name ?? 'N/A';
        $tmpData->status = $tmpData->status == 1 ? "Active" : "Inactive";

        if (isset($oldData)) {

            $oldData->binfo_category_id = $oldData->category->name ?? 'N/A';
            $oldData->binfo_subcategory_id = $oldData->subCategory->name ?? 'N/A';
            $oldData->title = $oldData->title ?? 'N/A';
            $oldData->description = $oldData->description ?? 'N/A';
            $oldData->file_name = $oldData->file_name ?? 'N/A';
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
        }

        $columnsToSend = [ 'binfo_category_id','binfo_subcategory_id','title','description','file_name','status'];

        $filteredTmpData = $tmpData->only($columnsToSend);

        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }
}
