<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        $patient = $stmt->fetch();

        if ($patient && password_verify($password, $patient['password'])) {
            $_SESSION['patient_id'] = $patient['id'];
            $_SESSION['patient_name'] = $patient['first_name'] . ' ' . $patient['last_name'];
            header("Location: patient_dashboard.php");
            exit;
        } else {
            echo "<p>Email ou mot de passe incorrect.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Patient</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Connexion Patient</h1>
    </header>
    <main>
        <form method="POST" action="patient_login.php">
            <label>Email :</label>
            <input type="email" name="email" required><br>
            <label>Mot de passe :</label>
            <input type="password" name="password" required><br>
            <button type="submit">Se connecter</button>
        </form>
        <p>Pas de compte ? <a href="register.php">S'inscrire</a></p>
    </main>
</body>
</html>