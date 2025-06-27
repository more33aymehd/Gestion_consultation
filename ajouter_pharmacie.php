<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$nom = $_POST['nom'];
$adresse = $_POST['adresse'];
$telephone = $_POST['telephone'];

$sql = "INSERT INTO pharmacies (nom, adresse, telephone) VALUES ('$nom', '$adresse', '$telephone')";
echo $conn->query($sql) ? "success" : "Erreur : " . $conn->error;
?>