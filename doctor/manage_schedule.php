<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    try {
        $stmt = $conn->prepare("INSERT INTO schedules (doctor_id, start_time, end_time) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['doctor_id'], $start_time, $end_time]);
        $message = "<p style='color: green; text-align: center;'>Créneau ajouté avec succès !</p>";
    } catch (PDOException $e) {
        $message = "<p style='color: red; text-align: center;'>Erreur : " . $e->getMessage() . "</p>";
    }
}

// Récupérer les créneaux existants
$stmt = $conn->prepare("SELECT * FROM schedules WHERE doctor_id = ?");
$stmt->execute([$_SESSION['doctor_id']]);
$schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer l'emploi du temps</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gérer votre emploi du temps</h1>
    </header>
    <nav>
        <ul>
            <li><a href="doctor_dashboard.php">Dashboard</a></li>
            <li><a href="prescription.php">Envoyer une ordonnance</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Ajouter un créneau horaire</h2>
            <p>Ajoutez une plage horaire où vous êtes disponible pour des consultations.</p>
            <?php echo $message; ?>
            <form method="POST" action="manage_schedule.php">
                <label for="start_time">Début :</label>
                <input type="datetime-local" id="start_time" name="start_time" required>
                <label for="end_time">Fin :</label>
                <input type="datetime-local" id="end_time" name="end_time" required>
                <button type="submit">Ajouter</button>
            </form>
            <h2>Vos créneaux horaires</h2>
            <?php if (empty($schedules)): ?>
                <p>Aucun créneau ajouté.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f1f5f9;">
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Début</th>
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php echo htmlspecialchars($schedule['start_time']); ?>
                                </td>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php echo htmlspecialchars($schedule['end_time']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>