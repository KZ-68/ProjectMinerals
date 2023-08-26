function checkPasswordStrength() {
    // On déclare une variable et on récupère la valeur de l'input password
    var password = document.getElementById('inputPassword').value;
    // On déclare des valeurs booléennes :
    var rule1 = false;
    var rule2 = false;
    var rule3 = false;
    var rule4 = false;
    var rule5 = false;
  
    // Test Rule 1
    var regex1 = /[A-Z]/g; // Regex pour au moins un caractère Majuscule
    var regexMatch1 = password.match(regex1); // L'input password doit correspondre au Regex
    // Si il y a correspondance : 
    if (regexMatch1) {
      rule1 = true; // On passe la valeur en true
      // On ajoute à l'id une image de validation
      document.getElementById('output-one').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      // Via jQuery, on ajoute à la classe des propriétés css
      $('.one').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Test Rule 2
    var regex2 = /[a-z]/g; // Regex pour au moins un caractère minuscule
    var regexMatch2 = password.match(regex2);
    if (regexMatch2) {
      rule2 = true;
      document.getElementById('output-two').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      
      $('.two').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Test Rule 3
    var regex3 = /[0-9]/g; // Regex pour au moins un caractère numérique
    var regexMatch3 = password.match(regex3);
    if (regexMatch3) {
      rule3 = true;
      document.getElementById('output-three').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      $('.three').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Test Rule 4
    var regex4 = /[!|@|#|$|%|^|&]/g; // Regex pour au moins un caractère spécial
    var regexMatch4 = password.match(regex4);
    if (regexMatch4) {
      rule4 = true;
      document.getElementById('output-four').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      $('.four').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Test Rule 5
    // Si le password contient au moins 8 caractères :
    if (password.length >= 8) {
      rule5 = true;
      document.getElementById('output-five').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      $('.five').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Output :
    // Si les cinqs valeurs booléennes sont vraies :
    if (rule1 && rule2 && rule3 && rule4 && rule5) {
        // Avec jQuery, on fait glisser vers le haut la zone info pour la faire disparaitre
      $('.info-window').slideUp();
    } else {
        // Sinon, on laisse la zone apparente
      $('.info-window').slideDown();

    // Vérification si chaque valeur booléennes est fausse : 
      if (!rule1) {
        // On ajoute à l'id une image d'erreur
        document.getElementById('output-one').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/delete.png'>";
        $('.one').css({
          'text-decoration': 'none',
          'color': 'red'
        });
      }
      if (!rule2) {
        document.getElementById('output-two').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/delete.png'>";
        $('.two').css({
          'text-decoration': 'none',
          'color': 'red'
        });
      }
      if (!rule3) {
        document.getElementById('output-three').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/delete.png'>";
        $('.three').css({
          'text-decoration': 'none',
          'color': 'red'
        });
      }
      if (!rule4) {
        document.getElementById('output-four').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/delete.png'>";
        $('.four').css({
          'text-decoration': 'none',
          'color': 'red'
        });
      }
      if (!rule5) {
        document.getElementById('output-five').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/delete.png'>";
        $('.five').css({
          'text-decoration': 'none',
          'color': 'red'
        });
      }
    }
    // Si tous les booléans sont faux, donc si il n'y a pas de caractère de saisie :
    if(!rule1 && !rule2 && !rule3 && !rule4 && !rule5) {
       $('.info-window').slideUp();
    }
  }