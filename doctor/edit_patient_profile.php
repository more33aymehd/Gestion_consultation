<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

// Récupérer le profil
try {
    $stmt = $conn->prepare("SELECT nom, email, adresse, telephone FROM patients WHERE id_patient = ?");
    $stmt->execute([$_SESSION['patient_id']]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur profil : " . htmlspecialchars($e->getMessage());
}

// Mettre à jour le profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    try {
        $stmt = $conn->prepare("UPDATE patients SET nom = ?, adresse = ?, telephone = ? WHERE id_patient = ?");
        $stmt->execute([$nom, $adresse, $telephone, $_SESSION['patient_id']]);
        $_SESSION['patient_name'] = $nom;
        header("Location: edit_patient_profile.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur mise à jour : " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Modifier votre profil</h1>
    </header>
    <nav>
        <ul>
            <li><a href="patient_dashboard.php">Tableau de bord</a></li>
            <li><a href="book_appointment.php">Prendre un rendez-vous</a></li>
            <li><a href="support_groups.php">Groupes de soutien</a></li>
            <li><a href="chat.php">Parler à Dr. Nancy</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($patient): ?>
                <h2>Votre profil</h2>
                <form method="POST" action="edit_patient_profile.php">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($patient['nom']); ?>" required>
                    <label for="email">Email :</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($patient['email']); ?>" disabled>
                    <label for="adresse">Adresse :</label>
                    <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($patient['adresse']); ?>">
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" value="<?php echo htmlspecialchars($patient['telephone']); ?>">
                    <button type="submit">Mettre à jour</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>