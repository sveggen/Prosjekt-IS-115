<?php


namespace App\helpers;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {

    public function sendEmail($recipient, $subject, $content): bool {

        $mail = new PHPMailer(true);
        try {

            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = $_ENV['MAIL_USERNAME'];                     // SMTP username
            $mail->Password = $_ENV['MAIL_PASSWORD'];                              // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Neo Youthclub');

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body = $content;

            if (is_array($recipient)){
                foreach($recipient as $member) {
                    $mail->addAddress($member);
                    $mail->send();
                    $mail->clearAddresses();
                }
            } else {
                $mail->addAddress($recipient);
                $mail->send();
            }

        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function sendMultipleEmails(array $recipient, $subject, $content){
    }
}