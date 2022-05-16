<?php
    
namespace App\Traits;
// use GuzzleHttp\Exception\RequestException;
// use Twilio\Rest\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
    
trait SendtOtpMailTrait {
    public function sendOtp($email, $code){
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);

        try {
            
            $mail->SMTPDebug = 1;
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';           
            $mail->SMTPAuth = true;
            $mail->Username = 'isagamers96@gmail.com';  
            $mail->Password = 'isagamers96';      
            $mail->SMTPSecure = 'tls';                
            $mail->Port = 587;

            $mail->setFrom('isagamers96@gmail.com', 'Restaurant Bot');
            $mail->addAddress($email);
            // $mail->addCC($request->emailCc);
            // $mail->addBCC($request->emailBcc);

            // $mail->addReplyTo('sender-reply-email', 'sender-reply-name');

            // if(isset($_FILES['emailAttachments'])) {
            //     for ($i=0; $i < count($_FILES['emailAttachments']['tmp_name']); $i++) {
            //         $mail->addAttachment($_FILES['emailAttachments']['tmp_name'][$i], $_FILES['emailAttachments']['name'][$i]);
            //     }
            // }


            $mail->isHTML(true);

            $mail->Subject = 'Verify Account Restaurant Bot';
            $mail->Body    = '<center> <h4> Your OTP '.$code.'  <h4> <center>';

            if( !$mail->send() ) {
                return back()->with("failed", "Email not sent.")->withErrors($mail->ErrorInfo);
            }
            
            else {
                return back()->with("success", "Email has been sent.");
            }

        } catch (Exception $e) {
             return back()->with('error','Message could not be sent.');
        }
    }
        
    public function generateCode(){
        return mt_rand();
    }      
}