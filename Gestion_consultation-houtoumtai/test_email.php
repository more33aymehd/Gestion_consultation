<?php
require_once 'phpmailer/PHPMailer/src/PHPMailer.php';
require_once 'phpmailer/PHPMailer/src/SMTP.php';
require_once 'phpmailer/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'houtoumtaiboukarbertrand@gmail.com';
    $mail->Password = 'pywydapbymzdcmsa';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('houtoumtaiboukarbertrand@gmail.com', 'Docteur');
    $mail->addAddress('houtoumtaiboukarbertrand@gmail.com');

    $mail->Subject = 'Test simple';
    $mail->Body    = 'Ceci est un test simple depuis PHP/PHPMailer';
    
    $mail->send();
    echo 'Email envoyé !';
} catch (Exception $e) {
    echo "Erreur lors de l'envoi : {$mail->ErrorInfo}";
}
