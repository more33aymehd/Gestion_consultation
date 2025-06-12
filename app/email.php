<?php
// Include required PHPMailer files
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create instance of PHPMailer
$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aymericketogo@gmail.com';
    $mail->Password   = 'anue ijtd wsux izng';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Sender and recipient
    $mail->setFrom('aymericketogo@gmail.com', 'Your Name');
    $mail->addAddress('aymerickngassa@icloud.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email Without Composer';
    $mail->Body    = '<h1>Hello!</h1>This email is sent using PHPMailer without Composer.';
    $mail->AltBody = 'Hello! This email is sent using PHPMailer without Composer.';

    $mail->send();
    echo 'Email sent successfully.';
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
?>
