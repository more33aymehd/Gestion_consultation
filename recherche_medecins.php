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
  <style>
.ordonnance-card {
    border: 1px solid #ccc;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    background: #f9f9f9;
}
.ordonnance-card h3 {
    margin-top: 0;
}
.medicaments-liste {
    margin-top: 10px;
    padding-left: 10px;
}
body {
    background-color: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #343a40;
    color: white;
    padding: 20px;
    position: fixed;
    height: 100%;
}

.sidebar h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #ffffff;
}

.sidebar a {
    display: block;
    padding: 10px 0;
    color: #ffffff;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.sidebar a:hover {
    background-color: #495057;
    border-radius: 5px;
    padding-left: 10px;
}

.main-content {
    margin-left: 250px;
    padding: 30px;
    width: 100%;
}

.section {
    background: white;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.section h2 {
    margin-bottom: 15px;
    font-size: 1.4rem;
    font-weight: bold;
    color: #333;
}

.btn-custom {
    background-color: #198754;
    color: white;
}

textarea {
    width: 100%;
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 10px;
}


</style>

  <!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.voir-medicaments').forEach(function(btn) {
    btn.addEventListener('click', function() {
      const id = this.dataset.id;
      const container = document.getElementById('medicaments_' + id);
      if (container.innerHTML.trim() !== '') return; // Évite de recharger si déjà chargé

      fetch('ajax_medicaments.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_ordonnance=' + encodeURIComponent(id)
      })
      .then(res => res.text())
      .then(html => container.innerHTML = html)
      .catch(err => container.innerHTML = 'Erreur de chargement');
    });
  });
});
</script>

    <script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({
    pageLanguage: 'fr',
    includedLanguages: 'en,fr,es,de,pt,ar', 
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
  <div class="dashboard-container">
    <div class="sidebar">
      <h2>🏥 Mon Espace</h2>
      <a href="mes_ordonnances.php">📄 Mes Ordonnances</a>
      <a href="groupes.php">👥 Groupes WhatsApp</a>
      <a href="geo.php">📍 Pharmacies proches</a>
      <a href="deconnexion.php">🚪 Déconnexion</a>
    </div>
    <div class="main-content">
      <div class="section">
        <h2>🔍 Rechercher un Médicament</h2>
        <form id="formRecherche" class="d-flex">
          <input type="text" name="nom" class="form-control me-2" placeholder="Nom du médicament" required>
          <button class="btn btn-primary" type="submit">Rechercher</button>
        </form>
        <div id="resultats1" class="mt-3"></div>
      </div>
      <div class="section">
        <h2>💬 Décrire votre problème</h2>
        <textarea id="description_probleme" rows="4" placeholder="Ex : J’ai des douleurs à la poitrine quand je respire profondément."></textarea><br>
        <div class="mt-2">
          <button class="btn btn-secondary" type="button" onclick="startDictation()">🎤 Parler</button>
          <button class="btn btn-info" onclick="detecterSpecialite()">🔍 Détecter la spécialité</button>
       </div>
        <p id="result_specialite" class="mt-2"></p>
        <div class="section">
         <h2>👨‍⚕️ Rechercher un Médecin par Spécialité</h2>
          <select id="specialite" name="specialite" class="form-select" required>
          <option value="">-- Sélectionner une spécialité --</option>
          <?php foreach ($specialites as $spec): ?>
             <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
          <?php endforeach; ?>
          </select>
        <div id="resultats" class="mt-3"></div>
      </div>
       <div class="section">
      <h2>🌐 Traduction</h2>
      <div id="google_translate_element"></div>
    </div>
    </div>

    </div>
  </div>
  


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
document.getElementById("formRecherche").addEventListener("submit", function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  const nom = formData.get("nom");

  fetch("recherche_medicament.php?nom=" + encodeURIComponent(nom))
    .then(res => res.text())
    .then(html => {
      document.getElementById("resultats1").innerHTML = html;
    });
});

function commander(nomMedicament, tel) {
  const msg = `Bonjour, je souhaite commander le médicament "${nomMedicament}"`;
  const url = `https://wa.me/${tel.replace(/\D/g, '')}?text=${encodeURIComponent(msg)}`;
  window.open(url, '_blank');
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


<script src="https://maps.googleapis.com/maps/api/js?key=TA_CLE_API_GOOGLE_MAPS"></script>





<!-- MODALE DE COMMANDE -->
<div class="modal fade" id="commandeModal" tabindex="-1" aria-labelledby="commandeLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="commander.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="commandeLabel">Commander un médicament</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="telephone" id="commandeTelephone">
          <input type="hidden" name="email" id="commandeEmail">
          <input type="hidden" name="pharmacie" id="commandePharmacie">
          <input type="hidden" name="medicament" id="commandeMedicament">
          <input type="hidden" name="prix" id="commandePrix">

          <p><strong>Médicament :</strong> <span id="commandeNomMedicament"></span></p>
          <p><strong>Pharmacie :</strong> <span id="commandeNomPharmacie"></span></p>
          <p><strong>Prix unitaire :</strong> <span id="commandePrixAffiche"></span> FCFA</p>

          <label for="quantite">Quantité :</label>
          <input type="number" name="quantite" id="quantite" class="form-control" min="1" required>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Envoyer la commande</button>
          <a href="email.php">send email</a>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const commandeModal = document.getElementById('commandeModal');
  commandeModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const nom = button.getAttribute('data-nom');
    const pharmacie = button.getAttribute('data-pharmacie');
    const telephone = button.getAttribute('data-telephone');
    const email = button.getAttribute('data-email');
    const prix = button.getAttribute('data-prix');

    document.getElementById('commandeNomMedicament').textContent = nom;
    document.getElementById('commandeNomPharmacie').textContent = pharmacie;
    document.getElementById('commandePrixAffiche').textContent = prix;

    document.getElementById('commandeMedicament').value = nom;
    document.getElementById('commandePharmacie').value = pharmacie;
    document.getElementById('commandeTelephone').value = telephone;
    document.getElementById('commandeEmail').value = email;
    document.getElementById('commandePrix').value = prix;
  });
});
</script>

</body>
</html>
