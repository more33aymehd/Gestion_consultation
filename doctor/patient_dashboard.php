<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: patient_login.php");
    exit;
}

// Récupérer les consultations
try {
    $stmt = $conn->prepare("SELECT c.id_consultation, c.date, c.contenu, c.prix, m.nom AS medecin_nom, m.specialite 
                            FROM consultations c 
                            JOIN medecins m ON c.id_medecin = m.id_medecin 
                            WHERE c.id_patient = ?");
    $stmt->execute([$_SESSION['patient_id']]);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur consultations : " . htmlspecialchars($e->getMessage());
}

// Récupérer les ordonnances
try {
    $stmt = $conn->prepare("SELECT cmd.id_commande, cmd.date_commande, med.nom AS medicament, med.prix, ph.nom AS pharmacie, ph.adresse AS pharmacie_adresse, m.nom AS medecin_nom 
                            FROM commandes cmd 
                            JOIN medicaments med ON cmd.id_medicament = med.id_medicament 
                            JOIN pharmacies ph ON cmd.id_pharmacy = ph.id_pharmacy 
                            JOIN consultations c ON c.id_patient = cmd.id_patient 
                            JOIN medecins m ON c.id_medecin = m.id_medecin 
                            WHERE cmd.id_patient = ?");
    $stmt->execute([$_SESSION['patient_id']]);
    $ordonnances = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Erreur ordonnances : " . htmlspecialchars($e->getMessage());
}

// Suggestions hôpitaux/pharmacies avec coordonnées
$hospitals = [];
$pharmacies = [];
$patient_coords = [3.8480, 11.5021]; // Default : Yaoundé
if (isset($_SESSION['new_consultation']) && $_SESSION['new_consultation']) {
    $stmt = $conn->prepare("SELECT adresse FROM patients WHERE id_patient = ?");
    $stmt->execute([$_SESSION['patient_id']]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    $adresse = $patient['adresse'] ?? 'Yaoundé';

    // Géocoder l’adresse du patient
    $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($adresse) . "&format=json&limit=1";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'HospitalApp/1.0');
    $response = curl_exec($ch);
    $geocode = json_decode($response, true);
    if (!empty($geocode)) {
        $patient_coords = [$geocode[0]['lat'], $geocode[0]['lon']];
    }

    // Hôpitaux
    $url = "https://nominatim.openstreetmap.org/search?q=hospital+near+" . urlencode($adresse) . "&format=json&limit=3";
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    $hospitals = json_decode($response, true) ?: [];

    // Pharmacies
    $url = "https://nominatim.openstreetmap.org/search?q=pharmacy+near+" . urlencode($adresse) . "&format=json&limit=3";
    curl_setopt($ch, CURLOPT_URL, $url);
    $response = curl_exec($ch);
    $pharmacies = json_decode($response, true) ?: [];
    curl_close($ch);

    unset($_SESSION['new_consultation']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Patient</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body>
    <header>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['patient_name']); ?></h1>
    </header>
    <nav>
        <ul>
            <li><a href="book_appointment.php">Prendre un rendez-vous</a></li>
            <li><a href="edit_patient_profile.php">Voir profil</a></li>
            <li><a href="support_groups.php">Groupes de soutien</a></li>
            <li><a href="chat.php">Parler à Dr. Nancy</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <main>
        <?php if (!empty($hospitals) || !empty($pharmacies)): ?>
        <section>
            <h2>Suggestions près de votre zone</h2>
            <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
            <script>
                var map = L.map('map').setView([<?php echo $patient_coords[0]; ?>, <?php echo $patient_coords[1]; ?>], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map);

                // Marqueur du patient
                L.marker([<?php echo $patient_coords[0]; ?>, <?php echo $patient_coords[1]; ?>])
                    .addTo(map)
                    .bindPopup('Votre position');

                // Marqueurs hôpitaux
                <?php foreach ($hospitals as $hospital): ?>
                    L.marker([<?php echo htmlspecialchars($hospital['lat']); ?>, <?php echo htmlspecialchars($hospital['lon']); ?>])
                        .addTo(map)
                        .bindPopup('<?php echo htmlspecialchars($hospital['display_name']); ?>');
                <?php endforeach; ?>

                // Marqueurs pharmacies
                <?php foreach ($pharmacies as $pharmacy): ?>
                    L.marker([<?php echo htmlspecialchars($pharmacy['lat']); ?>, <?php echo htmlspecialchars($pharmacy['lon']); ?>])
                        .addTo(map)
                        .bindPopup('<?php echo htmlspecialchars($pharmacy['display_name']); ?>');
                <?php endforeach; ?>
            </script>
            <?php if (!empty($hospitals)): ?>
                <h3>Hôpitaux :</h3>
                <ul>
                    <?php foreach ($hospitals as $hospital): ?>
                        <li><?php echo htmlspecialchars($hospital['display_name']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (!empty($pharmacies)): ?>
                <h3>Pharmacies :</h3>
                <ul>
                    <?php foreach ($pharmacies as $pharmacy): ?>
                        <li><?php echo htmlspecialchars($pharmacy['display_name']); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
        <?php endif; ?>
        <section>
            <h2>Vos consultations</h2>
            <?php if (empty($consultations)): ?>
                <p>Aucune consultation trouvée.</p>
            <?php else: ?>
                <table>
                    <tr>
                        <th>Date</th>
                        <th>Médecin</th>
                        <th>Motif</th>
                        <th>Prix</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($consultations as $consultation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consultation['date']); ?></td>
                            <td><?php echo htmlspecialchars($consultation['medecin_nom'] . ' (' . $consultation['specialite'] . ')'); ?></td>
                            <td><?php echo htmlspecialchars($consultation['contenu']); ?></td>
                            <td><?php echo htmlspecialchars($consultation['prix']); ?> CFA</td>
                            <td><a href="invoice.php?id_consultation=<?php echo $consultation['id_consultation']; ?>">Voir la facture</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </section>
        <section>
            <h2>Vos ordonnances</h2>
            <?php if (empty($ordonnances)): ?>
                <p>Aucune ordonnance trouvée.</p>
            <?php else: ?>
                <?php foreach ($ordonnances as $ordonnance): ?>
                    <div class="prescription">
                        <div class="prescription-header">
                            <h3>Ordonnance Médicale</h3>
                            <p>Plateforme de Gestion Santé, Yaoundé, Cameroun</p>
                        </div>
                        <div class="prescription-details">
                            <div class="prescription-doctor">
                                <p><strong>Médecin :</strong> <?php echo htmlspecialchars($ordonnance['medecin_nom']); ?></p>
                            </div>
                            <div class="prescription-patient">
                                <p><strong>Patient :</strong> <?php echo htmlspecialchars($_SESSION['patient_name']); ?></p>
                                <p><strong>Date :</strong> <?php echo htmlspecialchars($ordonnance['date_commande']); ?></p>
                            </div>
                        </div>
                        <div>
                            <p><strong>Médicament :</strong> <?php echo htmlspecialchars($ordonnance['medicament']); ?></p>
                            <p><strong>Prix :</strong> <?php echo htmlspecialchars($ordonnance['prix']); ?> CFA</p>
                            <p><strong>Pharmacie :</strong> <?php echo htmlspecialchars($ordonnance['pharmacie'] . ' - ' . $ordonnance['pharmacie_adresse']); ?></p>
                        </div>
                        <div class="prescription-footer">
                            <p>Signature : ___________________________</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>