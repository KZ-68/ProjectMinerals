function topFunction() {
    document.body.scrollTop = 0; // Safari
    document.documentElement.scrollTo({
        top: 0,
        behavior: "smooth"
    }); // Chrome, Firefox, IE et Opera
}