<?php
session_start();
if (!isset($_SESSION['id_patient'])) {
    header("Location: connexion.php");
    exit;
}
$id_patient = $_SESSION['id_patient'];
$nom_patient = $_SESSION['nom_patient'] ?? '';

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$stmt = $pdo->query("SELECT DISTINCT specialite FROM medecins WHERE specialite != '' ORDER BY specialite ASC");
$specialites = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Recherche Médecins AJAX</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .medecin { border: 1px solid #ccc; padding: 10px; margin-top: 10px; border-radius: 8px; position: relative; }
        .photo { float: right; width: 100px; height: 100px; object-fit: cover; margin-left: 10px; }
        button { padding: 8px 12px; margin-top: 10px; cursor: pointer; }
        .form-consultation-container { margin-top: 10px; background: #f9f9f9; padding: 10px; border-radius: 6px; }
        .message { margin-top: 8px; font-weight: bold; }
        label { font-weight: bold; }
    </style>
</head>
<body>

<h2>🔍 Rechercher un Médecin par Spécialité</h2>

<label for="specialite">Spécialité :</label>
<select id="specialite" name="specialite" required>
    <option value="">-- Sélectionner --</option>
    <?php foreach ($specialites as $spec): ?>
        <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
    <?php endforeach; ?>
</select>

<div id="resultats"></div>

<script>
document.getElementById('specialite').addEventListener('change', function(){
    let specialite = this.value;
    if(specialite === '') {
        document.getElementById('resultats').innerHTML = '';
        return;
    }

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax_medecins.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function(){
        if(xhr.status === 200){
            document.getElementById('resultats').innerHTML = xhr.responseText;
            attachEvents();
        } else {
            document.getElementById('resultats').innerHTML = 'Erreur lors de la recherche.';
        }
    };
    xhr.send('specialite=' + encodeURIComponent(specialite));
});

function attachEvents(){
    // Boutons Contacter
    document.querySelectorAll('.btn-contacter').forEach(btn => {
        btn.onclick = () => {
            const container = btn.nextElementSibling;
            container.style.display = 'block';
            btn.style.display = 'none';
        };
    });

    // Boutons Annuler
    document.querySelectorAll('.btn-annuler').forEach(btn => {
        btn.onclick = () => {
            const container = btn.closest('.form-consultation-container');
            container.style.display = 'none';
            container.previousElementSibling.style.display = 'inline-block'; // bouton Contacter
        };
    });

    // Soumission des formulaires consultation
    document.querySelectorAll('.form-consultation').forEach(form => {
        form.onsubmit = function(e){
            e.preventDefault();
            const formData = new FormData(this);
            const container = this.parentElement;
            const messageDiv = container.querySelector('.message');
            messageDiv.textContent = '';

            fetch('ajouter_consultation.php', {
                method: 'POST',
                body: formData
            }).then(r => r.json())
              .then(data => {
                  if(data.success){
                      messageDiv.style.color = 'green';
                      messageDiv.textContent = "Consultation enregistrée. Redirection vers WhatsApp...";
                      setTimeout(() => {
                          const telBtn = container.previousElementSibling;
                          const phone = telBtn.getAttribute('data-tel').replace(/[^0-9]/g, '');
                          const contenu = encodeURIComponent(formData.get('contenu'));
                          window.location.href = `https://wa.me/${phone}?text=${contenu}`;
                      }, 1500);
                  } else {
                      messageDiv.style.color = 'red';
                      messageDiv.textContent = data.message || 'Erreur lors de l\'enregistrement';
                  }
              }).catch(() => {
                  messageDiv.style.color = 'red';
                  messageDiv.textContent = 'Erreur réseau';
              });
        };
    });
}
</script>
<div style=" margin-bottom: 20px;">
    Bonjour, <?= htmlspecialchars($nom_patient) ?> |
    <form method="post" action="deconnexion.php" style="display:inline;">
        <button type="submit" style="cursor:pointer;">Déconnexion</button>
    </form>
</div>

</body>
</html>
