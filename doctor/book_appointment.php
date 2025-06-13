<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

// Récupérer les médecins
try {
    $stmt = $conn->prepare("SELECT id_medecin, nom, specialite FROM medecins");
    $stmt->execute();
    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur médecins : " . htmlspecialchars($e->getMessage());
}

// Prendre un rendez-vous
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_medecin = $_POST['id_medecin'];
    $date = $_POST['date'];
    $motif = $_POST['motif'];

    try {
        $stmt = $conn->prepare("INSERT INTO consultations (id_patient, id_medecin, date, contenu, statut, prix) 
                                VALUES (?, ?, ?, ?, 'en_attente', 5000)");
        $stmt->execute([$_SESSION['patient_id'], $id_medecin, $date, $motif]);
        $_SESSION['new_consultation'] = true;
        header("Location: patient_dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur rendez-vous : " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Prendre un rendez-vous</h1>
    </header>
    <nav>
        <ul>
            <li><a href="patient_dashboard.php">Tableau de bord</a></li>
            <li><a href="edit_patient_profile.php">Voir profil</a></li>
            <li><a href="support_groups.php">Groupes de soutien</a></li>
            <li><a href="chat.php">Parler à Dr. Nancy</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Choisir un médecin</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="book_appointment.php">
                <label for="id_medecin">Médecin :</label>
                <select id="id_medecin" name="id_medecin" required>
                    <?php foreach ($medecins as $medecin): ?>
                        <option value="<?php echo $medecin['id_medecin']; ?>">
                            <?php echo htmlspecialchars($medecin['nom'] . ' (' . $medecin['specialite'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="date">Date et heure :</label>
                <input type="datetime-local" id="date" name="date" required>
                <label for="motif">Motif :</label>
                <textarea id="motif" name="motif" required></textarea>
                <button type="submit">Confirmer</button>
            </form>
        </section>
    </main>
</body>
</html>