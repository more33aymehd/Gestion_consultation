<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT id_patient, first_name, last_name, password FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && password_verify($password, $patient['password'])) {
            $_SESSION['patient_id'] = $patient['id_patient'];
            $_SESSION['patient_name'] = $patient['first_name'] . ' ' . $patient['last_name'];
            header("Location: patient_dashboard.php");
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
    <title>Connexion Patient</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Connexion Patient</h1>
    </header>
    <main>
        <section>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="patient_login.php">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
            <p>Pas de compte ? <a href="register.php">S'inscrire</a></p>
        </section>
    </main>
</body>
</html>