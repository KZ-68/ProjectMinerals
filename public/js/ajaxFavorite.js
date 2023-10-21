$(document).ready(function() {
    let favoriteBtn = $('#favorite-btn');
    favoriteBtn.on("click", function() {
    let request =
        $.ajax({
            type: 'POST',
            url: '/wiki/mineral/{slug}/show/add-favorite',
            data: {
                    'slug' : favoriteBtn.data('slug')
                }
        });
        request.done(function (response) {
            favoriteBtn.css('background-color', '#edbe17');
            let messages = response.data;
            const messageContainer = $('#alert-success');

            $.each(messages, function(index, message) {
                const resultItem = $('<span>').html(message);
                if($('#alert-success span').length < 1) {
                    messageContainer.append(resultItem);
                } else {
                    return 0;
                }
            });

            if($('#alert-success span').html().trim()) {
                $('#alert-success').hide().delay(20).fadeIn().delay(5000).fadeOut();
            }

        });
        request.fail(function (error) {
            console.error("Erreur d'exécution du script :", error);
        });
    });

});