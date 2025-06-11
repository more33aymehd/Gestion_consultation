<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) {
    die("Erreur : " . $conn->connect_error);
}

$id = $_POST['id'];

$sql = "DELETE FROM medecins WHERE id_medecin = $id";
if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Erreur : " . $conn->error;
}
?>