<?php
require_once 'config.php';

header('Content-Type: application/json');

$id_consultation = isset($_GET['id_consultation']) ? (int)$_GET['id_consultation'] : 0;

try {
    // Récupérer les données de la consultation
    $stmt = $pdo->prepare("
        SELECT c.*, p.nom as patient_nom, p.email as patient_email, m.nom as medecin_nom
        FROM consultations c
        JOIN patients p ON c.id_patient = p.id_patient
        JOIN medecins m ON c.id_medecin = m.id_medecin
        WHERE c.id_consultation = ?
    ");
    $stmt->execute([$id_consultation]);
    $consultation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$consultation) {
        throw new Exception('Consultation introuvable');
    }

    // Générer le PDF (vous devez implémenter cette partie)
    $pdf_path = generate_prescription_pdf($consultation);

    // Envoyer l'email (vous devez implémenter cette partie)
    $email_sent = send_prescription_email($consultation['patient_email'], $pdf_path);

    if ($email_sent) {
        echo json_encode(['success' => true, 'message' => 'Email envoyé à ' . $consultation['patient_email']]);
    } else {
        throw new Exception('Échec de l\'envoi de l\'email');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

// Fonctions à implémenter selon vos besoins
function generate_prescription_pdf($consultation) {
    // Utiliser TCPDF, Dompdf ou autre librairie PDF
    // Retourner le chemin du fichier généré
    return 'path/to/generated_pdf.pdf';
}

function send_prescription_email($to_email, $pdf_path) {
    // Utiliser PHPMailer ou la fonction mail() de PHP
    // Retourner true si l'email a été envoyé avec succès
    return true;
}