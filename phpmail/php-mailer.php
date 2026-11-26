<?php

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function send($to, $subject, $message)
    {
        
        $subject = 'UAT-Prime Support';
        $from = 'uatsupport@primebank.com';
        $header = "X-Mailer: PHP/".phpversion() . "Return-Path: $from";

        try {
            $mail = new PHPMailer(true);            
            $mail->isSMTP();                                         // Set mailer to use SMTP
            $mail->SMTPAuth = true;                                  // Enable SMTP authentication
            $mail->SMTPSecure = 'tls';                             // Enable TLS encryption, `ssl` also accepted
            $mail->Host = 'smtp.gmail.com';  //'172.26.8.100';//Test
            $mail->Port = 587;
            $mail->Username = 'hasibur.rahman.shajid01@gmail.com';
            $mail->Password = 'anttheripfchhkwr';
            $mail->SMTPDebug = 3;
            
            // TCP port to connect to
            $mail->From = $from;
            $mail->FromName = 'UAT-Prime Bank Limited.';
            $mail->Sender = $from;

            if(is_array($to)) {
                foreach($to as $toRow) {
                    $mail->addAddress($toRow); 
                }
            } else {
                $mail->addAddress($to);     // Add a recipient
            }
        
            $mail->isHTML(true);                                  // Set email format to HTML
            //$mail->IsHTML(true);
            $mail->CreateHeader($header);

            $mail->Subject = $subject;
            $mail->Body    = $message;
            //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            //echo '<pre>';
            //print_r($mail);

            if(!$mail->send()) {            
                return false;
            } else {        
                return true;
            }
            
        } catch (Exception $e) {
            echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
        }

}