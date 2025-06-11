<?php
session_start();
require_once 'config.php';
require_once 'send_email.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

$recommended_hospitals = [];
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $city = $_POST['city'];
    $service = $_POST['service'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];

    try {
        // Enregistrer le rendez-vous
        $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, status) VALUES (?, ?, ?, 'pending')");
        $stmt->execute([$_SESSION['patient_id'], $doctor_id, $appointment_date]);
        $appointment_id = $conn->lastInsertId();

        // Rechercher les hôpitaux recommandés
        $stmt = $conn->prepare("SELECT * FROM hospitals WHERE city = ? AND services LIKE ?");
        $stmt->execute([$city, "%$service%"]);
        $recommended_hospitals = $stmt->fetchAll();

        // Envoyer un e-mail de confirmation
        $stmt = $conn->prepare("SELECT email, first_name FROM patients WHERE id = ?");
        $stmt->execute([$_SESSION['patient_id']]);
        $patient = $stmt->fetch();
        $subject = "Confirmation de votre rendez-vous";
        $body = "<h2>Bonjour {$patient['first_name']},</h2><p>Votre rendez-vous est en attente de confirmation pour le {$appointment_date}. Vous serez notifié une fois confirmé.</p>";
        $email_result = sendEmail($patient['email'], $subject, $body);
        
        $message = "<p style='color: green; text-align: center;'>Rendez-vous réservé avec succès ! En attente de confirmation du médecin.";
        if ($email_result !== true) {
            $message .= "<br>Erreur lors de l'envoi de l'e-mail : $email_result</p>";
        } else {
            $message .= "<br>Un e-mail de confirmation a été envoyé à {$patient['email']}.</p>";
        }
    } catch (PDOException $e) {
        $message = "<p style='color: red; text-align: center;'>Erreur lors de la réservation : " . $e->getMessage() . "</p>";
    }
}

// Récupérer les médecins disponibles
$doctors = $conn->query("SELECT id, first_name, last_name, specialty FROM doctors")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prendre un Rendez-vous</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Prendre un Rendez-vous</h1>
    </header>
    <nav>
        <ul>
            <li><a href="patient_dashboard.php">Dashboard</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Réserver une consultation</h2>
            <?php echo $message; ?>
            <form method="POST" action="book_appointment.php">
                <label for="city">Ville :</label>
                <input type="text" id="city" name="city" required>
                <label for="service">Service requis :</label>
                <select id="service" name="service" required>
                    <option value="Consultation générale">Consultation générale</option>
                    <option value="Paludisme">Paludisme</option>
                    <option value="VIH">VIH</option>
                    <option value="Suivi des addictions">Suivi des addictions</option>
                    <option value="Pédiatrie">Pédiatrie</option>
                    <option value="Cardiologie">Cardiologie</option>
                </select>
                <label for="doctor_id">Médecin :</label>
                <select id="doctor_id" name="doctor_id" required>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo $doctor['id']; ?>">
                            <?php echo htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name'] . ' - ' . $doctor['specialty']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="appointment_date">Date et heure :</label>
                <input type="datetime-local" id="appointment_date" name="appointment_date" required>
                <button type="submit">Réserver</button>
            </form>
            <?php if (!empty($recommended_hospitals)): ?>
                <h3>Hôpitaux recommandés :</h3>
                <div class="card-container">
                    <?php foreach ($recommended_hospitals as $hospital): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($hospital['name']); ?></h3>
                            <p><?php echo htmlspecialchars($hospital['address']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>