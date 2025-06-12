<?php
include("connexion.php");

$expediteur = "admin"; // côté admin
$message = $_POST['message'];
$date = date("Y-m-d H:i:s");

$id_patient = null;
$id_medecin = null;

$dest = $_POST['destinataire'];
if (strpos($dest, 'P') === 0) {
    $id_patient = substr($dest, 1);
} elseif (strpos($dest, 'M') === 0) {
    $id_medecin = substr($dest, 1);
}

$sql = "INSERT INTO messages (id_medecin, id_patient, expediteur, message, date_envoi)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $id_medecin, $id_patient, $expediteur, $message, $date);
$stmt->execute();

echo "success";
?>