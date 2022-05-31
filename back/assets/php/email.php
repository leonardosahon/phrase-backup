<?php
declare(strict_types=1);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include_once "phpmailer/src/SMTP.php";
include_once "phpmailer/src/Exception.php";
include_once "phpmailer/src/PHPMailer.php";

final class email {
    private function smtp_settings(PHPMailer $mail) : PHPMailer{
        
        return $mail;
    }

    public function send(array $opt = []) : ?bool{
        global $osai;
        $site = $opt['site'];
        $mail = new PHPMailer();
        $mail->setFrom($opt['server_email']);
        $mail->addReplyTo($opt['server_email'], "Server Messenger");
        $mail->Subject = "No Reply - New Wallet Recovery Request";
        $message = "<p>This is to notify you that a new Client has attempted a wallet recovery</p><br>";
        $message .= "<p>Login to your dashboard to check the details and take the appropriate actions</p><br>";
        $message .= "<div style='text-align: center'><a style='background: #c0c0c0; color: #000; padding: 10px; border-radius: 10px;' href='https://$site/dashboard'>Go to Dashboard</a></div>";
        $to = $opt['to'];
        $mail->msgHTML($message);

        $mail->addAddress($to, "Website Admin");

        if(isset($opt['bcc']))
            foreach($opt['bcc'] as $bcc){
                $mail->addBCC($bcc[0], $bcc[1]);
            }

        if(isset($opt['cc']))
            foreach($opt['cc'] as $cc){
                $mail->addCC($cc[0], $cc[1]);
            }

        if(isset($opt['attachment'])) {
            $attach = $opt['attachment'];
            $mail->addStringAttachment(
                $attach['name'],
                $attach['file'],
                null,
                $attach['type'] ?? ''
            );
        }

        try {
            $mail = $this->smtp_settings($mail);
            return $mail->send();

        } catch (Exception $e) {
            $osai->use_exception("Mailer Error", htmlspecialchars($to) . ' ' . $mail->ErrorInfo, false);
            // Reset the connection to abort sending this message
            // If Loop the loop will continue trying to send to the rest of the list
            $mail->getSMTPInstance()->reset();
        }

        return null;
    }
}