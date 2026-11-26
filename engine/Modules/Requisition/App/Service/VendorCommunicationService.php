<?php
namespace Modules\Requisition\App\Service;
// require $_SERVER['DOCUMENT_ROOT'] . '/phpmail/vendor/autoload.php';
require dirname(base_path()) . '/phpmail/vendor/autoload.php';
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class VendorCommunicationService
{
    public function send($to, $subject, $message, $attachments = [])
    {
        $subject = 'UAT-Prime Support';
        $from = 'vendor@primebank.com'; // Vendor mail sender (normal mail sender phpmail/php-mailer.php-te)
        $header = "X-Mailer: PHP/".phpversion() . "Return-Path: $from";

        try {
            $mail = new PHPMailer(true);            
            $mail->isSMTP();                                        
            $mail->SMTPAuth = true;                     
            $mail->SMTPSecure = 'tls';                     
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            // $mail->addReplyTo('shajid.hasibur1996@gmail.com', 'Md. Hasibur Rahman Shajid');
            $mail->Username = 'hasibur.rahman.shajid01@gmail.com';
            $mail->Password = 'anttheripfchhkwr';
            $mail->SMTPDebug = 0;
            
            // TCP port to connect to
            $mail->From = $from;
            $mail->FromName = 'Prime Bank Procurement';
            $mail->Sender = $from;

            if(!empty($attachments)){
                foreach($attachments as $attachment) {
                    if(file_exists($attachment)) {
                        $mail->addAttachment($attachment); // Add attachments
                    }
                }
            }
            
            if(is_array($to)) {
                foreach($to as $toRow) {
                    $mail->addAddress($toRow); 
                }
            } else {
                $mail->addAddress($to);     // Add a recipient
            }
        
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->CreateHeader($header);

            $mail->Subject = $subject;
            $mail->Body    = $message;
            
            if(!$mail->send()) {         
                return false;
            } else {        
                return true;
            }
            
        } catch (Exception $e) {
            echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
            return false;
        }
    }
}