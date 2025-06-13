<?php
session_start();
require_once 'config.php';

unset($_SESSION['patient_id']);
unset($_SESSION['patient_name']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $mot_de_passe = $_POST['mot_de_passe'];

    try {
        $stmt = $conn->prepare("SELECT id_medecin, nom, mot_de_passe FROM medecins WHERE email = ?");
        $stmt->execute([$email]);
        $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($medecin && $mot_de_passe === $medecin['mot_de_passe']) {
            $_SESSION['medecin_id'] = $medecin['id_medecin'];
            $_SESSION['medecin_name'] = $medecin['nom'];
            header("Location: doctor_dashboard.php");
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $error = "Erreur : " . htmlspecialchars($e->getMessage());
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
            <li><a href="patient_login.php">Connexion Patient</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="doctor_login.php">
                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" required>
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <button type="submit">Se connecter</button>
            </form>
        </section>
    </main>
</body>
</html>