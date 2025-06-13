<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_groupe = $_POST['id_groupe'];
    try {
        $stmt = $conn->prepare("INSERT INTO membres_groupe (id_groupe, id_patient) VALUES (?, ?)");
        $stmt->execute([$id_groupe, $_SESSION['patient_id']]);
        $message = "<p style='color: green; text-align: center;'>Inscription réussie au groupe !</p>";
    } catch (PDOException $e) {
        $message = "<p style='color: red; text-align: center;'>Erreur : " . $e->getMessage() . "</p>";
    }
}

$stmt = $conn->prepare("SELECT g.id_groupe, g.nom_groupe, g.type, g.description 
                        FROM groupes g 
                        LEFT JOIN membres_groupe m ON g.id_groupe = m.id_groupe AND m.id_patient = ? 
                        WHERE m.id_groupe IS NULL");
$stmt->execute([$_SESSION['patient_id']]);
$groupes_available = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT g.id_groupe, g.nom_groupe, g.type, g.description 
                        FROM groupes g 
                        JOIN membres_groupe m ON g.id_groupe = m.id_groupe 
                        WHERE m.id_patient = ?");
$stmt->execute([$_SESSION['patient_id']]);
$groupes_joined = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Groupes de santé</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Groupes de Santé</h1>
    </header>
    <nav>
        <ul>
            <li><a href="patient_dashboard.php">Tableau de bord</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section>
            <h2>Rejoindre un groupe</h2>
            <?php echo $message; ?>
            <form method="POST" action="support_groups.php">
                <label for="id_groupe">Groupe :</label>
                <select id="id_groupe" name="id_groupe" required>
                    <option value="">Sélectionnez un groupe</option>
                    <?php foreach ($groupes_available as $group): ?>
                        <option value="<?php echo $group['id_groupe']; ?>">
                            <?php echo htmlspecialchars($group['nom_groupe'] . ' - ' . $group['type']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">S'inscrire</button>
            </form>
        </section>
        <section>
            <h2>Vos groupes</h2>
            <?php if (empty($groupes_joined)): ?>
                <p>Aucun groupe rejoint.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Nom du groupe</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                    <?php foreach ($groupes_joined as $group): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($group['nom_groupe']); ?></td>
                            <td><?php echo htmlspecialchars($group['type']); ?></td>
                            <td><?php echo htmlspecialchars($group['description']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>