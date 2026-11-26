<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Contracts\Encryption\DecryptException;

use App\Http\Requests;
use App\Http\Requests\WFormRequest;
use App\Http\Requests\ComplaintRequest;
use App\Http\Requests\AttachmentUploadRequest;
use App\Http\Requests\WorkingOnHanderRequest;

use App\ProductType;
use App\Department;
use App\User;
use App\UserUnit;
use App\Unit;
use App\UnitChild;
use App\UnitItem;
use App\Source;
use App\WformCategory;
use App\WformMaster;
use App\Reference;
use App\WForm;
use App\WFormType;
use App\Complaint;
use App\ComplaintType;
use App\OutgoingSMS;
use App\OutgoingEMAIL;
use App\Attachment;
use App\Comment;
use App\WorkingDay;
use App\SMSEmail;
use App\TATBreach;

use Carbon;
use DateTime;

class TATBreachesController extends Controller
{
    //
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('ability:superadmin|admin,wformComplain')->only('index','newWForm','submitWform','newComplaint','submitComplaint');
        // $this->middleware('ability:superadmin|admin,supportExecutive')->only('handler');
        // $this->middleware('ability:superadmin|admin,wformComplain|supportExecutive')->only('wFormDetails','complaintDetails');
        // $this->middleware('ability:superadmin|admin,revokeAssignedRequest')->only('unassign');
        // $this->middleware('ability:superadmin|admin|logger,supportExecutive')->only('uploadNewAttachment');
        // $this->middleware('ability:superadmin|admin,supportExecutive')->only('workingOnHandler');
    }

    public function handler()
    {
        date_default_timezone_set('Asia/Dhaka');
        $tatBreachModel = new TATBreach;
        $tatBreachData = $tatBreachModel->select('reference_no')->where('created_at','>=',date('Y-m-d'))->pluck('reference_no')->toArray();

        $dataForView = array();

        $tblData = array(); 

        $wFormData = array();
        $wFormDataObj = array();
        $complaintData = array();
        $complaintDataObj = array();

        $unitDeptCondition = array();

        $isAdmin = false;
        $getUnitId = "";
        $getUnitIdArr = array();
        $getDepartmentId = "";
        $getDivisionId = "";

        $mostOldDateWform = "";
        $mostOldDateComplaint = "";

        $todayDate = date('Y-m-d');

        $wformModelName = new WForm;
        $wFormDataObj = $wformModelName
                            ->select(
                                "w_form.reference_number"
                                ,"w_form.account_number"
                                ,"w_form.customer_name"
                                ,"w_form.mobile_number"
                                ,"w_form.time_and_ext"
                                ,"w_form.w_form_type"
                                ,"w_form.product_type as product_type_ext"
                                ,"departments.name as product_type"
                                ,"unit_items.name as category_name"
                                ,"reference.unit_id"
                                ,"reference.created_by"
                                ,"reference.date"
                                ,"reference.status"
                                ,"reference.form_status"
                                ,"reference.access_by"
                                ,"reference.access_date"
                                ,DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")
                                ,DB::raw("(SELECT count(dates) dates FROM working_days where dates >FROM_UNIXTIME(reference.date,'%Y-%m-%d') and dates < '{$todayDate}') AS total_working_days")
                            )
                            ->with([
                                'last_comment'=>function($e) use ($todayDate){
                                    $e->select(
                                        'id','reference_number','user_id','time','created_at',
                                        DB::raw("(SELECT count(dates) dates FROM working_days where dates > FROM_UNIXTIME(comments.time,'%Y-%m-%d') and dates < '{$todayDate}') AS total_working_days2")
                                    );
                                }
                            ])
                            ->leftJoin('reference', 'reference.reference_number', '=', 'w_form.reference_number')
                            ->leftJoin('departments', 'departments.id', '=', 'w_form.product_type')
                            ->leftJoin('unit_items', function($join) {
                                $join->on('unit_items.master_id', '=', 'w_form.w_form_type');
                                $join->on('unit_items.issues_from', '=', DB::raw("'wform'"));
                            });
        $wFormDataObj = $wFormDataObj->where("reference.status","<>",11);
        $wFormDataObj = $wFormDataObj->where("reference.form_status","<>",11);

        if (!empty($tatBreachData)) {
            $wFormDataObj = $wFormDataObj->whereNotIn('reference.reference_number', $tatBreachData);
        }

        $wFormDataObj = $wFormDataObj
                            ->orderBy("w_form.reference_number","DESC")
                            ->get();
        if (!empty($wFormDataObj)) {
            $wFormData = $wFormDataObj->toArray();
            $lastRecord = end($wFormData);
            if (!empty($lastRecord)) {
                $mostOldDateWform = $lastRecord['UNXTIME'];
            }
        }

        $complaintModelName = new Complaint;
        $complaintDataObj = $complaintModelName
                            ->select(
                                 "complaint.reference_number"
                                ,"complaint.account_number"
                                ,"complaint.customer_name"
                                ,"complaint.mobile_number"
                                ,"complaint.time_and_ext"
                                ,"complaint.complaint_type"
                                ,"complaint.product_type as product_type_ext"
                                ,"departments.name as product_type"
                                ,"unit_items.name as issue_name"
                                ,"reference.unit_id"
                                ,"reference.created_by"
                                ,"reference.date"
                                ,"reference.status"
                                ,"reference.form_status"
                                ,"reference.access_by"
                                ,"reference.access_date"
                                ,DB::raw("FROM_UNIXTIME(reference.date,'%Y-%m-%d') AS UNXTIME")
                                ,DB::raw("(SELECT count(dates) dates FROM working_days where dates >FROM_UNIXTIME(reference.date,'%Y-%m-%d') and dates < '{$todayDate}') AS total_working_days")
                            )
                            ->with([
                                'last_comment'=>function($e) use ($todayDate){
                                    $e->select(
                                        'id','reference_number','user_id','time','created_at', 
                                        DB::raw("(SELECT count(dates) dates FROM working_days where dates > FROM_UNIXTIME(comments.time,'%Y-%m-%d') and dates < '{$todayDate}') AS total_working_days2")
                                    );
                                }
                            ])
                            ->leftJoin('reference', 'reference.reference_number', '=', 'complaint.reference_number')
                            ->leftJoin('departments', 'departments.id', '=', 'complaint.product_type')
                            ->leftJoin('unit_items', function($join) {
                                $join->on('unit_items.master_id', '=', 'complaint.complaint_type');
                                $join->on('unit_items.issues_from', '=', DB::raw("'complaint'"));
                            });

        $complaintDataObj = $complaintDataObj->where("reference.status","<>",11);
        $complaintDataObj = $complaintDataObj->where("reference.form_status","<>",11);
        
        if (!empty($tatBreachData)) {
            $complaintDataObj = $complaintDataObj->whereNotIn('reference.reference_number', $tatBreachData);
        }

        $complaintDataObj = $complaintDataObj
                            ->orderBy("complaint.reference_number","DESC")
                            ->get();

        if (!empty($complaintDataObj)) {
            $complaintData = $complaintDataObj->toArray();
            $lastRecord = end($complaintData);
            if (!empty($lastRecord)) {
                $mostOldDateComplaint = $lastRecord['UNXTIME'];
            }
        }
        

        $workingDaysWform = array();
        if (!empty($mostOldDateWform)) {
            $workingDayModel = new WorkingDay;
            $workingDayDataObj = $workingDayModel->where([['dates','>=',$mostOldDateWform],['status',1]])->pluck('dates','dates');
            if (!empty($workingDayDataObj)) {
                $workingDaysWform = $workingDayDataObj->toArray();
            }
        }
        $workingDaysCompaint = array();
        if (!empty($mostOldDateComplaint)) {
            $workingDayModel = new WorkingDay;
            $workingDayDataObj = $workingDayModel->where([['dates','>=',$mostOldDateComplaint],['status',1]])->pluck('dates','dates');
            if (!empty($workingDayDataObj)) {
                $workingDaysCompaint = $workingDayDataObj->toArray();
            }
        }

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

        // prd($wFormData);
        // prd($complaintData);
        if (!empty($wFormData)) {
            foreach ($wFormData as $data) {
                $lastAccessDayHour = 0;
                $lastAccessDayMinutes = 0;
                $lastAccessDaySeconds = 0;

                $todaysDayHour = 0;
                $todaysDayMinutes = 0;
                $todaysDaySeconds = 0;

                $lastAccessDate = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('Y-m-d')  : Carbon::createFromTimestamp($data['date'])->format('Y-m-d');
                $lastAccessTime = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('H:i:s')  : Carbon::createFromTimestamp($data['date'])->format('H:i:s');
                $lastAccessDateTime = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('Y-m-d H:i:s')  : Carbon::createFromTimestamp($data['date'])->format('Y-m-d H:i:s');

                $totalWorkingDaysOnThisReq = (!empty($data['last_comment'])) ? $data['last_comment']['total_working_days2']  : $data['total_working_days'] ;

                $totalWorkingHoursOnThisReq = (!empty($totalWorkingDaysOnThisReq)) ? $totalWorkingDaysOnThisReq * 8 : 0;


                $lastAccessDateForCalc = str_replace('-', '', $lastAccessDate);
                $lastAccessDateTimeNumb = str_replace("-", "", str_replace(" ", "", str_replace(":", "", $lastAccessDateTime)));


                $lastAccessDateForCalcMin = $lastAccessDateForCalc.$dataForView['office_from'];
                $lastAccessDateForCalcMax = $lastAccessDateForCalc.$dataForView['office_to'];
                
                $todaysDateCalc = date("Ymd");
                $todaysDateTimeCalc = date("YmdHis");
                $todaysDateCalcMin = $todaysDateCalc.$dataForView['office_from'];
                $todaysDateCalcMax = $todaysDateCalc.$dataForView['office_to'];

                $todaysDateTime = date('Y-m-d H:i:s');
                $todaysDate = date('Y-m-d');

                if (($lastAccessDateTimeNumb >= $lastAccessDateForCalcMin && $lastAccessDateTimeNumb <= $lastAccessDateForCalcMax)) {
                    $lastAccessDateTimeObj = new DateTime($lastAccessDateTime);

                    if ($lastAccessDate != $todaysDate) {
                        $lastAccessDateLastObj = new DateTime($lastAccessDate.' '.$$dataForView['office_to_str']);
                    } else {
                        if ($todaysDateTimeCalc >= $todaysDateCalcMin && $todaysDateTimeCalc <= $todaysDateCalcMax) {
                            $lastAccessDateLastObj = new DateTime($todaysDateTime);
                        } else {
                            $lastAccessDateLastObj = new DateTime($todaysDate.' '.$$dataForView['office_to_str']);
                        }
                    }
                    
                    $interval = $lastAccessDateTimeObj->diff($lastAccessDateLastObj);

                    $lastAccessDayHour = $interval->format('%h');
                    $lastAccessDayMinutes = $interval->format('%i');
                    $lastAccessDaySeconds = $interval->format('%s');
                }
                if(!empty($workingDaysWform[$todaysDate])) {

                    if (($todaysDateTimeCalc >= $todaysDateCalcMin && $todaysDateTimeCalc <= $todaysDateCalcMax) && ($lastAccessDate != $todaysDate)) {
                        $todaysDateTimeObj = new DateTime($todaysDateTime);
                        $todaysDateLastObj = new DateTime($todaysDate.' '.$dataForView['office_from_str']);

                        $interval = $todaysDateTimeObj->diff($todaysDateLastObj);

                        $todaysDayHour = $interval->format('%h');
                        $todaysDayMinutes = $interval->format('%i');
                        $todaysDaySeconds = $interval->format('%s');
                    } elseif (($todaysDateTimeCalc > $todaysDateCalcMax) && ($lastAccessDate != $todaysDate)) {
                        $todaysDateTimeObj = new DateTime($todaysDate.' '.$$dataForView['office_to_str']);
                        $todaysDateLastObj = new DateTime($todaysDate.' '.$dataForView['office_from_str']);

                        $interval = $todaysDateTimeObj->diff($todaysDateLastObj);

                        $todaysDayHour = $interval->format('%h');
                        $todaysDayMinutes = $interval->format('%i');
                        $todaysDaySeconds = $interval->format('%s');
                    }
                }

                $totalHoursOnThisQueue = $lastAccessDayHour + $totalWorkingHoursOnThisReq + $todaysDayHour;
                $totalMinutesOnThisQueue = $lastAccessDayMinutes + $todaysDayMinutes;
                $totalSecondsOnThisQueue = $lastAccessDaySeconds + $todaysDaySeconds;
                $queue_duration = $totalHoursOnThisQueue.'.'.$totalMinutesOnThisQueue;
                if ($queue_duration > 8) {
                    $tatBreachModel = new TATBreach;
                    $tatBreachModel->reference_no = $data['reference_number'];
                    $tatBreachModel->unit_id = $data['unit_id'];
                    $tatBreachModel->customer_name = $data['customer_name'];
                    $tatBreachModel->request_type = 'wform';
                    $tatBreachModel->queue_date = $lastAccessDate;
                    $tatBreachModel->queue_time = $queue_duration;
                    $tatBreachModel->status = 0;
                    $tatBreachModel->save();
                }
            }
        }
        if (!empty($complaintData)) {
            foreach ($complaintData as $data) {
                $lastAccessDayHour = 0;
                $lastAccessDayMinutes = 0;
                $lastAccessDaySeconds = 0;

                $todaysDayHour = 0;
                $todaysDayMinutes = 0;
                $todaysDaySeconds = 0;

                $lastAccessDate = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('Y-m-d')  : Carbon::createFromTimestamp($data['date'])->format('Y-m-d');
                $lastAccessTime = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('H:i:s')  : Carbon::createFromTimestamp($data['date'])->format('H:i:s');
                $lastAccessDateTime = (!empty($data['last_comment'])) ? Carbon::parse($data['last_comment']['created_at'])->format('Y-m-d H:i:s')  : Carbon::createFromTimestamp($data['date'])->format('Y-m-d H:i:s');

                $totalWorkingDaysOnThisReq = (!empty($data['last_comment'])) ? $data['last_comment']['total_working_days2']  : $data['total_working_days'] ;

                $totalWorkingHoursOnThisReq = (!empty($totalWorkingDaysOnThisReq)) ? $totalWorkingDaysOnThisReq * 8 : 0;


                $lastAccessDateForCalc = str_replace('-', '', $lastAccessDate);
                $lastAccessDateTimeNumb = str_replace("-", "", str_replace(" ", "", str_replace(":", "", $lastAccessDateTime)));


                $lastAccessDateForCalcMin = $lastAccessDateForCalc.$dataForView['office_from'];
                $lastAccessDateForCalcMax = $lastAccessDateForCalc.$dataForView['office_to'];
                
                $todaysDateCalc = date("Ymd");
                $todaysDateTimeCalc = date("YmdHis");
                $todaysDateCalcMin = $todaysDateCalc.$dataForView['office_from'];
                $todaysDateCalcMax = $todaysDateCalc.$dataForView['office_to'];

                $todaysDateTime = date('Y-m-d H:i:s');
                $todaysDate = date('Y-m-d');

                if (($lastAccessDateTimeNumb >= $lastAccessDateForCalcMin && $lastAccessDateTimeNumb <= $lastAccessDateForCalcMax)) {
                    $lastAccessDateTimeObj = new DateTime($lastAccessDateTime);

                    if ($lastAccessDate != $todaysDate) {
                        $lastAccessDateLastObj = new DateTime($lastAccessDate.' '.$$dataForView['office_to_str']);
                    } else {
                        if ($todaysDateTimeCalc >= $todaysDateCalcMin && $todaysDateTimeCalc <= $todaysDateCalcMax) {
                            $lastAccessDateLastObj = new DateTime($todaysDateTime);
                        } else {
                            $lastAccessDateLastObj = new DateTime($todaysDate.' '.$$dataForView['office_to_str']);
                        }
                    }
                    
                    $interval = $lastAccessDateTimeObj->diff($lastAccessDateLastObj);

                    $lastAccessDayHour = $interval->format('%h');
                    $lastAccessDayMinutes = $interval->format('%i');
                    $lastAccessDaySeconds = $interval->format('%s');
                }
                if(!empty($workingDaysCompaint[$todaysDate])) {

                    if (($todaysDateTimeCalc >= $todaysDateCalcMin && $todaysDateTimeCalc <= $todaysDateCalcMax) && ($lastAccessDate != $todaysDate)) {
                        $todaysDateTimeObj = new DateTime($todaysDateTime);
                        $todaysDateLastObj = new DateTime($todaysDate.' '.$dataForView['office_from_str']);

                        $interval = $todaysDateTimeObj->diff($todaysDateLastObj);

                        $todaysDayHour = $interval->format('%h');
                        $todaysDayMinutes = $interval->format('%i');
                        $todaysDaySeconds = $interval->format('%s');
                    } elseif (($todaysDateTimeCalc > $todaysDateCalcMax) && ($lastAccessDate != $todaysDate)) {
                        $todaysDateTimeObj = new DateTime($todaysDate.' '.$$dataForView['office_to_str']);
                        $todaysDateLastObj = new DateTime($todaysDate.' '.$dataForView['office_from_str']);

                        $interval = $todaysDateTimeObj->diff($todaysDateLastObj);

                        $todaysDayHour = $interval->format('%h');
                        $todaysDayMinutes = $interval->format('%i');
                        $todaysDaySeconds = $interval->format('%s');
                    }
                }

                $totalHoursOnThisQueue = $lastAccessDayHour + $totalWorkingHoursOnThisReq + $todaysDayHour;
                $totalMinutesOnThisQueue = $lastAccessDayMinutes + $todaysDayMinutes;
                $totalSecondsOnThisQueue = $lastAccessDaySeconds + $todaysDaySeconds;
                $queue_duration = $totalHoursOnThisQueue.'.'.$totalMinutesOnThisQueue;
                if ($queue_duration > 8) {
                    $tatBreachModel = new TATBreach;
                    $tatBreachModel->reference_no = $data['reference_number'];
                    $tatBreachModel->unit_id = $data['unit_id'];
                    $tatBreachModel->customer_name = $data['customer_name'];
                    $tatBreachModel->request_type = 'complaint';
                    $tatBreachModel->queue_date = $lastAccessDate;
                    $tatBreachModel->queue_time = $queue_duration;
                    $tatBreachModel->status = 0;
                    $tatBreachModel->save();
                }
            }
        }
    }

    public function sentMailOnTatBreach() {
        $tatBreachModel = new TATBreach;
        
        $userData = array();
        $unitWiseListing = array();
        $tatBreachIdLists = array();
        $unitLists = array();

        $tbData = array();
        $tbDataObj = $tatBreachModel->where('status',0)->orderBy('unit_id')->orderBy('request_type','DESC')->orderBy('queue_time','DESC')->get();
        if (!empty($tbDataObj)) {
            $tbData = $tbDataObj->toArray();
            foreach ($tbData as $value) {
                $id = $value['id'];
                $unit_id = $value['unit_id'];
                if (empty($unitWiseListing[$unit_id])) {
                   $unitWiseListing[$unit_id] = array();
                }
                $unitLists[$unit_id] = $unit_id;
                $unitWiseListing[$unit_id][] = $value;

                $tatBreachIdLists[$id] = $id;

            }
        }
        if (!empty($unitWiseListing)) {
            $userUnitModel = new UserUnit;
            $userDataObj = $userUnitModel
                                ->select('users.id','users.email','user_units.unit_id')
                                ->leftJoin('users','user_units.user_id','users.id')
                                ->whereIn("user_units.unit_id",$unitLists)
                                ->where("user_units.is_unit_head",1)
                                ->get();

            if (!empty($userDataObj)) {
                $userData = $userDataObj->toArray();

                foreach ($userData as $userVal) {
                    $unit_id_str = $userVal['unit_id'];
                    $unitIdArr = explode(',', $unit_id_str);
                    $messages = "";
                    if (!empty($unitIdArr)) {
                        foreach ($unitIdArr as $unit_id) {
                            if (!empty($unitWiseListing[$unit_id])) {
                                $messages .= 
                                '<html>'.
                                    '<head>'.
                                        '<title>WBMS</title>'.
                                    '</head>'.
                                    '<body>'.
                                        '<div style="width: 800px; border: 2px #a6a6a6 solid; margin: 0 auto; overflow: hidden; border-radius: 7px;">'.
                                            '<div style="padding-left: 30px; padding-top: 10px; ">'.
                                                '<div>'.
                                                    '<div >Dear Concern,</div>'.
                                                    '<div style="padding-bottom:15px;">Need your kind attention about below WForm / Complaint.</div>'.
                                                    '<div style="">'.
                                                        '<table style="border-collapse: collapse; border:1px solid #69899F;">'.
                                                            '<tr>'.
                                                                '<th style="text-align:center; border:1px solid #69899F;">Reference No</th>'.
                                                                '<th style="text-align:center; border:1px solid #69899F;">Customer Name</th>'.
                                                                '<th style="text-align:center; border:1px solid #69899F;">Request Type</th>'.
                                                                '<th style="text-align:center; border:1px solid #69899F;">Queue In (Date &amp; Time)</th>'.
                                                            '</tr>';
                                                            foreach ($unitWiseListing[$unit_id] as $value) {
                                                                $reference_no = $value['reference_no'];
                                                                $customer_name = $value['customer_name'];
                                                                $request_type = $value['request_type'];
                                                                $queue_date = $value['queue_date'];
                                                                $queue_time = str_replace('.', ':', $value['queue_time']);
                                                                $messages .= '<tr>'.
                                                                                '<td style="text-align:center; border:1px solid #69899F;">'.$reference_no.'</td>'.
                                                                                '<td style="text-align:center; border:1px solid #69899F;">'.$customer_name.'</td>'.
                                                                                '<td style="text-align:center; border:1px solid #69899F;">'.$request_type.'</td>'.
                                                                                '<td style="text-align:center; border:1px solid #69899F;">'.$queue_date.'-'.$queue_time.'</td>'.
                                                                            '</tr>';
                                                            }
                                                            $messages .= 
                                                        '</table>'.
                                                    '</div>'.
                                                    '<div style="padding-top:15px;padding-bottom:15px;">Regards, Lanka Bangla Finance Ltd.</div>'.
                                                '</div>'.
                                            '</div>'.
                                        '</div>'.
                                    '</body>'.
                                '</html>';
                                $messages = trim($messages);

                                date_default_timezone_set('Asia/Dhaka');
                                $savedtime = date("Y-m-d H:i:s");            
                                if($messages != "") {
                                    $outgoingEMAILModel = new OutgoingEMAIL;
                                    $outgoingEMAILModel->subject = 'Unit Head TAT Breach Mail';
                                    $outgoingEMAILModel->body = $messages;
                                    $outgoingEMAILModel->savetime = $savedtime;
                                    $outgoingEMAILModel->senttime = '';
                                    $outgoingEMAILModel->status = '3';
                                    // $outgoingEMAILModel->email_address = $userVal['email'];
                                    $outgoingEMAILModel->email_address = 'asrbotanynu@gmail.com';
                                    if ($outgoingEMAILModel->save()) {
                                        DB::table('tat_breaches')->whereIn('id',$tatBreachIdLists)->update(['status'=>1]);
                                    }
                                }
                            }
                        }
                    }
                    
                }
            }
        }
    }

}
