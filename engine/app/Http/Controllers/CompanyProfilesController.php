<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Http\UploadedFile;

use App\Http\Requests;
use App\Http\Requests\CompanyProfileRequest;
use App\Http\Controllers\Controller;

use App\CompanyProfile;
use Validator;



class CompanyProfilesController extends Controller
{

    protected $modelName;

    public function __construct()
    {
        // $this->title = 'This is Demo Title';
        $this->modelName = new CompanyProfile;
        parent::__construct();
    }

    /***********************************
    * store Method
    * @ Add / Modify Form Company Information
    * @ Sending data to store method
    *
    ************************************/
    public function add()
    {
        $uploadError = 0;
        $uploadErrorMsg = "";
        $dataForView = array();
        $companyProfileData = array();
       
        $companyProfileDataObj = $this->modelName->all()->first();
        if (!empty($companyProfileDataObj)) {
            $companyProfileData = $companyProfileDataObj->toArray();
        }
       
        $dataForView["id"] = (!empty($companyProfileData["id"])) ? $companyProfileData["id"] : "";
        $dataForView["name"] = (!empty($companyProfileData["name"])) ? $companyProfileData["name"] : "";
        $dataForView["address"] = (!empty($companyProfileData["address"])) ? $companyProfileData["address"] : "";
        $dataForView["company_vision"] = (!empty($companyProfileData["company_vision"])) ? $companyProfileData["company_vision"] : "";
        $dataForView["email"] = (!empty($companyProfileData["email"])) ? $companyProfileData["email"] : "";
        $dataForView["mobile_no"] = (!empty($companyProfileData["mobile_no"])) ? $companyProfileData["mobile_no"] : "";
        $dataForView["logo"] = (!empty($companyProfileData["logo"])) ? $companyProfileData["logo"] : "";
        
        
        $home_menu_icon = "glyphicon glyphicon-fire";

        $title = " Company Profile";
        $title_for_layout = 'Company Profile';

        return view('CompanyProfiles/add',compact("uploadErrorMsg","uploadError","companyProfileData","dataForView","title","title_for_layout","home_menu_icon"));
    }

    /***********************************
    * store Method
    * @ Add / Modify Company Information
    * @ Upload Company Profile Picture
    *
    ************************************/
    public function store(CompanyProfileRequest $request)
    {
        $companyProfileData = array();
       
        $companyProfileDataObj = $this->modelName->all()->first();
        if (!empty($companyProfileDataObj)) {
            $companyProfileData = $companyProfileDataObj->toArray();
        }
       
        $dataForView["id"] = (!empty($companyProfileData["id"])) ? $companyProfileData["id"] : "";
        $dataForView["name"] = (!empty($companyProfileData["name"])) ? $companyProfileData["name"] : "";
        $dataForView["address"] = (!empty($companyProfileData["address"])) ? $companyProfileData["address"] : "";
        $dataForView["company_vision"] = (!empty($companyProfileData["company_vision"])) ? $companyProfileData["company_vision"] : "";
        $dataForView["email"] = (!empty($companyProfileData["email"])) ? $companyProfileData["email"] : "";
        $dataForView["mobile_no"] = (!empty($companyProfileData["mobile_no"])) ? $companyProfileData["mobile_no"] : "";
        $dataForView["logo"] = (!empty($companyProfileData["logo"])) ? $companyProfileData["logo"] : "";

        if ($request->isMethod('post')) {

            if (!empty($dataForView["id"])) {
               $this->modelName = $this->modelName->find($dataForView["id"]); 
               $this->modelName->modified_by = Auth::user()->id;
            } else {
                $this->modelName->created_by = Auth::user()->id;
            }
            $this->modelName->name = $request->name;
            $this->modelName->address = $request->address;
            $this->modelName->company_vision = $request->company_vision;
            $this->modelName->email = $request->email;
            $this->modelName->mobile_no = $request->mobile_no;
            $this->modelName->status = 1;
            $this->modelName->ip = $this->getClientIp();


            if(!empty($request->file('company_logo'))) {

                $file = $request->file('company_logo');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $destinationPath = base_path() . '/public/img/company_profile/';
                // $newFileName = 'img_logo.' . $extension;
                $newFileName = 'img_logo.png';

                $this->modelName->logo = $newFileName; // If you want to save Image name on Database Just include img_ before photo name
                $request->file('company_logo')->move($destinationPath, $newFileName);
            }
           
            if ($this->modelName->save()) {
                toast()->success('Company Profile has been update successfully', 'Success');
                return redirect('CompanyProfiles')->with("uploadErrorMsg","uploadError","companyProfileData","dataForView");
            } else {
                flash('Failed to insert data', 'danger');
            }
           
        }
    }
}
