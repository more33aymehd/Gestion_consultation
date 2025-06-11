<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) {
    die("Erreur : " . $conn->connect_error);
}

$nom = $_POST['nom'];
$email = $_POST['email'];
$adresse = $_POST['adresse'];
$telephone = $_POST['telephone'];
$specialite = $_POST['specialite'];
$affiliation = $_POST['affiliation'];
$photo = $_POST['photo'];
$mot_de_passe = $_POST['mot_de_passe'];
$tarif = $_POST['tarif'];

$sql = "INSERT INTO medecins (nom, email, adresse, telephone, specialite, affiliation, photo, mot_de_passe, tarif)
VALUES ('$nom', '$email', '$adresse', '$telephone', '$specialite', '$affiliation', '$photo', '$mot_de_passe', '$tarif')";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Erreur : " . $conn->error;
}
?>