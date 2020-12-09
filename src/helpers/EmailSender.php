<?php


namespace App\helpers;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Deals with the distribution of dynamic emails.
 *
 * Class EmailSender
 * @package App\helpers
 */
class EmailSender {

    /**
     * Sends an email to one or more persons from a predefined
     * Gmail-address.
     *
     * @param $recipient array | string One or more recipients
     * @param $subject string The email's subject
     * @param $content string The email's content in HTML-format
     * @return bool True if email was sent, false if not
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

            // Sender's gmail account (Neo Youth Club's gmail)
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Neo Youthclub');

            $mail->isHTML(true); // sets content to html format
            $mail->Subject = $subject;
            $mail->Body = $content;

            // sends the email to multiple recipients
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