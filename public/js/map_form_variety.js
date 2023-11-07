let mymap, marqueur // Variable de la map et du marqueur

window.onload = () => {
    mymap = L.map('mineral-map').setView([51.505, -0.09], 5); // Position sur la map par défaut
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        minZoom: 1,
        maxZoom: 20
    }).addTo(mymap) // Ajout des tuiles à la map
    mymap.on("click", mapClickListener) // Ajout de l'événement au clic sur la map
    document.querySelector('#variety_region_name').addEventListener('blur', getRegion)
}

// Fonction de l'événement au clic
function mapClickListener(e) {
    // Récupère les coordonnées du clic
    let lat = e.latlng.lat;
    let lon = e.latlng.lng;

    var geojsonFeature = {

        "type": "Feature",
        "properties": {},
        "geometry": {
                "type": "Point",
                "coordinates": [lat, lon]
        }
    }

    let marker;

    L.geoJson(geojsonFeature, {

        pointToLayer: function(feature, latlng){

            marker = L.marker([lat, lon], {

                title: "Resource Location",
                alt: "Resource Location",
                riseOnHover: true,
                draggable: true,

            }).bindPopup("<input type='button' value='Delete this marker' class='marker-delete-button'/>");

            marker.on("popupopen", onPopupOpen);

            return marker;
        }
    }).addTo(mymap);

    // Affiche les coordonnées correspondantes dans le formulaire
    document.querySelector("#variety_latitude").value = lat // Coordonées de latitude
    document.querySelector("#variety_longitude").value = lng // Coordonnées de longitude
}

function getRegion() {
    let region = document.querySelector('#variety_region_name').value

    let xmlhttp = new XMLHttpRequest

    xmlhttp.onreadystatechange = () => {
        // Si la requête est terminée
        if(xmlhttp.readyState == 4) {
            if (xmlhttp.status = 200) {
                let response = JSON.parse(xmlhttp.response)

                let lat = response[0]["lat"]
                let lon = response[0]["lon"]
                document.querySelector("#variety_latitude").value = lat
                document.querySelector("#variety_longitude").value = lon
                let pos = [lat, lon]
                for (let i = 0; i < pos.length; i++) {
                    marker = new L.marker(pos, {
                        draggable: false
                    })
                }
                
                mymap.setView(pos, 6)
                console.log(pos)
            }
        }
    }

    xmlhttp.open("get", `https://geocode.maps.co/search?q=${region}`)

    xmlhttp.send()

}

function onPopupOpen() {

    var tempMarker = this;

    $(".marker-delete-button:visible").click(function () {
        mymap.removeLayer(tempMarker);
    });
}