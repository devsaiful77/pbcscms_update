<?php


namespace App\Services\CI;

use PHPMailer\PHPMailer\PHPMailer;
use Throwable;
class OTPSendEmailService
{
    public static function send($to, $subject, $message){

        $subject = $subject;
        $from = 'test_dmz@bracbank.com';
        $header = "X-Mailer: PHP/" . phpversion() . "Return-Path: $from";

        try {

            $mail = new PHPMailer(true);
            $mail->isSMTP();                                         // Set mailer to use SMTP
            $mail->SMTPAuth = false;                                 // Enable SMTP authentication
            $mail->SMTPSecure = 'tls';
            $mail->Host = 'smtp09.bracbank.com';//Prod: smtp10.bracbank.com
            $mail->Port = 465;

            // TCP port to connect to
            $mail->From = $from;
            $mail->FromName = 'UAT-BRAC Bank PLC.';
            $mail->Sender = $from;
            $mail->addAddress($to);

            //$mail->isHTML(true);                                  // Set email format to HTML
            //$mail->IsHTML(true);
            $mail->CreateHeader($header);

            $mail->Subject = $subject;
            $mail->Body = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            //echo '<pre>';
            //print_r($mail);

            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }

        } catch (Throwable $e) {
            dd($e->getMessage());
        }
    }
}
