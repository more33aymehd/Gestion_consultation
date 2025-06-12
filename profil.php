<?php
include("connexion.php");
session_start();

if (!isset($_SESSION['admin']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];
$req = mysqli_query($conn, "SELECT * FROM admins WHERE id_admin = $id");
$admin = mysqli_fetch_assoc($req);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil Admin - Allo Doc</title>
    <link rel="stylesheet" href="style_1.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f5f5f5;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: linear-gradient(to bottom, #e63946, #b71c1c);
            color: white;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profil-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: #fff;
            background-image: url('images/admin.png'); /* image par défaut */
            background-size: cover;
            background-position: center;
            margin-bottom: 20px;
        }

        .profil-name {
            font-size: 18px;
            font-weight: bold;
            margin-top: 5px;
            text-align: center;
        }

        .label {
            margin-top: 10px;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
            opacity: 0.7;
        }

        .main-content {
            margin-left: 250px;
            padding: 40px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="profil-img" style="background-image: url('images/<?= $admin['photo'] ?? 'admin.png' ?>');"></div>
    <div class="profil-name"><?= ucfirst($admin['nom']) ?></div>
    <div class="label">admin</div>
</div>

<div class="main-content">
    <h2>Bienvenue <?= ucfirst($admin['nom']) ?> 👋</h2>
    <p>Ceci est votre espace personnel. Vous pouvez gérer les médecins, patients, hôpitaux, etc.</p>
</div>

</body>
</html>