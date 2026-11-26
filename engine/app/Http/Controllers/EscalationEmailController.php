<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

use App\Http\Requests;
use App\Http\Requests\UnitRequest;
use App\Division;

use Carbon\Carbon;
use DateTime;

class EscalationEmailController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('ability:superadmin|admin,adminActivity|true');
        //parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
public function index(Request $request){
}

public function sendEscalationEmail(Request $request)
{

//prd($request->ip());

if($request->user == "escmailusr" && $request->passwd == "Pwd@!123" && !empty($request->ref_ref)){
        
        $refStatus = DB::select("SELECT ref.date AS ref_date, cmnt.time AS cmnt_time FROM `reference` ref LEFT JOIN comments cmnt ON cmnt.id = ( SELECT MAX(id) AS max_id FROM comments cmnt2 WHERE cmnt2.reference_number = ref.reference_number and isapproved = 1 LIMIT 1 ) where ref.reference_number = ? and ref.form_status <> 11", [$request->ref_ref]);
        //print_r($refStatus[0]);die;
        if(!empty($refStatus)){
        $refTime = (!empty($refStatus[0]->cmnt_time))? $refStatus[0]->cmnt_time : $refStatus[0]->ref_date;
        // print_r($refTime);die;
        $start_date = date('Y-m-d H:i:s', (int)$refTime);
        $end_date = date('Y-m-d H:i:s');
                  
        $workingDays = array();
        $dataForView = array();
        //pr($start_date);
        if (!empty($end_date)) {
            $workingDayDataObj = DB::table('working_days')->where([
                ['dates','>=',date('Y-m-d',strtotime($start_date))],
                ['dates','<=',date('Y-m-d',strtotime($end_date))],
                ['status',1]])->pluck('dates','dates');

            // $workingDayDataObj = DB::table('working_days')->whereBetween('dates',array($start_date,$end_date))->where([['status',1]])->pluck('dates','dates');
            if (!empty($workingDayDataObj)) {
                $workingDays = $workingDayDataObj->toArray();
            }
        }
        
        //pr($workingDays);

        $workingHours = DB::table('working_hours')->first();
        $workingHours = json_decode(json_encode($workingHours),true);

        if (!empty($workingHours['office_from'])) {
            $dataForView['office_from'] = $workingHours['office_from'];
            $dataForView['office_from_str'] = substr($dataForView['office_from'], 0,2).':'.substr($dataForView['office_from'], 2,2).':'.substr($dataForView['office_from'], 4,2);
        } else {
            $dataForView['office_from'] = '100000';
            $dataForView['office_from_str'] = '10:00:00';
        }
        if (!empty($workingHours['office_to'])) {
            $dataForView['office_to'] = $workingHours['office_to'];
            $dataForView['office_to_str'] = substr($dataForView['office_to'], 0,2).':'.substr($dataForView['office_to'], 2,2).':'.substr($dataForView['office_to'], 4,2);
        } else {
            $dataForView['office_to'] = '180000';
            $dataForView['office_to_str'] = '18:00:00';
        }

        $startDayHour = 0;
        $startDayMinutes = 0;
        $startDaySeconds = 0;

        $endDayHour = 0;
        $endDayMinutes = 0;
        $endDaySeconds = 0;


        $startDate = Carbon::parse($start_date)->format('Y-m-d');
        $startTime = Carbon::parse($start_date)->format('H:i:s');
        $end_date1 = Carbon::parse($end_date)->format('Y-m-d');
        $startDateTime = Carbon::parse($start_date)->format('Y-m-d H:i:s');
        //echo "SELECT count(dates) AS total_working_days FROM working_days where dates > '{$start_date}' and dates < '{$end_date1}'";
        $totalWorkingDaysQueue = array();
        $totalWorkingDaysQueueObj = DB::select("SELECT count(dates) AS total_working_days FROM working_days where dates > '{$start_date}' and dates < '{$end_date1}'");
        //pr($totalWorkingDaysQueueObj);
        $totalWorkingDaysOnThisReq = (!empty($totalWorkingDaysQueueObj[0])) ? $totalWorkingDaysQueueObj[0]->total_working_days  : 0 ;
        $totalWorkingHoursOnThisReq = (!empty($totalWorkingDaysOnThisReq)) ? $totalWorkingDaysOnThisReq * 8 : 0;
        //echo $totalWorkingDaysOnThisReq;
        $startDateForCalc = str_replace('-', '', $startDate);
        $startDateTimeNumb = str_replace("-", "", str_replace(" ", "", str_replace(":", "", $startDateTime)));


        $startDateForCalcMin = $startDateForCalc.$dataForView['office_from'];
        $startDateForCalcMax = $startDateForCalc.$dataForView['office_to'];


        date_default_timezone_set('Asia/Dhaka');
        $endDateCalc = date("Ymd",strtotime($end_date));
        $endDateTimeCalc = date("YmdHis",strtotime($end_date));
        $endDateCalcMin = $endDateCalc.$dataForView['office_from'];
        $endDateCalcMax = $endDateCalc.$dataForView['office_to'];

        $endDateTime = date('Y-m-d H:i:s',strtotime($end_date));
        $endDate = date('Y-m-d',strtotime($end_date));

        if (!empty($workingDays[$startDate])) {
            if (($startDateTimeNumb >= $startDateForCalcMin && $startDateTimeNumb <= $startDateForCalcMax)) {
                $startDateTimeObj = new DateTime($startDateTime);

                if ($startDate != $endDate) {
                    $startDateLastObj = new DateTime($startDate.' '.$dataForView['office_to_str']);
                } else {
                    if ($endDateTimeCalc >= $endDateCalcMin && $endDateTimeCalc <= $endDateCalcMax) {
                        $startDateLastObj = new DateTime($endDateTime);
                    } else {
                        $startDateLastObj = new DateTime($endDate.' '.$dataForView['office_to_str']);
                    }
                }

                $interval = $startDateTimeObj->diff($startDateLastObj);

                $startDayHour = $interval->format('%h');
                $startDayMinutes = $interval->format('%i');
                $startDaySeconds = $interval->format('%s');
            }
        }


        if(!empty($workingDays[$endDate])) {

            if (($endDateTimeCalc >= $endDateCalcMin && $endDateTimeCalc <= $endDateCalcMax) && ($startDate != $endDate)) {
                //echo 'fffff';
                $endDateTimeObj = new DateTime($endDateTime);
                $endDateLastObj = new DateTime($endDate.' '.$dataForView['office_from_str']);

                $interval = $endDateTimeObj->diff($endDateLastObj);

                $endDayHour = $interval->format('%h');
                $endDayMinutes = $interval->format('%i');
                $endDaySeconds = $interval->format('%s');
            } elseif (($endDateTimeCalc > $endDateCalcMax) && ($startDate != $endDate)) {
                //echo 'ddddd';
                $endDateTimeObj = new DateTime($endDate.' '.$dataForView['office_to_str']);
                $endDateLastObj = new DateTime($endDate.' '.$dataForView['office_from_str']);

                $interval = $endDateTimeObj->diff($endDateLastObj);

                $endDayHour = $interval->format('%h');
                $endDayMinutes = $interval->format('%i');
                $endDaySeconds = $interval->format('%s');
            }
        }
        
        $totalHoursOnThisQueue = $startDayHour + $totalWorkingHoursOnThisReq + $endDayHour;
        $totalMinutesOnThisQueue = $startDayMinutes + $endDayMinutes;
        $totalSecondsOnThisQueue = $startDaySeconds + $endDaySeconds;

        $queueDurationInMinutes = ($totalHoursOnThisQueue * 60) + $totalMinutesOnThisQueue;

       // echo '<br/>'.$request->ref_ref.'---'.$queueDurationInMinutes; die;
        if(issue_breach($request->ref_ref, $queueDurationInMinutes)){
            return "Success";
        }else{
            return "Failed";
        }

    }
        return "Reference Not Found";
    }

    return "Unauthorized";
}

}
