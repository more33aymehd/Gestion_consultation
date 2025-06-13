<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=gestion_sante", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
    die("Connexion échouée : " . htmlspecialchars($e->getMessage()));
}
?>