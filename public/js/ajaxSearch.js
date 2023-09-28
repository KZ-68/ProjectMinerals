$(document).ready(function () {
  $('#mineral-advancedSearch-form').submit(function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.ajax({
      type: 'POST',
      url: '/home',
      data: formData,
      success: function (data) {
          const resultsContainer = $('#search-results');
          resultsContainer.empty();
          if (data.results && data.results.length > 0) {
              data.results.forEach(function (result) {
                  const resultItem = $('<div>').html(result.name)
                  resultsContainer.append(resultItem);
              });
          } else {
              // Aucun résultat trouvé, affichez un message approprié
              resultsContainer.append('<p>Aucun résultat trouvé.</p>');
          }
      },
      error: function (error) {
          console.error('Erreur lors de la recherche :', error);
      }
    });
  });
});