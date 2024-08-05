const tableBody = document.getElementById("response")
               window.addEventListener('load', () => {
                    attachEventListeners();
                    loadClasses()
               })

               function optionClick(event) {
                    var selectedOption = event.target.value;
                    console.log("Selected option: " + selectedOption);

                    var xhttp = new XMLHttpRequest();

                    xhttp.onreadystatechange = function () {
                         if (this.readyState == 4 && this.status == 200) {
                              tableBody.innerHTML = this.responseText
                         }
                    }

                    xhttp.open("GET", './php/listing.php?state=' + selectedOption, true)
                    xhttp.send();

               }

               function attachEventListeners() {
                    var selectElement = document.getElementById('select');
                    selectElement.addEventListener('change', optionClick)
               }

               function loadClasses() {

                    var xhttp = new XMLHttpRequest();

                    xhttp.onreadystatechange = function () {
                         if (this.readyState == 4 && this.status == 200) {
                              tableBody.innerHTML = this.responseText
                         }
                    }

                    xhttp.open("GET", './php/listing.php', true)
                    xhttp.send();
               }

               function findClass(str) {
                    var xhttp = new XMLHttpRequest();

                    xhttp.onreadystatechange = function () {
                         if (this.readyState == 4 && this.status == 200) {
                              tableBody.innerHTML = this.responseText
                         }
                    }

                    xhttp.open("GET", './php/listing.php?class=' + str, true)
                    xhttp.send();
               }

               function toggleClass(id) {
                    var xhttp = new XMLHttpRequest();

                    xhttp.onreadystatechange = function () {
                         if (this.readyState == 4 && this.status == 200) {
                              var response = JSON.parse(this.responseText)

                              if (response.status == "success") {
                                   loadClasses();
                              }
                         }
                    }

                    xhttp.open("GET", './php/toggle_class_state.php?id=' + id, true)
                    xhttp.send();
               }