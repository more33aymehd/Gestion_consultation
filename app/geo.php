<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Pharmacies proches</title>
</head>
<body>
  <h2>Pharmacies proches de moi</h2>
  <button onclick="getLocation()">Afficher pharmacies proches</button>
  <div id="pharmacies"></div>

  <script>
  function getLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(success, error);
    } else {
      alert("La géolocalisation n'est pas supportée par ce navigateur.");
    }
  }

  function success(position) {
    const lat = position.coords.latitude;
    const lon = position.coords.longitude;

    // Envoi des coordonnées au serveur via fetch (AJAX)
    fetch('pharmacies_proches.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({latitude: lat, longitude: lon})
    })
    .then(response => response.json())
    .then(data => {
      let html = '<ul>';
      if (data.length === 0) {
        html = '<p>Aucune pharmacie proche trouvée.</p>';
      } else {
        data.forEach(pharma => {
          html += `<li><strong>${pharma.nom}</strong><br>${pharma.adresse}<br>Téléphone : ${pharma.telephone}<br>Distance : ${pharma.distance.toFixed(2)} km</li><hr>`;
        });
        html += '</ul>';
      }
      document.getElementById('pharmacies').innerHTML = html;
    });
  }

  function error() {
    alert("Impossible de récupérer votre position.");
  }
  </script>
</body>
</html>
