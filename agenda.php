<?php
// Connexion à la base de données
require_once 'config.php';

// ID du médecin (pour tester sans système de connexion)
$id_medecin = 2; // À changer selon votre médecin de test

// Récupération des informations du médecin
$req_medecin = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
$req_medecin->execute([$id_medecin]);
$medecin = $req_medecin->fetch();

// Date du jour au format YYYY-MM-DD
$aujourdhui = date('Y-m-d');

// Récupération des rendez-vous du jour
$req_rdv = $pdo->prepare("
    SELECT r.*, p.nom, p.telephone 
    FROM rendez_vous r
    JOIN patients p ON r.id_patient = p.id_patient
    WHERE r.id_medecin = ? AND r.date_rdv = ?
    ORDER BY r.heure_rdv
");
$req_rdv->execute([$id_medecin, $aujourdhui]);
$rendez_vous = $req_rdv->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda du Dr. <?= $medecin['nom'] ?></title>
    <style>
        /* Style de base */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar simplifié */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            padding: 20px;
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
        }
        
        /* Style pour les rendez-vous */
        .rdv-item {
            background: white;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .rdv-patient {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .rdv-heure {
            color: #3498db;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Menu latéral -->
        <div class="sidebar">
            <h2>Dr. <?= $medecin['nom'] ?></h2>
            <p>Spécialité: <?= $medecin['specialite'] ?></p>
            
            <nav>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="docteur.php" style="color: white; text-decoration: none;">Tableau de bord</a></li>
                    <li><a href="agenda.php" style="color: white; text-decoration: none;">Agenda</a></li>
                    <li><a href="patients.php" style="color: white; text-decoration: none;">Patients</a></li>
                </ul>
            </nav>
        </div>
        
        <!-- Contenu principal -->
        <div class="main-content">
            <h1>Agenda du <?= date('d/m/Y') ?></h1>
            
            <?php if (empty($rendez_vous)): ?>
                <p>Aucun rendez-vous aujourd'hui.</p>
            <?php else: ?>
                <?php foreach ($rendez_vous as $rdv): ?>
                    <div class="rdv-item">
                        <div class="rdv-heure"><?= substr($rdv['heure_rdv'], 0, 5) ?></div>
                        <div class="rdv-patient"><?= $rdv['nom'] ?></div>
                        <div>Téléphone: <?= $rdv['telephone'] ?></div>
                        <div>Motif: <?= $rdv['motif'] ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>