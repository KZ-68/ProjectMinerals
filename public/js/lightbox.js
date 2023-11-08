let description = document.getElementById("home-description");
let lightbox = document.getElementById("lightbox1");
let lightbox2 = document.querySelectorAll(".lightbox2");
let imageZoom = document.getElementById('image-zoom1');
let imageZoom2 = document.querySelectorAll(".image-zoom2");
let mineralImages = document.querySelectorAll("mineral-image");
let modals = document.querySelectorAll("div.images-list-modal");

function isDesktopScreen() {
    const mobiles = window.matchMedia("(min-device-width : 320px) and (max-device-width : 480px)");
    const tabletsPortrait = window.matchMedia("(min-device-width : 768px) and (max-device-width : 1023px) and (orientation: portrait)")
    const tabletsLandscape = window.matchMedia("(min-device-width : 768px) and (max-device-width : 1023px) and (orientation: landscape)")

    return !mobiles.matches && !tabletsPortrait.matches && !tabletsLandscape.matches;
}
    
if(description) {
    description.addEventListener("click", function() {

        if (!isDesktopScreen()) {
            return;
        }

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

if(modals.length > 0) {
    modals.forEach((modal, modalIndex) => {
        modal.addEventListener("click", function() {

            if (!isDesktopScreen()) {
                return;
            }

            const imageZoomEach = imageZoom2[modalIndex];
                let active1 = imageZoomEach.classList.toggle("active");

                    const lightbox2Each = lightbox2[modalIndex];
                    if (active1) {
                        lightbox2Each.style.backgroundColor = "#000000ed";  
                        lightbox2Each.style.display = 'block';
                    } 
                    lightbox2Each.addEventListener(
                        "click",
                        function() {
                            let active2 = imageZoomEach.classList.toggle("active");
                            if (!active2) {
                                lightbox2Each.style.display = 'none';
                            }
                        },
                        {
                            once: true
                        }
                    );

        });
    });
}





