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
              $.each(response.data, function(index, result) {
                  const resultItem = $('<div>').html(result.name);
                  resultsContainer.append(resultItem);
              });
              $('.adv-search').css('height', '1240px');
              $('.adv-search-fields').css('height', '1209px');
              resultsContainer.css('margin', '17px 0px');
          },
          error: function (error) {
            console.error('Erreur lors de la recherche :', error);
          }
      });
  });
});