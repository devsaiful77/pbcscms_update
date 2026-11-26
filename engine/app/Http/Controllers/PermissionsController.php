<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;

use App\Http\Requests;
use App\Http\Requests\PermissionRequest;
use App\Permission;

class PermissionsController extends Controller
{
    
    public function __construct()
    {
        $this->modelName = new Permission;
        parent::__construct();
    }
    
    public function index(Request $request)
    {
        
        $dataForView = array();
        $searchDataForView = array();
        $tblData = array();  

        $dataObj = $this->modelName
                        ->orderBy("id","DESC")
						->get();
                        //->paginate(PAGINATION_NUMBER);
        if (!empty($dataObj)) {
            $tblData = $dataObj->toArray();
        }

        $home_menu_icon = "fa fa-user-secret";
        $title = "Permission List";
        $title_for_layout = "Permission List";

        return view('Permissions.index',compact('title','title_for_layout','home_menu_icon','tblData','searchDataForView','dataObj'));
    }
    public function add()
    {
        $id = 0;
        $dataForView = array();     
        $allControllerList = [];

        foreach (Route::getRoutes()->getRoutes() as $route)
        {
            $action = $route->getAction();

            if (array_key_exists('controller', $action))
            {
                $explodedVal = explode('\\', $action['controller']);
                $cotrollerWithAction = end($explodedVal);
                $allControllerList[] = $action['controller'];
                $controllerWithActionArray = explode('@', $cotrollerWithAction);
                $contollerName = $controllerWithActionArray[0];
                $controllerWiseArray[$contollerName] = $contollerName;
            }
        }
                            
        $home_menu_icon = "fa fa-user-secret";
        $title = "Add Permission";
        $title_for_layout = "Add Permission";

        return view('Permissions.add',compact('title','title_for_layout','home_menu_icon','id','dataForView','controllerWiseArray'));
    }
  
    public function store(PermissionRequest $request)
    {
      
        if ($request->isMethod('post')) {   
                    
            $this->modelName->name = $request->name;
            $this->modelName->controller_name = $request->controller_name;
            $this->modelName->display_name = $request->display_name;
            $this->modelName->description = $request->description;

            if ($this->modelName->save()) {
               // toast()->success('Permission has been inserted successfully', 'Success');
                return redirect('Permissions');
            } else {
                flash('Failed to insert data', 'danger');
                return redirect('Permissions/add');
            }
        }
    }
}
