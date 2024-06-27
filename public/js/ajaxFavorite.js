let favoriteBtn = $('#favorite-btn');

$(document).ready(function() {
    
        favoriteBtn.on("click", function() {
            console.log(favoriteBtn.hasClass("added"));
            if(favoriteBtn.hasClass("added")) {
                removeFavorite();
                return 0;
            } else {
                addFavorite();
                return 0;
            }
        });
    
});

function removeFavorite() {
    
    let requestRemove =
    $.ajax({
        type: 'POST',
        url: window.location.href+'/remove-favorite',
        data: {
                'slug' : favoriteBtn.data('slug')
            }
    });
    requestRemove.done(function (response) {
        favoriteBtn.css('background-color', '#ffda55');
        // Récupération de la propriété message
        let messages = response.data;
        // Récupération de la balise d'affichage
        const messageContainer = $('#alert-success');
        if(messageContainer.children().length > 0) {
            messageContainer.removeClass();
        }
        // Récupération de la valeur de la propriété
        $.each(messages, function(index, message) {
            // Ajout du message dans une balise p
            const resultItem = $('<p>').html(message);

            messageContainer.append(resultItem);
            favoriteBtn.removeClass('added');
            favoriteBtn.text('Add to favorites');

            if(messageContainer.html().trim()) {
                messageContainer.hide().delay(20).fadeIn().delay(5000).fadeOut();
            }

        });
        return 0;
    });
    requestRemove.fail(function (error) {
        console.error("Erreur d'exécution du script :", error);
    });
        
}

function addFavorite() {
    let requestAdd =
        $.ajax({
            type: 'POST',
            url: window.location.href+'/add-favorite',
            data: {
                    'slug' : favoriteBtn.data('slug')
                }
        });
        requestAdd.done(function (response) {
            favoriteBtn.css('background-color', '#edbe17');
            
            let messages = response.data;
            
            const messageContainer = $('#alert-success');
            if(messageContainer.children().length > 0) {
                messageContainer.children().remove();
            }
            
            $.each(messages, function(index, message) {
                
                const resultItem = $('<p>').html(message);
                
                    messageContainer.append(resultItem);
                    favoriteBtn.addClass('added');
                    favoriteBtn.text('Remove to favorites');
            });

            if($('#alert-success p').html().trim()) {
                $('#alert-success').hide().delay(20).fadeIn().delay(5000).fadeOut();
            }

            return 0;
        });
        requestAdd.fail(function (error) {
            console.error("Erreur d'exécution du script :", error);
        });
}