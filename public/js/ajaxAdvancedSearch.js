$(document).ready(function() {
  $('#search-form').submit(function(event) {
      event.preventDefault(); 
      var formData = $(this).serialize();

      $.ajax({
          type: 'POST', // Méthode HTTP
          url: 'home', // Le nom de la route
          data: formData, // Les données à envoyer
          success: function(response) { // En cas de succès, la réponse en argument
              // On sélectionne la div pour insérer les résultats
              const resultsContainer = $('#search-results');
              resultsContainer.empty(); // On vide les éléments déjà présent
              // On boucle sur les données de la réponse
              $.each(response.data, function(index, result) {
                  // On crée une balise ancre 
                  let resultItem = $('<a>');
                  // On lui affecte une classe css
                  resultItem.addClass('result-item');
                  // On met l'attribut href, l'url et le slug
                  let href = resultItem.attr('href', "wiki/mineral/"+result.slug+"/show");
                  if (window.location.href === "fr/home") {
                    // On ajoute le contenu textuel
                    href.html(result.name);
                    // On affiche la balise
                    resultsContainer.append(href);
                  } else {
                      href.html(result.name);
                      resultsContainer.append(href);
                  }
              });
              if(window.matchMedia("(max-width : 480px)").matches) {
                $('.adv-search').css('height', '1600px');
                $('.adv-search-fields').css('height', '1600px');
              } else if (window.matchMedia("(max-width : 1024px)").matches) {
                $('.adv-search').css('height', '1600px');
                $('.adv-search-fields').css('height', '1600px');
              } else {
                $('.adv-search').css('height', '1040px');
                $('.adv-search-fields').css('height', '1209px');
              }
              $('#search-results').css('overflow-y', 'auto');
              $('#search-results').css('border', '2px solid #9affe2b0');
              $('#search-results').css('padding', '20px');
              resultsContainer.css('margin', '17px 0px');

              resetBtn(resultsContainer);
          },
          error: function (error) {
            console.error('Erreur lors de la recherche :', error);
          }
      });
  });
});

function resetBtn(container) {
  let resetBtnClass = $('<a id="reset-search-btn" href="#">');
  resetBtnClass.text('Reset');
  container.append(resetBtnClass);
  resetBtnClass.on( "click", function(event) {
    event.preventDefault();
    if(window.matchMedia("(max-width : 480px)").matches) {
      $('.adv-search').css('height', '1450px');
      $('.adv-search-fields').css('height', '1298px');
    } else if(window.matchMedia("(max-width : 1024px)").matches) {
      $('.adv-search').css('height', '1540px');
      $('.adv-search-fields').css('height', '1298px');
    } else {
      $('.adv-search').css('height', '825px');
      $('.adv-search-fields').css('height', '620px');
    }
    container.empty();
  } );
}

