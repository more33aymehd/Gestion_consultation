<?php
include("connexion.php");

$sql = "SELECT m.*, 
       COALESCE(p.nom, med.nom) AS destinataire
       FROM messages m
       LEFT JOIN patients p ON m.id_patient = p.id_patient
       LEFT JOIN medecins med ON m.id_medecin = med.id_medecin
       ORDER BY date_envoi DESC";

$res = $conn->query($sql);
$messages = [];

while ($row = $res->fetch_assoc()) {
    $messages[] = $row;
}

header("Content-Type: application/json");
echo json_encode($messages);
?>