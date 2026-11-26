<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Requests\UnitRequest;
use App\Department;
use App\Unit;
use App\UnitChild;

class UnitsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('ability:superadmin|admin,adminActivity|true');
        $this->middleware(['role_or_permission:superadmin|admin|adminActivity|true']);
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $unitModelName = new Unit;
        // http_build_query($searchDataForView)
        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();
        $dataObj = $unitModelName
                        ->select("id","name","description",DB::raw("CASE WHEN status = 1 THEN 'Active' WHEN status = 0 THEN 'Inactive' ELSE 'Invalid' END AS status_name"),"status")
                        ->orderBy("id","ASC")
                        ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $title = "Unit List";
        $title_for_layout = "Unit List";

        return view('Units.index',compact('title','title_for_layout','tblData','searchDataForView','dataObj'));
    }
    public function add(Request $request)
    {
        $id = 0;
        $dataForView = array();

        $departmentModelName = new Department;
        $allDepartmentData = $departmentModelName
                            ->select('id','name')
                            ->where('status',1)
                            ->orderBy('name','ASC')
                            ->pluck('name', 'id')
                            ->toArray();

        $checkedDepartmentData = $request->old('department');
        $title = "Add Unit";
        $title_for_layout = "Add Unit";

        return view('Units.add',compact('title','title_for_layout','id','dataForView','allDepartmentData','checkedDepartmentData'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required'
        ]);
        $unitModelName = new Unit;
        if ($request->isMethod('post')) {
            $unitModelName->name = $request->name;
            $unitModelName->description = $request->description;
            $unitModelName->status = 1;
            $unitModelName->created_by = Auth::user()->id;
            $unitModelName->ip = $this->getClientIp();
            if ($unitModelName->save()) {

                /* End of Saving Associate Data */
                flash('Unit has been inserted successfully', 'success');
                return redirect('Units');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('Units/add');
            }
        }
    }

    public function edit($id)
    {
        $unitModelName = new Unit;
        $title = " Edit Unit";
        $title_for_layout = 'Edit Unit';






        $dataForView = $unitModelName->where
                                            ([
                                                ['id', $id]
                                            ])->first();
        if ($dataForView->status == 0) {
            abort(403,'Edit Not Allowed !!!');
        }
        return view('Units.edit', compact('title', 'title_for_layout', 'dataForView','id'));
    }

    public function update($id = null, Request $request)
    {
        $this->validate($request,[
            'name'=>'required'
        ]);
        $unitModelName = new Unit;
        $data = $request->all();

        $additionalParams = (!empty($data['additionalParams'])) ? $data['additionalParams'] : "";
        unset($data['additionalParams']);

        $data['modified_by'] = Auth::user()->id;
        $update = $unitModelName->where([['id', $id]])->first();

        $departmentList = (!empty($data['department'])) ? $data['department'] : array();
        unset($data['department']);

        if ($update->update($data)) {
            /* Saving Associate Data */
            DB::table('unit_childs')->where('unit_id',$id)->delete();
            if (!empty($departmentList)) {
                $unitChildModelName = new UnitChild;
                foreach ($departmentList as $gKey => $gValue) {
                    $unitChildModelName = new UnitChild;
                    $unitChildModelName->unit_id = $id;
                    $unitChildModelName->department_id = $gValue;
                    $unitChildModelName->save();
                }
            }
            /* End of Saving Associate Data */
            flash('Unit has been updated successfully', 'success');
            return redirect('/Units'.$additionalParams);
        } else {
            flash('Failed to update Units, Please try again', 'danger');
        }
    }

    public function status($id = null, $status = "")
    {
        $unitModelName = new Unit;
        if ($status != 1 && $status != 0) {
            flash($status.' is not Allowed!!!','danger');
            return Redirect::back();
        }
        $data['status'] = $status;
        $update = $unitModelName->where([['id', $id]])->first();

        $update->update($data);

        if ($status == 1) {
            flash('Unit has been Active', 'success');
        } elseif ($status == 0) {
            flash('Unit has been Inactive', 'danger');
        }
        return Redirect::back();
    }
}
