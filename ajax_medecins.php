<?php
session_start();
if (!isset($_SESSION['id_patient'])) {
    http_response_code(403);
    echo "Accès refusé.";
    exit;
}

if (!isset($_POST['specialite']) || empty($_POST['specialite'])) {
    echo "Spécialité manquante.";
    exit;
}

$specialite = $_POST['specialite'];

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erreur serveur.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM medecins WHERE specialite = ? ORDER BY nom ASC");
$stmt->execute([$specialite]);
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$medecins) {
    echo "<p>Aucun médecin trouvé pour cette spécialité.</p>";
    exit;
}

foreach ($medecins as $m) {
    $photo = $m['photo'] ? htmlspecialchars($m['photo']) : 'default.png';
    echo '<div class="medecin">';
    echo '<img src="uploads/' . $photo . '" alt="Photo" class="photo">';
    echo '<strong>' . htmlspecialchars($m['nom']) . '</strong><br>';
    echo 'Téléphone : ' . htmlspecialchars($m['telephone']) . '<br>';
    echo 'Tarif : ' . htmlspecialchars($m['tarif']) . ' FCFA<br>';
    echo '<button class="btn-contacter" data-id="' . $m['id_medecin'] . '" data-nom="' . htmlspecialchars($m['nom']) . '" data-tel="' . htmlspecialchars($m['telephone']) . '">Contacter</button>';

    echo '<div class="form-consultation-container" style="display:none; margin-top:10px;">';
    echo '<form class="form-consultation">';
    echo '<input type="hidden" name="id_medecin" value="' . $m['id_medecin'] . '">';
    echo '<label>Motif / Description :</label><br>';
    echo '<textarea name="contenu" required rows="3" cols="50"></textarea><br>';
    echo '<button type="submit">Envoyer et contacter</button> ';
    echo '<button type="button" class="btn-annuler">Annuler</button>';
    echo '</form>';
    echo '<div class="message"></div>';
    echo '</div>';

    echo '</div>';
}
?>
