document.getElementById('loginForm').addEventListener('submit', function (event) {
     event.preventDefault(); // Prevent form submission

     var matricule = document.getElementsByName('matricule')[0].value;
     var password = document.getElementsByName('password')[0].value;
     console.log('form');

     var xhr = new XMLHttpRequest();
     xhr.onreadystatechange = function () {
          if (xhr.readyState === 4 && xhr.status === 200) {
               var response = JSON.parse(xhr.responseText);

               if (response.status === 'success') {
                    window.location.href = './listing.html';
               } else {
                    alert(response.message);
               }
          }
     };
     xhr.open('POST', './php/login.php', true);
     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
     xhr.send('matricule=' + encodeURIComponent(matricule) + '&password=' + encodeURIComponent(password));
});