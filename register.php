<?php  
include("connexion.php");  

$msg = '';  
if(isset($_POST['submit'])){  
    $nom = $_POST['nom'];  
    $email = $_POST['email'];  
    $password = $_POST['password'];  
    $cpassword = $_POST['cpassword'];  

    if ($password !== $cpassword) {
        $msg = "Les mots de passe ne correspondent pas.";
    } else {
        $check = "SELECT * FROM patients WHERE email = '$email'";
        $res = mysqli_query($conn, $check);
        if(mysqli_num_rows($res) > 0){  
            $msg = "Un utilisateur avec cet email existe déjà.";  
        } else {
            $sql = "INSERT INTO patients(nom, email, mot_de_passe, sexe, statut) 
                    VALUES('$nom', '$email', '$password', 'M', 'actif')";
            if (mysqli_query($conn, $sql)) {
                header('location:login.php');
                exit();
            } else {
                $msg = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>  
<!DOCTYPE html>  
<html lang="fr">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Inscription Patient</title>  
    <link rel="stylesheet" href="style_1.css">  
</head>  
<body>  
    <div class="form">  
        <form action="" method="post">
            <h1><span style="color: rgb(0, 212, 71)">+</span> Allo Doc</h1><br>  
            <h2 style="color: rgb(0, 140, 255)">Inscription</h2>  
            <p class="msg"><?= $msg ?></p>  
            <div class="form-group">  
                <input type="text" name="nom" placeholder="Entrer votre nom" class="form-control" required>  
            </div>  
            <div class="form-group">  
                <input type="email" name="email" placeholder="Entrer votre e-mail" class="form-control" required>  
            </div>  
            <div class="form-group">  
                <input type="password" name="password" placeholder="Entrer votre mot de passe" class="form-control" required>  
            </div>  
            <div class="form-group">  
                <input type="password" name="cpassword" placeholder="Confirmer votre mot de passe" class="form-control" required>  
            </div>  
            <button class="btn" name="submit">Je m'inscris</button>  
            <p>Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>  
        </form>  
    </div>  
</body>  
</html>