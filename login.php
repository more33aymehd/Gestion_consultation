<?php    
include("connexion.php");    
$msg = '';    

if (isset($_POST['submit'])) {    
    $email = $_POST['email'];    
    $password = $_POST['password'];    

    // Vérifier dans admins
    $query = "SELECT * FROM admins WHERE email = '$email' AND mot_de_passe = '$password'";
    $res = mysqli_query($conn, $query);
    if (mysqli_num_rows($res) > 0) {
        $admin = mysqli_fetch_assoc($res);
        $_SESSION['admin'] = $admin['nom'];
        $_SESSION['id'] = $admin['id_admin'];
        header('location:admin.php');
        exit();
    }

    // Vérifier dans medecins
    $query = "SELECT * FROM medecins WHERE email = '$email' AND mot_de_passe = '$password'";
    $res = mysqli_query($conn, $query);
    if (mysqli_num_rows($res) > 0) {
        $med = mysqli_fetch_assoc($res);
        $_SESSION['medecin'] = $med['nom'];
        $_SESSION['id'] = $med['id_medecin'];
        header('location:medecin.php');
        exit();
    }

    // Vérifier dans patients
    $query = "SELECT * FROM patients WHERE email = '$email' AND mot_de_passe = '$password'";
    $res = mysqli_query($conn, $query);
    if (mysqli_num_rows($res) > 0) {
        $pat = mysqli_fetch_assoc($res);
        $_SESSION['patient'] = $pat['nom'];
        $_SESSION['id'] = $pat['id_patient'];
        header('location:patient.php');
        exit();
    }

    $msg = "Email ou mot de passe incorrect.";
}
?>

<!DOCTYPE html>    
<html lang="fr">    
<head>    
    <meta charset="UTF-8">    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <title>Connexion</title>    
    <link rel="stylesheet" href="style_1.css">    
</head>    
<body>    
    <div class="form">  
        <form action="" method="post">
            <h1><span style="color: rgb(0, 212, 71)">+</span> Allo Doc</h1><br>   
            <h2><span style="color: rgb(0, 140, 255)">Connexion</span></h2>    
            <p class="msg"><?= $msg ?></p>    
            <div class="form-group">    
                <input type="email" name="email" placeholder="Entrer votre E-mail" class="form-control" required>    
            </div>    
            <div class="form-group">    
                <input type="password" name="password" placeholder="Entrer votre mot de passe" class="form-control" required>    
            </div>    
            <button class="btn" name="submit">Connexion</button>    
            <p>Vous n'avez pas de compte ? <a href="register.php">S'inscrire</a></p>    
        </form>    
    </div>    
</body>    
</html>