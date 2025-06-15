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
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 20px;
      background: #f3f6f9;
    }

    h2 {
      text-align: center;
      color: #007bff;
      margin-bottom: 30px;
    }

    #filters {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    select, input[type="text"] {
      padding: 10px;
      font-size: 16px;
      border-radius: 8px;
      border: 1px solid #ccc;
      width: 250px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    #groupes-container {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: 0 auto;
      min-height: 100px;
    }

    .groupe-item {
      margin-bottom: 15px;
      padding: 15px;
      border-left: 5px solid #25D366;
      background: #f9f9f9;
      border-radius: 5px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .groupe-item:hover {
      background: #eefdf3;
    }

    .groupe-item a {
      color: #25D366;
      text-decoration: none;
      font-weight: bold;
    }

    .groupe-item a::before {
      content: "📱 ";
    }

    #pagination {
      text-align: center;
      margin-top: 20px;
    }

    #pagination button {
      margin: 0 5px;
      padding: 8px 12px;
      border: none;
      background: #007bff;
      color: white;
      border-radius: 5px;
      cursor: pointer;
    }

    #pagination button:disabled {
      background: #ccc;
      cursor: not-allowed;
    }

    @media (max-width: 600px) {
      #filters {
        flex-direction: column;
        align-items: center;
      }

      select, input[type="text"] {
        width: 90%;
      }
    }
  </style>
</head>
<body>
  <h2>🔍 Rechercher un groupe WhatsApp</h2>

  <div id="filters">
    <select id="specialite">
      <option value="">-- Toutes les spécialités --</option>
      <?php foreach ($specialites as $spec): ?>
        <option value="<?= htmlspecialchars($spec) ?>"><?= htmlspecialchars($spec) ?></option>
      <?php endforeach; ?>
    </select>

    <input type="text" id="recherche" placeholder="🔎 Nom du groupe...">
  </div>

  <div id="groupes-container">Sélectionnez une spécialité ou tapez un mot-clé.</div>
  <div id="pagination"></div>

  <script>
    let page = 1;

    function chargerGroupes() {
      const spec = document.getElementById('specialite').value;
      const recherche = document.getElementById('recherche').value;
      const params = new URLSearchParams({ specialite: spec, recherche, page });

      fetch('ajax_groupes_whatsapp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params.toString()
      })
      .then(res => res.json())
      .then(data => {
        document.getElementById('groupes-container').innerHTML = data.html;
        document.getElementById('pagination').innerHTML = data.pagination;
      });
    }

    document.getElementById('specialite').addEventListener('change', () => { page = 1; chargerGroupes(); });
    document.getElementById('recherche').addEventListener('input', () => { page = 1; chargerGroupes(); });

    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('page-btn')) {
        page = parseInt(e.target.dataset.page);
        chargerGroupes();
      }
    });

    // Chargement initial
    chargerGroupes();
  </script>
</body>
</html>
