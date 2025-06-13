<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    // Validation des données
    $required_fields = ['id_medecin', 'id_patient', 'date', 'contenu'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Le champ $field est requis");
        }
    }

    // Commencer une transaction
    $pdo->beginTransaction();

    // Enregistrer la consultation
    $stmt = $pdo->prepare("
        INSERT INTO consultations (id_medecin, id_patient, date, contenu, prix)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['id_medecin'],
        $_POST['id_patient'],
        $_POST['date'],
        $_POST['contenu'],
        $_POST['prix'] ?? null
    ]);
    $consultation_id = $pdo->lastInsertId();
    

    // Enregistrement dans la table ordonnances
$stmt = $pdo->prepare("
    INSERT INTO ordonnances (id_medecin, id_patient, date_ord, notes, statut)
    VALUES (?, ?, ?, ?, 'active')
");
$stmt->execute([
    $_POST['id_medecin'],
    $_POST['id_patient'],
    $_POST['date'],
    $_POST['contenu']
]);
$id_ordonnance = $pdo->lastInsertId();

// Insertion des médicaments dans ordonnance_medicaments
foreach ($_POST['medicaments'] as $medicament) {
    $stmt = $pdo->prepare("
        INSERT INTO ordonnance_medicaments (id_ordonnance, id_medicament, posologie, duree, quantite)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $id_ordonnance,
        $medicament['id_medicament'],
        $medicament['posologie'],
        $medicament['duree'],
        $medicament['duree'] * 1 // quantite approximative (ou calculé selon la posologie si besoin)
    ]);
}


    // Envoyer l'email si demandé
    $email_sent = false;
    if (!empty($_POST['send_email'])) {
        // Récupérer l'email du patient
        $stmt = $pdo->prepare("SELECT email FROM patients WHERE id_patient = ?");
        $stmt->execute([$_POST['id_patient']]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && !empty($patient['email'])) {
            // Générer et envoyer le PDF (à implémenter)
            $email_sent = true; // Mettre à false si l'envoi échoue
        }
    }

    // Valider la transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'email_sent' => $email_sent,
        'message' => 'Ordonnance enregistrée avec succès'
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}