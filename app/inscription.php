<?php
// inscription.php
$pdo = new PDO("mysql:host=localhost;dbname=gestion_sante;charset=utf8", "root", "");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $age = intval($_POST['age'] ?? 0);
    $sexe = $_POST['sexe'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $maladie = $_POST['maladie'] ?? '';
    $photo = $_POST['photo'] ?? ''; // ici, pour simplifier, on stocke juste un chemin ou URL
    $mdp = $_POST['mot_de_passe'] ?? '';

    if ($nom && $age >= 0 && ($sexe === 'M' || $sexe === 'F') && $email && $mdp) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $statut = 'actif'; // par défaut

        $stmt = $pdo->prepare("INSERT INTO patients 
            (nom, age, sexe, adresse, email, telephone, maladie, photo, mot_de_passe, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        try {
            $stmt->execute([$nom, $age, $sexe, $adresse, $email, $telephone, $maladie, $photo, $hash, $statut]);
            echo "Inscription réussie. Vous pouvez maintenant vous connecter.";
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Veuillez remplir correctement tous les champs obligatoires.";
    }
}
?>

<form method="post">
    Nom : <input type="text" name="nom" required><br>
    Age : <input type="number" name="age" min="0" required><br>
    Sexe : 
    <select name="sexe" required>
        <option value="">--Choisir--</option>
        <option value="M">Masculin</option>
        <option value="F">Féminin</option>
    </select><br>
    Adresse : <textarea name="adresse"></textarea><br>
    Email : <input type="email" name="email" required><br>
    Téléphone : <input type="text" name="telephone"><br>
    Maladie : <textarea name="maladie"></textarea><br>
    Photo (URL ou chemin) : <input type="text" name="photo"><br>
    Mot de passe : <input type="password" name="mot_de_passe" required><br>
    <button type="submit">S'inscrire</button>
</form>
