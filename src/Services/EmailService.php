<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    // Constructor to initialize the PHPMailer instance with SMTP settings from environment variables
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $_ENV['MAIL_PORT'];
    }

    // Send an error email with box ID, prayer zone, and error message
    public function sendErrorEmail($boxId, $prayerZone, $errorMessage)
    {
        try {
            $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            // $this->mailer->addAddress('phu@expressinmusic.com');
            $this->mailer->addAddress('chesspamungkas@gmail.com');
            $this->mailer->Subject = 'Error in Prayer Time Generation';
            $this->mailer->Body = "An error occurred during prayer time generation.\n\n" .
                "Box ID: $boxId\n" .
                "Prayer Zone: $prayerZone\n" .
                "Error Message: $errorMessage";

            $this->mailer->send();
            echo "Error email sent successfully." . PHP_EOL;
        } catch (Exception $e) {
            echo "Error sending email: " . $this->mailer->ErrorInfo . PHP_EOL;
        }
    }
}
