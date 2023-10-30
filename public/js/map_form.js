let mymap, marker, oldmarker // Variable de la map et du marqueur
oldmarker = marker;
let countryName = document.querySelector('#mineral_country_name');
let editCountryName = document.querySelector('#edit_mineral_country_name');
let coordinates = document.querySelectorAll('.coordinate-data');

window.onload = () => {
    mymap = L.map('mineral-map').setView([51.505, -0.09], 5); // Position sur la map par défaut
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        minZoom: 1,
        maxZoom: 20
    }).addTo(mymap) // Ajout des tuiles à la map
    
    for(coordinate in coordinates) { 
        if(!coordinates[coordinate].dataset) {
            break;
        }
        let lat = coordinates[coordinate].dataset.lat
        let lng = coordinates[coordinate].dataset.lng

        marker = new L.marker([lat, lng])
            .addTo(mymap);
            mymap.setView([lat, lng], 4)
            
        const xmlhttp = new XMLHttpRequest

        xmlhttp.onreadystatechange = () => {
            // Si la requête est terminée
            if(xmlhttp.readyState == 4) {
                if (xmlhttp.status = 200) {
                    let response = JSON.parse(xmlhttp.response)

                    let country = response['address']['country']
                    marker.bindPopup("<p>"+country+"</p>")
                }
            }
        }

        xmlhttp.open("get", "https://geocode.maps.co/reverse?lat="+lat+"&lon="+lng+"")

        xmlhttp.send()

    }
    
    mymap.on("click", mapClickListener) // Ajout de l'événement au clic sur la map
    
    if(countryName){
        countryName.addEventListener('blur', getCountry);
    } else {
        editCountryName.addEventListener('blur', getCountry);
    }
}

// Fonction de l'événement au clic
function mapClickListener(e) {
    // Récupère les coordonnées du clic
    let pos = e.latlng

    // Ajout d'un marqueur
    addMarker(pos)

    // Affiche les coordonnées correspondantes dans le formulaire
    if(countryName) {
        document.querySelector("#mineral_latitude").value = pos.lat // Coordonées de latitude
        document.querySelector("#mineral_longitude").value = pos.lng // Coordonnées de longitude
    } else {
        document.querySelector("#edit_mineral_latitude").value = pos.lat // Coordonées de latitude
        document.querySelector("#edit_mineral_longitude").value = pos.lng // Coordonnées de longitude
    }
    
}

// Fonction d'ajout de marqueur
function addMarker(pos) {
    
    // Condition si un marqueur existe
    marker = new L.marker(pos, {
        // Autorise le déplacement du marqueur
        draggable: true
    })

    marker.addTo(mymap) // Ajout du marqueur à la map
}

function getCountry() {

    if(countryName) {
        let country = countryName.value

        let xmlhttp = new XMLHttpRequest

        xmlhttp.onreadystatechange = () => {
            // Si la requête est terminée
            if(xmlhttp.readyState == 4) {
                if (xmlhttp.status = 200) {
                    let response = JSON.parse(xmlhttp.response)

                    let lat = response[0]["latlng"][0]
                    let lng = response[0]["latlng"][1]
                    document.querySelector("#mineral_latitude").value = lat
                    document.querySelector("#mineral_longitude").value = lng
                    let pos = [lat, lng]
                    for (let i = 0; i < pos.length; i++) {
                        addMarker(pos)
                    }
                    
                    mymap.setView(pos, 5)
                    console.log(pos)
                }
            }
        }

        xmlhttp.open("get", `https://restcountries.com/v3.1/name/${country}?format=json`)

        xmlhttp.send()
    } else {
        let country = editCountryName.value

        let xmlhttp = new XMLHttpRequest

        xmlhttp.onreadystatechange = () => {
            // Si la requête est terminée
            if(xmlhttp.readyState == 4) {
                if (xmlhttp.status = 200) {
                    let response = JSON.parse(xmlhttp.response)

                    let lat = response[0]["latlng"][0]
                    let lng = response[0]["latlng"][1]
                    document.querySelector("#edit_mineral_latitude").value = lat
                    document.querySelector("#edit_mineral_longitude").value = lng
                    let pos = [lat, lng]
                    for (let i = 0; i < pos.length; i++) {
                        addMarker(pos)
                    }
                    
                    mymap.setView(pos, 5)
                    console.log(pos)
                }
            }
        }

        xmlhttp.open("get", `https://restcountries.com/v3.1/name/${country}?format=json`)

        xmlhttp.send()
    }
    
}