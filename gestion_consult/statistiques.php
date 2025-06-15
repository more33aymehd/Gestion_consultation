<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$data = [
    "medecins" => 0,
    "patients" => 0,
    "hopitaux" => 0,
    "consultations" => 0
];

$data['medecins'] = $conn->query("SELECT COUNT(*) FROM medecins")->fetch_row()[0];
$data['patients'] = $conn->query("SELECT COUNT(*) FROM patients")->fetch_row()[0];
$data['hopitaux'] = $conn->query("SELECT COUNT(*) FROM hopitaux")->fetch_row()[0];
$data['consultations'] = $conn->query("SELECT COUNT(*) FROM consultations")->fetch_row()[0];

header('Content-Type: application/json');
echo json_encode($data);
?>