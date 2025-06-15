<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$res = $conn->query("SELECT * FROM pharmacies");
$pharmacies = [];

while ($row = $res->fetch_assoc()) {
    $pharmacies[] = $row;
}

header("Content-Type: application/json");
echo json_encode($pharmacies);
?>