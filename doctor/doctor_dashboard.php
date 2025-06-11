<?php
session_start();
require_once 'config.php';
require_once 'send_email.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Gérer l'acceptation/rejet des rendez-vous
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['action'] == 'accept' ? 'confirmed' : 'rejected';
    
    try {
        $stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE id = ? AND doctor_id = ?");
        $stmt->execute([$status, $appointment_id, $_SESSION['doctor_id']]);
        
        // Envoyer un e-mail au patient
        $stmt = $conn->prepare("SELECT p.email, p.first_name, a.appointment_date 
                                FROM appointments a 
                                JOIN patients p ON a.patient_id = p.id 
                                WHERE a.id = ?");
        $stmt->execute([$appointment_id]);
        $patient = $stmt->fetch();
        
        $subject = "Mise à jour de votre rendez-vous";
        $body = "<h2>Bonjour {$patient['first_name']},</h2><p>Votre rendez-vous du {$patient['appointment_date']} a été " . ($status == 'confirmed' ? 'confirmé' : 'rejeté') . ".</p>";
        $email_result = sendEmail($patient['email'], $subject, $body);
    } catch (PDOException $e) {
        echo "<p>Erreur : " . $e->getMessage() . "</p>";
    }
}

// Récupérer les rendez-vous du médecin
$stmt = $conn->prepare("SELECT a.id, a.appointment_date, a.status, p.first_name, p.last_name 
                        FROM appointments a 
                        JOIN patients p ON a.patient_id = p.id 
                        WHERE a.doctor_id = ?");
$stmt->execute([$_SESSION['doctor_id']]);
$appointments = $stmt->fetchAll();
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
        <h1>Bienvenue, Dr. <?php echo htmlspecialchars($_SESSION['doctor_name']); ?></h1>
    </header>
    <nav>
        <ul>
            <li><a href="doctor_dashboard.php">Dashboard</a></li>
            <li><a href="manage_schedule.php">Gérer l'emploi du temps</a></li>
            <li><a href="prescription.php">Envoyer une ordonnance</a></li>
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
                            <th style="padding: 0.75rem; border: 1px solid #e2e8f0;">Patient</th>
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
                                    <?php if ($appointment['status'] == 'pending'): ?>
                                        <form method="POST" action="doctor_dashboard.php" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" name="action" value="accept" class="button">Accepter</button>
                                        </form>
                                        <form method="POST" action="doctor_dashboard.php" style="display: inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" name="action" value="reject" class="button" style="background-color: #dc3545;">Rejeter</button>
                                        </form>
                                    <?php elseif ($appointment['status'] == 'confirmed'): ?>
                                        <a href="prescription.php?appointment_id=<?php echo $appointment['id']; ?>" class="button">Prescrire</a>
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