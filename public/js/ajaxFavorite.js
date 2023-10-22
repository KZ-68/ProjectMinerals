$(document).ready(function() {
    let favoriteBtn = $('#favorite-btn');

    if(favoriteBtn.hasClass('added')) {
        favoriteBtn.on("click", function() {
            let requestRemove =
                $.ajax({
                    type: 'POST',
                    url: '/wiki/mineral/{slug}/show/remove-favorite',
                    data: {
                            'slug' : favoriteBtn.data('slug'),
                            'favorite' : favoriteBtn.data('favorite')
                        }
                });
                requestRemove.done(function (response) {
                    favoriteBtn.css('background-color', '#ffda55');
                    // Récupération de la propriété message
                    let messages = response.data;
                    // Récupération de la balise d'affichage
                    const messageContainer = $('#alert-success');
                    // Récupération de la valeur de la propriété
                    $.each(messages, function(index, message) {
                        // Ajout du message dans une balise p
                        const resultItem = $('<p>').html(message);
                        /* 
                            Ajout d'un guardian, si il est possible de cliquer plusieurs fois, 
                            on bloque après le premier message de succès
                        */
                        if($('#alert-success p').length < 1) {
                            messageContainer.append(resultItem);
                        } else {
                            return 0;
                        }
                    });
        
                    if($('#alert-success p').html().trim()) {
                        $('#alert-success').hide().delay(20).fadeIn().delay(5000).fadeOut();
                    }
        
                });
                requestRemove.fail(function (error) {
                    console.error("Erreur d'exécution du script :", error);
                });
        })
    } else {
        favoriteBtn.on("click", function() {
            let requestAdd =
                $.ajax({
                    type: 'POST',
                    url: '/wiki/mineral/{slug}/show/add-favorite',
                    data: {
                            'slug' : favoriteBtn.data('slug')
                        }
                });
                requestAdd.done(function (response) {
                    favoriteBtn.css('background-color', '#edbe17');
                    
                    let messages = response.data;
                    
                    const messageContainer = $('#alert-success');
                    
                    $.each(messages, function(index, message) {
                        
                        const resultItem = $('<p>').html(message);
                        
                        if($('#alert-success p').length < 1) {
                            messageContainer.append(resultItem);
                        } else {
                            return 0;
                        }
                    });
        
                    if($('#alert-success p').html().trim()) {
                        $('#alert-success').hide().delay(20).fadeIn().delay(5000).fadeOut();
                    }
        
                });
                requestAdd.fail(function (error) {
                    console.error("Erreur d'exécution du script :", error);
                });
            });
    }
    
});