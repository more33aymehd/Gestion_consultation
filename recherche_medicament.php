<?php
require 'db.php';

$nom = trim($_GET['nom'] ?? '');

if ($nom !== '') {
    $stmt = $pdo->prepare("
        SELECT m.nom AS nom_medicament, m.quantite, m.prix, 
               p.nom AS nom_pharmacie, p.adresse, p.telephone,p.email
        FROM medicaments m
        JOIN pharmacies p ON m.id_pharmacy = p.id_pharmacy
        WHERE m.nom LIKE ? AND m.quantite > 0
    ");
    $stmt->execute(['%' . $nom . '%']);
    $resultats = $stmt->fetchAll();

    if ($resultats) {
        foreach ($resultats as $r) {
            echo "<div style='border:1px solid #ccc; padding:10px; margin:10px 0'>";
            echo "<strong>Médicament :</strong> " . htmlspecialchars($r['nom_medicament']) . "<br>";
            echo "<strong>Pharmacie :</strong> " . htmlspecialchars($r['nom_pharmacie']) . "<br>";
            echo "<strong>Adresse :</strong> " . htmlspecialchars($r['adresse']) . "<br>";
            echo "<strong>Téléphone :</strong> " . htmlspecialchars($r['telephone']) . "<br>";
            echo "<strong>Email :</strong> " . htmlspecialchars($r['email']) . "<br>";
            echo "<strong>Prix :</strong> " . htmlspecialchars($r['prix']) . " FCFA<br>";
            echo "<strong>Quantité dispo :</strong> " . $r['quantite'] . "<br>";
           echo "<button class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#commandeModal'
                    data-nom='" . htmlspecialchars($r['nom_medicament']) . "'
                    data-pharmacie='" . htmlspecialchars($r['nom_pharmacie']) . "'
                    data-telephone='" . htmlspecialchars($r['telephone']) . "'
                    data-email='" . htmlspecialchars($r['email']) . "'
                    data-prix='" . htmlspecialchars($r['prix']) . "'>
                    Commander
                </button>";

            echo "</div>";
        }
    } else {
        echo "Aucun médicament trouvé.";
    }
}
?>
