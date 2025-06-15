<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$sql = "
    SELECT c.id_consultation, c.contenu, c.prix, c.date,
           m.nom AS nom_medecin,
           p.nom AS nom_patient
    FROM consultations c
    JOIN medecins m ON c.id_medecin = m.id_medecin
    JOIN patients p ON c.id_patient = p.id_patient
    ORDER BY c.date DESC
";

$res = $conn->query($sql);
$consultations = [];

while ($row = $res->fetch_assoc()) {
    $consultations[] = $row;
}

header("Content-Type: application/json");
echo json_encode($consultations);
?>