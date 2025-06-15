<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['medecin'])) {
    header('Location: login.php');
    exit();
}

$id_medecin = $_SESSION['id'] ?? null;

$search = $_GET['search'] ?? '';

// Récupérer tous les patients liés via consultations
if ($search) {
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.* 
        FROM patients p
        JOIN consultations c ON c.id_patient = p.id_patient
        WHERE c.id_medecin = ? AND (p.nom LIKE ? OR p.email LIKE ?)
        ORDER BY p.nom
    ");
    $stmt->execute([$id_medecin, "%$search%", "%$search%"]);
} else {
    $stmt = $pdo->prepare("
        SELECT DISTINCT p.*
        FROM patients p
        JOIN consultations c ON c.id_patient = p.id_patient
        WHERE c.id_medecin = ?
        ORDER BY p.nom
    ");
    $stmt->execute([$id_medecin]);
}

$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Patients | GestionSanté</title>
    <link rel="stylesheet" href="docteur.css">
</head>
<body>
    <div class="main-content" style="margin-left: 280px; padding: 2rem;">
        <h1>Liste des Patients</h1>
        <form method="get" class="search-bar" style="margin-bottom: 1.5rem;">
            <input type="text" name="search" placeholder="Rechercher par nom ou email..." value="<?= htmlspecialchars($search) ?>" />
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>

        <?php if (empty($patients)): ?>
            <p>Aucun patient trouvé.</p>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom</th>
                        <th>Âge</th>
                        <th>Sexe</th>
                        <th>Adresse</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Maladie</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($patients as $patient): ?>
                        <tr onclick="window.location='patient_detail.php?id=<?= $patient['id_patient'] ?>'">
                            <td>
                                <img src="<?= $patient['photo'] ? 'images/' . $patient['photo'] : 'assets/default-patient.png' ?>" class="patient-avatar-sm" />
                            </td>
                            <td><?= htmlspecialchars($patient['nom']) ?></td>
                            <td><?= htmlspecialchars($patient['age']) ?></td>
                            <td><?= htmlspecialchars($patient['sexe']) ?></td>
                            <td><?= htmlspecialchars($patient['adresse']) ?></td>
                            <td><?= htmlspecialchars($patient['email']) ?></td>
                            <td><?= htmlspecialchars($patient['telephone']) ?></td>
                            <td><?= htmlspecialchars($patient['maladie']) ?></td>
                            <td><?= htmlspecialchars($patient['statut']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
