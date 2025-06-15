<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_medecin = (int)$_POST['id_medecin'];
    $id_patient = (int)$_POST['id_patient'];
    $date_rdv = $_POST['date_rdv'];
    $heure_rdv = $_POST['heure_rdv'];
    $motif = trim($_POST['motif']);

    // Insertion dans la table rendez_vous
    $stmt = $pdo->prepare("
        INSERT INTO rendez_vous (id_medecin, id_patient, date_rdv, heure_rdv, motif, statut)
        VALUES (?, ?, ?, ?, ?, 'prévu')
    ");

    if ($stmt->execute([$id_medecin, $id_patient, $date_rdv, $heure_rdv, $motif])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement']);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
}
