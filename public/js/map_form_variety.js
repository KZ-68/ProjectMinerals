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
    let pos = e.latlng

    // Ajout d'un marqueur
        addMarker(pos)

    // Affiche les coordonnées correspondantes dans le formulaire
    document.querySelector("#variety_latitude").value = pos.lat // Coordonées de latitude
    document.querySelector("#variety_longitude").value = pos.lng // Coordonnées de longitude
}

// Fonction d'ajout de marqueur
function addMarker(pos) {
    // Condition si un marqueur existe

    marqueur = L.marker(pos, {
        // Autorise le déplacement du marqueur
        draggable: true
    })

    if (marqueur.value == pos ) {
        mymap.removeLayer(marqueur)
    }

    marqueur.on("draggend", function (e) {
        pos = e.target.getLatLng()
        document.querySelector("#variety_latitude").value = pos.lat
        document.querySelector("#variety_longitude").value = pos.lng
    })

    marqueur.addTo(mymap) // Ajout du marqueur à la map
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
                    addMarker(pos)
                }
                
                mymap.setView(pos, 6)
                console.log(pos)
            }
        }
    }

    xmlhttp.open("get", `https://geocode.maps.co/search?q=${region}`)

    xmlhttp.send()

}