<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['medecin_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Récupérer les consultations
try {
    $stmt = $conn->prepare("SELECT c.id_consultation, c.date, c.contenu, c.prix, c.statut, p.nom AS patient_nom 
                            FROM consultations c 
                            JOIN patients p ON c.id_patient = p.id_patient 
                            WHERE c.id_medecin = ?");
    $stmt->execute([$_SESSION['medecin_id']]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur consultations : " . htmlspecialchars($e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Médecin</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue, Dr. <?php echo htmlspecialchars($_SESSION['medecin_name']); ?></h1>
    </header>
    <nav>
        <ul>
            <li><a href="accept_reject_appointment.php">Gérer les rendez-vous</a></li>
            <li><a href="edit_doctor_profile.php">Modifier le profil</a></li>
            <li><a href="chat.php">Parler à Dr. Nancy</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Vos consultations</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if (empty($consultations)): ?>
                <p>Aucune consultation trouvée.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Motif</th>
                        <th>Prix</th>
                        <th>Statut</th>
                    </tr>
                    <?php foreach ($consultations as $consultation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consultation['date']); ?></td>
                            <td><?php echo htmlspecialchars($consultation['patient_nom']); ?></td>
                            <td><?php echo htmlspecialchars($consultation['contenu']); ?></td>
                            <td><?php echo htmlspecialchars($consultation['prix']); ?> CFA</td>
                            <td><?php echo htmlspecialchars($consultation['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>