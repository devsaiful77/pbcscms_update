<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

use App\Http\Requests\UserProfileRequest;


use App\User;
use Schema;
use Image;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
       // $this->middleware('role:superadmin|admin')->except('index');
        $this->middleware(['role:superadmin|admin'])->except('index');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = "Home";
        $title_for_layout = "Home";

        return view('Home/home',compact('title','title_for_layout'));
    }

    public function period()
    {

        $title = "Installment Period";
        $title_for_layout = "Installment Period";
        $home_menu_icon = "fa fa-clock-o";

        return view('Home/period',compact('title','title_for_layout','home_menu_icon'));
    }



    public function changeProfilePhoto(Request $request)
    {
        $uploadError = 0;
        $uploadErrorMsg = "";
        $dataForView = array();
        $companyProfileData = array();


        $home_menu_icon = "fa fa-user";
        $title = "Profile Photo";
        $title_for_layout = 'Profile Photo';


        return view('Users/change_profile_photo',compact("uploadErrorMsg","uploadError","companyProfileData","dataForView","title","title_for_layout"));
    }

    public function uploadProfilePhoto(UserProfileRequest $request)
    {
        $this->modelName = new User;

        if ($request->isMethod('post')) {
            $userId = Auth::user()->id;
            $this->modelName = $this->modelName->find($userId);
            if(!empty($request->file('profile_picture'))) {

                $file = $request->file('profile_picture');
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $authPropic = Auth::user()->profile_picture;
                $profile_picture = (!empty($authPropic)) ? $authPropic : 'profile_pic_'.sha1($filename . time()) . '.' . $extension;
                $destinationPath = base_path() . '/public/img/profile_picture/';
                $newFileName = $profile_picture;
                $this->modelName->profile_picture = $newFileName; // If you want to save Image name on Database Just include img_ before photo name
                $request->file('profile_picture')->move($destinationPath, $newFileName);
                Image::make($destinationPath.'/'.$newFileName)->resize(300, 200)->save($destinationPath.'/thumbnail/'.$newFileName); // use this if you want facade style code
            }

            if ($this->modelName->save()) {
                toast()->success('Profile Picture has been update successfully', 'Success');
                return redirect('ChangeProfilePhoto')->with("uploadErrorMsg","uploadError","companyProfileData","dataForView");
            } else {
                toast()->error('Profile Picture update failed, Please try again later.', 'Error');
            }
        }
    }

}
