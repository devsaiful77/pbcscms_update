<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Http\Requests;
use App\Http\Requests\UnitRequest;
use App\SMSEmail;
use App\OutgoingEMAIL;

use Mail;

class NotificationsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        parent::__construct();
        //$this->middleware('role:superadmin|admin');
        $this->middleware(['role:superadmin|admin']);
    }

    public function sendMail()
    {
        date_default_timezone_set('Asia/Dhaka');
        $dataArr = array();
        $outGoingEmailModel = new OutgoingEMAIL();
        $dataObj = $outGoingEmailModel->where('status',3)->get();

        if (!empty($dataObj)) {
            $dataArr = $dataObj->toArray();
            foreach ($dataObj as $obj) {
                $data = $obj->toArray();
                Mail::send('SMSEmails.mail', $data, function($message) use ($data) {
                 $message
                    ->to($data['email_address'], $data['email_address'])
                    ->subject($data['subject']);
                });

                $obj->status = 1;
                $obj->senttime = date('Y-m-d H:i:s');
                $obj->save();
            }
        }
        // return $dataArr;
    }
}
