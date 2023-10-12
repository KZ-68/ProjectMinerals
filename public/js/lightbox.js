let description = document.getElementById("home-description");
let lightbox = document.getElementById("lightbox1");
let lightbox2 = document.querySelectorAll(".lightbox2");
let imageZoom = document.getElementById('image-zoom1');
let imageZoom2 = document.querySelectorAll(".image-zoom2");
let mineralImages = document.querySelectorAll("mineral-image");
let modals = document.querySelectorAll("div.images-list-modal");

if(description) {
    description.addEventListener("click", function() {
        let active = imageZoom.classList.toggle("active");
        if (active) {
            lightbox.style.backgroundColor = "rgba(0, 0, 0, 0.7)";  
            lightbox.style.display = 'block';
        } else {
            lightbox.style.display = 'none';
            lightbox.style.backgroundColor = "unset";  
        }
    });
}

if(modals.length > 1) {
    modals.forEach(modal => {
        modal.addEventListener("click", function() {
            imageZoom2.forEach(imageZoomEach => {
                let active1 = imageZoomEach.classList.toggle("active");
                lightbox2.forEach(lightbox2Each => {
                    if (active1) {
                        lightbox2Each.style.backgroundColor = "rgba(0, 0, 0, 0.7)";  
                        lightbox2Each.style.display = 'block';
                    } 
                    lightbox2Each.addEventListener("click", function() {
                        let active2 = imageZoomEach.classList.toggle("active");
                        if (!active2) {
                            lightbox2Each.style.display = 'none';
                            lightbox2Each.style.backgroundColor = "unset";  
                        } 
                    });
                })
            })
            
        });
    });
}
