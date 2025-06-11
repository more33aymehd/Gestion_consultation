<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
        $stmt = $conn->prepare("INSERT INTO patients (first_name, last_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $password, $phone, $address]);
        echo "<p>Inscription réussie ! <a href='patient_login.php'>Connectez-vous</a></p>";
    } catch (PDOException $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Inscription Patient</h1>
    </header>
    <main>
        <form method="POST" action="register.php">
            <label>Prénom :</label>
            <input type="text" name="first_name" required><br>
            <label>Nom :</label>
            <input type="text" name="last_name" required><br>
            <label>Email :</label>
            <input type="email" name="email" required><br>
            <label>Mot de passe :</label>
            <input type="password" name="password" required><br>
            <label>Téléphone :</label>
            <input type="text" name="phone"><br>
            <label>Adresse :</label>
            <textarea name="address"></textarea><br>
            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="patient_login.php">Connexion</a></p>
    </main>
</body>
</html>