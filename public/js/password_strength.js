function checkPasswordStrength() {
    var password = document.getElementById('inputPassword').value;
    var rule1 = false;
    var rule2 = false;
    var rule3 = false;
    var rule4 = false;
    var rule5 = false;
  
    // Test Rule 1
    var regex1 = /[A-Z]/g;
    var regexMatch1 = password.match(regex1);
    if (regexMatch1) {
      rule1 = true;
      document.getElementById('output-one').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      $('.one').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Test Rule 2
    var regex2 = /[a-z]/g;
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
    var regex3 = /[0-9]/g;
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
    var regex4 = /[!|@|#|$|%|^|&]/g;
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
    if (password.length >= 8) {
      rule5 = true;
      document.getElementById('output-five').innerHTML = "<img src='https://cdn4.iconfinder.com/data/icons/momenticons-basic/32x32/accept1.png'>";
      $('.five').css({
        'text-decoration': 'line-through',
        'color': 'green'
      });
    }
    // Output the result
    if (rule1 && rule2 && rule3 && rule4 && rule5) {
      $('.info-window').slideUp();
    } else {
      $('.info-window').slideDown();
      if (!rule1) {
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
    if(!rule1 && !rule2 && !rule3 && !rule4 && !rule5) {
       $('.info-window').slideUp();
    }
  }