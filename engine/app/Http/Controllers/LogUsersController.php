<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\LogUser;
use App\User;


class LogUsersController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        // $this->middleware(['role:superadmin|admin,accessReport']);
        $this->middleware(['role:superadmin|admin|audit', 'permission:accessReport']);
    }

    public function index(Request $request)
    {

        $searchDataForView = array();
        $searchDataForView['date_from'] = $request->date_from;
        $searchDataForView['date_to'] = $request->date_from;
        $logsData = "";
        $userID = "";
        $title = "LogUsers";
        $title_for_layout = 'LogUsers';
        $users = User::where('status', 1)->get();

        if (!empty($request->all())) {

            $userID = $request->user_id;

            if (!empty($request->date_from)) {
                $todate = "";
                if (!empty($request->date_to)) {
                    $todate = date('Y-m-d 23:59:59', strtotime($request->date_to));
                } else {
                    $todate = date('Y-m-d 23:59:59', time());
                }

                $logUser = new LogUser();
                $dataObj = $logUser
                    ->select(
                        "log_users.*",
                        "users.name as user_name"
                    )
                    ->Join('users', 'log_users.user_id', '=', 'users.id')
                    ->where('log_users.user_id', $request->user_id)
                    ->whereBetween('log_users.created_at', [date('Y-m-d 00:00:00', strtotime($request->date_from)), $todate])
                    ->get();

                //echo $todate.'----'.date('Y-m-d',strtotime($request->date_from));
                //pr($request->all());
                //prd($dataObj->toArray());

                $logsData = $dataObj;
                $searchDataForView['date_from'] = date('d-m-Y', strtotime($request->date_from));
                $searchDataForView['date_to'] = date('d-m-Y', strtotime($todate));
            } else {
                //flash('Please select From Date!!!', 'danger');
            }
        } else {
            //flash('Please select From Date!!!', 'danger');
        }

        return view('LogUsers/index', compact("title_for_layout", "title", "logsData", "searchDataForView", "users", "userID"));
    }
}
