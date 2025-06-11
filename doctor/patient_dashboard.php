<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

// Récupérer les rendez-vous du patient
$stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.status, d.first_name, d.last_name, d.specialty 
                        FROM appointments a 
                        JOIN doctors d ON a.doctor_id = d.id 
                        WHERE a.patient_id = ?");
$stmt->execute([$_SESSION['patient_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Patient</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['patient_name']); ?></h1>
    </header>
    <nav>
        <ul>
            <li><a href="book_appointment.php">Prendre un rendez-vous</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Vos rendez-vous</h2>
            <?php if (empty($appointments)): ?>
                <p>Aucun rendez-vous prévu.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f1f5f9;">
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Médecin</th>
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Date</th>
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Statut</th>
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
                                </td>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php echo htmlspecialchars($appointment['appointment_date']); ?>
                                </td>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php echo htmlspecialchars($appointment['status']); ?>
                                </td>
                                <td style="padding: 0.75rem; border: 1px solid #e2e8f0;">
                                    <?php if ($appointment['status'] == 'confirmed'): ?>
                                        <a href="invoice.php?appointment_id=<?php echo $appointment['id']; ?>" class="button">Voir la facture</a>
                                    <?php endif; ?>
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