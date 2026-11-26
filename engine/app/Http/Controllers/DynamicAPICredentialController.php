<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;

use App\DynamicAPICredential;
use App\CIFModificationUrl;


class DynamicAPICredentialController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware(['role_or_permission:superadmin|admin|accessDynamicAPICredential']);
    }

    public function index()
    {
        $title = "Dynamic API Credential List";
        $title_for_layout = "Dynamic API Credential List";
        $tblData = array();
        $dataObj = DynamicAPICredential::select("cif_modification_url.id","cif_modification_url.name","dynamic_api_credential.*")
            ->join('cif_modification_url', 'dynamic_api_credential.api', 'cif_modification_url.id')
            ->orderBy("dynamic_api_credential.id","DESC")
            ->get();
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }
        return view('DynamicAPICredential.index',compact('title','title_for_layout','tblData'));
    }

    public function add()
    {
        $title = "Add Dynamic API Credential";
        $title_for_layout = "Add Dynamic API Credential";
        $cifUrl = CIFModificationUrl::select("id","name")
            ->where("status", 1)
            ->orderBy("id","DESC")
            ->get();
        return view('DynamicAPICredential.add',compact('title','title_for_layout','cifUrl'));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'api'       =>  'required',
            'user_name' =>  'required',
            'password'  =>  'required',
        ]);
        $modelName = new DynamicAPICredential;
        if ($request->isMethod('post')) {
            $modelName->api = $request->get('api');
            $modelName->username = $request->get('user_name');
            $modelName->password = encrypt($request->get('password'));
            if ($modelName->save()) {
                flash('Dynamic API Credential has been stored successfully', 'success');
                return redirect('/DynamicAPICredential');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('/DynamicAPICredential/add');
            }
        }
    }

    public function edit($id = null)
    {
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            abort(403,'Un-Authorize Access!!!');
        }
        $title = "Edit Dynamic API Credential";
        $title_for_layout = 'Edit Dynamic API Credential';
        $dataForView = DynamicAPICredential::where('id', $id)->first();
        $cifUrl = CIFModificationUrl::select("id","name")
            ->where("status", 1)
            ->orderBy("id","DESC")
            ->get();
        return view('DynamicAPICredential.edit', compact('title', 'title_for_layout', 'dataForView','cifUrl'));
    }

    public function update($id = null, Request $request)
    {
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            abort(403,'Un-Authorize Access!!!');
        }
        $this->validate($request,[
            'api'       =>  'required',
            'user_name' =>  'required',
            'password'  =>  'required',
        ]);
        $modelName = new DynamicAPICredential;
        if ($request->isMethod('post')) {
            $modelName = $modelName->where('id', $id)->first();
            $modelName->api = $request->get('api');
            $modelName->username = $request->get('user_name');
            $modelName->password = encrypt($request->get('password'));
            if ($modelName->save()) {
                flash('Dynamic API Credential has been updated successfully', 'success');
                return redirect('/DynamicAPICredential');
            } else {
                flash('Failed to update data', 'danger');
                return redirect()->back();
            }
        }
    }

}
