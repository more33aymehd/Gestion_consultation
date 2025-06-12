<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
$specialite = $_POST['specialite'] ?? '';

if ($specialite) {
    $stmt = $pdo->prepare("SELECT * FROM groupes_whatsapp WHERE specialite = ?");
    $stmt->execute([$specialite]);
} else {
    $stmt = $pdo->query("SELECT * FROM groupes_whatsapp");
}

$groupes = $stmt->fetchAll();

if (!$groupes) {
    echo "<p>Aucun groupe trouvé.</p>";
} else {
    foreach ($groupes as $g) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0;'>";
        echo "<strong>" . htmlspecialchars($g['nom']) . "</strong><br>";
        echo "<em>" . htmlspecialchars($g['description']) . "</em><br>";
        echo "<a href='" . htmlspecialchars($g['lien']) . "' target='_blank'>Rejoindre le groupe</a>";
        echo "</div>";
    }
}
?>
