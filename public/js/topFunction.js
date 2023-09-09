let topBtn = document.querySelector(".topBtn");

if(document.body.scrollTop == 0) {
    topBtn.style.display = "none";
} 

window.onscroll = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
          topBtn.style.display = "block";
        } else {
          topBtn.style.display = "none";
        }
      }



function topFunction() {
    document.body.scrollTop = 0; // Safari
    document.documentElement.scrollTo({
        top: 0,
        behavior: "smooth"
    }); // Chrome, Firefox, IE et Opera
}