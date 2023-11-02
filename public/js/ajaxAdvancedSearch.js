$(document).ready(function() {
  $('#search-form').submit(function(event) {
      event.preventDefault();
      let mobile = [
        window.matchMedia('(min-device-width: 320px) and (max-width: 480px)')
      ]

      var formData = $(this).serialize();

      $.ajax({
          type: 'POST',
          url: 'home', 
          data: formData,
          success: function(response) {
              const resultsContainer = $('#search-results');
              resultsContainer.empty();
              $.each(response.data, function(index, result) {
                  const resultItem = $('<div>').html(result.name);
                  resultsContainer.append(resultItem);
              });
              if(mobile.matches) {
                $('.adv-search').css('height', '1600px');
                $('.adv-search-fields').css('height', '1600px');
                $('#search-results').css('overflow-y', 'auto');
                $('#search-results').css('border', '2px solid #9affe2b0');
                $('#search-results').css('padding', '20px');
                resultsContainer.css('margin', '17px 0px');
              } else {
                $('.adv-search').css('height', '1240px');
                $('.adv-search-fields').css('height', '1209px');
                $('#search-results').css('overflow-y', 'auto');
                $('#search-results').css('border', '2px solid #9affe2b0');
                $('#search-results').css('padding', '20px');
                resultsContainer.css('margin', '17px 0px');
              }
              
          },
          error: function (error) {
            console.error('Erreur lors de la recherche :', error);
          }
      });
  });
});