<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

if (!isset($_GET['id_medecin'])) {
    header("Location: book_appointment.php");
    exit;
}

try {
    $id_medecin = $_GET['id_medecin'];
    $stmt = $conn->prepare("SELECT nom, specialite FROM medecins WHERE id_medecin = ?");
    $stmt->execute([$id_medecin]);
    $medecin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medecin) {
        $error = "Médecin non trouvé.";
    }
} catch (PDOException $e) {
    $error = "Erreur : " . htmlspecialchars($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'], $_POST['motif'])) {
    $date = $_POST['date'];
    $motif = $_POST['motif'];

    try {
        $stmt = $conn->prepare("INSERT INTO consultations (id_patient, id_medecin, date, contenu) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['patient_id'], $id_medecin, $date, $motif]);
        $_SESSION['new_consultation'] = true;
        header("Location: patient_dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de la prise de rendez-vous : " . htmlspecialchars($e->getMessage());
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
            <li><a href="book_appointment.php">Choisir un médecin</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Rendez-vous avec <?php echo isset($medecin['nom']) ? htmlspecialchars($medecin['nom']) : ''; ?></h2>
            <?php if (isset($error)): ?>
                <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (isset($medecin)): ?>
                <p><strong>Médecin :</strong> <?php echo htmlspecialchars($medecin['nom'] . ' (' . $medecin['specialite'] . ')'); ?></p>
                <form method="POST" action="book_appointment_form.php?id_medecin=<?php echo htmlspecialchars($id_medecin); ?>">
                    <label for="date">Date :</label>
                    <input type="datetime-local" id="date" name="date" required>
                    <label for="motif">Motif :</label>
                    <textarea id="motif" name="motif" required></textarea>
                    <button type="submit">Confirmer le rendez-vous</button>
                </form>
            <?php else: ?>
                <p>Erreur : Médecin non disponible.</p>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>