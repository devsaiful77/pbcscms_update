<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Imports\HolidaysImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Requests\HolidayRequest;
use App\Holiday;
use App\User;
use App\WorkingDay;
use Excel;

class FeedbackController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role_or_permission:superadmin|admin|CEX_ADMIN|adminActivity|true|feedback']);
        parent::__construct();
    }

    public function index()
    {
        $feedbacks = Feedback::select('id', 'cif_number', 'ticket_number', 'mobile_no', 'email', 'comments', 'log_date', 'status', 'loger', 'created_at')
            ->where('status', 0)
            ->orderBy('created_at','desc')
            ->paginate(10);
        return view('feedback.index', compact('feedbacks'));
    }
    public function bulkStore(Request $request)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $userId = $user->user_id;
        if(!empty($request->ids)){
            foreach ($request->ids as $id){
                $feedback = Feedback::find($id);
                $feedback->status = 1;
                $feedback->seen_by = $userId ?? '';
                $feedback->save();
            }
        }else{
            return redirect()->back()->with('warning', 'No Items selected!');
        }

        return redirect()->route('feedback.index')->with('success', 'Feedback Status Change By Read!');
    }
    public function singleStatus($id)
    {
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $userId = $user->user_id;

        $feedback = Feedback::find($id);
        $feedback->status = 1;
        $feedback->seen_by = $userId ?? '';
        $feedback->save();
        return redirect()->back()->with('success', 'Feedback Status Change By Read!');
    }
}
