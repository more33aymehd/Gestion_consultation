<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';
require 'fpdf/fpdf.php';

// Récupérer les données POST
$nom_medicament = $_POST['medicament'] ?? '';

$quantite = $_POST['quantite'] ?? 1;
$nom_pharmacie = $_POST['pharmacie'] ?? '';
$email_pharmacie = $_POST['email'] ?? '';
echo $email_pharmacie;
$nom_patient = $_SESSION['nom_patient'] ?? 'Un patient';
$telephone_patient = $_POST['telephone_patient'] ?? '';

// 1. Générer le PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Bon de commande de médicament', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, "Pharmacie : $nom_pharmacie", 0, 1);
$pdf->Cell(0, 10, "Médicament : $nom_medicament", 0, 1);
$pdf->Cell(0, 10, "Quantité : $quantite", 0, 1);
$pdf->Cell(0, 10, "Patient : $nom_patient", 0, 1);
$pdf->Cell(0, 10, "Téléphone : $telephone_patient", 0, 1);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Date : ' . date('d/m/Y H:i'), 0, 1);

// Enregistrer temporairement
$filepath = __DIR__ . "/commande_" . time() . ".pdf";
$pdf->Output('F', $filepath);

// 2. Envoyer l'email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // serveur SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'aymericketogo@gmail.com'; 
    $mail->Password   = 'anue ijtd wsux izng'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    $mail->setFrom('aymericketogo@gmail.com', 'Commande Médicaments');
    $mail->addAddress($email_pharmacie, $nom_pharmacie);

    // PJ
    $mail->addAttachment($filepath, 'Commande.pdf');

    $mail->isHTML(true);
    $mail->Subject = "Nouvelle commande de medicament";
    $mail->Body    = "
        Bonjour,<br><br>
        Une nouvelle commande a été effectuée par <strong>$nom_patient</strong> pour le médicament <strong>$nom_medicament</strong> (x$quantite).<br>
        Le bon de commande est en pièce jointe.<br><br>
        Contact du patient : $telephone_patient<br><br>
        Cordialement.";

    $mail->send();
    unlink($filepath); // Supprimer le fichier temporaire
    echo json_encode(['success' => true, 'message' => 'Commande envoyee par email.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => "Erreur d'envoi : {$mail->ErrorInfo}"]);
}
?>
