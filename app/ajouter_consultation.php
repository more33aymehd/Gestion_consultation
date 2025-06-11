<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['id_patient'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");

$id_patient = $_SESSION['id_patient'];
$id_medecin = intval($_POST['id_medecin'] ?? 0);
$contenu = trim($_POST['contenu'] ?? '');

if ($id_medecin <= 0 || $contenu === '') {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

// Récupérer le tarif du médecin
$stmt = $pdo->prepare("SELECT tarif FROM medecins WHERE id_medecin = ?");
$stmt->execute([$id_medecin]);
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    echo json_encode(['success' => false, 'message' => 'Médecin introuvable']);
    exit;
}

$prix = $medecin['tarif'];

// Insérer la consultation
$stmt = $pdo->prepare("INSERT INTO consultations (id_patient, id_medecin, contenu, prix) VALUES (?, ?, ?, ?)");
$success = $stmt->execute([$id_patient, $id_medecin, $contenu, $prix]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
}
