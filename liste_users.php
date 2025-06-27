<?php
include("connexion.php");

$medecins = $conn->query("SELECT id_medecin, nom FROM medecins");
$patients = $conn->query("SELECT id_patient, nom FROM patients");

$data = [
    "medecins" => [],
    "patients" => []
];

while ($m = $medecins->fetch_assoc()) $data["medecins"][] = $m;
while ($p = $patients->fetch_assoc()) $data["patients"][] = $p;

header("Content-Type: application/json");
echo json_encode($data);
?>