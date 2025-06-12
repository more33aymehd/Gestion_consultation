<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$id = $_POST['id'];
$sql = "DELETE FROM pharmacies WHERE id_pharmacy=$id";
echo $conn->query($sql) ? "success" : "Erreur : " . $conn->error;
?>