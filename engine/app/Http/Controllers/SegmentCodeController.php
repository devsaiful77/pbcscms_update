<?php

namespace App\Http\Controllers;

use Excel;
use App\SegmentCode;
use Illuminate\Http\Request;
use App\Imports\SegmentImport;
use App\Http\Requests\SegmentRequest;
use Illuminate\Support\Facades\Redirect;

class SegmentCodeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|admin|adminActivity|true|segment']);
        parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $dataForView = array();
        $searchDataForView = $request->all();
        $tblData = array();
        $segmetnModelName = SegmentCode::select('id', 'name', 'code', 'status', 'created_at')->orderBy("created_at","asc")->get();
        if (!empty($segmetnModelName)) {
            $tblData = $segmetnModelName->toArray();
        }
        $title = "Segment List";
        $title_for_layout = "Segment List";
        return view('Segment.index',compact('title','title_for_layout','tblData','searchDataForView'));
    }
    public function store(SegmentRequest $request)
    {
        $segment = new SegmentCode;
        $segment->code = $request->code;
        $segment->name =  $request->name ?? '';
        $segment->save();
        flash('Segment code created successfully', 'success');
        return redirect('segment/index');
    }

    public function edit($id)
    {
        $segmentModelName = new SegmentCode;
        $title = "Edit Segment";
        $title_for_layout = 'Edit Segment';
        $data = $segmentModelName->where([['id', decrypt($id)]])->first();
        return view('Segment.edit', compact('title', 'title_for_layout', 'data'));
    }

    public function delete($id = null)
    {
        $segmetnModelName = SegmentCode::find(decrypt($id));
        $segmetnModelName->delete();
        flash('Segment code has been deleted', 'danger');
        return Redirect::back();
    }

    public function update(SegmentRequest $request, $id = null)
    {
        $cifModelName = SegmentCode::find($id);
        if ($request->isMethod('post')) {
            $cifModelName->name = $request->get('name');
            $cifModelName->code = $request->get('code');
            if ($cifModelName->save()) {
                flash('Segment code has been updated successfully', 'success');
                return redirect('/segment/index');
            } else {
                flash('Failed to update data', 'danger');
                return redirect()->back();
            }
        }
    }

    public function segmentstatus(Request $request, $id = null)
    {
        $data = SegmentCode::find(decrypt($id));
        if (!empty($data)) {
            $data->update(['status' => $request->status]);
            if ($request->status == 1) {
                flash('Segment code has been activated successfully', 'success');
            } else {
                flash('Segment code has been inactivated !!', 'warning');
            }
        } else {
            flash('Not Found', 'danger');
        }
        return redirect()->back();
    }

    public function segmentExcelUpload(SegmentRequest $request)
    {
        if(!empty($request->file('file'))) {
            $file = $request->file('file');
            try {
                Excel::import(new SegmentImport(), $file);

            } catch (Exception $e) {
               flash('Failed to Insert Data', 'error');
            }
        } else {
            flash('Please upload file to add segment.', 'error');
        }
        return redirect('segment/index');
    }
}
