<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM doctors WHERE email = ?");
        $stmt->execute([$email]);
        $doctor = $stmt->fetch();

        if ($doctor && $password === $doctor['password']) {
            $_SESSION['doctor_id'] = $doctor['id'];
            $_SESSION['doctor_name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
            header("Location: doctor_dashboard.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Erreur de base de données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Médecin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Connexion Médecin</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="register.php">Inscription</a></li>
            <li><a href="patient_login.php">Connexion Patient</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Se connecter</h2>
            <?php if (isset($error)): ?>
                <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="doctor_login.php">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" value="amadou.diop@example.com" required>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" value="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </section>
    </main>
</body>
</html>