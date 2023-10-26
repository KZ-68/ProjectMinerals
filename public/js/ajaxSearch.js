$(document).ready(function() {
    $('#mineral-search-form').submit(function(event) {
        event.preventDefault();
  
        var formData = $(this).serialize();
        
        $.ajax({
            type: 'POST',
            url: '/wiki/mineral', 
            data: formData,
            success: function(response) {
                const resultsContainer = $('#minerals-list');
                resultsContainer.empty();
                $.each(response.data, function(index, result) {
                    const resultItem = $('<li>').html(result.name);
                    resultsContainer.append(resultItem);
                    resultsContainer.append('</li>');
                });
                resultsContainer.css('margin', '17px 0px');
            },
            error: function (error) {
              console.error('Erreur lors de la recherche :', error);
            }
        });
    });
  });