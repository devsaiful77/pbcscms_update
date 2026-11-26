<?php
namespace App\Http\Controllers;


use App\Enum\FlowEnum;
use App\GroupInfo;
use App\SubgroupInfo;
use App\IssueGroupWorkflow;
use App\IssueSubgroupWorkflow;
use App\IssueWorkflow;
use App\SubWorkflow;
use App\Reference;
use App\UnitItem;
use App\Services\UtilService;

use Illuminate\Http\Request;
use App\Http\Requests\SubFlowRequest;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Throwable;

class WorkflowController extends Controller
{

	public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware(['role:superadmin|admin|CEX_ADMIN'])->except('getGroupwiseWorkflow','issueAttachment','index','show');
        $this->middleware(['role_or_permission:superadmin|admin|accessWorkflow'])->only('index','show');
        parent::__construct();
    }

    public function index()
    {
       // dd(Auth::user()->permission('accessWorkflow')->get());
        $rows = IssueWorkflow::all();
        return view('workflow.index',['rows'=>$rows]);
    }

    public function create()
    {
        $group_carts = Session::get('group_cart');
        $rows = [];
        if ($group_carts){
            foreach ($group_carts as $key => $r) {

                $rows[] = $r;
            }
        }
        return view('workflow.create',['rows'=>$rows]);
    }

    public function addToGroup(Request $request)
    {
        $group= GroupInfo::find($request->id);
        $group_cart = Session::get('group_cart');
        if ($group_cart) {

            foreach ($group_cart as $key => $c) {
                if($c['id']==$group->id) {
                    $group_cart [$c['id']] = array(
                        'id' => $group->id,
                        'name'=>$group->name,

                    );
                    break;
                }else{
                    $group_cart [$group->id] = array(
                        'id' => $group->id,
                        'name'=>$group->name,
                    );

                }
                session()->put('group_cart',$group_cart);
            }
            session()->put('group_cart',$group_cart);
        }else{
            $group_cart [$group->id] = array(
                'id' => $group->id,
                'name'=>$group->name,
            );
            session()->put('group_cart',$group_cart);
        }
        $group_cart = Session::get('group_cart');
        $rows = [];
        foreach ($group_cart  as $key=>$r){

            $rows[] = $r ;
        }

        return view('partials.group_info',['rows'=>$rows]);
    }
    public function addToGroupEdit(Request $request)
    {
        $group= GroupInfo::find($request->id);
        $group_cart = Session::get('group_cart');
        //session()->forget('cart');
        $counter = $request->counter;

        if ($group_cart) {

            foreach ($group_cart as $key => $c) {
                if($c['id']==$group->id) {
                    $group_cart [$c['id']] = array(
                        'id' => $group->id,
                        'name'=>$group->name,

                    );
                    break;
                }else{
                    $group_cart [$group->id] = array(
                        'id' => $group->id,
                        'name'=>$group->name,
                    );

                }
                session()->put('group_cart',$group_cart);
            }
            session()->put('group_cart',$group_cart);
        }else{
            $group_cart [$group->id] = array(
                'id' => $group->id,
                'name'=>$group->name,
            );
            session()->put('group_cart',$group_cart);
        }
        $group_cart = Session::get('group_cart');
        $rows = [];
        foreach ($group_cart  as $key=>$r){

            $rows[] = $r ;
        }

        return view('partials.group_info_edit',['rows'=>$rows,'counter'=>$counter]);
    }

    public function groupClear()
    {
        Session::forget('group_cart');
        return redirect()->back();
    }

    public function getGroups()
    {
        $group_carts = Session::get('group_cart');
        $rows = [];
        foreach ($group_carts  as $key=>$r){

            $rows[] = $r ;
        }
        return view('partials.group_info',['rows'=>$rows]);
    }

    public function getGroupsEdit()
    {
        $group_carts = Session::get('group_cart');
        $rows = [];
        foreach ($group_carts  as $key=>$r){

            $rows[] = $r ;
        }
        $counter = \request('counter');
        return view('partials.group_info_edit',['rows'=>$rows,'counter'=>$counter]);
    }

    public function deleteGroup()
    {
        //session()->forget('cart');
        $id = \request('id');
        $group_cart= Session::get('group_cart');
        unset($group_cart[$id]);
        Session::put('group_cart', $group_cart);
        return redirect('/group-list');
    }

    public function deleteGroupEdit()
    {
        //session()->forget('cart');
        $id = \request('id');
        $counter = \request('counter');
        $group_cart= Session::get('group_cart');
        unset($group_cart[$id]);
        Session::put('group_cart', $group_cart);
        return redirect('/group-list-edit')->with('counter');
    }

    public function store(Request $request)
    {
        // prd($request->all());
        $this->validate($request,[
            'flow_type'=>'required',
            'issue_id'=>'required'
        ]);
        if(empty($request['touch_group'])){
			flash('Please select touch group!!!', 'danger');
            return redirect()->back();
        }
		if($request->flow_type==FlowEnum::FORWARD){
            $total=0;
            $item_attach = false;

			foreach ($request->touch_group as $key=>$priority_group){
                // && (!empty($request['priority_hold_checker_' . $priority_group]))//&& (!empty($request['priority_sla_checker_' . $priority_group])) && (!empty($request['priority_attach_checker_' . $priority_group]))&&
                if(!empty($request['priority_touch_checker_' . $priority_group]) && $request['priority_touch_checker_' . $priority_group] != 2){
                $total =  ++$key;
                }else{
                    flash('Please allow touch group checker (Touch Yes) !!!', 'danger');
                    return redirect()->back();
                }
            }
		}

        if($request->flow_type==FlowEnum::REGULAR){
            $total=0;
            $item_attach = false;
            foreach ($request->touch_group as $key=>$priority_group){
						// && (!empty($request['priority_hold_checker_' . $priority_group]))//&& (!empty($request['priority_sla_checker_' . $priority_group])) && (!empty($request['priority_attach_checker_' . $priority_group]))&&
                     if((!empty($request['priority_touch_checker_' . $priority_group])) && (!empty($request['priority_hold_maker_' . $priority_group]))&& (!empty($request['priority_sla_maker_' . $priority_group]))&&  (!empty($request['priority_attach_maker_' . $priority_group]))){

                        if(empty($request->groups)) {
    						if($request['priority_touch_checker_' . $priority_group]==2){
    								flash('Please allow touch group checker and enter all information correctly!!!', 'danger');
    								return redirect()->back();
    							}else{

    								 if((empty($request['priority_hold_checker_' . $priority_group])) || (empty($request['priority_sla_checker_' . $priority_group])) || (empty($request['priority_attach_checker_' . $priority_group]))){

    									flash('Please select touch group checker information correctly!!!', 'danger');
    									return redirect()->back();

    							}else{

                                    if($request['priority_attach_checker_' . $priority_group]==1){
                                         $item_attach = true;

                                     if(empty($request['priority_attach_checker_item_' . $priority_group])){
                                         $item_attach =false;
                                     }
                                     }else{
                                         $item_attach =true;
                                     }
                                     //echo $request['priority_touch_checker_' . $priority_group];die;

                                     if($item_attach==false){
                                         flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                                         return redirect()->back();
                                     }
                                }

    						}
                        }else{
                            if($request['priority_touch_checker_' . $priority_group]==1){

                                if($request['priority_attach_checker_' . $priority_group]==1){
                                     $item_attach = true;

                                 if(empty($request['priority_attach_checker_item_' . $priority_group])){
                                     $item_attach =false;
                                 }
                                 }else{
                                     $item_attach =true;
                                 }
                                 //echo $request['priority_touch_checker_' . $priority_group];die;

                                 if($item_attach==false){
                                     flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                                     return redirect()->back();
                                }

                            }else{
                                $request['priority_touch_checker_' . $priority_group] = 0;
                                $request['priority_hold_checker_' . $priority_group] = 0;
                                $request['priority_sla_checker_' . $priority_group] = 0;
                                $request['priority_attach_checker_' . $priority_group] = 0;
                            }
                        }

                         if($request['priority_attach_maker_' . $priority_group]==1){
                             $item_attach = true;
						 if(empty($request['priority_attach_maker_item_' . $priority_group])){
							 $item_attach =false;
						 }
                         }else{
                             $item_attach =true;
                         }
                         if($item_attach==false){
							 flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                             return redirect()->back();
                         }

                        $total =  ++$key;
                     }else{
						flash('Please allow touch group maker/checker correctly!!!', 'danger');
						return redirect()->back();
					 }

            }
            if($total!=count($request->touch_group)){
				flash('Please select touch group information correctly!!!', 'danger');
                return redirect()->back();
            }

            if(!empty($request->groups)) {
                $total_ = 0;
                $item_group_attach = false;

                foreach ($request->groups as $key2 => $group) {

					// (!empty($request['touch_checker_' . $key2])) && (!empty($request['hold_checker_' . $key2])) && (!empty($request['sla_checker_' . $key2])) && (!empty($request['attach_checker_' . $key2]))

                    if ((!empty($request['hold_maker_' . $key2])) &&  (!empty($request['sla_maker_' . $key2])) &&  (!empty($request['attach_maker_' . $key2]))) {

						if($request['touch_checker_' . $key2] == 1){

							if((empty($request['hold_checker_' . $key2])) || (empty($request['sla_checker_' . $key2])) || (empty($request['attach_checker_' . $key2]))){

								flash('Please select next level group checker information correctly!!!', 'danger');
								return redirect()->back();

							}else{

                                if ($request['attach_checker_' . $key2] == 1) {
                                    $item_group_attach = true;

                                    if (empty($request['attach_checker_item_' . $key2])) {
                                        $item_group_attach = false;
                                    }
                                } else {
                                    $item_group_attach = true;
                                }
                                if ($item_group_attach == false) {
                                    flash('Please enter next level group attachment quantity correctly!!!', 'danger');
                                    return redirect()->back();
                                }
                            }

						}else{

                            $request['hold_checker_' . $key2] = 0;
                            $request['sla_checker_' . $key2] = 0;
                            $request['attach_checker_' . $key2] = 0;
                            $request['attach_checker_item_' . $key2] = 0;
                        }

                        if ($request['attach_maker_' . $key2] == 1) {
                            $item_group_attach = true;
                            if (empty($request['attach_maker_item_' . $key2])) {
                                $item_group_attach = false;
                            }
                        } else {
                            $item_group_attach = true;
                        }
                        if ($item_group_attach == false) {
							flash('Please enter next level group attachment quantity correctly!!!', 'danger');
                            return redirect()->back();
                        }

                        $total_ = ++$key2;
                    }else{
						flash('Please select next level group maker correctly!!!', 'danger');
						return redirect()->back();
					 }

                }

                if ($total_ != count($request->groups)) {
					flash('Please select next level group correctly!!!', 'danger');
                    return redirect()->back();
                }
            }
        }
        $row= '';
        DB::transaction(function () use ($request, &$row) {
        $row = IssueWorkflow::create([
           'flow_type'=>$request->flow_type,
           'issue_id'=>$request->issue_id,
            'log'=>$request->log,
            'execute'=>$request->execute,
            'complain_sla_time' => ($request->flow_type==FlowEnum::FORWARD)? $request->complain_sla_time:0
            //'send_back'=>$request->send_back,
            //'cant_reach_to_customer'=>$request->cant_reach_to_customer
        ]);
        if (!empty($row)){
            $audit = new \OwenIt\Auditing\Models\Audit();
            $audit->auditable_type = 'App\IssueWorkflow';
            $audit->user_type = 'App\User';
            $audit->user_id = Auth::id();
            $audit->event = 'created';
            $audit->auditable_id = $row->issue_workflow_id;
            $audit->old_values = [];
            $audit->new_values = json_decode($row);
            $audit->ip_address = request()->ip();
            $audit->url = request()->fullUrl();
            $audit->user_agent = request()->userAgent();
            $audit->save();
        }

        foreach ($request->touch_group as $key=>$priority_group){

            $issueWgroup1 = IssueGroupWorkflow::create([
                    'issue_workflow_id' => $row->issue_workflow_id,
                    'group_info_id' => $priority_group,
                    'touch_checker' => ($request['priority_touch_checker_' . $priority_group]==1)?1:0,
                    'hold_maker' => ($request['priority_hold_maker_' . $priority_group]==1)?1:0,
                    'hold_checker' => ($request['priority_hold_checker_' . $priority_group]==1)?1:0,
                    'sla_maker' => $request['priority_sla_maker_' . $priority_group],
                    'sla_checker' => $request['priority_sla_checker_' . $priority_group],
                    'attach_maker' => ($request['priority_attach_maker_' . $priority_group]==1)?1:0,
                    'attach_maker_item' => $request['priority_attach_maker_item_' . $priority_group],
                    'attach_checker' => ($request['priority_attach_checker_' . $priority_group]==1)?1:0,
                    'attach_checker_item' => $request['priority_attach_checker_item_' . $priority_group],
                    'is_touch_point' => 1
                ]);

                if(!empty($issueWgroup1)){
                    $audit = new \OwenIt\Auditing\Models\Audit();
                    $audit->auditable_type = 'App\IssueGroupWorkflow';
                    $audit->user_type = 'App\User';
                    $audit->user_id = Auth::id();
                    $audit->event = 'created';
                    $audit->auditable_id = $issueWgroup1->issue_group_workflow_id;
                    $audit->old_values = [];
                    $audit->new_values = json_decode($issueWgroup1);
                    $audit->ip_address = request()->ip();
                    $audit->url = request()->fullUrl();
                    $audit->user_agent = request()->userAgent();
                    $audit->save();
                }

        }
            if(!empty($request->groups)) {
                foreach ($request->groups as $key1 => $group) {
                    $issueWgroup = IssueGroupWorkflow::create([
                        'issue_workflow_id' => $row->issue_workflow_id,
                        'group_info_id' => $group,
                        'touch_checker' => ($request['touch_checker_' . $key1] == 1) ? 1 : 0,
                        'hold_maker' => ($request['hold_maker_' . $key1] == 1) ? 1 : 0,
                        'hold_checker' => ($request['hold_checker_' . $key1] == 1) ? 1 : 0,
                        'sla_maker' => $request['sla_maker_' . $key1],
                        'sla_checker' => $request['sla_checker_' . $key1],
                        'attach_maker' => ($request['attach_maker_' . $key1] == 1) ? 1 : 0,
                        'attach_maker_item' => $request['attach_maker_item_' . $key1],
                        'attach_checker' => ($request['attach_checker_' . $key1] == 1) ? 1 : 0,
                        'attach_checker_item' => $request['attach_checker_item_' . $key1]
                    ]);

                    if(!empty($issueWgroup)){
                        $audit = new \OwenIt\Auditing\Models\Audit();
                        $audit->auditable_type = 'App\IssueGroupWorkflow';
                        $audit->user_type = 'App\User';
                        $audit->user_id = Auth::id();
                        $audit->event = 'created';
                        $audit->auditable_id = $issueWgroup->issue_group_workflow_id;
                        $audit->old_values = [];
                        $audit->new_values = json_decode($issueWgroup);
                        $audit->ip_address = request()->ip();
                        $audit->url = request()->fullUrl();
                        $audit->user_agent = request()->userAgent();
                        $audit->save();
                    }

                }
            }
        });

        Session::forget('group_cart');
		flash('Workflow Successfully Created!!!', 'success');
        return redirect('workflow');
    }

    public function destroy($id)
    {
        $row = IssueWorkflow::find($id, ['issue_id','issue_workflow_id','flow_type']);
        if (empty($row)){
            flash('This workflow dose not exit', 'danger');
            return redirect('/workflow');
        }
        $reference = Reference::where('issue_id',$row->issue_id)->where('form_status','<>',11)->get();
        //prd($reference->toArray());
        if(count($reference)!=0){
            flash('Some issues already open. Please close all issues and try to delete!!!', 'danger');
            return redirect('workflow');
        }
        if (!empty($row)){
            $audit = new \OwenIt\Auditing\Models\Audit();
            $audit->auditable_type = 'App\IssueWorkflow';
            $audit->user_type = 'App\User';
            $audit->user_id = Auth::id();
            $audit->event = 'deleted';
            $audit->auditable_id = $row->issue_workflow_id;
            $audit->old_values = json_decode($row);
            $audit->new_values = [];
            $audit->ip_address = request()->ip();
            $audit->url = request()->fullUrl();
            $audit->user_agent = request()->userAgent();
            $audit->save();
        }
        $row->delete();
        flash('Workflow Successfully Deleted!!!', 'success');
        return redirect('workflow');
    }

    public function show($id)
    {
        $row = IssueWorkflow::find($id);
        if (empty($row)){
            flash('This workflow has been Updated', 'warning');
            return redirect('/workflow');
        }
        $rows = IssueGroupWorkflow::where('issue_workflow_id',$id)->get();
        return view('workflow.view',['row'=>$row,'rows'=>$rows]);
    }

    public function specialPermission()
    {
        $rows = DB::table('issue_subgroup_workflow')
        ->join('issue_workflows','issue_workflows.issue_id','=','issue_subgroup_workflow.issue_id')
        ->join('unit_items','unit_items.id','=','issue_workflows.issue_id')
        ->join('subgroup_info','subgroup_info.id','=','issue_subgroup_workflow.subgroup_info_id')
        ->select('issue_subgroup_workflow.*','issue_workflows.*','unit_items.name as issue_name','subgroup_info.name as name')
            ->get();

        return view('workflow.special-permission.index',compact('rows'));
    }

    public function createSpecialPermission()
    {
        $row = '';
        return view('workflow.special-permission.create',['row'=>$row]);
    }

    public function storeSpecialPermission(Request $request)
    {
        $row = IssueSubgroupWorkflow::create([
            'issue_workflow_id' => $request['issue_workflow_id'],
            'issue_id' => $request['issue_id'],
            'group_info_id' => $request['group_info_id'],
            'subgroup_info_id' => $request['subgroup_info_id'],
            'touch_checker' => $request['touch_checker'],
            'hold_maker' => $request['hold_maker'],
            'hold_checker' => $request['hold_checker'],
            'sla_maker' => $request['sla_maker'],
            'sla_checker' => $request['sla_checker'],
            'attach_maker' => $request['attach_maker'],
            'attach_maker_item' => $request['attach_maker_item'],
            'attach_checker' => $request['attach_checker'],
            'attach_checker_item' => $request['attach_checker_item']
        ]);
        return redirect('workflow/special-permission');
    }

    public function destroySpecialPermission($id)
    {
        $row = IssueSubgroupWorkflow::find($id);
        $row->delete();
        return redirect('workflow/special-permission');
    }

    public function issueAttachment(Request $request)
    {
        $complaint_id = $request->complaint_id;
        $group_id = $group_info_id = SubgroupInfo::where('id',Auth::user()->user_unit->subgroup_info_id)->pluck('group_info_id')->first();
        $issue = UnitItem::where('master_id',$complaint_id)->where('issues_from',$request->type)->first();
        $row  = \App\IssueWorkflow::where('issue_id',$issue->id)->first();
        $workflow = \App\IssueGroupWorkflow::where('issue_workflow_id',$row->issue_workflow_id)->where('group_info_id',$group_id)->first();
        $attachment_item = $workflow->attach_maker_item;
        return view('partials.maker_attachment_item',['attachment_item'=>$attachment_item, 'type'=>$request->type, 'issue' => $issue]);
    }

    public function getTypeWiseIssue($issue_type)
    {
        $items  = DB::select('SELECT * FROM unit_items WHERE unit_items.id NOT IN (SELECT issue_id
                   FROM issue_workflows) AND unit_items.status="1" AND issues_from="'.$issue_type.'" order by unit_items.name asc');
        return $items;
    }

    public function getTypeWiseIssueForSpecial($issue_type)
    {
        $items = DB::select('SELECT * FROM unit_items WHERE unit_items.status="1" AND issues_from="'.$issue_type.'" order by unit_items.name asc');
        return $items;
    }

    public function edit($id)
    {
        $group_carts = Session::get('group_cart');
        $rows = [];
        if ($group_carts){
            foreach ($group_carts as $key => $r) {

                $rows[] = $r;
            }
        }
        $row = IssueWorkflow::find($id);
        if (empty($row)) {
            abort(403,'Un-Authorize Access');
        }

        $touch_groups = DB::table('issue_group_workflows')
                            ->join('group_info','issue_group_workflows.group_info_id','=','group_info.id')
                            ->where('is_touch_point',1)
                            ->where('issue_workflow_id',$id)
                            ->get()
                            ->keyBy('group_info_id');

        $utilServices = new UtilService();
        $allTouchGroup = $utilServices->getAllTouchGroupList();

        // pr($allTouchGroup->toArray());
        // prd($touch_groups->toArray());

        $non_touchGroups = DB::table('issue_group_workflows')
            ->join('group_info','issue_group_workflows.group_info_id','=','group_info.id')
            ->where('is_touch_point',0)
            ->where('issue_workflow_id',$id)
            ->get();
        return view('workflow.edit',['row'=>$row,'rows'=>$rows,'touch_groups'=>$touch_groups,'non_touchGroups'=>$non_touchGroups,'all_touch_group'=>$allTouchGroup]);
    }

    public function update(Request $request, $id)
    {

        // return $request->all();
        $newTouchGroups = $request->touch_group;
        $notExistsIssueWFlowGroup = DB::table('issue_group_workflows')
                                        ->select('group_info_id')
                                        ->where('issue_workflow_id',$id)
                                        ->where('is_touch_point',1)
                                        ->whereNotIn('group_info_id',$newTouchGroups)
                                        ->pluck('group_info_id','group_info_id')
                                        ->toArray();
        $existsGroupInfo = DB::table('reference')
                                ->select(
                                     'group_info.name AS group_name'
                                    , DB::raw('COUNT(reference.reference_number) AS assigned_ref')

                                )
                                ->leftJoin('group_info','group_info.id','reference.subgroup_id')
                                ->whereIn('reference.subgroup_id',$notExistsIssueWFlowGroup)
                                ->where('reference.form_status','<>','11')
                                ->where('reference.issue_id','=',$request->issue_id)
                                ->groupBy('reference.subgroup_id')
                                ->get()
                                ->toArray();

        if (!empty($existsGroupInfo)) {
            flash('Touch group remove not possible!!!<br>'.json_encode($existsGroupInfo), 'danger');
            return redirect()->back();
        }

        $this->validate($request,[
            //"priority_touch_checker_0"    => "required",
            'flow_type'=>'required',
            'issue_id'=>'required'

        ]);

        if(empty($request['touch_group'])){
            flash('Please select touch group!!!', 'danger');
            return redirect()->back();
        }

		/*
		if($request->flow_type==FlowEnum::FORWARD){

			if(empty($request->groups)) {
				flash('Please select next level group for Forward Flow!!!', 'danger');
				return redirect()->back();
			}
			if(!empty($request->groups)) {

				if(count($request->groups)>1){
				flash('Please select one next level group for Forward Flow!!!', 'danger');
				return redirect()->back();
				}
			}

		}
		*/
		//dd($request);
		if($request->flow_type==FlowEnum::FORWARD) {
            $total=0;
            $item_attach = false;
			foreach ($request->touch_group as $key=>$priority_group){
                if(!empty($request['priority_touch_checker_' . $priority_group]) && $request['priority_touch_checker_' . $priority_group] != 2){
                    $total =  ++$key;
                } else {
                    flash('Please allow touch group checker (Touch Yes) !!!', 'danger');
                    return redirect()->back();
                }
            }
		}

        if($request->flow_type==FlowEnum::REGULAR){
            $total=0;
            $item_attach = false;
            foreach ($request->touch_group as $key=>$priority_group){
                if((!empty($request['priority_touch_checker_' . $priority_group])) && (!empty($request['priority_hold_maker_' . $priority_group]))&& (!empty($request['priority_sla_maker_' . $priority_group]))&&  (!empty($request['priority_attach_maker_' . $priority_group]))){
                    if(empty($request->groups)) {
                        if($request['priority_touch_checker_' . $priority_group]==2){
                            flash('Please allow touch group checker and enter all information correctly!!!', 'danger');
                            return redirect()->back();
                        } else {
                            if((empty($request['priority_hold_checker_' . $priority_group])) || (empty($request['priority_sla_checker_' . $priority_group])) || (empty($request['priority_attach_checker_' . $priority_group]))){
                                flash('Please select touch group checker information correctly!!!', 'danger');
                                return redirect()->back();
                            }else{
                                if($request['priority_attach_checker_' . $priority_group]==1){
                                    $item_attach = true;
                                    if(empty($request['priority_attach_checker_item_' . $priority_group])){
                                        $item_attach =false;
                                    }
                                }else{
                                    $item_attach =true;
                                }
                                //echo $request['priority_touch_checker_' . $priority_group];die;
                                if($item_attach==false){
                                    flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                                    return redirect()->back();
                                }
                            }
                        }
                    }else{
                        if($request['priority_touch_checker_' . $priority_group]==1){
                            if($request['priority_attach_checker_' . $priority_group]==1){
                                $item_attach = true;
                                if(empty($request['priority_attach_checker_item_' . $priority_group])){
                                    $item_attach =false;
                                }
                            }else{
                                $item_attach =true;
                            }
                            //echo $request['priority_touch_checker_' . $priority_group];die;

                            if($item_attach==false){
                                flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                                return redirect()->back();
                            }
                        }else{
                            $request['priority_touch_checker_' . $priority_group] = 0;
                            $request['priority_hold_checker_' . $priority_group] = 0;
                            $request['priority_sla_checker_' . $priority_group] = 0;
                            $request['priority_attach_checker_' . $priority_group] = 0;
                        }
                    }
                    if($request['priority_attach_maker_' . $priority_group]==1){
                        $item_attach = true;
                        if(empty($request['priority_attach_maker_item_' . $priority_group])){
                            $item_attach =false;
                        }
                    }else{
                        $item_attach =true;
                    }
                    if($item_attach==false){
                        flash('Please enter touch group attachment quantity correctly!!!', 'danger');
                        return redirect()->back();
                    }
                    $total =  ++$key;
                }else{
                    flash('Please allow touch group maker/checker correctly!!!', 'danger');
                    return redirect()->back();
                }
            }

            if($total!=count($request->touch_group)){
				flash('Please select touch group information correctly!!!', 'danger');
                return redirect()->back();
            }
            //prd($request->all());
            if(!empty($request->groups)) {
                $total_ = 0;
                $item_group_attach = false;
                foreach ($request->groups as $key2 => $group) {
					//echo $request['hold_maker_' . $key2].'dddddddddd'.$request['sla_maker_' . $key2].'aaa'.$request['attach_maker_' . $key2];die;
					if(empty($request['hold_maker_' . $key2]) && empty($request['sla_maker_' . $key2]) && empty($request['attach_maker_' . $key2])) {
                        //Do Nothing
                    }elseif ((!empty($request['hold_maker_' . $key2])) && (!empty($request['sla_maker_' . $key2])) && (!empty($request['attach_maker_' . $key2]))) {
                        if($request['touch_checker_' . $key2] == 1){
                            if((empty($request['hold_checker_' . $key2])) || (empty($request['sla_checker_' . $key2])) || (empty($request['attach_checker_' . $key2]))){
                                flash('Please select next level group checker information correctly!!!', 'danger');
                                return redirect()->back();
                            }else{
                                if ($request['attach_checker_' . $key2] == 1) {
                                    $item_group_attach = true;
                                    if (empty($request['attach_checker_item_' . $key2])) {
                                        $item_group_attach = false;
                                    }
                                } else {
                                    $item_group_attach = true;
                                }
                                if ($item_group_attach == false) {
                                    flash('Please enter next level group attachment quantity correctly!!!', 'danger');
                                    return redirect()->back();
                                }
                            }
                        }else{
                            $request['hold_checker_' . $key2] = 0;
                            $request['sla_checker_' . $key2] = 0;
                            $request['attach_checker_' . $key2] = 0;
                            $request['attach_checker_item_' . $key2] = 0;
                        }

                        if ($request['attach_maker_' . $key2] == 1) {
                            $item_group_attach = true;
                            if (empty($request['attach_maker_item_' . $key2])) {
                                $item_group_attach = false;
                            }
                        } else {
                            $item_group_attach = true;
                        }
                        if ($item_group_attach == false) {
                            flash('Please enter next level group attachment quantity correctly!!!', 'danger');
                            return redirect()->back();
                        }
                        $total_ = ++$key2;
                    }else{
						flash('Please select next level group maker correctly!!!', 'danger');
						return redirect()->back();
				    }
                }

                /*if ($total_ != count($request->groups)) {
					flash('Please select next level group correctly!!!', 'danger');
                    return redirect()->back();
                }*/
            }
        }

        $exist = IssueWorkflow::find($id, ['issue_workflow_id','flow_type','issue_id']);

        if (!empty($exist)){
            $exist->touch_group = $request->touch_group_old;
            $exist->none_touch_group = $request->non_touch_group_old;
            Session()->put('old_data', $exist);
        }

        $row= '';
        DB::transaction(function () use ($request, &$row, $id) {
            // Old workflow groups are taken from DB (workflow is the source of truth), not from the submitted form
            $oldWorkflow = IssueWorkflow::where('issue_workflow_id', $id)->lockForUpdate()->first(['issue_workflow_id','issue_id']);
            $oldGroups = [];
            if (!empty($oldWorkflow)) {
                $oldGroups = DB::table('issue_group_workflows')
                    ->where('issue_workflow_id', $oldWorkflow->issue_workflow_id)
                    ->pluck('group_info_id')
                    ->toArray();
                // issue_group_workflows rows are removed by FK cascade
                $oldWorkflow->delete();
            }

            $row = IssueWorkflow::create([
            'flow_type'=>$request->flow_type,
            'issue_id'=>$request->issue_id,
                'log'=>$request->log,
                'execute'=>$request->execute,
                'complain_sla_time' => ($request->flow_type==FlowEnum::FORWARD)? $request->complain_sla_time:0
                //'send_back'=>$request->send_back,
                //'cant_reach_to_customer'=>$request->cant_reach_to_customer
            ]);

            // Touch Group
            foreach ($request->touch_group as $key=>$priority_group){

                    $issueWgroup = IssueGroupWorkflow::create([
                        'issue_workflow_id' => $row->issue_workflow_id,
                        'group_info_id' => $priority_group,
                        'touch_checker' => ($request['priority_touch_checker_' . $priority_group]==1)?1:0,
                        'hold_maker' => ($request['priority_hold_maker_' . $priority_group]==1)?1:0,
                        'hold_checker' => ($request['priority_hold_checker_' . $priority_group]==1)?1:0,
                        'sla_maker' => $request['priority_sla_maker_' . $priority_group],
                        'sla_checker' => $request['priority_sla_checker_' . $priority_group],
                        'attach_maker' => ($request['priority_attach_maker_' . $priority_group]==1)?1:0,
                        'attach_maker_item' => $request['priority_attach_maker_item_' . $priority_group],
                        'attach_checker' => ($request['priority_attach_checker_' . $priority_group]==1)?1:0,
                        'attach_checker_item' => $request['priority_attach_checker_item_' . $priority_group],
                        'is_touch_point' => 1
                    ]);

            }

            // Next Level group
            if(!empty($request->groups)) {
                foreach ($request->groups as $key1 => $group) {
                    $issueWgroup2 = IssueGroupWorkflow::create([
                        'issue_workflow_id' => $row->issue_workflow_id,
                        'group_info_id' => $group,
                        'touch_checker' => ($request['touch_checker_' . $group] == 1) ? 1 : 0,
                        'hold_maker' => ($request['hold_maker_' . $group] == 1) ? 1 : 0,
                        'hold_checker' => ($request['hold_checker_' . $group] == 1) ? 1 : 0,
                        'sla_maker' => $request['sla_maker_' . $group],
                        'sla_checker' => $request['sla_checker_' . $group],
                        'attach_maker' => ($request['attach_maker_' . $group] == 1) ? 1 : 0,
                        'attach_maker_item' => $request['attach_maker_item_' . $group],
                        'attach_checker' => ($request['attach_checker_' . $group] == 1) ? 1 : 0,
                        'attach_checker_item' => $request['attach_checker_item_' . $group]
                    ]);

                }
            }

            // Removed group -> its subgroup/user records in issue_group_members of this issue are removed
            // ============= issue group =============
            if (!empty($oldWorkflow)) {
                // New Touch and next level group id (if the issue itself changed, every old group is removed from the old issue)
                $newGroups = ($oldWorkflow->issue_id == $request->issue_id)
                    ? array_merge($request->touch_group ?? [], $request->groups ?? [])
                    : [];
                $removedGroups = array_values(array_diff($oldGroups, $newGroups));

                if (!empty($removedGroups)) {
                    $issueGroupIds = DB::table('issue_groups')->where('unit_item_id', $oldWorkflow->issue_id)->pluck('id');
                    $subgroups = DB::table('subgroup_info')->whereIn('group_info_id', $removedGroups)->pluck('id');

                    if ($issueGroupIds->isNotEmpty() && $subgroups->isNotEmpty()) {
                        DB::table('issue_group_members')
                            ->whereIn('issue_group_id', $issueGroupIds)
                            ->whereIn('subgroup_info_id', $subgroups)
                            ->delete();
                    }
                }
            }
        });

            /* Audit Logs Insert [Zihad]*/
            $old_data = Session()->get('old_data');
            $new_data = IssueWorkflow::find($row->issue_workflow_id, ['issue_workflow_id','flow_type','issue_id']);
            $new_data->touch_group = !empty($request->touch_group) ? json_encode($request->touch_group) : "";
            $new_data->none_touch_group = !empty($request->groups) ? json_encode($request->groups) : "";

            if (!empty($old_data)){
                $audit = new \OwenIt\Auditing\Models\Audit();
                $audit->auditable_type = 'App\IssueWorkflow';
                $audit->user_type = 'App\User';
                $audit->user_id = Auth::id();
                $audit->event = 'updated';
                $audit->auditable_id = $old_data['issue_workflow_id'];
                $audit->old_values = json_decode($old_data);
                $audit->new_values = json_decode($new_data);
                $audit->ip_address = request()->ip();
                $audit->url = request()->fullUrl();
                $audit->user_agent = request()->userAgent();
                $audit->save();
            }

        Session()->forget('old_data');
        Session::forget('group_cart');
		flash('Workflow Successfully Updated!!!', 'success');
        return redirect('workflow');
    }

    public function getGroupwiseWorkflow($id,$issue)
    {
        $row = IssueWorkflow::where('issue_id',$issue)->first();
        $issue_workflow = IssueGroupWorkflow::where('issue_workflow_id',$row->issue_workflow_id)->where('group_info_id',$id)->first();
        return view('partials.special_workflow',['row'=>$issue_workflow]);
    }

    public function subflowIndex()
    {
        $issueItems = DB::select(
                        "SELECT
                            unit_items.id,
                            unit_items.master_id,
                            unit_items.name,
                            issue_categories.name AS issue_cat_name
                        FROM issue_workflows
                        LEFT JOIN unit_items ON (unit_items.id = issue_workflows.issue_id)
                        LEFT JOIN issue_categories ON (issue_categories.id = unit_items.issue_categories_id)
                        LEFT JOIN issue_group_workflows ON (issue_group_workflows.issue_workflow_id = issue_workflows.issue_workflow_id)
                        WHERE
                            issue_group_workflows.group_info_id IN (SELECT group_info.id FROM group_info WHERE group_info.group_level_id<>1)AND
                            unit_items.status=1
                        GROUP BY
                            issue_workflows.issue_workflow_id
                        HAVING COUNT(issue_workflows.issue_workflow_id) > 1

                        ORDER BY
                            issue_categories.name DESC,
                            unit_items.name ASC
                        "
                   );
        // prd($issueItems);
        return view('workflow.subflow_index',compact('issueItems'));
    }

    public function subflowEdit($id=NULL)
    {
        $subWorkFlowModel = new SubWorkflow();
        $existsSubWorkFlow = $subWorkFlowModel->where('issue_id',$id)->get();
        $tmpIssueItems = DB::select(
                        "SELECT
                            issue_workflows.issue_workflow_id,
                            unit_items.id,
                            unit_items.master_id,
                            unit_items.name,
                            issue_categories.name AS issue_cat_name
                        FROM issue_workflows

                        LEFT JOIN unit_items ON (unit_items.id = issue_workflows.issue_id)
                        LEFT JOIN issue_categories ON (issue_categories.id = unit_items.issue_categories_id)
                        WHERE
                            unit_items.id = $id
                        ORDER BY
                            issue_categories.name DESC,
                            unit_items.name ASC
                        "
                   );

        if (!empty($tmpIssueItems[0])) {
            $issueItems = $tmpIssueItems[0];
        } else {
            abort(403,'Un-Authorize Access');
        }

        /*$issueWItems = DB::select("SELECT issue_workflows.issue_workflow_id, issue_workflows.issue_id, unit_items.name AS issue_name, issue_categories.name AS issue_cat_name, issue_group_workflows.group_info_id, group_info.name AS group_name FROM issue_workflows LEFT JOIN issue_group_workflows ON (issue_group_workflows.issue_workflow_id = issue_workflows.issue_workflow_id) LEFT JOIN unit_items ON (unit_items.id = issue_workflows.issue_id) LEFT JOIN issue_categories ON (issue_categories.id = unit_items.issue_categories_id) LEFT JOIN group_info ON (group_info.id = issue_group_workflows.group_info_id) WHERE unit_items.id = $id AND unit_items.status=1 AND group_info.group_level_id<>1 AND unit_items.issues_from='complaint'ORDER BY issue_categories.name DESC, unit_items.name ASC ");*/

        $wFlowNextLevelGroup = DB::select(
                        "SELECT
                            issue_group_workflows.group_info_id,
                            group_info.name AS group_name
                        FROM issue_workflows
                        LEFT JOIN issue_group_workflows ON (issue_group_workflows.issue_workflow_id = issue_workflows.issue_workflow_id)
                        LEFT JOIN group_info ON (group_info.id = issue_group_workflows.group_info_id)
                        WHERE
                            issue_workflows.issue_id = $id AND
                            group_info.group_level_id<>1
                        ORDER BY
                            issue_group_workflows.issue_group_workflow_id ASC
                        "
                   );
        return view('workflow.subflow_set',compact('id','existsSubWorkFlow','issueItems','wFlowNextLevelGroup'));
    }

    public function subflowUpdate($id=NULL,SubFlowRequest $request)
    {
        $issue_workflow_id = $request->issue_workflow_id;
        $issue_id = $request->issue_id;

        $delSubWorkFlowModel = new SubWorkflow();
        $delSubWorkFlowModel->where('issue_id',$issue_id)->delete();

        $newsubgroup = $request->newsubgroup;
        if (!empty($newsubgroup)) {
            foreach($newsubgroup AS $newsgroup) {
                $subWorkFlowModel = new SubWorkflow();
                $subWorkFlowModel->issue_workflow_id = $issue_workflow_id;
                $subWorkFlowModel->issue_id = $issue_id;
                $subWorkFlowModel->group_info_id = $newsgroup['group_info_id'];
                $subWorkFlowModel->options = $newsgroup['options'];
                $subWorkFlowModel->status = 1;
                $subWorkFlowModel->created_by = Auth::user()->id;
                $subWorkFlowModel->ip = $request->ip();
                $subWorkFlowModel->save();
            }
        }
        flash('Subflow have been updated!!!', 'success');
        return redirect('/workflow/subflow');
    }
}
