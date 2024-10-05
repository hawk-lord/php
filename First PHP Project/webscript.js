
"use strict"; 

const loadTable = function() {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("tableparagraph").innerHTML = this.responseText;
      }
    };
    xmlhttp.open("GET", "web.php?method=loadTable", true);
    xmlhttp.send();
}

const emptyTable = function() {
    document.getElementById("tableparagraph").innerHTML = "";
}

const addAmount = function(number) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("orderamount" + number).innerHTML = this.responseText;
      }
    };
    xmlhttp.open("POST", "web.php?method=addAmount&number=" + number, true);
    xmlhttp.send();
}

const clearAmount = function(number) {
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
		document.getElementById("orderamount" + number).innerHTML = this.responseText;
      }
    };
    xmlhttp.open("POST", "web.php?method=clearAmount&number=" + number, true);
    xmlhttp.send();
}


