<?php
session_start();
require_once 'config.php';
require_once 'send_email.php';

if (!isset($_SESSION['doctor_id'])) {
    header("Location: doctor_login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $medication = $_POST['medication'];
    $instructions = $_POST['instructions'];

    try {
        // Enregistrer l'ordonnance
        $stmt = $conn->prepare("INSERT INTO prescriptions (appointment_id, medication, instructions) VALUES (?, ?, ?)");
        $stmt->execute([$appointment_id, $medication, $instructions]);
        
        // Récupérer l'e-mail du patient
        $stmt = $conn->prepare("SELECT p.email, p.first_name FROM appointments a JOIN patients p ON a.patient_id = p.id WHERE a.id = ?");
        $stmt->execute([$appointment_id]);
        $patient = $stmt->fetch();
        
        if ($patient && !empty($patient['email'])) {
            $subject = "Votre ordonnance";
            $body = "<h2>Bonjour {$patient['first_name']},</h2><p>Votre ordonnance : <br>Médicament : " . htmlspecialchars($medication) . "<br>Instructions : " . htmlspecialchars($instructions) . "</p>";
            $email_result = sendEmail($patient['email'], $subject, $body);
            
            if ($email_result === true) {
                $message = "<p style='color: green; text-align: center;'>Ordonnance envoyée avec succès à {$patient['email']} !</p>";
            } else {
                $message = "<p style='color: red; text-align: center;'>Erreur lors de l'envoi de l'e-mail : $email_result</p>";
            }
        } else {
            $message = "<p style='color: red; text-align: center;'>Erreur : Aucun e-mail valide trouvé pour ce patient.</p>";
        }
    } catch (PDOException $e) {
        $message = "<p style='color: red; text-align: center;'>Erreur : " . $e->getMessage() . "</p>";
    }
}

// Récupérer les rendez-vous confirmés
$stmt = $conn->prepare("SELECT a.id, p.first_name, p.last_name, a.appointment_date 
                        FROM appointments a 
                        JOIN patients p ON a.patient_id = p.id 
                        WHERE a.doctor_id = ? AND a.status = 'confirmed'");
$stmt->execute([$_SESSION['doctor_id']]);
$appointments = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer une ordonnance</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Envoyer une ordonnance</h1>
    </header>
    <nav>
        <ul>
            <li><a href="doctor_dashboard.php">Dashboard</a></li>
            <li><a href="manage_schedule.php">Gérer l'emploi du temps</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Rédiger une ordonnance</h2>
            <?php echo $message; ?>
            <form method="POST" action="prescription.php">
                <label for="appointment_id">Rendez-vous :</label>
                <select id="appointment_id" name="appointment_id" required>
                    <option value="">Sélectionnez un rendez-vous</option>
                    <?php foreach ($appointments as $appointment): ?>
                        <option value="<?php echo $appointment['id']; ?>">
                            <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name'] . ' - ' . $appointment['appointment_date']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="medication">Médicament :</label>
                <input type="text" id="medication" name="medication" required>
                <label for="instructions">Instructions :</label>
                <textarea id="instructions" name="instructions" required></textarea>
                <button type="submit">Envoyer</button>
            </form>
        </section>
    </main>
</body>
</html>