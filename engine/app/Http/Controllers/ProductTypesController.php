<?php

namespace App\Http\Controllers;

use App\ProductType;
use App\Http\Requests;
use App\ProductTypeTmp;
use Illuminate\Http\Request;

use App\Http\Requests\UnitRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class ProductTypesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $checker = false;

    public function __construct()
    {
       // $this->middleware('ability:superadmin|admin,adminActivity|true');
        $this->middleware(['role_or_permission:superadmin|admin|adminActivity|accessProductType|accessProductTypeChecker|true']);
        parent::__construct();
    }

    public function accessProductTypeChecker(){
        if (Auth::check() && Auth::user()->hasPermissionTo('accessProductTypeChecker')) {
            return $this->checker = true;
        }else{
            return $this->checker;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $productTypeModelName = new ProductType;
        // http_build_query($searchDataForView) division
        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();
        $dataObj = $productTypeModelName
                        ->select("id","name","description",DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),"status")
                        ->orderBy("name","ASC")
						->get();
                        //->paginate(PAGINATION_NUMBER);
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Product Type List";
        $title_for_layout = "Product Type List";
        $checker = $this->accessProductTypeChecker();
        return view('ProductTypes.index',compact('title','title_for_layout','tblData','searchDataForView','dataObj', 'checker'));
    }
    public function add()
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $id = 0;
        $tmpId = 0;
        $dataForView = array();


        $title = "Add Product Type";
        $title_for_layout = "Add Product Type";

        return view('ProductTypes.add',compact('title','title_for_layout','id','dataForView', 'tmpId'));
    }

    public function store(UnitRequest $request)
    {
        
        $productTypeModelName = new ProductType;

        if ($request->isMethod('post')) {

            $productTypeModelName->name = $request->name;
            $productTypeModelName->description = (!empty($request->description)) ? $request->description : '';
            $productTypeModelName->status = 1;
            $productTypeModelName->created_by = Auth::user()->id;
            $productTypeModelName->ip = $this->getClientIp();

            if ($productTypeModelName->save()) {
                flash('Product Type has been inserted successfully', 'success');
                return redirect('ProductTypes');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('ProductTypes/add');
            }
        }
    }

    public function edit($id = null, Request $req)
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }
        $tmpId = 0;

        $productTypeModelName = new ProductType;
        $title = " Edit Product Type";
        $title_for_layout = 'Edit Product Type';
        $searchDataForView = $req->all();


        $dataForView = $productTypeModelName->where
                                            ([
                                                ['id', $id]
                                            ])->first();

        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }
        return view('ProductTypes.add', compact('title', 'title_for_layout', 'dataForView','searchDataForView','id', 'tmpId'));
    }

    public function productTypesTmpEdit($id = null, Request $req)
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker) {
            abort(403, 'You do not have permission to access this page.');
        }

        $productTypeModelName = new ProductTypeTmp;
        $title = " Edit Product Type";
        $title_for_layout = 'Edit Product Type';
        $searchDataForView = $req->all();
        $tmpId = $id;


        $dataForView = $productTypeModelName->where
                                            ([
                                                ['id', $id]
                                            ])->first();

        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }
        return view('ProductTypes.add', compact('title', 'title_for_layout', 'dataForView','searchDataForView','id', 'tmpId'));
    }

    public function update($id = null, UnitRequest $request)
    {
        $isTemp = !empty($request->tmpId) && $request->tmpId != 0;

        $columnArr = ['name'];
        $charArr = ['<', '>', '"', "'", '|', '=', '#', '%', '&', '*', '!'];
        $validationResult = validateSpecialChars($request->all(), $columnArr, $charArr);
        if ($validationResult !== true) {
            return back()->withErrors($validationResult)->withInput();
        }
        
        $tableName = $isTemp ? 'product_types_tmp' : 'product_types';

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:'.$tableName.',name,'.$id,
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // $productTypeModelName = new ProductType;
        $data = $request->all();

        // $existingTmpRecords = ProductTypeTmp::where('master_id', $id)->get();
        $existingRecords = $isTemp ? ProductTypeTmp::where('id', $id)->get() : ProductTypeTmp::where('master_id', $id)->get();
        
        $hasInvalidRecord = $existingRecords->contains(function ($record) {
            return $record->form_status != 7 && $record->form_status != -1;
        });
        if ($hasInvalidRecord) {
            flash('An entry already exists, please wait for Checker Approval', 'danger');
        }
            

        $additionalParams = (!empty($data['additionalParams'])) ? $data['additionalParams'] : "";
        unset($data['additionalParams']);

        $data['modified_by'] = Auth::user()->id;

        if ($isTemp) {
            $tmpProductType = ProductTypeTmp::where('id', $id)->first();
        }else{
            $tmpProductType = new ProductTypeTmp;
            $update = ProductType::where('id', $id)->first();
        }

        $tmpProductType->name = $request->name;
        $tmpProductType->description = (!empty($request->description)) ? $request->description : '';
        $tmpProductType->status = 2;
        $tmpProductType->form_status = 0;
        $tmpProductType->created_by = Auth::user()->id;
        $tmpProductType->ip = $this->getClientIp();
        $tmpProductType->action = "Edit";
        $tmpProductType->master_id = $isTemp ? $tmpProductType->master_id : $update->id;
       
        if ($tmpProductType->save()) {
            flash('Product Type has been updated successfully', 'success');
            return redirect('/ProductTypes'.$additionalParams);
        } else {
            flash('Failed to update Product Types, Please try again', 'danger');
        }
    }

    public function status($id = null, $status = "")
    {
        $productTypeModelName = new ProductType;
        if ($status != 1 && $status != 0) {
            flash($status.' is not Allowed!!!','danger');
            return Redirect::back();
        }

        $data['status'] = $status;
        $update = $productTypeModelName->where([['id', $id]])->first();

        $update->update($data);

        if ($status == 1) {
            flash('Product Type has been Active', 'success');
        } elseif ($status == 0) {
            flash('Product Type has been Inactive', 'danger');
        }
        // return redirect('/Groups');
        return Redirect::back();

    }
    
    public function approveProductTypes($id)
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }
        DB::beginTransaction();
        
        try {
            $tmpProductTypes = DB::table('product_types_tmp')->where('id', $id)->where('status', '!=', 3)->first();
            // $tmpDepartment = DB::table('divisions_tmp')->where('id', $id)->where('status', 0)->first();
            
            if (!$tmpProductTypes) {
                flash('Department not found or not approved yet.', 'danger');
                return redirect()->back();
            }

            $productTypesData = [
                'name'        => $tmpProductTypes->name,
                'description' => $tmpProductTypes->description,
                'status'      => $tmpProductTypes->status == 2 ? 1 : $tmpProductTypes->status,
                'created_by'  => Auth::user()->id,
                'modified_by' => Auth::user()->id,
                'ip'          => $this->getClientIp(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if($tmpProductTypes->master_id != null && $tmpProductTypes->action != "Add"){
                DB::table('product_types')->where('id', $tmpProductTypes->master_id)->update($productTypesData);
            }else{
                DB::table('product_types')->insert($productTypesData);
            }

            DB::table('product_types_tmp')->where('id', $tmpProductTypes->id)->delete();
            DB::commit();
            if($tmpProductTypes->status == 0){
                flash('Product Types has been inActive.', 'danger');
            }elseif($tmpProductTypes->status == 1){
                flash('Product Types has been activated.', 'success');
            }else{
                flash('Product Types has been approved.', 'success');
            }
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash('An error occurred during the approval process: ' . $e->getMessage(), 'danger');
            return redirect()->back();
        }
    }

    public function productTypesTmpList()
    {
        $checker = $this->accessProductTypeChecker();
        if($checker){
            $isChecker = true;
            $tmpData = ProductTypeTmp::whereNotIn('form_status', [7, -1])->get();
        }else{
            $isChecker = false;
            $tmpData = ProductTypeTmp::whereIn('form_status', [7, -1])->get();
        }

        return view('ProductTypes.index_tmp',compact('tmpData', 'isChecker',));
    }

    public function productTypesAssign($id)
    {
        $tmpData = ProductTypeTmp::where('id', $id)->first();

        $tmpData->form_status = 1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->save();

        $data = [
            'message' => 'Assigned Successfully from your End!',
            'id' => $id,
        ];

        return response()->json($data, 200);
        // flash('Assigned Successfully from your End!', 'success');
        // return redirect()->back();
    }

    public function checkerTableData($id){
        $tmpData = ProductTypeTmp::findOrFail($id);

        if (!empty($tmpData->master_id)) {
            $oldData = ProductType::findOrFail($tmpData->master_id);
        }

        $tmpData->product_name = $tmpData->name ?? null;
        $tmpData->status = $tmpData->status == 1 ? "Active" : "Inactive";

        unset($tmpData->name);

        if (isset($oldData)) {
            $oldData->product_name = $oldData->name ?? null;
            $oldData->status = $oldData->status == 1 ? "Active" : "Inactive";
            unset($oldData->name);
        }

        $columnsToSend = ['product_name', 'description','status', 'action'];

        $filteredTmpData = $tmpData->only($columnsToSend);

        $filteredOldData = isset($oldData) ? $oldData->only($columnsToSend) : null;

        $response = [
            'old_data' => $filteredOldData,
            'new_data' => $filteredTmpData
        ];

        return response()->json($response, 200);
    }

    public function buttonControl($id){
        $tmpData = ProductTypeTmp::findOrFail($id);

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

    public function productTypesSendback(Request $request, $id)
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);

        $tmpData = ProductTypeTmp::where('id', $id)->first();
        $tmpData->form_status = 7;
        $tmpData->modified_by = null;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Send back Successfully!', 'success');
        return redirect()->back();
    }
    public function productTypesReject(Request $request, $id)
    {
        $checker = $this->accessProductTypeChecker();
        if ($checker == false) {
            abort(403, 'You do not have permission to access this page.');
        }

        $request->validate([
            'comments' => 'required'
        ]);
        
        $tmpData = ProductTypeTmp::where('id', $id)->first();
        $tmpData->form_status = -1;
        $tmpData->modified_by = Auth::user()->user_id;
        $tmpData->comments = $request->comments;
        $tmpData->save();

        flash('Rejected !!!', 'danger');
        return redirect()->back();
    }
}
