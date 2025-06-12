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
$stmt = $pdo->query("SELECT * FROM groupes_whatsapp ORDER BY date_creation DESC");
$groupes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'fr',
    includedLanguages: 'en,fr,es,de,pt,ar', // Langues autorisées
    layout: google.translate.TranslateElement.InlineLayout.SIMPLE
  }, 'google_translate_element');
}
</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

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
<h3>💬 Décrivez votre problème :</h3>
<textarea id="description_probleme" rows="4" cols="50" placeholder="Ex : J’ai des douleurs à la poitrine quand je respire profondément."></textarea><br>
<button type="button" onclick="startDictation()">🎤 Parler</button>
<button onclick="detecterSpecialite()">🔍 Trouver la spécialité automatiquement</button>
<p id="result_specialite" style="margin-top:10px; font-weight: bold;"></p>
<div id="google_translate_element"></div>

<label for="specialite">Spécialité :</label>
<select id="specialite" name="specialite" required>
    <option value="">-- Sélectionner --</option>
    <?php foreach ($specialites as $spec): ?>
        <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
    <?php endforeach; ?>
</select>

<div id="resultats"></div>
<a href="groupes.php">Rejoins des Groupes</a>



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
                         window.open(`https://wa.me/${phone}?text=${encodeURIComponent(contenu)}`, '_blank');

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
function detecterSpecialite() {
    const description = document.getElementById('description_probleme').value.trim();
    const resultat = document.getElementById('result_specialite');

    if (!description) {
        resultat.textContent = "Veuillez décrire votre problème.";
        resultat.style.color = "red";
        return;
    }

    resultat.textContent = "🔄 Analyse en cours...";
    resultat.style.color = "black";

    fetch('detecter_specialite.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'description=' + encodeURIComponent(description)
    })
    .then(res => res.json())
    .then(data => {
        if (data.specialite) {
            resultat.textContent = "✅ Spécialité détectée : " + data.specialite;
            resultat.style.color = "green";
            document.getElementById('specialite').value = data.specialite;
            // Déclencher l'événement pour charger les médecins
            document.getElementById('specialite').dispatchEvent(new Event('change'));
        } else {
            resultat.textContent = "❌ Spécialité non reconnue. Essayez d’être plus précis.";
            resultat.style.color = "red";
        }
    })
    .catch(() => {
        resultat.textContent = "Erreur réseau.";
        resultat.style.color = "red";
    });
}

</script>
<script>
function startDictation() {
  if ('webkitSpeechRecognition' in window) {
    const recognition = new webkitSpeechRecognition();
    recognition.lang = "fr-FR"; // Langue française
    recognition.continuous = false;
    recognition.interimResults = false;

    recognition.onresult = function(event) {
      const texte = event.results[0][0].transcript;
      document.getElementById('description_probleme').value = texte;
      recognition.stop();
    };

    recognition.onerror = function(event) {
      alert("Erreur de reconnaissance vocale : " + event.error);
      recognition.stop();
    };

    recognition.start();
  } else {
    alert("Votre navigateur ne supporte pas la reconnaissance vocale.");
  }
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
