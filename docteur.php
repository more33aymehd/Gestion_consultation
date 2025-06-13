<?php
require_once 'config.php';

// ID médecin fixé manuellement (à adapter)
$id_medecin = 2;

// Récupération des données du médecin
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
$stmt->execute([$id_medecin]);
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    die("Médecin introuvable, vérifie l'ID.");
}

$today = date('Y-m-d');

// Statistiques pour le dashboard
$stats = [];
$stmt = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE id_medecin = ?");
$stmt->execute([$id_medecin]);
$stats['consultations_total'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE id_medecin = ? AND date >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute([$id_medecin]);
$stats['consultations_7j'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(DISTINCT id_patient) FROM consultations WHERE id_medecin = ?");
$stmt->execute([$id_medecin]);
$stats['patients_actifs'] = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE id_medecin = ? AND expediteur = 'patient'");
$stmt->execute([$id_medecin]);
$stats['messages_non_lus'] = $stmt->fetchColumn();

// Récupérer rendez-vous du jour - attention ta base ne semble pas avoir cette table (à adapter)
$rdvs = []; // vide car pas de table rendez_vous dans tes tables

// Dernières consultations avec détails
$stmt = $pdo->prepare("
    SELECT c.*, p.nom, p.photo 
    FROM consultations c 
    LEFT JOIN patients p ON c.id_patient = p.id_patient 
    WHERE c.id_medecin = ?
    ORDER BY c.date DESC 
    LIMIT 5
");
$stmt->execute([$id_medecin]);
$consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Groupes de suivi
$stmt = $pdo->prepare("SELECT * FROM groupes WHERE id_medecin = ? ORDER BY nom_groupe");
$stmt->execute([$id_medecin]);
$groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Messages récents
$stmt = $pdo->prepare("
    SELECT m.*, p.nom as patient_nom 
    FROM messages m
    LEFT JOIN patients p ON m.id_patient = p.id_patient
    WHERE m.id_medecin = ?
    ORDER BY m.date_envoi DESC 
    LIMIT 3
");
$stmt->execute([$id_medecin]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Dr. <?= htmlspecialchars($medecin['nom']) ?> | GestionSanté</title>
    <link rel="stylesheet" href="docteur.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="profile">
                <img src="<?= $medecin['photo'] ? 'images/' . $medecin['photo'] : 'assets/default-avatar.png' ?>" alt="Photo profil" />
                <h3>Dr. <?= htmlspecialchars($medecin['nom']) ?></h3>
                <p><?= htmlspecialchars($medecin['specialite']) ?></p>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="docteur.php"><i class="fas fa-home"></i> Tableau de bord</a></li>
                    <li><a href="agenda.php"><i class="fas fa-calendar-check"></i> Agenda</a></li>
                    <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                    <li><a href="consultations.php"><i class="fas fa-notes-medical"></i> Consultations</a></li>
                    <li><a href="ordonnance.php"><i class="fas fa-prescription"></i> Ordonnances</a></li>
                    <li><a href="groupes.php"><i class="fas fa-object-group"></i> Groupes</a></li>
                    <li><a href="messagerie.php"><i class="fas fa-comments"></i> Messagerie <span class="badge"><?= $stats['messages_non_lus'] ?></span></a></li>
                    <li><a href="stats.php"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
                    <li><a href="parametres.php"><i class="fas fa-cog"></i> Paramètres</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <header>
                <h1>Tableau de Bord</h1>
                <div class="search-bar">
                    <input type="text" id="search-patient" placeholder="Rechercher un patient..." />
                    <button><i class="fas fa-search"></i></button>
                </div>
                <div class="header-actions">
                    <button class="btn-notif"><i class="fas fa-bell"></i> <span class="notif-badge"><?= $stats['messages_non_lus'] ?></span></button>
                    <button class="btn-help"><i class="fas fa-question-circle"></i></button>
                </div>
            </header>

            <!-- Widgets, consultations, messages etc... ici (copie la partie de ton code d'origine) -->

            <div class="bottom-section">
                <div class="widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-history"></i> Dernières consultations</h3>
                        <a href="consultations.php" class="view-all">Voir tout</a>
                    </div>
                    <div class="widget-content">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Patient</th>
                                    <th>Date</th>
                                    <th>Prix</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($consultations as $consult): ?>
                                <tr onclick="window.location='consultation_detail.php?id=<?= $consult['id_consultation'] ?>'">
                                    <td>
                                        <img src="<?= $consult['photo'] ? 'images/' . $consult['photo'] : 'assets/default-patient.png' ?>" class="patient-avatar-sm" />
                                        <?= htmlspecialchars($consult['nom']) ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($consult['date'])) ?></td>
                                    <td><?= number_format($consult['prix'], 2) ?> XAF</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="widget">
                    <div class="widget-header">
                        <h3><i class="fas fa-comments"></i> Messages récents</h3>
                        <a href="messagerie.php" class="view-all">Voir tout</a>
                    </div>
                    <div class="widget-content">
                        <?php if (empty($messages)): ?>
                            <p class="no-data">Aucun message récent</p>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                            <div class="message" onclick="window.location='messagerie.php#msg-<?= $msg['id_message'] ?>'">
                                <img src="assets/<?= $msg['expediteur'] === 'patient' ? 'default-patient.png' : 'default-avatar.png' ?>" class="message-avatar" />
                                <div class="message-content">
                                    <div class="message-header">
                                        <strong><?= $msg['expediteur'] === 'patient' ? htmlspecialchars($msg['patient_nom']) : 'Vous' ?></strong>
                                        <span><?= date('H:i', strtotime($msg['date_envoi'])) ?></span>
                                    </div>
                                    <p><?= substr(htmlspecialchars($msg['message']), 0, 50) ?>...</p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
    document.getElementById('search-patient').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            window.location.href = 'patients.php?search=' + encodeURIComponent(this.value);
        }
    });
</script>
</body>
</html>
