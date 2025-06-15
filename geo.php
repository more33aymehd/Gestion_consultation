<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Pharmacies proches</title>
  <style>
    * { box-sizing: border-box; }

    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    h2 {
      text-align: center;
      color: #007bff;
      padding: 20px;
      margin: 0;
    }

    .container {
      display: flex;
      flex-direction: row;
      height: calc(100vh - 80px);
    }

    #map {
      flex: 1;
      height: 100%;
    }

    #pharmacies {
      flex: 1;
      overflow-y: auto;
      padding: 20px;
      background: #f8f9fa;
    }

    #pharmacies ul {
      list-style: none;
      padding: 0;
    }

    #pharmacies li {
      background: white;
      padding: 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: background 0.2s;
    }

    #pharmacies li:hover {
      background: #e9f5ff;
    }

    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      #map {
        height: 300px;
      }
    }
  </style>
</head>
<body>

  <h2>📍 Pharmacies proches de votre position</h2>

  <div class="container">
    <div id="map"></div>
    <div id="pharmacies"><p>Chargement des pharmacies...</p></div>
  </div>

  <script>
    let map;
    const markers = [];
    const infos = [];

    function initMap() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
          const userLocation = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };

          map = new google.maps.Map(document.getElementById("map"), {
            center: userLocation,
            zoom: 13
          });

          new google.maps.Marker({
            position: userLocation,
            map,
            icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png",
            title: "Votre position"
          });

          fetch("pharmacies.php")
            .then(res => res.json())
            .then(data => {
              const listContainer = document.getElementById("pharmacies");
              if (data.length === 0) {
                listContainer.innerHTML = "<p>Aucune pharmacie trouvée.</p>";
              } else {
                let html = "<ul>";
                data.forEach((pharmacy, index) => {
                  const pos = {
                    lat: parseFloat(pharmacy.latitude),
                    lng: parseFloat(pharmacy.longitude)
                  };

                  const marker = new google.maps.Marker({
                    position: pos,
                    map,
                    title: pharmacy.nom
                  });

                  const info = new google.maps.InfoWindow({
                    content: `<strong>${pharmacy.nom}</strong><br>${pharmacy.adresse}<br>Tél: ${pharmacy.telephone}`
                  });

                  marker.addListener("click", () => {
                    infos.forEach(iw => iw.close());
                    info.open(map, marker);
                  });

                  markers.push(marker);
                  infos.push(info);

                  html += `
                    <li data-index="${index}">
                      <strong>${pharmacy.nom}</strong><br>
                      ${pharmacy.adresse}<br>
                      Tél : ${pharmacy.telephone}
                    </li>
                  `;
                });
                html += "</ul>";
                listContainer.innerHTML = html;

                // Ajouter les événements de clic sur la liste
                document.querySelectorAll('#pharmacies li').forEach(item => {
                  item.addEventListener('click', function () {
                    const index = parseInt(this.getAttribute('data-index'));
                    const marker = markers[index];
                    const info = infos[index];

                    infos.forEach(iw => iw.close()); // Fermer tous les autres
                    map.panTo(marker.getPosition());
                    map.setZoom(15);
                    info.open(map, marker);
                  });
                });
              }
            });

        }, () => {
          alert("Impossible de localiser votre position.");
        });
      } else {
        alert("La géolocalisation n'est pas supportée par votre navigateur.");
      }
    }
  </script>

  <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCx6_tEAJWH1neOhEwR7seSg6cHPclKREg&callback=initMap">
  </script>

</body>
</html>
