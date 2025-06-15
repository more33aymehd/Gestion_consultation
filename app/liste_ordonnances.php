<?php
session_start();
require 'login.php';

if (!isset($_SESSION['id_patient'])) {
    echo "<p style='color:red;'>Aucun patient en session</p>";
    exit;
}

$id_patient = $_SESSION['id_patient'];

$stmt = $pdo->prepare("SELECT id_ordonnance, date_ord, notes, statut FROM ordonnances WHERE id_patient = ? ORDER BY date_ord DESC");
$stmt->execute([$id_patient]);
$ordonnances = $stmt->fetchAll();

if (!$ordonnances) {
    echo "<p>Aucune ordonnance trouvée.</p>";
    exit;
}

foreach ($ordonnances as $ord) {
    echo '
    <div class="ordonnance-card" data-id="' . $ord['id_ordonnance'] . '">
        <h3>Ordonnance du ' . htmlspecialchars($ord['date_ord']) . '</h3>
        <p><strong>Statut :</strong> ' . htmlspecialchars($ord['statut']) . '</p>
        <p><strong>Notes :</strong> ' . nl2br(htmlspecialchars($ord['notes'] ?? 'Aucune')) . '</p>
        <button class="voir-medicaments" data-id="' . $ord['id_ordonnance'] . '">Voir les médicaments</button>
        <div class="medicaments-liste" id="medicaments_' . $ord['id_ordonnance'] . '"></div>
    </div>';
}
?>
