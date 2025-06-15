<?php
require_once 'config.php';

// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion
if (!isset($_SESSION['medecin'])) {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID du médecin depuis la session
$id_medecin = $_SESSION['id'];

// Récupération des données du médecin
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
$stmt->execute([$id_medecin]);
$medecin = $stmt->fetch();

if (!$medecin) {
    die("Médecin introuvable");
}

// Date actuelle
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Récupération des rendez-vous
$stmt = $pdo->prepare("
    SELECT r.*, p.nom, p.telephone 
    FROM rendez_vous r
    JOIN patients p ON r.id_patient = p.id_patient
    WHERE r.id_medecin = ? AND r.date_rdv = ?
    ORDER BY r.heure_rdv
");
$stmt->execute([$id_medecin, $date]);
$rdvs = $stmt->fetchAll();

if ($rdvs === false) {
    die("Erreur lors de la récupération des rendez-vous");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda | Dr. <?= htmlspecialchars($medecin['nom']) ?></title>
    <link rel="stylesheet" href="agenda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-calendar-alt"></i> Agenda du Dr. <?= htmlspecialchars($medecin['nom']) ?></h1>
            <div class="header-actions">
                <input type="date" id="date-selector" value="<?= $date ?>">
                <button class="btn-add" onclick="openModal()">
                    <i class="fas fa-plus"></i> Nouveau RDV
                </button>
            </div>
        </header>

        <div class="agenda-grid">
            <?php if (empty($rdvs)): ?>
                <p class="no-rdv">Aucun rendez-vous pour cette date</p>
            <?php else: ?>
                <?php foreach ($rdvs as $rdv): ?>
                    <div class="rdv-card">
                        <div class="rdv-time"><?= substr($rdv['heure_rdv'], 0, 5) ?></div>
                        <div class="rdv-patient">
                            <strong><?= htmlspecialchars($rdv['nom']) ?></strong>
                            <p><?= htmlspecialchars($rdv['motif']) ?></p>
                        </div>
                        <div class="rdv-phone">
                            <i class="fas fa-phone"></i> <?= htmlspecialchars($rdv['telephone']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal simplifié -->
    <div class="modal" id="rdv-modal">
        <div class="modal-content">
            <h3>Nouveau rendez-vous</h3>
            <form>
                <input type="hidden" id="medecin-id" value="<?= $id_medecin ?>">
                
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" id="rdv-date" value="<?= $date ?>">
                </div>
                
                <div class="form-group">
                    <label>Heure</label>
                    <input type="time" id="rdv-heure">
                </div>
                
                <div class="form-group">
                    <label>Patient</label>
                    <select id="rdv-patient">
                        <option value="">Choisir un patient</option>
                        <?php 
                        $patients = $pdo->query("SELECT id_patient, nom FROM patients ORDER BY nom")->fetchAll();
                        foreach ($patients as $p): ?>
                            <option value="<?= $p['id_patient'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Motif</label>
                    <input type="text" id="rdv-motif" placeholder="Motif de consultation">
                </div>
                
                <div class="form-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Annuler</button>
                    <button type="button" class="btn-save" onclick="saveRdv()">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('date-selector').addEventListener('change', function() {
            window.location = `agenda.php?id_medecin=<?= $id_medecin ?>&date=${this.value}`;
        });
        
        function openModal() {
            document.getElementById('rdv-modal').style.display = 'flex';
        }
        
        function closeModal() {
            document.getElementById('rdv-modal').style.display = 'none';
        }
        
        function saveRdv() {
            const data = {
                id_medecin: document.getElementById('medecin-id').value,
                id_patient: document.getElementById('rdv-patient').value,
                date_rdv: document.getElementById('rdv-date').value,
                heure_rdv: document.getElementById('rdv-heure').value,
                motif: document.getElementById('rdv-motif').value
            };

            // Validation rapide
            if (!data.id_patient || !data.date_rdv || !data.heure_rdv || !data.motif) {
                alert("Veuillez remplir tous les champs.");
                return;
            }

            fetch('save_rdv.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert("Rendez-vous enregistré !");
                    window.location.reload(); // recharge pour voir le nouveau RDV
                } else {
                    alert("Erreur : " + result.message);
                }
            })
            .catch(error => {
                console.error("Erreur réseau :", error);
                alert("Erreur lors de la requête.");
            });
        }
    </script>
</body>
</html>