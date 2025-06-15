function afficherPopup(message) {
    alert(message); // Remplace par une modale si besoin
}

function demanderRappel(ordonnance) {
    fetch("api/generer_rappel.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ ordonnance: ordonnance })
    })
    .then(res => res.json())
    .then(data => {
        if (data.message) {
            afficherPopup(data.message);
        }
    })
    .catch(err => console.error("Erreur IA :", err));
}

// Exemple d’appel automatique
window.addEventListener("load", function () {
    demanderRappel("Prends 1 comprimé de Doliprane après le repas.");
});
