<?php
// Include required PHPMailer files
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Create instance of PHPMailer
$mail = new PHPMailer(true);
require 'fpdf/fpdf.php';

// Génère le PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Commande de Medicament', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Medicament : Paracétamol', 0, 1);
$pdf->Cell(0, 10, 'Quantitée : 2', 0, 1);

// Sauvegarde temporaire
$pdfFile = 'commande.pdf';
$pdf->Output('F', $pdfFile);
try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aymericketogo@gmail.com';
    $mail->Password   = 'anue ijtd wsux izng';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;
    $mail->addAttachment($pdfFile);


    // Sender and recipient
    $mail->setFrom('aymericketogo@gmail.com', 'Your Name');
    $mail->addAddress('aymericketogo@gmail.com');

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
