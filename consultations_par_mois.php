<?php
$conn = new mysqli("localhost", "root", "", "gestion_sante");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$sql = "SELECT MONTH(date) AS mois, COUNT(*) AS total
        FROM consultations
        GROUP BY MONTH(date)
        ORDER BY mois ASC";

$mois_fr = ["", "Janv", "Févr", "Mars", "Avril", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"];

$html = '<div class="histogram">';

$max = 1;
$valeurs = [];

$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $mois = (int)$row['mois'];
    $total = (int)$row['total'];
    $valeurs[] = ["mois" => $mois, "total" => $total];
    if ($total > $max) $max = $total;
}

foreach ($valeurs as $val) {
    $hauteur = round(($val['total'] / $max) * 100); // en %
    $html .= '<div class="bar" style="height: ' . $hauteur . '%">
                <span>' . $val['total'] . '</span>
              </div>';
}

$html .= '</div><div class="labels">';
foreach ($valeurs as $val) {
    $html .= '<div class="label">' . $mois_fr[$val['mois']] . '</div>';
}
$html .= '</div>';

echo $html;
?>