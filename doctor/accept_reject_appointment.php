<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['medecin_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Récupérer les rendez-vous en attente
try {
    $stmt = $conn->prepare("SELECT c.id_consultation, c.date, c.contenu, p.nom AS patient_nom 
                            FROM consultations c 
                            JOIN patients p ON c.id_patient = p.id_patient 
                            WHERE c.id_medecin = ? AND c.statut = 'en_attente' 
                            ORDER BY c.date");
    $stmt->execute([$_SESSION['medecin_id']]);
    $rendez_vous = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des rendez-vous : " . htmlspecialchars($e->getMessage());
}

// Traiter l'acceptation ou le rejet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'], $_POST['id_consultation'])) {
    $id_consultation = $_POST['id_consultation'];
    $action = $_POST['action'];

    try {
        $statut = ($action == 'accept') ? 'accepte' : 'rejete';
        $stmt = $conn->prepare("UPDATE consultations SET statut = ? WHERE id_consultation = ? AND id_medecin = ?");
        $stmt->execute([$statut, $id_consultation, $_SESSION['medecin_id']]);
        if ($action == 'accept') {
            header("Location: consultation.php?id_consultation=$id_consultation");
        } else {
            header("Location: accept_reject_appointment.php");
        }
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour : " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Gérer les rendez-vous</h1>
    </header>
    <nav>
        <ul>
            <li><a href="doctor_dashboard.php">Tableau de bord</a></li>
            <li><a href="manage_schedule.php">Gérer l'emploi du temps</a></li>
            <li><a href="edit_doctor_profile.php">Modifier le profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Rendez-vous en attente</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif (empty($rendez_vous)): ?>
                <p>Aucun rendez-vous en attente.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Motif</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($rendez_vous as $rdv): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rdv['date']); ?></td>
                            <td><?php echo htmlspecialchars($rdv['patient_nom']); ?></td>
                            <td><?php echo htmlspecialchars($rdv['contenu']); ?></td>
                            <td>
                                <form method="POST" action="accept_reject_appointment.php" style="display:inline;">
                                    <input type="hidden" name="id_consultation" value="<?php echo $rdv['id_consultation']; ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit">Accepter</button>
                                </form>
                                <form method="POST" action="accept_reject_appointment.php" style="display:inline;">
                                    <input type="hidden" name="id_consultation" value="<?php echo $rdv['id_consultation']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit">Rejeter</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>