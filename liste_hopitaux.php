<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$res = $conn->query("SELECT * FROM hopitaux");
$hopitaux = [];

while ($row = $res->fetch_assoc()) {
    $hopitaux[] = $row;
}

header("Content-Type: application/json");
echo json_encode($hopitaux);
?>