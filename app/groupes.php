<?php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
$stmt = $pdo->query("SELECT DISTINCT specialite FROM groupes_whatsapp WHERE specialite IS NOT NULL AND specialite != ''");
$specialites = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Groupes WhatsApp</title>
</head>
<body>
    <h2>🔍 Rechercher un groupe WhatsApp par spécialité</h2>

    <select id="specialite">
        <option value="">-- Toutes les spécialités --</option>
        <?php foreach ($specialites as $spec): ?>
            <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
        <?php endforeach; ?>
    </select>

    <div id="groupes-container">Sélectionnez une spécialité pour voir les groupes.</div>

    <script>
        document.getElementById('specialite').addEventListener('change', function () {
            let spec = this.value;
            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax_groupes_whatsapp.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                document.getElementById('groupes-container').innerHTML = this.responseText;
            };
            xhr.send('specialite=' + encodeURIComponent(spec));
        });
    </script>
</body>
</html>
