function browseImages(str) {
  if (str.length == 0) {
      $("#results").innerHTML = "";
      return;
  } else {
      var xmlhttp = new XMLHttpRequest();
      xmlhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
              document.getElementById("results").innerHTML = this.responseText;
          }
      };
      xmlhttp.open("GET", "/photo-gallery/index.php/ajax?q=" + str, true);
      xmlhttp.send();
  }
}
