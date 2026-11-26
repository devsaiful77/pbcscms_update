<?php

namespace Modules\Requisition\App\Service;

use App\OutgoingEMAIL;
use Modules\Requisition\App\Models\RequisitionSMSEmail;

class RequisitionEmailService
{
    public function outgoingSMSEmail($requisitionType = "", $requisitionNumber = "", $notificationType = "")
    {
        $msg = array();
        $mail = "";

        if ($notificationType == "opening") {

            $smsEmailModel = new RequisitionSMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id', 'DESC')->first();

            if (!empty($smsEmailData)) {
                $mail = $smsEmailData['issue_opening_email'];
            }
            if (!empty($mail)) {
                $mail = str_replace("{requisition_number}", $requisitionNumber, $mail);
                $mail = str_replace("{requisition_type}", $requisitionType, $mail);
                $msg['mail'] = $mail;
            }
        }



        if ($notificationType == "approve") {

            $smsEmailModel = new RequisitionSMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id', 'DESC')->first();

            if (!empty($smsEmailData)) {
                $mail = $smsEmailData['requisition_approve_email'];
            }
            if (!empty($mail)) {
                $mail = str_replace("{requisition_number}", $requisitionNumber, $mail);
                $mail = str_replace("{requisition_type}", $requisitionType, $mail);
                $msg['mail'] = $mail;
            }
        }

        if ($notificationType == "cancel") {

            $smsEmailModel = new RequisitionSMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id', 'DESC')->first();

            if (!empty($smsEmailData)) {
                $mail = $smsEmailData['requisition_cancel_email'];
            }
            if (!empty($mail)) {
                $mail = str_replace("{requisition_number}", $requisitionNumber, $mail);
                $mail = str_replace("{requisition_type}", $requisitionType, $mail);
                $msg['mail'] = $mail;
            }
        }

        if ($notificationType == "close") {

            $smsEmailModel = new RequisitionSMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id', 'DESC')->first();

            if (!empty($smsEmailData)) {
                $mail = $smsEmailData['issue_closing_email'];
            }
            if (!empty($mail)) {
                $mail = str_replace("{requisition_number}", $requisitionNumber, $mail);
                $mail = str_replace("{requisition_type}", $requisitionType, $mail);
                $msg['mail'] = $mail;
            }
        }

        if ($notificationType == "sendback") {

            $smsEmailModel = new RequisitionSMSEmail();
            $smsEmailData = $smsEmailModel->orderBy('id', 'DESC')->first();

            if (!empty($smsEmailData)) {
                $mail = $smsEmailData['requisition_sendback_email'];
            }
            if (!empty($mail)) {
                $mail = str_replace("{requisition_number}", $requisitionNumber, $mail);
                $mail = str_replace("{requisition_type}", $requisitionType, $mail);
                $msg['mail'] = $mail;
            }
        }

        return $msg;
    }

    public function sendEMAIL($email_address, $mail, $ref_no = "", $supp_status = NULL)
    {
        // dd($email_address, $mail, $ref_no, $supp_status);
        date_default_timezone_set('Asia/Dhaka');
        $savedtime = date("Y-m-d H:i:s");
        if ($mail != "") {
            $outgoingEMAILModel = new OutgoingEMAIL();
            $outgoingEMAILModel->subject = 'PB Support';
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
