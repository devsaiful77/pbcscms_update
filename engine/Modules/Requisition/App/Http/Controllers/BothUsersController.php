<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\Enum\AccessApp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class BothUsersController extends Controller
{
    public function index()
    {
        return view('requisition::Both-user/index');
    }

    public function service()
    {
        Session::put('module', AccessApp::ServiceComplaint);
        return redirect('/');
    }

    public function requisiution()
    {
        Session::put('module', AccessApp::Requisition);
        return redirect()->route('requisition.requisition.index');
    }
}
