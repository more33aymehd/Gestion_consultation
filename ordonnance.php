<?php
require_once 'config.php';

// ID médecin fixé manuellement (peut être changé via l'URL)
$id_medecin = isset($_GET['id_medecin']) ? (int)$_GET['id_medecin'] : 1;

// Récupération des données du médecin
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
if (!$stmt->execute([$id_medecin])) {
    die("Erreur lors de la récupération des données du médecin.");
}
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    die("Médecin introuvable, vérifiez l'ID.");
}

// Récupération des consultations (utilisées comme ordonnances)
$stmt_consultations = $pdo->prepare("
    SELECT c.*, p.nom as patient_nom, p.photo as patient_photo, p.email as patient_email
    FROM consultations c
    JOIN patients p ON c.id_patient = p.id_patient
    WHERE c.id_medecin = ?
    ORDER BY c.date DESC
    LIMIT 50
");

if (!$stmt_consultations->execute([$id_medecin])) {
    die("Erreur lors de la récupération des consultations.");
}
$consultations = $stmt_consultations->fetchAll(PDO::FETCH_ASSOC);

// Récupération des médicaments disponibles
$stmt_medicaments = $pdo->query("
    SELECT m.*, ph.nom as pharmacie_nom 
    FROM medicaments m
    JOIN pharmacies ph ON m.id_pharmacy = ph.id_pharmacy
    ORDER BY m.nom
");

if (!$stmt_medicaments) {
    die("Erreur lors de la récupération des médicaments.");
}
$medicaments = $stmt_medicaments->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordonnances | Dr. <?= htmlspecialchars($medecin['nom']) ?> | GestionSanté</title>
    <link rel="stylesheet" href="ordonnance.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="main-content">
        <header>
            <h1><i class="fas fa-prescription"></i> Gestion des ordonnances</h1>
            <button class="btn-primary" id="add-ordonnance">
                <i class="fas fa-plus"></i> Nouvelle ordonnance
            </button>
        </header>

        <!-- Liste des consultations comme ordonnances -->
        <div class="ordonnances-list">
            <?php if (empty($consultations)): ?>
                <div class="no-data">
                    <i class="fas fa-file-prescription"></i>
                    <p>Aucune ordonnance enregistrée</p>
                </div>
            <?php else: ?>
                <?php foreach ($consultations as $consultation): ?>
                    <div class="ordonnance-card" data-id="<?= $consultation['id_consultation'] ?>">
                        <div class="ordonnance-header">
                            <div class="patient-info">
                                <img src="<?= $consultation['patient_photo'] ? 'uploads/'.$consultation['patient_photo'] : 'assets/default-patient.png' ?>" class="patient-avatar">
                                <div>
                                    <h3><?= htmlspecialchars($consultation['patient_nom']) ?></h3>
                                    <span class="ordonnance-date"><?= date('d/m/Y', strtotime($consultation['date'])) ?></span>
                                </div>
                            </div>
                            <div class="ordonnance-actions">
                                <button class="btn-icon btn-send-mail" title="Envoyer par email">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="ordonnance-body">
                            <div class="ordonnance-notes">
                                <h4>Notes de consultation :</h4>
                                <p><?= nl2br(htmlspecialchars($consultation['contenu'])) ?></p>
                            </div>
                            <div class="ordonnance-medicaments">
                                <h4>Médicaments prescrits :</h4>
                                <?php 
                                $medics = $pdo->prepare("
                                    SELECT m.nom, m.prix, ph.nom as pharmacie
                                    FROM commandes c
                                    JOIN medicaments m ON c.id_medicament = m.id_medicament
                                    JOIN pharmacies ph ON m.id_pharmacy = ph.id_pharmacy
                                    WHERE c.id_patient = ? AND c.date_commande >= ?
                                    ORDER BY c.date_commande DESC
                                ");

                                if (!$medics->execute([$consultation['id_patient'], $consultation['date']])) {
                                    echo "<p>Erreur lors de la récupération des médicaments.</p>";
                                } else {
                                    $medics = $medics->fetchAll(PDO::FETCH_ASSOC);
                                    if (!empty($medics)): ?>
                                        <ul>
                                            <?php foreach ($medics as $medic): ?>
                                                <li>
                                                    <strong><?= htmlspecialchars($medic['nom']) ?></strong>
                                                    - <?= htmlspecialchars($medic['pharmacie']) ?> 
                                                    (<?= htmlspecialchars($medic['prix']) ?>€)
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="no-medic">Aucun médicament prescrit</p>
                                    <?php endif; ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal pour nouvelle ordonnance -->
    <div class="modal" id="ordonnance-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Nouvelle ordonnance</h2>
                <button class="btn-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="ordonnance-form">
                    <input type="hidden" name="id_medecin" value="<?= $id_medecin ?>">
                    
                    <div class="form-group">
                        <label for="patient-select">Patient :</label>
                        <select id="patient-select" name="id_patient" required>
                            <option value="">Sélectionner un patient</option>
                            <?php 
                            $patients = $pdo->query("SELECT id_patient, nom, email FROM patients ORDER BY nom")->fetchAll();
                            foreach ($patients as $patient): ?>
                                <option value="<?= $patient['id_patient'] ?>" data-email="<?= htmlspecialchars($patient['email']) ?>">
                                    <?= htmlspecialchars($patient['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ordonnance-date">Date :</label>
                        <input type="date" id="ordonnance-date" name="date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="medicaments-container">
                        <h3>Médicaments prescrits</h3>
                        <div class="medicament-item">
                            <div class="medicament-select">
                                <select name="medicaments[0][id_medicament]" required>
                                    <option value="">Sélectionner un médicament</option>
                                    <?php foreach ($medicaments as $medicament): ?>
                                        <option value="<?= $medicament['id_medicament'] ?>">
                                            <?= htmlspecialchars($medicament['nom']) ?> 
                                            (<?= htmlspecialchars($medicament['pharmacie_nom']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="medicament-details">
                                <input type="text" name="medicaments[0][posologie]" 
                                       placeholder="Posologie (ex: 1 comprimé matin et soir)" required>
                                <input type="number" name="medicaments[0][duree]" 
                                       placeholder="Durée (jours)" min="1" required>
                                <button type="button" class="btn-remove-medic" style="display: none;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn-add-medic">
                        <i class="fas fa-plus"></i> Ajouter un médicament
                    </button>
                    
                    <div class="form-group">
                        <label for="ordonnance-notes">Notes :</label>
                        <textarea id="ordonnance-notes" name="contenu" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="ordonnance-prix">Prix :</label>
                        <input type="number" id="ordonnance-prix" name="prix" step="0.01" min="0">
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="send_email" checked> Envoyer par email au patient
                        </label>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-cancel">Annuler</button>
                        <button type="submit" class="btn-primary">Enregistrer et envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Gestion du modal
        const modal = document.getElementById('ordonnance-modal');
        const openModal = () => modal.style.display = 'flex';
        const closeModal = () => modal.style.display = 'none';
        
        document.getElementById('add-ordonnance').addEventListener('click', openModal);
        document.querySelector('.btn-close').addEventListener('click', closeModal);
        document.querySelector('.btn-cancel').addEventListener('click', closeModal);

        // Ajout/suppression de médicaments
        let medicCount = 1;
        
        document.querySelector('.btn-add-medic').addEventListener('click', function() {
            const container = document.querySelector('.medicaments-container');
            const newItem = document.querySelector('.medicament-item').cloneNode(true);
            
            newItem.innerHTML = newItem.innerHTML.replace(/medicaments\[0\]/g, `medicaments[${medicCount}]`);
            const removeBtn = newItem.querySelector('.btn-remove-medic');
            removeBtn.style.display = 'block';
            removeBtn.addEventListener('click', function() {
                newItem.remove();
            });
            
            container.appendChild(newItem);
            medicCount++;
        });

        // Envoi de l'ordonnance par email
        document.querySelectorAll('.btn-send-mail').forEach(btn => {
            btn.addEventListener('click', function() {
                const ordId = this.closest('.ordonnance-card').dataset.id;
                if (confirm("Envoyer cette ordonnance par email au patient ?")) {
                    fetch(`send_prescription.php?id_consultation=${ordId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert("Ordonnance envoyée avec succès !");
                            } else {
                                alert("Erreur lors de l'envoi mon type : " + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert("Une erreur est survenue lors de l'envoi");
                        });
                }
            });
        });

        // Soumission du formulaire
document.getElementById('ordonnance-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('save_consultation.php', { // Changer ici
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Ordonnance enregistrée' + (data.email_sent ? ' et envoyée par email' : ''));
            closeModal();
            window.location.reload();
        } else {
            alert('Erreur: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
});
    </script>
</body>
</html>