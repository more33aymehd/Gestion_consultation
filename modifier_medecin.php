<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) {
    die("Erreur : " . $conn->connect_error);
}

$id = $_POST['id_medecin'];
$nom = $_POST['nom'];
$email = $_POST['email'];
$adresse = $_POST['adresse'];
$telephone = $_POST['telephone'];
$specialite = $_POST['specialite'];
$affiliation = $_POST['affiliation'];
$photo = $_POST['photo'];
$mot_de_passe = $_POST['mot_de_passe'];
$tarif = $_POST['tarif'];

$sql = "UPDATE medecins SET 
        nom='$nom', email='$email', adresse='$adresse',
        telephone='$telephone', specialite='$specialite',
        affiliation='$affiliation', photo='$photo',
        mot_de_passe='$mot_de_passe', tarif='$tarif'
        WHERE id_medecin=$id";

if ($conn->query($sql) === TRUE) {
    echo "success";
} else {
    echo "Erreur : " . $conn->error;
}
?>