<?php
require 'db.php';

if (!isset($_POST['id_ordonnance'])) {
    echo "<p style='color:red;'>ID de l'ordonnance manquant.</p>";
    exit;
}

$id_ordonnance = $_POST['id_ordonnance'];

// Récupérer les médicaments liés à l'ordonnance via une table pivot (ex: medicaments_ordonnance)
$sql = "
    SELECT 
        mo.id_medicament, 
        m.nom, 
        mo.quantite
    FROM ordonnance_medicaments mo
    JOIN medicaments m ON mo.id_medicament = m.id_medicament
    WHERE mo.id_ordonnance = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id_ordonnance]);
$medicaments = $stmt->fetchAll();

if (!$medicaments) {
    echo "<p>Aucun médicament trouvé pour cette ordonnance.</p>";
    exit;
}

// Affichage des médicaments avec champs de quantité modifiable
echo '<ul style="list-style:none; padding:0;">';
foreach ($medicaments as $med) {
    echo '
    <li style="margin-bottom:10px;">
        <label><strong>' . htmlspecialchars($med['nom']) . '</strong></label><br>
        Quantité :
        <input type="number" name="quantites[' . $med['id_medicament'] . ']" value="' . intval($med['quantite']) . '" min="1" style="width:60px;">
        <input type="hidden" name="medicaments[]" value="' . $med['id_medicament'] . '">
    </li>';
}
echo '</ul>';
