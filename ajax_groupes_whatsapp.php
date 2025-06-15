<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");

$specialite = $_POST['specialite'] ?? '';
$recherche = $_POST['recherche'] ?? '';
$page = max(1, intval($_POST['page'] ?? 1));
$parPage = 5;
$offset = ($page - 1) * $parPage;

$sql = "SELECT * FROM groupes_whatsapp WHERE 1";
$params = [];

if ($specialite !== '') {
    $sql .= " AND specialite = :spec";
    $params['spec'] = $specialite;
}
if ($recherche !== '') {
    $sql .= " AND nom LIKE :rech";
    $params['rech'] = "%$recherche%";
}

$countSql = str_replace("SELECT *", "SELECT COUNT(*)", $sql);
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetchColumn();

$sql .= " LIMIT $offset, $parPage";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$groupes = $stmt->fetchAll();

$html = '';
if (!$groupes) {
    $html = "<p>Aucun groupe trouvé.</p>";
} else {
    foreach ($groupes as $groupe) {
        $html .= "<div class='groupe-item'>";
        $html .= "<div><strong>" . htmlspecialchars($groupe['nom']) . "</strong><br>";
        $html .= "Spécialité : " . htmlspecialchars($groupe['specialite']) . "</div>";
        $html .= "<a href='" . htmlspecialchars($groupe['lien']) . "' target='_blank'>Rejoindre</a>";
        $html .= "</div>";
    }
}

// Pagination
$nbPages = ceil($total / $parPage);
$pagination = '';
if ($nbPages > 1) {
    for ($i = 1; $i <= $nbPages; $i++) {
        $pagination .= '<button class="page-btn" data-page="' . $i . '" ' . ($i == $page ? 'disabled' : '') . '>' . $i . '</button>';
    }
}

echo json_encode([
    'html' => $html,
    'pagination' => $pagination
]);
