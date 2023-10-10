let description = document.getElementById("home-description");
let lightbox = document.getElementById("lightbox1");
let imageZoom = document.getElementById("image-zoom1");

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