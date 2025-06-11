<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Gestion Santé</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color:rgb(212, 0, 0);
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 54px;
        }

        .container {
            display: flex;
        }

        .sidebar {
            width: 143px;
            background-color:rgb(255, 217, 217);
            padding: 20px;
            height: 130vh;
        }

        .sidebar a {
            display: block;
            margin: 15px 0;
            text-decoration: none;
            color:rgb(0, 0, 0);
            font-weight: bold;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        h2 {
            color:rgb(0, 0, 0);
            text-align: center;
        }

        .btn {
            padding: 8px 16px;
            background-color:rgb(48, 151, 0);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .form-container {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            display: none;
            background-color: #f9f9f9;
        }

        .form-container input,
        .form-container textarea {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 8px;
        }

        .action-btns button {
            margin-right: 5px;
        }

        .patient-card {
            width: 250px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .patient-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .patient-card .info {
            padding: 10px;
        }

        .patient-card .info h3 {
            margin: 5px 0;
            font-size: 18px;
            color:rgb(0, 0, 0);
            text-align: center;
        }

        .patient-card .info p {
            margin: 5px 0;
            font-size: 14px;
        }

        .patient-card button {
            background-color: #e63946;
            color: white;
            border: none;
            width: 100%;
            padding: 8px;
            cursor: pointer;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }

        .consultation-box {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #fefefe;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .consultation-box h3 {
            margin-bottom: 5px;
            color: #0c4b91;
        }
        .consultation-box .meta {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
        }
        .consultation-box .contenu {
            font-size: 15px;
        }

        .ms{
            background-color: white;
            border: none;
        }
        .ms:hover{
            transition: 0.2s;
            transform: scale(0.8);
            cursor: pointer
        }
        .h2{
            display: flex;
            text-align: center;
            align-items: center;
            justify-content: center;
        }
        .active:focus{
            background-color: red;
            text-align: center;
            border-radius: 2rem;
            padding: 0.3rem;
        }
        .head{
            display: inline-block;
        }
        .search{
            padding: 0.4rem;
            border-radius: 1rem;
            border: 1px solid white;
        }
    </style>
</head>
<body>

<header>
    <div class="head">
        + Allo Doc
    </div>
    <div class="search-bar">
        <input type="text" id="input-recherche" placeholder="Recherche..." oninput="filtrerTous()" class="search">
    </div>
</header>

<div class="container">
    <nav class="sidebar">
        <a href="#" onclick="afficherSection('medecins')" class="active">Médecins</a>
        <a href="#" onclick="afficherSection('patients')" class="active">Patients</a>
        <a href="#" onclick="afficherSection('consultations')" class="active">Consultations</a>
        <a href="#" onclick="afficherSection('hopitaux')" class="active">Hôpitaux</a>
        <a href="#" onclick="afficherSection('pharmacies')" class="active">Pharmacies</a>
        <a href="#">Déconnexion</a>
    </nav>

    <div class="content">
        <div id="section-medecins">
            <h2>Médecins</h2>
            <div class="h2">
                <button class="btn" onclick="document.getElementById('form-medecin').style.display='block'">Ajouter un médecin</button>
            </div>

            <div id="form-medecin" class="form-container">
                <h3>Ajouter un médecin</h3>
                <form id="ajoutMedecinForm" method="POST">
                    <input type="text" name="nom" placeholder="Nom complet" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <textarea name="adresse" placeholder="Adresse"></textarea>
                    <input type="text" name="telephone" placeholder="Téléphone">
                    <input type="text" name="specialite" placeholder="Spécialité">
                    <input type="text" name="affiliation" placeholder="Affiliation (hôpital)">
                    <input type="text" name="photo" placeholder="Nom du fichier image">
                    <input type="text" name="mot_de_passe" placeholder="Mot de passe">
                    <input type="number" name="tarif" placeholder="Tarif consultation">

                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-medecin').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <div id="form-modifier-medecin" class="form-container">
                <h3>Modifier un médecin</h3>
                <form id="modifierMedecinForm">
                    <input type="hidden" name="id_medecin">
                    <input type="text" name="nom" placeholder="Nom complet" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <textarea name="adresse" placeholder="Adresse"></textarea>
                    <input type="text" name="telephone" placeholder="Téléphone">
                    <input type="text" name="specialite" placeholder="Spécialité">
                    <input type="text" name="affiliation" placeholder="Affiliation (hôpital)">
                    <input type="text" name="photo" placeholder="Nom du fichier image">
                    <input type="text" name="mot_de_passe" placeholder="Mot de passe">
                    <input type="number" name="tarif" placeholder="Tarif consultation">
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-modifier-medecin').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <table id="table-medecins">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Spécialité</th>
                        <th>Affiliation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Médecins chargés dynamiquement -->
                </tbody>
            </table>
        </div>

        <!-- Section Hôpitaux -->
        <div id="section-hopitaux" style="display: none;">
            <h2>Hôpitaux</h2>

            <div class="h2">
                <button class="btn" onclick="document.getElementById('form-hopital').style.display='block'">Ajouter un hôpital</button>
            </div>

            <div id="form-hopital" class="form-container">
                <h3>Ajouter un hôpital</h3>
                <form id="ajoutHopitalForm">
                    <input type="text" name="nom" placeholder="Nom de l'hôpital" required>
                    <textarea name="adresse" placeholder="Adresse" required></textarea>
                    <input type="text" name="telephone" placeholder="Téléphone">
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-hopital').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <!-- Formulaire modification -->
            <div id="form-modifier-hopital" class="form-container">
                <h3>Modifier un hôpital</h3>
                <form id="modifierHopitalForm">
                    <input type="hidden" name="id_hopital">
                    <input type="text" name="nom" required>
                    <textarea name="adresse" required></textarea>
                    <input type="text" name="telephone">
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-modifier-hopital').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <table id="table-hopitaux">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        
        <div id="section-pharmacies" style="display: none;">
            <h2>Pharmacies</h2>

            <div class="h2">
                <button class="btn" onclick="document.getElementById('form-pharmacie').style.display='block'">Ajouter une pharmacie</button>
            </div>

            <div id="form-pharmacie" class="form-container">
                <h3>Ajouter une pharmacie</h3>
                <form id="ajoutPharmacieForm">
                    <input type="text" name="nom" placeholder="Nom de la pharmacie" required>
                    <textarea name="adresse" placeholder="Adresse" required></textarea>
                    <input type="text" name="telephone" placeholder="Téléphone">
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-pharmacie').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <!-- Formulaire modification -->
            <div id="form-modifier-pharmacie" class="form-container">
                <h3>Modifier une pharmacie</h3>
                <form id="modifierPharmacieForm">
                    <input type="hidden" name="id_pharmacy">
                    <input type="text" name="nom" required>
                    <textarea name="adresse" required></textarea>
                    <input type="text" name="telephone">
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn">Enregistrer les modifications</button>
                        <button type="button" class="btn" onclick="document.getElementById('form-modifier-pharmacie').style.display='none'">Annuler</button>
                    </div>
                </form>
            </div>

            <table id="table-pharmacies">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Adresse</th>
                        <th>Téléphone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Section Patients -->
        <div id="section-patients" style="display: none;">
            <h2>Patients</h2>
            <div id="liste-patients" style="display: flex; flex-wrap: wrap; gap: 20px; margin-top: 20px;"></div>
        </div>

        <!-- Section Consultations -->
        <div id="section-consultations" style="display: none;">
            <h2>Consultations</h2>
            <div id="liste-consultations" style="margin-top: 20px;"></div>
        </div>

        <!-- Résultats de recherche -->
        <div id="section-recherche" style="display: none;">
            <h2>Résultats de la recherche</h2>
            <div id="resultats-recherche"></div>
        </div>

    </div>
</div>

<script>
    function afficherSection(section) {

        const sections = ['medecins', 'hopitaux', 'pharmacies', 'patients', 'consultations'];

        sections.forEach(s => {
            const el = document.getElementById('section-' + s);
            if (el) el.style.display = 'none';
        });

        const sectionDemandee = document.getElementById('section-' + section);
        if (sectionDemandee) sectionDemandee.style.display = 'block';

        switch (section) {
            case 'hopitaux':
                chargerHopitaux();
                break;
            case 'pharmacies':
                chargerPharmacies();
                break;
            case 'patients':
                chargerPatients();
                break;
            case 'consultations':
                chargerConsultations();
                break;
        }
    }


    // Charger tous les médecins
    function chargerMedecins() {
        fetch('liste_medecins.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('#table-medecins tbody');
                tbody.innerHTML = '';
                data.forEach(m => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${m.nom}</td>
                        <td>${m.email}</td>
                        <td>${m.specialite}</td>
                        <td>${m.affiliation}</td>
                        <td class="action-btns">
                            <button onclick="modifierMedecin(${m.id_medecin})" class="ms"><img src="modifier.png"></button>
                            <button onclick="supprimerMedecin(${m.id_medecin})" class="ms"><img src="delete.png"></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }

    document.getElementById("ajoutMedecinForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        fetch("ajouter_medecin.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if (data.includes("success")) {
                form.reset();
                document.getElementById("form-medecin").style.display = "none";
                chargerMedecins();
            } else {
                alert("Erreur : " + data);
            }
        });
    });

    // Charger les médecins au démarrage
    chargerMedecins();

    function supprimerMedecin(id) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce médecin ?")) {
        fetch("supprimer_medecin.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "id=" + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(data => {
            if (data.includes("success")) {
                chargerMedecins(); // recharge la liste sans recharger la page
            } else {
                alert("Erreur : " + data);
            }
        });
    }
    }

    function modifierMedecin(id) {
    fetch('liste_medecins.php')
        .then(res => res.json())
        .then(data => {
            const medecin = data.find(m => m.id_medecin == id);
            const form = document.getElementById("modifierMedecinForm");

            form.id_medecin.value = medecin.id_medecin;
            form.nom.value = medecin.nom;
            form.email.value = medecin.email;
            form.adresse.value = medecin.adresse || '';
            form.telephone.value = medecin.telephone || '';
            form.specialite.value = medecin.specialite || '';
            form.affiliation.value = medecin.affiliation || '';
            form.photo.value = medecin.photo || '';
            form.mot_de_passe.value = medecin.mot_de_passe || '';
            form.tarif.value = medecin.tarif || '';

            document.getElementById("form-modifier-medecin").style.display = "block";
            document.getElementById("form-medecin").style.display = "none";
        });
    }

    document.getElementById("modifierMedecinForm").addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(e.target);

        fetch("modifier_medecin.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(data => {
            if (data.includes("success")) {
                e.target.reset();
                document.getElementById("form-modifier-medecin").style.display = "none";
                chargerMedecins();
            } else {
                alert("Erreur : " + data);
            }
        });
    });


    function chargerHopitaux() {
        fetch('liste_hopitaux.php')
            .then(res => res.json())
            .then(data => {
                const tbody = document.querySelector('#table-hopitaux tbody');
                tbody.innerHTML = '';
                data.forEach(h => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${h.nom}</td>
                        <td>${h.adresse}</td>
                        <td>${h.telephone || ''}</td>
                        <td>
                            <button onclick="modifierHopital(${h.id_hopital})" class="ms"><img src="modifier.png"></button>
                            <button onclick="supprimerHopital(${h.id_hopital})" class="ms"><img src="delete.png"></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
    }

        document.getElementById("ajoutHopitalForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("ajouter_hopital.php", { method: "POST", body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        e.target.reset();
                        document.getElementById("form-hopital").style.display = "none";
                        chargerHopitaux();
                    } else alert("Erreur : " + data);
                });
        });

        function modifierHopital(id) {
            fetch("liste_hopitaux.php")
                .then(res => res.json())
                .then(data => {
                    const hopital = data.find(h => h.id_hopital == id);
                    const form = document.getElementById("modifierHopitalForm");
                    form.id_hopital.value = hopital.id_hopital;
                    form.nom.value = hopital.nom;
                    form.adresse.value = hopital.adresse;
                    form.telephone.value = hopital.telephone || '';
                    document.getElementById("form-modifier-hopital").style.display = "block";
                    document.getElementById("form-hopital").style.display = "none";
                });
        }

        document.getElementById("modifierHopitalForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("modifier_hopital.php", { method: "POST", body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        e.target.reset();
                        document.getElementById("form-modifier-hopital").style.display = "none";
                        chargerHopitaux();
                    } else alert("Erreur : " + data);
                });
        });

        function supprimerHopital(id) {
            if (confirm("Supprimer cet hôpital ?")) {
                fetch("supprimer_hopital.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id)
                })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        chargerHopitaux();
                    } else {
                        alert("Erreur : " + data);
                    }
                });
            }
        }

        function chargerPharmacies() {
            fetch('liste_pharmacies.php')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector('#table-pharmacies tbody');
                    tbody.innerHTML = '';
                    data.forEach(p => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${p.nom}</td>
                            <td>${p.adresse}</td>
                            <td>${p.telephone || ''}</td>
                            <td>
                                <button onclick="modifierPharmacie(${p.id_pharmacy})" class="ms"><img src="modifier.png"></button>
                                <button onclick="supprimerPharmacie(${p.id_pharmacy})" class="ms"><img src="delete.png"></button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                });
        }

        document.getElementById("ajoutPharmacieForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("ajouter_pharmacie.php", { method: "POST", body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        e.target.reset();
                        document.getElementById("form-pharmacie").style.display = "none";
                        chargerPharmacies();
                    } else alert("Erreur : " + data);
                });
        });

        function modifierPharmacie(id) {
            fetch("liste_pharmacies.php")
                .then(res => res.json())
                .then(data => {
                    const p = data.find(p => p.id_pharmacy == id);
                    const form = document.getElementById("modifierPharmacieForm");
                    form.id_pharmacy.value = p.id_pharmacy;
                    form.nom.value = p.nom;
                    form.adresse.value = p.adresse;
                    form.telephone.value = p.telephone || '';
                    document.getElementById("form-modifier-pharmacie").style.display = "block";
                    document.getElementById("form-pharmacie").style.display = "none";
                });
        }

        document.getElementById("modifierPharmacieForm").addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(e.target);

            fetch("modifier_pharmacie.php", { method: "POST", body: formData })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        e.target.reset();
                        document.getElementById("form-modifier-pharmacie").style.display = "none";
                        chargerPharmacies();
                    } else alert("Erreur : " + data);
                });
        });

        function supprimerPharmacie(id) {
            if (confirm("Supprimer cette pharmacie ?")) {
                fetch("supprimer_pharmacie.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id)
                })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        chargerPharmacies();
                    } else alert("Erreur : " + data);
                });
            }
        }function chargerPatients() {
            fetch('liste_patients.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('liste-patients');
                    container.innerHTML = '';
                    data.forEach(p => {
                        const card = document.createElement('div');
                        card.className = 'patient-card';
                        card.innerHTML = `
                            <img src="images/${p.photo}" alt="${p.nom}">
                            <div class="info">
                                <h3>${p.nom}</h3>
                                <p><strong>Maladie :</strong> ${p.maladie}</p>
                                <p><strong>Statut :</strong> ${p.statut}</p>
                            </div>
                            <button onclick="supprimerPatient(${p.id_patient})">Supprimer</button>
                        `;
                        container.appendChild(card);
                    });
                });
        }

        function supprimerPatient(id) {
            if (confirm("Supprimer ce patient ?")) {
                fetch("supprimer_patient.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id=" + encodeURIComponent(id)
                })
                .then(res => res.text())
                .then(data => {
                    if (data.includes("success")) {
                        chargerPatients();
                    } else alert("Erreur : " + data);
                });
            }
        }

        function chargerConsultations() {
            fetch('liste_consultations.php')
                .then(res => res.json())
                .then(data => {
                    const container = document.getElementById('liste-consultations');
                    container.innerHTML = '';

                    if (data.length === 0) {
                        container.innerHTML = "<p>Aucune consultation trouvée.</p>";
                        return;
                    }

                    data.forEach(c => {
                        const div = document.createElement('div');
                        div.className = 'consultation-box';
                        div.innerHTML = `
                            <h3>Consultation ${c.id_consultation}</h3>
                            <div class="meta">
                                <strong>Patient :</strong> ${c.nom_patient} |
                                <strong>Médecin :</strong> ${c.nom_medecin} |
                                <strong>Prix :</strong> ${c.prix} FCFA |
                                <strong>Date :</strong> ${c.date}
                            </div>
                            <div class="contenu">
                                ${c.contenu}
                            </div>
                        `;
                        container.appendChild(div);
                    });
                });
        }

        function filtrerTous() {
            const val = document.getElementById("input-recherche").value.toLowerCase();

            // 🔍 Médecins
            document.querySelectorAll("#table-medecins tbody tr").forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(val) ? "" : "none";
            });

            // 🔍 Hôpitaux
            document.querySelectorAll("#table-hopitaux tbody tr").forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(val) ? "" : "none";
            });

            // 🔍 Pharmacies
            document.querySelectorAll("#table-pharmacies tbody tr").forEach(tr => {
                tr.style.display = tr.textContent.toLowerCase().includes(val) ? "" : "none";
            });

            // 🔍 Patients (cards)
            document.querySelectorAll("#liste-patients .patient-card").forEach(card => {
                card.style.display = card.textContent.toLowerCase().includes(val) ? "" : "none";
            });

            // 🔍 Consultations
            document.querySelectorAll("#liste-consultations .consultation-box").forEach(box => {
                box.style.display = box.textContent.toLowerCase().includes(val) ? "" : "none";
            });
        }

</script>

</body>
</html>