<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom']);
    $age = (int)$_POST['age'];
    $sexe = $_POST['sexe'];
    $adresse = trim($_POST['adresse']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST['telephone']);
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("SELECT id_patient FROM patients WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Cet e-mail est déjà enregistré.";
        } else {
            $stmt = $conn->prepare("INSERT INTO patients (nom, age, sexe, adresse, email, telephone, mot_de_passe) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $age, $sexe, $adresse, $email, $telephone, $mot_de_passe]);
            $success = "Inscription réussie ! Connectez-vous.";
        }
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Patient</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Inscription Patient</h1>
    </header>
    <nav>
        <ul>
            <li><a href="index.php">Accueil</a></li>
            <li><a href="patient_login.php">Connexion</a></li>
            <li><a href="doctor_login.php">Connexion Médecin</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Inscription</h2>
            <?php if (isset($error)): ?>
                <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <p style="color: green; text-align: center;"><?php echo htmlspecialchars($success); ?></p>
            <?php endif; ?>
            <form method="POST" action="register.php">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
                <label for="age">Âge :</label>
                <input type="number" id="age" name="age" min="0" required>
                <label for="sexe">Sexe :</label>
                <select id="sexe" name="sexe" required>
                    <option value="M">Masculin</option>
                    <option value="F">Féminin</option>
                </select>
                <label for="adresse">Adresse :</label>
                <input type="text" id="adresse" name="adresse" required>
                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" required>
                <label for="telephone">Téléphone :</label>
                <input type="text" id="telephone" name="telephone" required>
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
                <button type="submit">S'inscrire</button>
            </form>
        </section>
    </main>
</body>
</html>