$(document).ready(function() {
  $('#search-form').submit(function(event) {
      event.preventDefault();

      var formData = $(this).serialize();

      $.ajax({
          type: 'POST',
          url: '/home', 
          data: formData,
          dataType: 'json',
          success: function(response) {
              const resultsContainer = $('#search-results');
              resultsContainer.empty();
              $.each(response, function(index, result) {
                  
                  const resultItem = $('<div>').html(result.name)
                  resultsContainer.append(resultItem)
              });
          },
          error: function (error) {
            console.error('Erreur lors de la recherche :', error);
          }
      });
  });
});