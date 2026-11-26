<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;
use Carbon\Carbon;
use App\Reference;
use App\OutgoingEMAIL;
use App\OutgoingSMS;
// use App\Http\Controllers\CI\CustomerInterfaceController;
use App\Services\CI\SendBackNotificationService;

class SendBackNotificationController extends Controller
{
    public $CIConObj = '';
    public function __construct()
    {
        parent::__construct();
        $this->CIConObj = new SendBackNotificationService();

    }



    public function sendBackNotification(Request $request){
        // $issueId = 114;
        // $ref = 'S230823AC000005';
        // $emailAddress = 'muajjam.imu@gmail.com';
        // $mobileNo = '01911194724';

        if($request->user == "escmailusr" && $request->passwd == "Pwd@!123" && !empty($request->ref_ref)){

                $OutgoingEMAILModel = OutgoingEMAIL::where('reference_number',$request->ref_ref)->orderBy('id', 'desc')->get();
                $OutgoingSMSModel = OutgoingSMS::where('reference_number', $request->ref_ref)->orderBy('id', 'desc')->get();
    
                if(count($OutgoingEMAILModel)> 0 && $OutgoingEMAILModel[0]->subject == "Send Back Alert"){
                    $issueId = Reference::select('issue_id')-where('reference_number', $request->ref_ref)->first();

                    if(count($OutgoingEMAILModel) == 1 && Carbon::parse($OutgoingEMAILModel[0]->savetime)->addMinutes(1) <= date('Y-m-d H:i:s', strtotime(Carbon::now()))){
                        // addHour
                        $this->CIConObj->sendBackNotifyWithOutReason($request->ref_ref, $issueId, $emailAddress, $mobileNo);
                    }elseif(count($OutgoingEMAILModel) == 2 && Carbon::parse($OutgoingEMAILModel[0]->savetime)->addMinutes(1) <= date('Y-m-d H:i:s', strtotime(Carbon::now()))){
                        // addHour
                        $this->CIConObj->sendBackTicketClosedAfter24hour($request->ref_ref, $issueId, $emailAddress, $mobileNo);
                    }elseif(count($OutgoingEMAILModel) == 3 && Carbon::parse($OutgoingEMAILModel[0]->savetime)->addMinutes(1) <= date('Y-m-d H:i:s', strtotime(Carbon::now()))){
                        // addHour
                        $ref = Reference::where('reference_number', $request->ref_ref)->update([
                            'form_status' => 11,
                        ]);
                    }
                }
    
                    // if(count($OutgoingEMAILModel) > 0){
                    //     \Log::info("$request->ref_ref");
                    // }else{
                    //     \Log::info("data nai");
                    // }

        }
    }
}
