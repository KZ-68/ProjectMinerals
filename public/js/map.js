let coordinates = document.querySelectorAll('.coordinate-data')
    let mymap// Variable de la map
        window.onload = () => {
            mymap = L.map('mineral-map').setView([51.505, -0.09], 4); // Position sur la map par défaut
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

                let marker = new L.marker([lat, lng])
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
        
        } 