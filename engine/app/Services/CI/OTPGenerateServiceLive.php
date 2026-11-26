<?php

namespace App\Services\CI;

use App\OtpCode;
use Carbon\Carbon;
use App\SMSEmail;
use App\OutgoingSMS;
use App\OutgoingEMAIL;
use SimpleXMLElement;

class OTPGenerateServiceLive
{
    public static function otpCodeGenerate($cif_number, $w_form_type, $product_type, $account_number, $reference_number, $mobile_number, $email_address, $otp_mode)
    {
        $otp = rand(111111, 999999);
        $encryptOtp = encrypt($otp);
        $otpObj = new OtpCode();
        $otpObj->cif_number = $cif_number;
        $otpObj->service_type = $w_form_type;
        $otpObj->product_type = $product_type;
        $otpObj->account_number = $account_number;
        $otpObj->reference_number = $reference_number;
        $otpObj->mobile = $mobile_number;
        $otpObj->send_status = 0;
        $otpObj->otp = $encryptOtp;
        $otpObj->otp_status = 'Pending';
        $otpObj->save();
        // $data['otpGenId'] = $otpObj->id;
        $data['otpGenId'] = encrypt($otpObj->id);
        $data['otpCode'] = $otp;
        $maskedOtp = self::maskOtpNumber($otp);
        // dd($maskedOtp);
        $otpResponse = self::otpCodeSend($cif_number, $maskedOtp, $product_type, $otp, $mobile_number, $reference_number, $email_address, $otp_mode);

        OtpCode::where('id', $otpObj->id)->update([
            /*'otp_status' => $otpResponse->getData()->errorCode == 000? 'Success' : 'Failed',*/
            'send_status' => $otpResponse->getData()->errorCode == 000? 1 : 0,
            'error_code' => $otpResponse->getData()->errorCode,
            'response_message' => $otpResponse->getData()->errorMessage,
            'global_transaction_id' => $otpResponse->getData()->globalTransactionId,
            'service_code' => $otpResponse->getData()->serviceCode,
            'channel_code' => $otpResponse->getData()->channelCode,
            'expire_at' => Carbon::now()->addSecond(180),
            'generated_date_time' => Carbon::now(),
        ]);

        if ($otpResponse->getData()->errorCode == 000) {
            OutgoingSMS::where('id', $otpResponse->getData()->sendSms)->update([
                'senttime' => Carbon::now(),
                'status' => 1
            ]);
        }else{
            OutgoingSMS::where('id', $otpResponse->getData()->sendSms)->update([
                'senttime' => Carbon::now(),
                'status' => 4
            ]);
        }

        return $data;
    }


    public static function maskOtpNumber($otpNumber)
    {
        $visibleDigits = 2;
        $totalLength = strlen($otpNumber);
        $maskedDigits = $totalLength - (2 * $visibleDigits);
        $mask = str_repeat('*', $maskedDigits);
        $visiblePart = substr($otpNumber, 0, $visibleDigits);
        $maskedPart = substr($otpNumber, $visibleDigits, $maskedDigits);
        $lastVisiblePart = substr($otpNumber, -$visibleDigits);
        $maskedOtpNumber = $visiblePart . $mask . $lastVisiblePart;
        return $maskedOtpNumber;
    }

    public static function otpCodeSend($cif_number, $maskedOtp, $product_type, $code, $mobile_number, $reference_number, $email_address, $otp_mode)
    {
        $smsEmailModel = new SMSEmail();
        $smsEmailData = $smsEmailModel->orderBy('id','DESC')->first();
        if (!empty($smsEmailData)) {
            $sms = $smsEmailData['otp_sms'];
            $mail = $smsEmailData['otp_email'];
        }

        // Store OTP outgoingsmstable table:
        if ($otp_mode == 1){
            if (!empty($sms)) {
                $unmaskedSms = str_replace("{otp_code}", $code, $sms);
                $maskedSms = str_replace("{otp_code}", $maskedOtp, $sms);
                $msg['sms'] = $unmaskedSms;
                $msg['maskedSms'] = $maskedSms;
                $sendSms = self::sendSMS($mobile_number, $msg['maskedSms'], $reference_number, 0, 0);
            }
        }
        // Store OTP outgoingemailtable table:
        if ($otp_mode == 2){
            if (!empty($mail)) {
                $mail = str_replace("{otp_code}", $code, $mail);
                $msg['mail'] = $mail;
                $sendSms = self::sendEMAIL($email_address, $msg['mail'],$reference_number,0, "Ticket Log OTP",0);
            }
        }

        $sourceTimestamp = now()->format('Y-m-d\TH:i:s.v');
        $globalTransactionId = 'AYS_OTP' . now()->format('YmdHisv');

        $endpoint = "https://esb.bracbank.com:4444/UBA/SendNotification/V1";
        $request_body_masked = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://bracbank.com/UBASendNotificationES/V1" xmlns:v11="http://bracbank.com/utilityCommon/V1">
            <soapenv:Header/>
            <soapenv:Body>
               <v1:sendNotification>
                  <v1:sendNotificationDetails>
                     <v1:header>
                        <v11:serviceId>004</v11:serviceId>
                        <v11:serviceName>SendNotification</v11:serviceName>
                        <v11:sourceTimestamp>'.$sourceTimestamp.'</v11:sourceTimestamp>
                        <v11:password>@YS@BBL!20230608</v11:password>
                        <v11:channelName>AYS</v11:channelName>
                        <v11:channelCode>IB</v11:channelCode>
                        <v11:globalTransactionId>'.$globalTransactionId.'</v11:globalTransactionId>
                     </v1:header>
                     <v1:cif>'.$cif_number.'</v1:cif>
                     <v1:mobileNumber>'.$mobile_number.'</v1:mobileNumber>
                     <v1:smsCode>50</v1:smsCode>
                     <v1:smsTemplate>'.$maskedSms.'</v1:smsTemplate>
                     <v1:smsRemarks>OTP</v1:smsRemarks>
                  </v1:sendNotificationDetails>
               </v1:sendNotification>
            </soapenv:Body>
         </soapenv:Envelope>
        ';

        $msc = microtime(true);

        CIRequestResponseService::ciRequestResponse([
            'cif_number' => $cif_number,
            'product_type' => $product_type,
            'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
            'endpoint' => $endpoint,
            'json_node' => $request_body_masked,
        ]);

        $request_body_unmasked = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v1="http://bracbank.com/UBASendNotificationES/V1" xmlns:v11="http://bracbank.com/utilityCommon/V1">
            <soapenv:Header/>
            <soapenv:Body>
               <v1:sendNotification>
                  <v1:sendNotificationDetails>
                     <v1:header>
                        <v11:serviceId>004</v11:serviceId>
                        <v11:serviceName>SendNotification</v11:serviceName>
                        <v11:sourceTimestamp>'.$sourceTimestamp.'</v11:sourceTimestamp>
                        <v11:password>@YS@BBL!20230608</v11:password>
                        <v11:channelName>AYS</v11:channelName>
                        <v11:channelCode>IB</v11:channelCode>
                        <v11:globalTransactionId>'.$globalTransactionId.'</v11:globalTransactionId>
                     </v1:header>
                     <v1:cif>'.$cif_number.'</v1:cif>
                     <v1:mobileNumber>'.$mobile_number.'</v1:mobileNumber>
                     <v1:smsCode>50</v1:smsCode>
                     <v1:smsTemplate>'.$unmaskedSms.'</v1:smsTemplate>
                     <v1:smsRemarks>OTP</v1:smsRemarks>
                  </v1:sendNotificationDetails>
               </v1:sendNotification>
            </soapenv:Body>
         </soapenv:Envelope>
        ';

        $headers = array("Content-type: text/xml;charset=\"utf-8\"", "Accept: text/xml", "Cache-Control: no-cache", "Pragma: no-cache", "SOAPAction: casaListAndDetails", "Content-length: ".strlen($request_body_unmasked));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_body_unmasked);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_errno($ch);
        }
        curl_close($ch);
        if (isset($error_msg)) {
            // print_r($error_msg);
        }

        $errorCode = null;
        $errorMessage = "";
        $serviceCode = "";
        $channelCode = "";
        $msc = microtime(true) - $msc;
        $execution_time = number_format($msc,2);

        if (!empty($response)) {
            $soapXML = new SimpleXMLElement($response);

            $soapXML->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
            $soapXML->registerXPathNamespace('ns1', 'http://bracbank.com/UBASendNotificationES/V1');
            $soapXML->registerXPathNamespace('ns2', 'http://bracbank.com/utilityCommon/V1');

            if ($soapXML->xpath('//ns1:errorCode')) {
                $errorCode = (string)$soapXML->xpath('//ns1:errorCode')[0];
            } elseif ($soapXML->xpath('//ns2:errorCode')) {
                $errorCode = (string)$soapXML->xpath('//ns2:errorCode')[0];
            }

            if ($soapXML->xpath('//ns1:errorMessage')) {
                $errorMessage = (string)$soapXML->xpath('//ns1:errorMessage')[0];
            } elseif ($soapXML->xpath('//ns2:errorMessage')) {
                $errorMessage = (string)$soapXML->xpath('//ns2:errorMessage')[0];
            }

            if ($soapXML->xpath('//ns1:globalTransactionId')) {
                $globalTransactionId = (string)$soapXML->xpath('//ns1:globalTransactionId')[0];
            } elseif($soapXML->xpath('//ns2:globalTransactionId')) {
                $globalTransactionId = (string)$soapXML->xpath('//ns2:globalTransactionId')[0];
            }

            if ($soapXML->xpath('//ns1:serviceId')) {
                $serviceCode = (string)$soapXML->xpath('//ns1:serviceId')[0];
            } elseif($soapXML->xpath('//ns2:serviceId')) {
                $serviceCode = (string)$soapXML->xpath('//ns2:serviceId')[0];
            }

            if ($soapXML->xpath('//ns1:channelCode')) {
                $channelCode = (string)$soapXML->xpath('//ns1:channelCode')[0];
            } elseif($soapXML->xpath('//ns2:channelCode')) {
                $channelCode = (string)$soapXML->xpath('//ns2:channelCode')[0];
            }

            // $errorCode = $soapXML->xpath('//*[local-name()="errorCode"]')[0];
            // $errorMessage = $soapXML->xpath('//*[local-name()="errorMessage"]')[0];
            // $globalTransactionId = $soapXML->xpath('//*[local-name()="globalTransactionId"]')[0];

            if ($product_type == 2 || $product_type == 4) {
                $accountNumbers = $soapXML->xpath('//*[local-name()="accountNumber"]');
                $productDescriptions = $soapXML->xpath('//*[local-name()="productDescription"]');
                foreach ($accountNumbers as $index => $accountNumber) {
                    $accountNumberValue = (string)$accountNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[$accountNumberValue] = "$accountNumberValue | $productDescriptionValue";
                }
            }
            if ($product_type == 1) {
                $maskedCardNumbers = $soapXML->xpath('//*[local-name()="maskedCardNumber"]');
                $productDescriptions = $soapXML->xpath('//*[local-name()="productDesc"]');
                foreach ($maskedCardNumbers as $index => $maskedCardNumber) {
                    $maskedCardNumberValue = (string)$maskedCardNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[(string)$maskedCardNumberValue] = "$maskedCardNumberValue | $productDescriptionValue";
                }
            }
            if ($product_type == 3) {
                $maskedCardNumbers = $soapXML->xpath('//*[local-name()="maskedCardNumber"]');
                $productDescriptions = $soapXML->xpath('//*[local-name()="schemeCode"]');
                foreach ($maskedCardNumbers as $index => $maskedCardNumber) {
                    $maskedCardNumberValue = (string)$maskedCardNumber;
                    $productDescriptionValue = (string)$productDescriptions[$index];
                    $accountNumberList[(string)$maskedCardNumberValue] = "$maskedCardNumberValue | $productDescriptionValue";
                }
            }
            CIRequestResponseService::ciRequestResponse([
                'cif_number' => $cif_number,
                'product_type' => $product_type,
                'global_transaction_id' => (!empty($globalTransactionId)) ? $globalTransactionId : '',
                'status_code' => (!empty($errorCode)) ? $errorCode : '',
                'status_msg' => (!empty($errorMessage)) ? $errorMessage : '',
                'endpoint' => $endpoint,
                'json_node' => $response,
                'execution_time' => $execution_time,
            ]);

        }

        return response()->json([
            'data' => $response,
            'sendSms' => $sendSms,
            'maskedOtp' => $maskedOtp,
            'errorCode' => $errorCode,
            'channelCode' => $channelCode,
            'serviceCode' => $serviceCode,
            'errorMessage' => $errorMessage,
            'globalTransactionId' => $globalTransactionId,
        ]);
    }


    public static function sendSMS($mobile_no, $msg, $ref_no = "",$supp_status=NULL,$sendbackStatus=0)
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
                return $outgoingSMSModel->id;
            }
        }
    }

    public static function sendEMAIL($email_address, $mail, $ref_no = "",$supp_status=NULL, $subject=null,$sendbackStatus=0)
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
