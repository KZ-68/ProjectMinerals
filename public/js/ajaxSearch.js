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
                    const resultList = $('<li>');
                    let resultItem = $('<a>');
                    resultItem.addClass('result-item');
                    let href = resultItem.attr('href', "/wiki/mineral/"+result.slug+"/show");
                    href.html(result.name);
                    resultList.append(href);
                    resultsContainer.append(resultList);
                });
                resultsContainer.css('margin', '17px 0px');
                $('nav').remove();
            },
            error: function (error) {
              console.error('Erreur lors de la recherche :', error);
            }
        });
    });
  });