<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$sql = "SELECT id_medecin, nom, email, specialite, affiliation FROM medecins";
$result = $conn->query($sql);

$medecins = [];
while ($row = $result->fetch_assoc()) {
    $medecins[] = $row;
}

header('Content-Type: application/json');
echo json_encode($medecins);
?>