<?php
session_start();
require 'db.php';

$id_patient = $_SESSION['id_patient'] ?? 0;
$id_ordonnance = $_POST['id_ordonnance'];
$quantites = $_POST['quantites'] ?? [];

echo "<h2>Commande enregistrée</h2>";
echo "<p>Ordonnance n° $id_ordonnance</p>";

foreach ($quantites as $id_med => $qte) {
    echo "<p>Médicament ID $id_med : $qte unités</p>";
}
