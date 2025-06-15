<?php
// pharmacies.php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
$stmt = $pdo->query("SELECT nom, adresse, telephone, latitude, longitude FROM pharmacies");
$pharmacies = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($pharmacies);
?>
