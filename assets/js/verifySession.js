window.addEventListener('load', () => {
     let xhttp = new XMLHttpRequest()

     xhttp.onreadystatechange = function () {
          if (xhttp.readyState == 4 && xhttp.status == 200) {
               let session = xhttp.responseText
               if (session) {
                    document.querySelector('.logged-in-nav').classList.remove('d-none')
               } else {
                    document.querySelector('.logged-out-nav').classList.remove('d-none')
               }
          }else{
               console.log('something went wrong');
          }
     }
     xhttp.open('GET', './php/listing.php?session=' + true, true)
     xhttp.send()
})
