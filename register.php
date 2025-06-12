<?php
include("connexion.php");
$msg = "";

if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $age = $_POST['age'];
    $sexe = $_POST['sexe'];
    $adresse = $_POST['adresse'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $maladie = $_POST['maladie'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $statut = "actif"; // statut par défaut

    // Photo
    $photo = $_FILES['photo']['name'];
    $tmp_photo = $_FILES['photo']['tmp_name'];
    $destination = "images/" . $photo;

    // Vérifie si utilisateur existe déjà
    $check = mysqli_query($conn, "SELECT * FROM patients WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Ce patient existe déjà !";
    } elseif ($password != $cpassword) {
        $msg = "Les mots de passe ne correspondent pas !";
    } else {
        // Enregistrement de la photo
        move_uploaded_file($tmp_photo, $destination);

        $insert = "INSERT INTO patients (nom, age, sexe, adresse, email, telephone, maladie, statut, photo, mot_de_passe)
                   VALUES ('$nom', '$age', '$sexe', '$adresse', '$email', '$telephone', '$maladie', '$statut', '$photo', '$password')";

        if (mysqli_query($conn, $insert)) {
            header("Location: login.php");
            exit();
        } else {
            $msg = "Erreur lors de l'inscription : " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Patient</title>
    <link rel="stylesheet" href="style_1.css">
</head>
<body>
    <div class="form">
        <form action="" method="post" enctype="multipart/form-data">
            <h1><span style="color: rgb(0, 212, 71)">+</span> Allo Doc</h1><br>
            <h2>Inscription</h2>
            <p class="msg"><?= $msg ?></p>

            <div class="form-group">
                <input type="text" name="nom" placeholder="Nom complet" required>
            </div>

            <div class="form-group">
                <input type="number" name="age" placeholder="Âge" required>
            </div>

            <div class="form-group">
                Sexe :
                <select name="sexe" required>
                    <option value="M">Homme</option>
                    <option value="F">Femme</option>
                </select>
            </div>

            <div class="form-group">
                <input type="text" name="adresse" placeholder="Adresse" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="E-mail" required>
            </div>

            <div class="form-group">
                <input type="text" name="telephone" placeholder="Téléphone" required>
            </div>

            <div class="form-group">
                <input type="text" name="maladie" placeholder="Maladie actuelle (ex : Hypertension)" required>
            </div>

            <div class="form-group">
                <input type="file" name="photo" accept="image/*" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>

            <div class="form-group">
                <input type="password" name="cpassword" placeholder="Confirmer mot de passe" required>
            </div>

            <button class="btn" name="submit">Je m'inscris</button>
            <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
        </form>
    </div>
</body>
</html>