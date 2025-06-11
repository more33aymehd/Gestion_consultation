<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'gestion_sante';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Configuration du site
define('SITE_NAME', 'GestionSanté');
define('BASE_URL', 'http://localhost/gestion_sante');
define('UPLOAD_DIR', __DIR__.'/uploads');

// Démarrer la session si ce n'est pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour sécuriser les sorties
function secure($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Vérifier l'authentification
function check_auth() {
    if (!isset($_SESSION['id_medecin'])) {
        header("Location: login_medecin.php");
        exit();
    }
}