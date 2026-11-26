<?php

namespace Modules\Requisition\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Requisition\App\Models\RequisitionSMSEmail;

class RequisitionSMSEmailsController extends Controller
{
    // function __construct()
    // {
    //     $this->middleware('role:superadmin');
    // }

    public function index(Request $request)
    {
        $sMSEmailModelName = new RequisitionSMSEmail;
        $dataForView = $sMSEmailModelName->first();

        $id = (!empty($dataForView['id'])) ? $dataForView['id'] : 0;

        $title = "SMS and Email Template";
        $title_for_layout = "SMS and Email Template";
        return view('requisition::SMSEmails.index', compact('title', 'title_for_layout', 'dataForView', 'id'));
    }


    public function store($id = null, Request $request)
    {
        try {
            $id = decrypt($id);
        } catch (DecryptException $e) {
            abort(403, 'Something went wrong. Please contact with technical support');
        }

        if ($id == 0) {
            /* Save for the first time */
            $sMSEmail = new RequisitionSMSEmail;
            $sMSEmail->created_by = Auth::user()->id;
            $sMSEmail->updated_by = Auth::user()->id;
        } else {
            /* Update for next time */
            $sMSEmailModel = new RequisitionSMSEmail;
            $sMSEmail = $sMSEmailModel->where('id', $id)->first();
            if (empty($sMSEmail)) {
                $sMSEmail = new RequisitionSMSEmail;
                $sMSEmail->created_by = Auth::user()->id;
            }
            $sMSEmail->updated_by = Auth::user()->id;
        }

        $sMSEmail->issue_opening_sms = trim($request->issue_opening_sms);
        $sMSEmail->issue_opening_email = trim($request->issue_opening_email);
        $sMSEmail->issue_closing_sms = trim($request->issue_closing_sms);
        $sMSEmail->issue_closing_email = trim($request->issue_closing_email);
        $sMSEmail->requisition_approve_sms = trim($request->requisition_approve_sms);
        $sMSEmail->requisition_approve_email = trim($request->requisition_approve_email);
        $sMSEmail->requisition_cancel_sms = trim($request->requisition_cancel_sms);
        $sMSEmail->requisition_cancel_email = trim($request->requisition_cancel_email);
        $sMSEmail->requisition_sendback_sms = trim($request->requisition_sendback_sms);
        $sMSEmail->requisition_sendback_email = trim($request->requisition_sendback_email);

        if ($sMSEmail->save()) {
            flash('SMS and Emails has been saved successfully', 'success');
            return redirect()->route('requisition.sms-email.index');
        } else {
            flash('Failed to save SMS and Emails, Please try again', 'danger');
        }
    }
}
