<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$id = $_POST['id_hopital'];
$nom = $_POST['nom'];
$adresse = $_POST['adresse'];
$telephone = $_POST['telephone'];

$sql = "UPDATE hopitaux SET nom='$nom', adresse='$adresse', telephone='$telephone' WHERE id_hopital=$id";
echo $conn->query($sql) ? "success" : "Erreur : " . $conn->error;
?>