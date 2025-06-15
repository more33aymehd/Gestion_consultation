<?php
require_once 'config.php';

// Librairies nécessaires
require_once __DIR__ . '/tcpdf/tcpdf.php';
require_once __DIR__ . '/phpmailer/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/phpmailer/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$id_consultation = isset($_GET['id_consultation']) ? (int)$_GET['id_consultation'] : 0;

try {
    // Vérifie que l'ID est valide
    if (!$id_consultation) {
        throw new Exception('ID consultation invalide.');
    }

    // Récupération des données de la consultation
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
        throw new Exception('Consultation introuvable.');
    }
    

   $stmt = $pdo->prepare("
    SELECT m.nom AS nom_medicament, m.prix, ph.nom AS pharmacie,
           om.posologie, om.duree, om.quantite
    FROM ordonnance_medicaments om
    JOIN medicaments m ON om.id_medicament = m.id_medicament
    JOIN pharmacies ph ON m.id_pharmacy = ph.id_pharmacy
    WHERE om.id_ordonnance = (
        SELECT id_ordonnance FROM ordonnances 
        WHERE id_patient = ? AND date_ord = ?
        ORDER BY id_ordonnance DESC LIMIT 1
    )
");
$stmt->execute([$consultation['id_patient'], $consultation['date']]);
$medicaments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$consultation['medicaments'] = $medicaments;



    // Génère le PDF
    $pdf_path = generate_prescription_pdf($consultation);

    if (!file_exists($pdf_path)) {
        throw new Exception("Fichier PDF introuvable à $pdf_path");
    }

    // Vérifie l'email du patient
    if (empty($consultation['patient_email']) || !filter_var($consultation['patient_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email du patient invalide : " . $consultation['patient_email']);
    }

    // Envoie de l'email
    $sent = send_prescription_email($consultation['patient_email'], $pdf_path);

    if ($sent) {
        echo json_encode(['success' => true, 'message' => 'Email envoyé à ' . $consultation['patient_email']]);
    } else {
        throw new Exception('Échec de l\'envoi de l\'email.');
    }

} catch (Exception $e) {
    error_log('[Erreur] ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}


// ---------- Fonction : Génération PDF ----------
function generate_prescription_pdf($consultation) {
    $dir = __DIR__ . '/prescriptions';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $html = '<h1>Ordonnance Médicale</h1>';
    $html .= '<p><strong>Patient :</strong> ' . htmlspecialchars($consultation['patient_nom']) . '</p>';
    $html .= '<p><strong>Médecin :</strong> ' . htmlspecialchars($consultation['medecin_nom']) . '</p>';
    $html .= '<p><strong>Date :</strong> ' . htmlspecialchars($consultation['date']) . '</p>';
    $html .= '<p><strong>Notes :</strong><br>' . nl2br(htmlspecialchars($consultation['contenu'])) . '</p>';

    $html .= '<h3>Médicaments prescrits</h3>';

if (!empty($consultation['medicaments'])) {
    $html .= '<table border="1" cellpadding="5">
        <tr>
            <th>Nom</th><th>Pharmacie</th><th>Prix</th>
            <th>Posologie</th><th>Durée (j)</th><th>Quantité</th>
        </tr>';
    foreach ($consultation['medicaments'] as $medic) {
        $html .= '<tr>
            <td>' . htmlspecialchars($medic['nom_medicament']) . '</td>
            <td>' . htmlspecialchars($medic['pharmacie']) . '</td>
            <td>' . number_format($medic['prix'], 2) . '€</td>
            <td>' . htmlspecialchars($medic['posologie']) . '</td>
            <td>' . (int)$medic['duree'] . '</td>
            <td>' . (int)$medic['quantite'] . '</td>
        </tr>';
    }
    $html .= '</table>';
} else {
    $html .= '<p><em>Aucun médicament prescrit.</em></p>';
}


    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf_path = __DIR__ . '/prescriptions/prescription_' . $consultation['id_consultation'] . '.pdf';
    $pdf->Output($pdf_path, 'F');

    return $pdf_path;
}

// ---------- Fonction : Envoi Email ----------
function send_prescription_email($to_email, $pdf_path) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'houtoumtaiboukarbertrand@gmail.com'; // Remplace par ton email
        $mail->Password = 'pywydapbymzdcmsa'; // Mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('houtoumtaiboukarbertrand@gmail.com', 'Docteur');
        $mail->addAddress($to_email);

        $mail->Subject = 'Votre ordonnance médicale';
        $mail->Body = "Bonjour,\n\nVeuillez trouver ci-joint votre ordonnance médicale au format PDF.\n\nCordialement,\nLe Médecin";
        $mail->addAttachment($pdf_path);

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
        return false;
    }
}
