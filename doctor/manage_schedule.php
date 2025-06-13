<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['medecin_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Récupérer tous les hôpitaux disponibles
try {
    $stmt = $conn->prepare("SELECT id_hopital, nom FROM hopitaux ORDER BY nom");
    $stmt->execute();
    $hopitaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur hôpitaux : " . htmlspecialchars($e->getMessage());
}

// Récupérer les horaires du médecin
try {
    $stmt = $conn->prepare("SELECT h.id_horaire, h.jour, h.heure_debut, h.heure_fin, hop.nom AS hopital 
                            FROM horaires h 
                            JOIN hopitaux hop ON h.id_hopital = hop.id_hopital 
                            WHERE h.id_medecin = ? ORDER BY h.jour, h.heure_debut");
    $stmt->execute([$_SESSION['medecin_id']]);
    $horaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur horaires : " . htmlspecialchars($e->getMessage());
}

// Ajouter un horaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $jour = $_POST['jour'];
    $heure_debut = $_POST['heure_debut'];
    $heure_fin = $_POST['heure_fin'];
    $id_hopital = $_POST['id_hopital'];

    try {
        $stmt = $conn->prepare("INSERT INTO horaires (id_medecin, id_hopital, jour, heure_debut, heure_fin) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['medecin_id'], $id_hopital, $jour, $heure_debut, $heure_fin]);
        header("Location: manage_schedule.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur ajout : " . htmlspecialchars($e->getMessage());
    }
}

// Supprimer un horaire
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id_horaire'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM horaires WHERE id_horaire = ? AND id_medecin = ?");
        $stmt->execute([$_POST['id_horaire'], $_SESSION['medecin_id']]);
        header("Location: manage_schedule.php");
        exit;
    } catch (PDOException $e) {
        $error = "Erreur suppression : " . htmlspecialchars($e->getMessage());
    }
}
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
            <li><a href="doctor_dashboard.php">Tableau de bord</a></li>
            <li><a href="accept_reject_appointment.php">Gérer les rendez-vous</a></li>
            <li><a href="edit_doctor_profile.php">Modifier le profil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Vos horaires</h2>
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php elseif (empty($horaires)): ?>
                <p>Aucun horaire défini.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Jour</th>
                        <th>Heure début</th>
                        <th>Heure fin</th>
                        <th>Hôpital</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($horaires as $horaire): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($horaire['jour']); ?></td>
                            <td><?php echo htmlspecialchars($horaire['heure_debut']); ?></td>
                            <td><?php echo htmlspecialchars($horaire['heure_fin']); ?></td>
                            <td><?php echo htmlspecialchars($horaire['hopital']); ?></td>
                            <td>
                                <form method="POST" action="manage_schedule.php" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_horaire" value="<?php echo $horaire['id_horaire']; ?>">
                                    <button type="submit" onclick="return confirm('Supprimer cet horaire ?')">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <h3>Ajouter un horaire</h3>
            <?php if (empty($hopitaux)): ?>
                <p>Aucun hôpital disponible. <a href="mailto:admin@hospitalapp.com">Contacter l’administrateur</a> pour ajouter des hôpitaux.</p>
            <?php else: ?>
                <form method="POST" action="manage_schedule.php">
                    <input type="hidden" name="action" value="add">
                    <label for="jour">Jour :</label>
                    <input type="date" id="jour" name="jour" required>
                    <label for="heure_debut">Heure début :</label>
                    <input type="time" id="heure_debut" name="heure_debut" required>
                    <label for="heure_fin">Heure fin :</label>
                    <input type="time" id="heure_fin" name="heure_fin" required>
                    <label for="id_hopital">Hôpital :</label>
                    <select id="id_hopital" name="id_hopital" required>
                        <?php foreach ($hopitaux as $hopital): ?>
                            <option value="<?php echo $hopital['id_hopital']; ?>"><?php echo htmlspecialchars($hopital['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Ajouter</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>