<?php
require 'db.php';

$id_ordonnance = $_POST['id_ordonnance'] ?? 0;

$stmt = $pdo->prepare("
    SELECT m.nom, o.posologie, o.duree, o.quantite
    FROM ordonnance_medicaments o
    JOIN medicaments m ON o.id_medicament = m.id_medicament
    WHERE o.id_ordonnance = ?
");
$stmt->execute([$id_ordonnance]);
$medicaments = $stmt->fetchAll();

if ($medicaments) {
    echo "<ul>";
    foreach ($medicaments as $med) {
        echo "<li><strong>{$med['nom']}</strong> – Posologie : {$med['posologie']}, Durée : {$med['duree']} jours, Quantité : {$med['quantite']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>Aucun médicament trouvé pour cette ordonnance.</p>";
}
