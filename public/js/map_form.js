let mymap // Variable de la map et du marqueur
let countryName = document.querySelector('#mineral_country_name');
let editCountryName = document.querySelector('#edit_mineral_country_name');
let coordinates = document.querySelectorAll('.coordinate-data');
let formular = document.querySelector('.formulaire');

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
    if(countryName) {
        document.querySelector("#mineral_latitude").value = lat // Coordonées de latitude
        document.querySelector("#mineral_longitude").value = lon // Coordonnées de longitude
    } else {
        document.querySelector("#edit_mineral_latitude").value = lat // Coordonées de latitude
        document.querySelector("#edit_mineral_longitude").value = lon // Coordonnées de longitude
    }
    
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
                        marker = new L.marker(pos, {
                            draggable: false
                        })
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
                        marker = new L.marker(pos, {
                            draggable: false
                        })
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

function onPopupOpen() {

    var tempMarker = this;

    $(".marker-delete-button:visible").click(function () {
        mymap.removeLayer(tempMarker);
    });
}