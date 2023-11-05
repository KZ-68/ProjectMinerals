$(document).ready(function() {
  $('#search-form').submit(function(event) {
      event.preventDefault(); 
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
              if(window.matchMedia("(max-width : 480px)").matches) {
                $('.adv-search').css('height', '1600px');
                $('.adv-search-fields').css('height', '1600px');
              } else {
                $('.adv-search').css('height', '1240px');
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
      $('.adv-search').css('height', '1300px');
      $('.adv-search-fields').css('height', '1298px');
    } else {
      $('.adv-search').css('height', '765px');
      $('.adv-search-fields').css('height', '620px');
    }
    container.empty();
  } );
}

