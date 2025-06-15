<?php
require_once 'config.php';

// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier la connexion
if (!isset($_SESSION['medecin'])) {
    header('Location: login.php');
    exit();
}

// Récupérer l'ID du médecin depuis la session
$id_medecin = $_SESSION['id'];

// Récupération des données du médecin
$stmt = $pdo->prepare("SELECT * FROM medecins WHERE id_medecin = ?");
if (!$stmt->execute([$id_medecin])) {
    die("Erreur lors de la récupération des données du médecin.");
}
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    die("Médecin introuvable, vérifiez l'ID.");
}

// Statistiques de base
$stats = [];

// Total consultations
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE id_medecin = ?");
if ($stmt_total->execute([$id_medecin])) {
    $stats['total_consultations'] = $stmt_total->fetchColumn();
} else {
    $stats['total_consultations'] = 0;
}

// Patients uniques
$stmt_patients = $pdo->prepare("SELECT COUNT(DISTINCT id_patient) FROM consultations WHERE id_medecin = ?");
if ($stmt_patients->execute([$id_medecin])) {
    $stats['patients_uniques'] = $stmt_patients->fetchColumn();
} else {
    $stats['patients_uniques'] = 0;
}

// Revenus du mois
$stmt_revenus = $pdo->prepare("SELECT SUM(prix) FROM consultations WHERE id_medecin = ? AND MONTH(date) = MONTH(CURRENT_DATE())");
if ($stmt_revenus->execute([$id_medecin])) {
    $stats['revenus_mois'] = $stmt_revenus->fetchColumn() ?? 0;
} else {
    $stats['revenus_mois'] = 0;
}

// RDV annulés
$stmt_annules = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE id_medecin = ? AND statut = 'annulé'");
if ($stmt_annules->execute([$id_medecin])) {
    $stats['rdv_annules'] = $stmt_annules->fetchColumn();
} else {
    $stats['rdv_annules'] = 0;
}

// Consultations par mois (pour le graphique)
$consultations_par_mois = $pdo->prepare("
    SELECT 
        MONTH(date) as mois, 
        COUNT(*) as nombre,
        SUM(prix) as revenus
    FROM consultations 
    WHERE id_medecin = ? 
    AND YEAR(date) = YEAR(CURRENT_DATE())
    GROUP BY MONTH(date)
    ORDER BY mois
");
if (!$consultations_par_mois->execute([$id_medecin])) {
    die("Erreur lors de la récupération des consultations par mois.");
}
$consultations_par_mois = $consultations_par_mois->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques | Dr. <?= htmlspecialchars($medecin['nom']) ?> | GestionSanté</title>
    <link rel="stylesheet" href="stats.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="main-content">
        <header>
            <h1><i class="fas fa-chart-bar"></i> Statistiques</h1>
            <div class="period-selector">
                <select id="select-period">
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                    <option value="all">Toutes périodes</option>
                </select>
            </div>
        </header>

        <!-- Cartes de statistiques -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value"><?= $stats['total_consultations'] ?></div>
                <div class="stat-label">Consultations</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?= $stats['patients_uniques'] ?></div>
                <div class="stat-label">Patients uniques</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-value"><?= number_format($stats['revenus_mois'], 0, ',', ' ') ?> XAF</div>
                <div class="stat-label">Revenus ce mois</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
                <div class="stat-value"><?= $stats['rdv_annules'] ?></div>
                <div class="stat-label">RDV annulés</div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-container">
            <div class="chart-card">
                <h2>Consultations par mois</h2>
                <canvas id="consultationsChart"></canvas>
            </div>
            
            <div class="chart-card">
                <h2>Revenus par mois</h2>
                <canvas id="revenusChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Données pour les graphiques
        const mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        const consultationsData = <?= json_encode(array_map(function($m) { 
            return ['mois' => $m['mois'], 'nombre' => $m['nombre']]; 
        }, $consultations_par_mois)) ?>;

        const revenusData = <?= json_encode(array_map(function($m) { 
            return ['mois' => $m['mois'], 'montant' => $m['revenus']];
        }, $consultations_par_mois)) ?>;

        // Préparation des données pour Chart.js
        const consultationsParMois = Array(12).fill(0);
        const revenusParMois = Array(12).fill(0);

        consultationsData.forEach(item => {
            consultationsParMois[item.mois - 1] = item.nombre;
        });

        revenusData.forEach(item => {
            revenusParMois[item.mois - 1] = item.montant;
        });

        // Graphique des consultations
        new Chart(
            document.getElementById('consultationsChart'),
            {
                type: 'bar',
                data: {
                    labels: mois,
                    datasets: [{
                        label: 'Nombre de consultations',
                        data: consultationsParMois,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );

        // Graphique des revenus
        new Chart(
            document.getElementById('revenusChart'),
            {
                type: 'line',
                data: {
                    labels: mois,
                    datasets: [{
                        label: 'Revenus (XAF)',
                        data: revenusParMois,
                        fill: true,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        );
    </script>
</body>
</html>