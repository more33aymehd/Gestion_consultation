<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Gestion Santé</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <!-- Menu latéral -->
    <div class="col-md-2 bg-light p-3">
      <h5>Menu</h5>
      <ul class="nav flex-column">
        <li class="nav-item"><a class="nav-link" href="crud/patients.php">Patients</a></li>
        <li class="nav-item"><a class="nav-link" href="crud/medecins.php">Médecins</a></li>
        <li class="nav-item"><a class="nav-link" href="crud/medicaments.php">Médicaments</a></li>
        <li class="nav-item"><a class="nav-link" href="crud/ordonnances.php">Ordonnances</a></li>
        <li class="nav-item"><a class="nav-link" href="crud/groupes.php">Groupes</a></li>
      </ul>
    </div>

    <!-- Contenu -->
    <div class="col-md-10 p-4">
      <h2>Bienvenue dans le tableau de bord</h2>
      <p>Sélectionne une section dans le menu pour gérer les données.</p>
    </div>
  </div>
</div>
</body>
</html>
