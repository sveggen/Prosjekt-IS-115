<?php


namespace App\helpers;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailSender {

    /**
     * Sends an email to one or persons.
     *
     * @param $recipient array | string One or more recipients
     * @param $subject string The email's subject.
     * @param $content string The emails content in html format.
     * @return bool
     */
    public function sendEmail($recipient, string $subject, string $content): bool {

        $mail = new PHPMailer(true);
        try {

            $mail->isSMTP(); //select protocol
            $mail->Host = 'smtp.gmail.com'; // select server (gmail)
            $mail->SMTPAuth = true; // set authentication to true
            $mail->Username = $_ENV['MAIL_USERNAME']; // username to gmail account
            $mail->Password = $_ENV['MAIL_PASSWORD']; //password to gmail account
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //sets encryption to TLS
            $mail->Port = 587; //TCP port to connect to

            // sender username (Neo Youthclub's gmail)
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Neo Youthclub');

            $mail->isHTML(true); // sets content to html format
            $mail->Subject = $subject;
            $mail->Body = $content;

            // loops over the array if there are multiple recipients
            // and sends the email to all of them
            if (is_array($recipient)) {
                foreach ($recipient as $member) {
                    $mail->addAddress($member);
                    $mail->send();
                    $mail->clearAddresses(); // clears all the recipients
                }
                // sends an email to the only recipient
            } else {
                $mail->addAddress($recipient);
                $mail->send();
            }

        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}