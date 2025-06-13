<?php
require_once 'config.php';
require_once 'languages.php';

$language = isset($_GET['lang']) ? $_GET['lang'] : 'fr';
if (!in_array($language, ['fr', 'en', 'sw'])) {
    $language = 'fr';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getTranslation('welcome', $language); ?> - Gestion Hospitalière</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <h1><?php echo getTranslation('welcome', $language); ?> - Plateforme de Gestion Hospitalière</h1>
    </header>
    <div class="slider">
        <div class="slider-item active" style="background-image: url('assets/images/banner1.jpg');">
            <h2><?php echo getTranslation('welcome', $language); ?></h2>
            <p>Prenez rendez-vous en quelques clics avec des médecins qualifiés.</p>
        </div>
        <div class="slider-item" style="background-image: url('assets/images/banner2.jpg');">
            <h2>Accès facile aux soins</h2>
            <p>Trouvez des hôpitaux près de chez vous pour tous vos besoins médicaux.</p>
        </div>
        <div class="slider-item" style="background-image: url('assets/images/banner3.jpg');">
            <h2>Confidentialité garantie</h2>
            <p>Vos données médicales sont sécurisées et protégées.</p>
        </div>
    </div>
    <nav>
        <ul>
            <li><a href="index.php?lang=<?php echo $language; ?>"><?php echo getTranslation('welcome', $language); ?></a></li>
            <li><a href="register.php?lang=<?php echo $language; ?>"><?php echo getTranslation('register', $language); ?></a></li>
            <li><a href="patient_login.php?lang=<?php echo $language; ?>"><?php echo getTranslation('login', $language); ?> Patient</a></li>
            <li><a href="doctor_login.php?lang=<?php echo $language; ?>"><?php echo getTranslation('login', $language); ?> Médecin</a></li>
            <li>
                <select onchange="window.location.href='index.php?lang='+this.value">
                    <option value="fr" <?php echo $language == 'fr' ? 'selected' : ''; ?>>Français</option>
                    <option value="en" <?php echo $language == 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="sw" <?php echo $language == 'sw' ? 'selected' : ''; ?>>Swahili</option>
                </select>
            </li>
        </ul>
    </nav>
    <main>
        <section>
            <h2><?php echo getTranslation('our_services', $language); ?></h2>
            <div class="card-container">
                <div class="card">
                    <img src="assets/images/service1.jpg" alt="Consultation">
                    <h3>Consultation générale</h3>
                    <p>Rencontrez des médecins qualifiés pour vos besoins quotidiens.</p>
                </div>
                <div class="card">
                    <img src="assets/images/service2.jpg" alt="Paludisme">
                    <h3>Traitement du paludisme</h3>
                    <p>Diagnostics et traitements rapides pour le paludisme.</p>
                </div>
                <div class="card">
                    <img src="assets/images/service3.jpg" alt="Addictions">
                    <h3>Suivi des addictions</h3>
                    <p>Accompagnement personnalisé pour surmonter les addictions.</p>
                </div>
            </div>
            <a href="patient_login.php?lang=<?php echo $language; ?>" class="button"><?php echo getTranslation('start_now', $language); ?></a>
        </section>

        <section>
    <h2>Trouver un hôpital adapté</h2>
    <form id="hospital-form">
        <label for="problem">Décrivez votre problème médical :</label>
        <input type="text" id="problem" name="problem" required>
        <button type="submit">Rechercher</button>
    </form>
    <div id="hospital-list"></div>
</section>
<script>
    // Liste statique des hôpitaux avec spécialités
    const hospitals = [
        { name: "Hôpital Général de Dakar", address: "Dakar, Sénégal", specialties: ["cardiologie", "pneumologie", "général"] },
        { name: "Clinique Pasteur", address: "Dakar, Sénégal", specialties: ["orthopédie", "gynécologie"] },
        { name: "Hôpital Fann", address: "Dakar, Sénégal", specialties: ["neurologie", "psychiatrie"] }
    ];

    // Simulation d'IA pour suggérer des hôpitaux
    function suggestHospitals(problem) {
        problem = problem.toLowerCase();
        let suggestions = hospitals.filter(h => 
            h.specialties.some(s => problem.includes(s)) || problem.includes("général")
        );
        
        if (suggestions.length === 0) {
            suggestions = hospitals; // Par défaut, tous les hôpitaux
        }

        return suggestions;
    }

    // Gestion du formulaire
    document.getElementById('hospital-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const problem = document.getElementById('problem').value;
        const suggestions = suggestHospitals(problem);
        const hospitalList = document.getElementById('hospital-list');
        hospitalList.innerHTML = '<h3>Hôpitaux suggérés :</h3>' + 
            suggestions.map(h => `<p>${h.name} - ${h.address}</p>`).join('');
    });
</script>
    </main>
    <script src="script.js"></script>
</body>
</html>