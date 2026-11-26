<?php
/**
 * User:Tanay Kumar Roy
 * Email:tanayroy12@gmail.com
 * Created by Tanay Kumar Roy<tanayroy12@gmail.com> on 3/31/2020.
 */

namespace App\Http\Controllers;


use App\LogActivity;

class LogActivityController extends Controller
{
	public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        //$this->middleware('role:superadmin|admin');
        $this->middleware(['role:superadmin|admin']);
    }
    public function index(){
        $rows = LogActivity::all();
        return view('log_activity.index',['rows'=>$rows]);
    }
}
