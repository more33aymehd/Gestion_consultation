<?php
// pharmacies_proches.php

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['latitude']) || !isset($data['longitude'])) {
    echo json_encode([]);
    exit;
}

$userLat = floatval($data['latitude']);
$userLon = floatval($data['longitude']);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Rayon de recherche en km
    $radius = 5;

    // Requête pour trouver les pharmacies dans un rayon avec la formule Haversine
    $sql = "SELECT id_pharmacy, nom, adresse, telephone, latitude, longitude,
    (6371 * acos(
        cos(radians(:userLat)) * cos(radians(latitude)) *
        cos(radians(longitude) - radians(:userLon)) +
        sin(radians(:userLat)) * sin(radians(latitude))
    )) AS distance
    FROM pharmacies
    HAVING distance <= :radius
    ORDER BY distance ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':userLat', $userLat);
    $stmt->bindValue(':userLon', $userLon);
    $stmt->bindValue(':radius', $radius);
    $stmt->execute();

    $pharmacies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pharmacies);

} catch (PDOException $e) {
    echo json_encode([]);
}
