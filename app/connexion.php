<?php
// connexion.php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $mdp = $_POST['mot_de_passe'] ?? '';

    if ($nom && $mdp) {
        // Recherche par nom (attention si noms non uniques !)
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE nom = ?");
        $stmt->execute([$nom]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient && password_verify($mdp, $patient['mot_de_passe'])) {
            if ($patient['statut'] !== 'actif') {
                $erreur = "Votre compte n'est pas actif.";
            } else {
                $_SESSION['id_patient'] = $patient['id_patient'];
                $_SESSION['nom_patient'] = $patient['nom'];
                header("Location: recherche_medecins.php");
                exit;
            }
        } else {
            $erreur = "Nom ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<form method="post">
    Nom : <input type="text" name="nom" required><br>
    Mot de passe : <input type="password" name="mot_de_passe" required><br>
    <button type="submit">Se connecter</button>
</form>

<?php if (!empty($erreur)): ?>
    <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
<?php endif; ?>
