<?php
session_start();
require 'db.php'; // Assure-toi que ce fichier existe et connecte bien à ta base

if (!isset($_SESSION['id_patient'])) {
    echo "<p style='color:red;'>Aucun patient en session.</p>";
    exit;
}

$id_patient = $_SESSION['id_patient'];

$stmt = $pdo->prepare("SELECT id_ordonnance, date_ord, notes, statut FROM ordonnances WHERE id_patient = ? ORDER BY date_ord DESC");
$stmt->execute([$id_patient]);
$ordonnances = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
 

  <meta charset="UTF-8">
  <title>Mes ordonnances</title>
  
  <style>
   
/* Général */
body {
  font-family: 'Segoe UI', sans-serif;
  background-color: #f4f6f9;
  margin: 0;
  padding: 20px;
  color: #333;
}

h2 {
  text-align: center;
  margin-bottom: 30px;
}

/* Carte ordonnance */
.ordonnance-card {
  background: #ffffff;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  padding: 20px;
  margin-bottom: 20px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.03);
  transition: transform 0.2s ease;
}

.ordonnance-card:hover {
  transform: scale(1.01);
}

.ordonnance-card h3 {
  font-size: 18px;
  color: #007bff;
  margin-bottom: 10px;
}

.ordonnance-card p {
  margin: 5px 0;
}

button {
  margin-right: 10px;
  margin-top: 10px;
}

/* Liste médicaments */
.medicaments-liste {
  margin-top: 15px;
  padding: 15px;
  border-left: 4px solid #007BFF;
  background: #f8f9fa;
  border-radius: 6px;
}

/* Boutons */
.btn {
  padding: 8px 16px;
  border-radius: 6px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-success {
  background-color: #28a745;
  color: white;
}

.btn-success:hover {
  background-color: #218838;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

/* MODALE Bootstrap personnalisée */
.modal-content {
  border-radius: 10px;
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

.modal-header {
  border-bottom: 1px solid #dee2e6;
  background-color: #f1f1f1;
}

.modal-title {
  font-size: 18px;
  font-weight: 600;
}

.modal-body {
  background: #fff;
}

.modal-footer {
  border-top: 1px solid #dee2e6;
  background-color: #f8f9fa;
}

/* Champ texte / formulaire */
input[type="text"], input[type="number"], select {
  width: 100%;
  padding: 8px;
  margin-top: 6px;
  margin-bottom: 10px;
  border-radius: 6px;
  border: 1px solid #ced4da;
}

/* Responsive */
@media screen and (max-width: 768px) {
  .ordonnance-card {
    padding: 15px;
  }

  .btn {
    width: 100%;
    margin-bottom: 10px;
  }
}


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
        background-color: #fff;
        border-left: 3px solid #007BFF;
        padding: 10px;
    }
    /* Fond de la modale (gris foncé transparent) */
.modal {
  display: none; /* Masquée par défaut */
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5); /* Fond semi-transparent */
}

/* Contenu de la modale */
.modal-content {
  background-color: #fff;
  margin: 10% auto; /* Centrée verticalement et horizontalement */
  padding: 20px;
  border-radius: 10px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  animation: fadeIn 0.3s ease-in-out;
}

/* Bouton de fermeture (X) */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
}

/* Animation d’apparition */
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.9); }
  to { opacity: 1; transform: scale(1); }
}

  </style>
</head>
<body>
  <h2>Liste de mes ordonnances</h2>

  <?php
  if (!$ordonnances) {
      echo "<p>Aucune ordonnance trouvée.</p>";
  } else {
      foreach ($ordonnances as $ord) {
          echo '
          <div class="ordonnance-card">
              <h3>Ordonnance du ' . htmlspecialchars($ord['date_ord']) . '</h3>
              <p><strong>Statut :</strong> ' . htmlspecialchars($ord['statut']) . '</p>
              <p><strong>Notes :</strong> ' . nl2br(htmlspecialchars($ord['notes'] ?? 'Aucune')) . '</p>
              <button class="voir-medicaments" data-id="' . $ord['id_ordonnance'] . '">Voir les médicaments</button>
              
              <button class="btn btn-primary btn-commander" data-id="' . $ord['id_ordonnance'] . '" data-bs-toggle="modal" data-bs-target="#commandeModal">Commander</button>

              <div class="medicaments-liste" id="medicaments_' . $ord['id_ordonnance'] . '"></div>
          </div>';
      }
  }
  ?>
  
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.voir-medicaments').forEach(function(btn) {
      btn.addEventListener('click', function() {
        const id = this.dataset.id;
        const container = document.getElementById('medicaments_' + id);
        if (container.innerHTML.trim() !== '') return;

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
  <!-- MODALE DE COMMANDE -->
<div class="modal fade" id="commandeModal" tabindex="-1" aria-labelledby="commandeLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form action="envoyer_commande.php" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="commandeLabel">Commander des médicaments</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_ordonnance" id="commandeIdOrdonnance">
          <div id="listeMedicamentsCommande">Chargement...</div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Envoyer la commande</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('commandeModal');

  document.querySelectorAll('.btn-commander').forEach(btn => {
    btn.addEventListener('click', function () {
      const idOrdonnance = this.getAttribute('data-id');
      document.getElementById('commandeIdOrdonnance').value = idOrdonnance;

      fetch('ajax_medicaments_commande.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_ordonnance=' + encodeURIComponent(idOrdonnance)
      })
      .then(res => res.text())
      .then(html => {
        document.getElementById('listeMedicamentsCommande').innerHTML = html;
      })
      .catch(() => {
        document.getElementById('listeMedicamentsCommande').innerHTML = '<p style="color:red;">Erreur de chargement.</p>';
      });
    });
  });
});
</script>
<script>
  // Ouvrir la modale
  function ouvrirModal() {
    document.getElementById('commandeModal').style.display = 'block';
  }

  // Fermer la modale
  document.querySelector('.close').onclick = function() {
    document.getElementById('commandeModal').style.display = 'none';
  }

  // Fermer en cliquant en dehors
  window.onclick = function(event) {
    let modal = document.getElementById('commandeModal');
    if (event.target === modal) {
      modal.style.display = 'none';
    }
  }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
