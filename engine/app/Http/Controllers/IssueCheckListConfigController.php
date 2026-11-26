<?php

namespace App\Http\Controllers;


use App\IssueCheckListConfig;
use App\IssueConfig;
use Illuminate\Http\Request;
use App\Http\Requests\IssueConfigRequest;

class IssueCheckListConfigController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        //$this->middleware('ability:superadmin|admin,accessIssues')->except('issueFormField');
        $this->middleware(['role_or_permission:superadmin|admin|accessIssues'])->except('issueFormField');
    }

    public function store(IssueConfigRequest $request)
    {
        if (!empty($request->issue_id)) {
		  IssueCheckListConfig::where('issue_id',$request->issue_id)->delete();
            if(!empty($request->addmore)){
                foreach ($request->addmore as $key=>$r){
                    IssueCheckListConfig::create([
                        'issue_id'=>$request->issue_id,
                        'label_name'=>$r['label_name'],
                        'field_type'=>$r['field_type'],
                        'field_name'=>$r['field_name'],
                        'options'=>$r['options'],
                        'placeholder'=>$r['placeholder'],
                        'maximum_length'=>$r['maximum_length'],
                        'is_required'=>$r['is_required'],
                    ]);
                }
                flash('Issue Checklist have been configured successfully.', 'success');
            }
        } else {
            flash('Failed to configure Issue Checklist.', 'danger');
        }
        return redirect('Issues');
    }

    public function update(Request $request,$id)
    {
		$this->validate($request,[
           'label_name.*' =>'required',
           'field_name.*' =>'required'
        ]);

        $a=array();
		$required = (!empty($request->is_required)) ? $request->is_required : 0;

		$rows = IssueCheckListConfig::where('issue_id',$id)->delete();


		if($required > 0){
            foreach($request->is_required as $r ){
                $a[]=$r;
            }

            foreach ($request->label_name as $key=>$label_name){

                IssueCheckListConfig::create([
                    'issue_id'=>$request->issue_id,
                    'label_name'=>$label_name,
                    'field_type'=>$request['field_type'][$key],
                    'field_name'=>$request['field_name'][$key],
                    'options'=>$request['options'][$key],
                    'placeholder'=>$request['placeholder'][$key],
                    'maximum_length'=>$request['maximum_length'][$key],
                    'is_required'=>$a[$key],
                ]);
            }
        }
        return redirect('Issues');
    }

    public function issueFormField(Request $request)
    {
        //dd($request);
        $service_request = $request->issue_id;
        $rows = IssueCheckListConfig::where('issue_id',$service_request)->get();

        return view('partials.extra_form_field',['issue_fields'=>$rows]);
    }
}
