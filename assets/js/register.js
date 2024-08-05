window.addEventListener("load", () => {
     var xhttp = new XMLHttpRequest()

     xhttp.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
               document.getElementById('course').innerHTML += this.responseText
          } else {

          }
     }

     xhttp.open("GET", "./php/get_courses.php", true)
     xhttp.send()
})

const handleRegisteration = () => {
     var xhttp = new XMLHttpRequest();

     var name = encodeURIComponent(document.getElementsByName('name')[0].value)
     var matricule = encodeURIComponent(document.getElementsByName('matricule')[0].value)
     var password = encodeURIComponent(document.getElementsByName('password')[0].value)
     var email = encodeURIComponent(document.getElementsByName('email')[0].value)
     var course = encodeURIComponent(document.getElementsByName('course')[0].value)


     xhttp.onreadystatechange = function () {
          if (this.readyState == 4 && this.status == 200) {
               var response = JSON.parse(this.responseText)

               if (response.status === "success") {
                    window.location.href = "./login.html"
               } else {
                    alert(response.message);
               }
          }
     }

     xhttp.open("POST", "./php/register.php", true)
     xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
     xhttp.send("name=" + name + "&email=" + email + "&matricule=" + matricule + "&password=" + password + "&course=" + course)
}

document.getElementById('registerForm').addEventListener("submit", (event) => {
     event.preventDefault();
     handleRegisteration();
})
