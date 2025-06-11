<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION["user_id"] = $user["id"];
        header("Location: chat.php");
        exit;
    } else {
        echo "Identifiants incorrects.";
    }
}
?>

<h2>Connexion</h2>
<form method="post">
  <input type="text" name="username" placeholder="Nom d'utilisateur" required><br>
  <input type="password" name="password" placeholder="Mot de passe" required><br>
  <button type="submit">Connexion</button>
</form>
