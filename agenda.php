<?php
require_once 'config.php';

// ID médecin fixé manuellement (peut être changé via l'URL)
$id_medecin = isset($_GET['id_medecin']) ? (int)$_GET['id_medecin'] : 2;

// Récupération des données du médecin
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
if (!$stmt->execute([$id_medecin])) {
    die("Erreur lors de la récupération des données du médecin.");
}
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    die("Médecin introuvable, vérifiez l'ID.");
}

// Date actuelle et gestion des dates via URL
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Récupération des rendez-vous pour la date sélectionnée
$stmt = $pdo->prepare("
    SELECT r.*, p.nom, p.telephone, p.photo 
    FROM rendez_vous r
    JOIN patients p ON r.id_patient = p.id_patient
    WHERE r.id_medecin = ? 
    AND r.date_rdv = ?
    ORDER BY r.heure_rdv
");

if (!$stmt->execute([$id_medecin, $date])) {
    die("Erreur lors de la récupération des rendez-vous.");
}
$rdvs = $stmt->fetchAll();

// Statistiques
$stmt_stats_today = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE id_medecin = ? AND date_rdv = ?");
if (!$stmt_stats_today->execute([$id_medecin, date('Y-m-d')])) {
    die("Erreur lors de la récupération des statistiques pour aujourd'hui.");
}
$rdv_aujourdhui = $stmt_stats_today->fetchColumn();

$stmt_stats_week = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE id_medecin = ? AND YEARWEEK(date_rdv, 1) = YEARWEEK(NOW(), 1)");
if (!$stmt_stats_week->execute([$id_medecin])) {
    die("Erreur lors de la récupération des statistiques pour cette semaine.");
}
$rdv_semaine = $stmt_stats_week->fetchColumn();

// Liste des médecins pour le sélecteur
$medecins = $pdo->query("SELECT id_medecin, nom, specialite FROM medecins ORDER BY nom")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda | Dr. <?= htmlspecialchars($medecin['nom']) ?> | GestionSanté</title>
    <link rel="stylesheet" href="agenda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="main-content">
        <header>
            <h1><i class="fas fa-calendar-alt"></i> Agenda du Dr. <?= htmlspecialchars($medecin['nom']) ?></h1>
            <div class="agenda-actions">
                <div class="date-navigation">
                    <button class="btn-nav" id="prev-date"><i class="fas fa-chevron-left"></i></button>
                    <h2 id="current-date"><?= date('l d F Y', strtotime($date)) ?></h2>
                    <button class="btn-nav" id="next-date"><i class="fas fa-chevron-right"></i></button>
                </div>
                <button class="btn-primary" id="add-rdv"><i class="fas fa-plus"></i> Nouveau RDV</button>
            </div>
        </header>

        <!-- Agenda -->
        <div class="agenda-content">
            <div class="time-column">
                <?php for ($h = 8; $h < 20; $h++): ?>
                    <div class="time-slot"><?= sprintf("%02d:00", $h) ?></div>
                    <div class="time-slot-half"><?= sprintf("%02d:30", $h) ?></div>
                <?php endfor; ?>
            </div>

            <div class="agenda-grid">
                <?php foreach ($rdvs as $rdv): 
                    $heure = (int)substr($rdv['heure_rdv'], 0, 2);
                    $minutes = (int)substr($rdv['heure_rdv'], 3, 2);
                ?>
                    <div class="rdv-item" style="top: <?= ($heure - 8) * 60 + $minutes ?>px;">
                        <div class="rdv-header">
                            <img src="<?= $rdv['photo'] ? 'uploads/'.$rdv['photo'] : 'assets/default-patient.png' ?>" class="patient-avatar">
                            <div class="rdv-patient">
                                <strong><?= htmlspecialchars($rdv['nom']) ?></strong>
                                <span><?= htmlspecialchars($rdv['motif']) ?></span>
                            </div>
                            <div class="rdv-time"><?= substr($rdv['heure_rdv'], 0, 5) ?></div>
                        </div>
                        <div class="rdv-actions">
                            <button class="btn-icon btn-start" title="Commencer"><i class="fas fa-play"></i></button>
                            <button class="btn-icon btn-call" title="Appeler"><i class="fas fa-phone"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Panel de détails -->
            <div class="agenda-sidebar">
                <div class="sidebar-header">
                    <h3>Détails du jour</h3>
                    <span class="badge"><?= count($rdvs) ?> RDV</span>
                </div>
                <div class="sidebar-stats">
                    <div class="stat-item">
                        <i class="fas fa-calendar-day"></i>
                        <div>
                            <span class="stat-value"><?= $rdv_aujourdhui ?></span>
                            <span class="stat-label">Aujourd'hui</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <i class="fas fa-calendar-week"></i>
                        <div>
                            <span class="stat-value"><?= $rdv_semaine ?></span>
                            <span class="stat-label">Cette semaine</span>
                        </div>
                    </div>
                </div>
                <div class="rdv-list">
                    <?php if (empty($rdvs)): ?>
                        <p class="no-rdv">Aucun rendez-vous programmé</p>
                    <?php else: ?>
                        <?php foreach ($rdvs as $rdv): ?>
                            <div class="rdv-card">
                                <div class="rdv-card-header">
                                    <span class="rdv-time"><?= substr($rdv['heure_rdv'], 0, 5) ?></span>
                                    <span class="rdv-status <?= $rdv['statut'] ?? 'prevu' ?>"><?= $rdv['statut'] ?? 'Prévu' ?></span>
                                </div>
                                <div class="rdv-card-body">
                                    <img src="<?= $rdv['photo'] ? 'uploads/'.$rdv['photo'] : 'assets/default-patient.png' ?>" class="patient-avatar">
                                    <div class="rdv-info">
                                        <h4><?= htmlspecialchars($rdv['nom']) ?></h4>
                                        <p><i class="fas fa-phone"></i> <?= htmlspecialchars($rdv['telephone']) ?></p>
                                        <p><i class="fas fa-comment-medical"></i> <?= htmlspecialchars($rdv['motif']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter un RDV -->
    <div class="modal" id="rdv-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nouveau rendez-vous</h3>
                <button class="btn-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="rdv-form">
                    <input type="hidden" id="rdv-id_medecin" value="<?= $id_medecin ?>">
                    <div class="form-group">
                        <label for="rdv-date">Date</label>
                        <input type="text" id="rdv-date" class="form-control" value="<?= $date ?>">
                    </div>
                    <div class="form-group">
                        <label for="rdv-heure">Heure</label>
                        <input type="text" id="rdv-heure" class="form-control" placeholder="HH:MM">
                    </div>
                    <div class="form-group">
                        <label for="rdv-patient">Patient</label>
                        <select id="rdv-patient" class="form-control">
                            <option value="">Sélectionner un patient</option>
                            <?php 
                            $patients = $pdo->query("SELECT id_patient, nom FROM patients ORDER BY nom")->fetchAll();
                            foreach ($patients as $patient): ?>
                                <option value="<?= $patient['id_patient'] ?>"><?= htmlspecialchars($patient['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="rdv-motif">Motif</label>
                        <input type="text" id="rdv-motif" class="form-control" placeholder="Motif de la consultation">
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script>
        // Initialisation des datepickers
        flatpickr("#rdv-date", {
            dateFormat: "Y-m-d",
            locale: "fr",
            defaultDate: "<?= $date ?>"
        });
        
        flatpickr("#rdv-heure", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            minuteIncrement: 15
        });

        // Changement de médecin
        function changeMedecin(id) {
            window.location.href = `agenda.php?id_medecin=${id}&date=<?= $date ?>`;
        }

        // Navigation par date
        document.getElementById('prev-date').addEventListener('click', () => {
            const prevDate = new Date("<?= $date ?>");
            prevDate.setDate(prevDate.getDate() - 1);
            window.location.href = `agenda.php?id_medecin=<?= $id_medecin ?>&date=${prevDate.toISOString().split('T')[0]}`;
        });
        
        document.getElementById('next-date').addEventListener('click', () => {
            const nextDate = new Date("<?= $date ?>");
            nextDate.setDate(nextDate.getDate() + 1);
            window.location.href = `agenda.php?id_medecin=<?= $id_medecin ?>&date=${nextDate.toISOString().split('T')[0]}`;
        });

        // Gestion du modal
        const modal = document.getElementById('rdv-modal');
        const openModal = () => modal.style.display = 'flex';
        const closeModal = () => modal.style.display = 'none';
        
        document.getElementById('add-rdv').addEventListener('click', openModal);
        document.querySelector('.btn-close').addEventListener('click', closeModal);
        document.querySelector('.btn-cancel').addEventListener('click', closeModal);

        // Soumission du formulaire (simplifié)
        document.getElementById('rdv-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Fonctionnalité à implémenter : enregistrement en base de données');
            closeModal();
        });
    </script>
</body>
</html>