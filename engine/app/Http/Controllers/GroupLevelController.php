<?php
/**
 * User:Tanay Kumar Roy
 * Email:tanayroy12@gmail.com
 * Created by Tanay Kumar Roy<tanayroy12@gmail.com> on 4/12/2020.
 */

namespace App\Http\Controllers;


use App\GroupLevel;
use Illuminate\Http\Request;

class GroupLevelController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        $this->middleware(['role:superadmin|admin']);
    }
    public function index(){
        $rows = GroupLevel::all();
        return view('group_level.index',['rows'=>$rows]);
    }
    public function create(){
        return view('group_level.create');
    }
    public function store(Request $request){
        $this->validate($request,[
           'name'=>'required'
        ]);
        $row = GroupLevel::create([
           'name'=>$request->name
        ]);
        return redirect('group-level');
    }
}
