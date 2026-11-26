<?php

namespace App\Services\CI;

use App\OtpCode;
use Throwable;
use Carbon\Carbon;


class SendBackNotificationService{
   
    public function sendBackNotifyWithReason($ref, $issueId, $emailAddress, $mobileNo)
    {
        // $issueId = 114;
        // $ref = 'S230823AC000005';
        // $emailAddress = 'muajjam.imu@gmail.com';
        // $mobileNo = '01911194724';
        $outgoingSMSMessage = $this->outgoingSMSEmail("sendbackWithReason",$issueId,$ref,"notification",$name="");

        if (!empty($outgoingSMSMessage['sms'])) {
            $this->sendSMS($mobileNo, $outgoingSMSMessage['sms'],$ref,0,1);
        }

        if (!empty($outgoingSMSMessage['mail'])) {
            $this->sendEMAIL($emailAddress, $outgoingSMSMessage['mail'],$ref,0, "Send Back Alert",1);
        }
        echo "send with reason <br>";
    }

    public function sendBackNotifyWithOutReason($ref, $issueId, $emailAddress, $mobileNo)
    {
        $outgoingSMSMessage = $this->outgoingSMSEmail("sendbackWithOutReason",$issueId,$ref,"notification",$name="");

        if (!empty($outgoingSMSMessage['sms'])) {
            $this->sendSMS($mobileNo, $outgoingSMSMessage['sms'],$ref,0,1);
        }

        if (!empty($outgoingSMSMessage['mail'])) {
            $this->sendEMAIL($emailAddress, $outgoingSMSMessage['mail'],$ref,0, "Send Back Alert",1);
        }
        echo 'send with out reason <br>';
    }

    public function sendBackTicketClosedAfter24hour($ref, $issueId, $emailAddress, $mobileNo)
    {
        $outgoingSMSMessage['sms'] = "If the form is not submitted within the next 24 hours, the ticket will be automatically closed";
        $outgoingSMSMessage['mail'] = "If the form is not submitted within the next 24 hours, the ticket will be automatically closed";

        if (!empty($outgoingSMSMessage['sms'])) {
            $this->sendSMS($mobileNo, $outgoingSMSMessage['sms'],$ref,0,1);
        }

        if (!empty($outgoingSMSMessage['mail'])) {
            $this->sendEMAIL($emailAddress, $outgoingSMSMessage['mail'],$ref,0, "Send Back Alert",1);
        }
        echo 'ticket closed';
    }


    public function outgoingSMSEmail($supportType = "", $masterId = "", $referenceNumber = "",$notificationType = "",$issue_name = "")
    {
        $msg = array();
        $sms = "";
        $mail = "";
        if (empty($issue_name)) {
            $unitItemModelName = new UnitItem;
            $unitItemData = $unitItemModelName
                ->select("master_id","name")
                ->where("issues_from", $supportType)
                ->where("master_id",$masterId)
                ->first();
            if (!empty($unitItemData)) {
                $issue_name = $unitItemData['name'];
            }
        }
        if ($supportType == "noncustomer") {
            $smsEmailModel = new SMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();
            if (!empty($smsEmailData)) {
                $sms = $smsEmailData['non_cust_sms'];
                $mail = $smsEmailData['non_cust_email'];
            }
            if (!empty($sms)) {
                $sms = str_replace("{reference_no}", $referenceNumber, $sms);
                $sms = str_replace("{form_request}", $issue_name, $sms);
                $msg['sms'] = $sms;
            }
            if (!empty($mail)) {
                $mail = str_replace("{reference_no}", $referenceNumber, $mail);
                $mail = str_replace("{form_request}", $issue_name, $mail);
                $msg['mail'] = $mail;
            }
        }
        if ($supportType == "sendbackWithReason") {

            $SendBackReason = DB::table('comments')
            ->select('reference_number', 'action' ,'issendback', 'isapproved')
            ->where('reference_number',$referenceNumber)
            ->where('issendback',1)
            ->where('isapproved',1)
            ->latest()
            ->first();

            $smsEmailModel = new SMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();
            if (!empty($smsEmailData)) {
                $sms = $smsEmailData['send_back_sms'];
                $mail = $smsEmailData['send_back_email'];
            }

            if (!empty($sms)) {
                $sms = str_replace("{reference_no}", $referenceNumber, $sms);
                $sms = str_replace("{form_request}", $issue_name, $sms);
                $sms = str_replace("{Send_back_reason}", $SendBackReason->action, $sms);
                $msg['sms'] = $sms;
            }

            if (!empty($mail)) {
                $mail = str_replace("{reference_no}", $referenceNumber, $mail);
                $mail = str_replace("{form_request}", $issue_name, $mail);
                $mail = str_replace("{Send_back_reason}", $SendBackReason->action, $mail);
                $msg['mail'] = $mail;
            }
        }

        if ($supportType == "sendbackWithOutReason") {
            $smsEmailModel = new SMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();

            if (!empty($smsEmailData)) {
                $sms = $smsEmailData['send_back_auto_sms'];
                $mail = $smsEmailData['send_back_auto_email'];
            }

            if (!empty($sms)) {
                $sms = str_replace("{reference_no}", $referenceNumber, $sms);
                $sms = str_replace("{form_request}", $issue_name, $sms);
                $msg['sms'] = $sms;
            }

            if (!empty($mail)) {
                $mail = str_replace("{reference_no}", $referenceNumber, $mail);
                $mail = str_replace("{form_request}", $issue_name, $mail);
                $msg['mail'] = $mail;
            }
        }
        if (!empty($issue_name)) {
            $smsEmailModel = new SMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();
            if (!empty($smsEmailData)) {
                if ($notificationType == "open") {
                    if ($supportType == "wform") {
                        $sms = $smsEmailData['issue_opening_sms_wform'];
                        $mail = $smsEmailData['issue_opening_email_wform'];
                    } elseif ($supportType == "complaint") {
                        $sms = $smsEmailData['issue_opening_sms_complaint'];
                        $mail = $smsEmailData['issue_opening_email_complaint'];
                    }
                } elseif($notificationType == "close") {
                    if ($supportType == "wform") {
                        $sms = $smsEmailData['issue_closing_sms_wform'];
                        $mail = $smsEmailData['issue_closing_email_wform'];
                    } elseif ($supportType == "complaint") {
                        $sms = $smsEmailData['issue_closing_sms_complaint'];
                        $mail = $smsEmailData['issue_closing_email_complaint'];
                    }
                }
            }
            if (!empty($sms)) {
                $sms = str_replace("{reference_no}", $referenceNumber, $sms);
                $sms = str_replace("{form_request}", $issue_name, $sms);
                $msg['sms'] = $sms;
            }
            if (!empty($mail)) {
                $mail = str_replace("{reference_no}", $referenceNumber, $mail);
                $mail = str_replace("{form_request}", $issue_name, $mail);
                $msg['mail'] = $mail;
            }
        }
        return $msg;
    }

    public function sendSMS($mobile_no, $msg, $ref_no = "",$supp_status=NULL,$sendbackStatus=0)
    {
        date_default_timezone_set('Asia/Dhaka');
        $savedtime = date("Y-m-d H:i:s");
        $mobile_no_1 = str_replace("+88(00)", "+88", $mobile_no);
        $mnumber = formatMobileNumber($mobile_no_1);
        if($mnumber != "") {
            if(is_numeric($mnumber) && strlen($mnumber) == 14) {
                $outgoingSMSModel = new OutgoingSMS;
                $outgoingSMSModel->sentSMSid = 0;
                $outgoingSMSModel->send_back_status = $sendbackStatus;
                $outgoingSMSModel->message = $msg;
                $outgoingSMSModel->savetime = $savedtime;
                $outgoingSMSModel->senttime = '';
                $outgoingSMSModel->status = '3';
                $outgoingSMSModel->support_status = $supp_status;
                $outgoingSMSModel->mobileNo = $mnumber;
                $outgoingSMSModel->reference_number = $ref_no;
                $outgoingSMSModel->save();
            }
        }
    }

    public function sendEMAIL($email_address, $mail, $ref_no = "",$supp_status=NULL, $subject=null,$sendbackStatus=0)
    {
        date_default_timezone_set('Asia/Dhaka');
        $savedtime = date("Y-m-d H:i:s");
        if($mail != "") {
            $outgoingEMAILModel = new OutgoingEMAIL;
            $outgoingEMAILModel->subject = $subject;
            $outgoingEMAILModel->send_back_status = $sendbackStatus;
            $outgoingEMAILModel->body = $mail;
            $outgoingEMAILModel->savetime = $savedtime;
            $outgoingEMAILModel->senttime = '';
            $outgoingEMAILModel->status = '3';
            $outgoingEMAILModel->support_status = $supp_status;
            $outgoingEMAILModel->email_address = $email_address;
            $outgoingEMAILModel->reference_number = $ref_no;
            $outgoingEMAILModel->save();
        }
    }
}