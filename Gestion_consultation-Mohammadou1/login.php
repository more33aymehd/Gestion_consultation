<?php 
session_start();   

$conn = mysqli_connect("localhost", "root", "", "gestion_sante");
if (!$conn) {
    die("Connection échouée");
}

$msg = '';    

if (isset($_POST['submit'])) {    
    $email = $_POST['email'];    
    $password = $_POST['password'];    

    // Vérifier dans admins
    $query = "SELECT * FROM admins WHERE nom = ? AND mot_de_passe = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $admin = mysqli_fetch_assoc($res);
        $_SESSION['admin'] = $admin['nom'];
        $_SESSION['id'] = $admin['id_admin'];
        header('location:admin.php');
        exit();
    }

    // Vérifier dans medecins
    $query = "SELECT * FROM medecins WHERE nom = ? AND mot_de_passe = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $med = mysqli_fetch_assoc($res);
        $_SESSION['medecin'] = $med['nom'];
        $_SESSION['id'] = $med['id_medecin'];
        header('location:docteur.php');
        exit();
    }

    // Vérifier dans patients
    $query = "SELECT * FROM patients WHERE nom = ? AND mot_de_passe = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($res) > 0) {
        $pat = mysqli_fetch_assoc($res);
        $_SESSION['nom_patient'] = $pat['nom'];
        $_SESSION['id_patient'] = $pat['id_patient'];
        header('location:recherche_medecins.php');
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
                <input type="text" name="email" placeholder="Entrer votre Nom" class="form-control" required>    
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