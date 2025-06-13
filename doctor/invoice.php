<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id']) || !isset($_GET['id_consultation'])) {
    header("Location: patient_login.php");
    exit;
}

$id_consultation = $_GET['id_consultation'];

try {
    $stmt = $conn->prepare("SELECT c.date, c.contenu, c.prix, m.nom AS medecin_nom, m.specialite, p.nom AS patient_nom 
                            FROM consultations c 
                            JOIN medecins m ON c.id_medecin = m.id_medecin 
                            JOIN patients p ON c.id_patient = p.id_patient 
                            WHERE c.id_consultation = ? AND c.id_patient = ?");
    $stmt->execute([$id_consultation, $_SESSION['patient_id']]);
    $consultation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$consultation) {
        die("Erreur : Consultation non trouvée.");
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Facture</h1>
    </header>
    <nav>
        <ul>
            <li><a href="patient_dashboard.php">Tableau de bord</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <div class="invoice">
            <div class="invoice-header">
                <h2>Facture de Consultation</h2>
                <p>Plateforme de Gestion Santé, Yaoundé, Cameroun</p>
            </div>
            <div class="invoice-details">
                <p><strong>Patient :</strong> <?php echo htmlspecialchars($consultation['patient_nom']); ?></p>
                <p><strong>Médecin :</strong> <?php echo htmlspecialchars($consultation['medecin_nom']); ?></p>
                <p><strong>Spécialité :</strong> <?php echo htmlspecialchars($consultation['specialite']); ?></p>
                <p><strong>Date :</strong> <?php echo htmlspecialchars($consultation['date']); ?></p>
                <p><strong>Motif :</strong> <?php echo htmlspecialchars($consultation['contenu']); ?></p>
            </div>
            <table>
                <tr>
                    <th>Description</th>
                    <th>Montant</th>
                </tr>
                <tr>
                    <td>Consultation médicale</td>
                    <td><?php echo htmlspecialchars($consultation['prix']); ?> CFA</td>
                </tr>
            </table>
            <p class="total">Total : <?php echo htmlspecialchars($consultation['prix']); ?> CFA</p>
        </div>
    </main>
</body>
</html>