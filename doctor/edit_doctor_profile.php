<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['medecin_id'])) {
    header("Location: doctor_login.php");
    exit;
}

// Récupérer le profil du médecin
try {
    $stmt = $conn->prepare("SELECT nom, specialite, description, experience, photo FROM medecins WHERE id_medecin = ?");
    $stmt->execute([$_SESSION['medecin_id']]);
    $medecin = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération du profil : " . htmlspecialchars($e->getMessage());
}

// Mettre à jour le profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $experience = $_POST['experience'];
    $photo = $medecin['photo'];

    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['photo']['name']);
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo = $target_file;
        } else {
            $error = "Erreur lors du téléchargement de la photo.";
        }
    }

    try {
        $stmt = $conn->prepare("UPDATE medecins SET description = ?, experience = ?, photo = ? WHERE id_medecin = ?");
        $stmt->execute([$description, $experience, $photo, $_SESSION['medecin_id']]);
        header("Location: edit_doctor_profile.php");
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
    <title>Modifier le profil</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .profile-photo {
            margin-bottom: 1em;
        }
        .profile-form {
            width: 100%;
            max-width: 600px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Modifier votre profil</h1>
    </header>
    <nav>
        <ul>
            <li><a href="doctor_dashboard.php">Tableau de bord</a></li>
            <li><a href="manage_schedule.php">Gérer l'emploi du temps</a></li>
            <li><a href="accept_reject_appointment.php">Gérer les rendez-vous</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <section class="profile-container">
            <?php if (isset($error)): ?>
                <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <?php if ($medecin): ?>
                <h2>Votre profil</h2>
                <div class="profile-photo">
                    <?php if ($medecin['photo']): ?>
                        <img src="<?php echo htmlspecialchars($medecin['photo']); ?>" alt="Photo de profil" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%;">
                    <?php else: ?>
                        <p>Aucune photo disponible.</p>
                    <?php endif; ?>
                </div>
                <form method="POST" action="edit_doctor_profile.php" enctype="multipart/form-data" class="profile-form">
                    <label for="photo">Changer la photo :</label>
                    <input type="file" id="photo" name="photo" accept="image/*">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" value="<?php echo htmlspecialchars($medecin['nom']); ?>" disabled>
                    <label for="specialite">Spécialité :</label>
                    <input type="text" id="specialite" value="<?php echo htmlspecialchars($medecin['specialite']); ?>" disabled>
                    <label for="description">Description :</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($medecin['description']); ?></textarea>
                    <label for="experience">Expérience :</label>
                    <input type="text" id="experience" name="experience" value="<?php echo htmlspecialchars($medecin['experience']); ?>">
                    <button type="submit">Mettre à jour</button>
                </form>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>