<?php
require 'db.php';

header('Content-Type: application/json');

$nom = trim($_GET['nom'] ?? '');

if ($nom !== '') {
    $stmt = $pdo->prepare("
        SELECT m.nom AS nom_medicament, m.quantite, m.prix, 
               p.nom AS nom_pharmacie, p.adresse, p.telephone, p.latitude, p.longitude
        FROM medicaments m
        JOIN pharmacies p ON m.id_pharmacy = p.id_pharmacy
        WHERE m.nom LIKE ? AND m.quantite > 0
    ");
    $stmt->execute(['%' . $nom . '%']);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($resultats);
}
?>
