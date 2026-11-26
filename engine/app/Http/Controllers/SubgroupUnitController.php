<?php
/**
 * User:Tanay Kumar Roy
 * Email:tanayroy12@gmail.com
 * Created by Tanay Kumar Roy<tanayroy12@gmail.com> on 3/19/2020.
 */

namespace App\Http\Controllers;


use App\SubgroupUnit;
use Illuminate\Http\Request;

class SubgroupUnitController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        $this->middleware(['role:superadmin|admin']);
    }
    public function index(){
        $tblData = SubgroupUnit::all();
        return view('subgroup_unit.index',compact('tblData'));
    }
    public function create(){
        return view('subgroup_unit.create');
    }
    public function store(Request $request){
        $this->validate($request,[
            'group_info_id'=>'required',
            'department_id'=>'required',
            'subgroup_info_id'=>'required',
        ]);
        foreach ($request->units as $unit) {

            $row = SubgroupUnit::create([
                'group_info_id' => $request->group_info_id,
                'department_id' => $request->department_id,
                'subgroup_info_id' => $request->subgroup_info_id,
                'unit_id' => $unit,
                'is_active'=>1
            ]);
        }
        return redirect('unit-assign');
    }
    public function edit($id){
        $row = SubgroupUnit::find(decrypt($id));
        return view('subgroup_unit.edit',compact('row'));
    }
    public function update(Request $request,$id){
        $this->validate($request,[
            'name'=>'required'
        ]);
        $row = SubgroupUnit::find($id);
        $row->update([
            'group_info_id'=>$request->group_info_id,
            'department_id'=>$request->department_id,
            'name'=>$request->name,
            'description'=>$request->description
        ]);
        return redirect('unit-assign');
    }
    public function activate($id,$state){
        $row = SubgroupUnit::find(decrypt($id));
        $row->update([
           'is_active'=>$state
        ]);
        return redirect()->back();
    }
}
