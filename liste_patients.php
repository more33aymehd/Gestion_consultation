<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$res = $conn->query("SELECT id_patient, nom, maladie, statut, photo FROM patients");
$patients = [];

while ($row = $res->fetch_assoc()) {
    $patients[] = $row;
}

header("Content-Type: application/json");
echo json_encode($patients);
?>