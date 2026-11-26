<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request;
use App\Http\Requests\RestrictedIpRequest;
use App\Http\Requests\RoleRequest;
use App\Permission;
use App\RestrictedIp;
use App\RestrictedIps;
use App\User;
use Illuminate\Support\Facades\DB;

class RestrictedIpController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
       // $this->middleware('role:superadmin|admin');
        $this->middleware(['role:superadmin|admin']);
    }

    public function index()
    {
        $restrictedIps= new RestrictedIp();

        $restrictedIpsData = $restrictedIps->get();
        $title = "LogUsers";
        $title_for_layout = 'LogUsers';

        return view('RestrictedIps/index',compact("title_for_layout","title","restrictedIpsData"));
    }
    public function create()
    {
        $title = "Restricted Ips";
        $title_for_layout = 'Create Restricted Ips';
        $users = User::where('status',1)->get();
        return view('RestrictedIps/create',compact("title_for_layout","title",'users'));
    }

    public function store(RestrictedIpRequest $request)
    {
        $ipAddress = $request->ipAddress;

        if(!empty($ipAddress)) {
            $restrictedIps = new RestrictedIp;
            $restrictedIps->user_id = $request->user_id;
            $restrictedIps->ip = $request->ipAddress;
            $restrictedIps->save();
        }

        flash('Restricted Ip successfully created.', 'success');
        return redirect('/restrictedIps');
    }

    public function edit($id)
    {
        $title = "RestrictedIp";
        $title_for_layout = 'Edit RestrictedIp';

        $restrictedIp =RestrictedIp::find($id);
        $users = User::where('status',1)->get();
        return view('RestrictedIps/edit',compact(['title_for_layout','title','restrictedIp','users']));
    }

    public function update(RestrictedIpRequest $request, $id)
    {
        $restrictedIp = RestrictedIp::find($id);
        $restrictedIp->user_id = $request->user_id;
        $restrictedIp->ip = $request->ipAddress;
        $restrictedIp->save();
        flash('Restricted Ip successfully updated.', 'success');
        return redirect('/restrictedIps');
    }

    public function destroy($id)
    {
        RestrictedIp::destroy($id);

        flash('Restricted Ip successfully deleted.', 'success');
        return back();
    }

}
