// JS to open and close sidebar with overlay effect

//Ein und Ausblenden der Navigation für Mobils in header.tmp.php
function open_sidebar() {
  document.getElementById("mySidebar").style.display = "block";
  document.getElementById("myOverlay").style.display = "block";
  document.getElementById("myHtml").style.overflow ="hidden";
}

function close_sidebar() {
  document.getElementById("mySidebar").style.display = "none";
  document.getElementById("myOverlay").style.display = "none";
  document.getElementById("myHtml").style.overflow ="auto";
}

//An und Ausblenden der Infobar für Mobils auf start.php
function einblenden(){
    document.getElementById('ausblenden').classList.remove('w3-hide');
    document.getElementById('einblenden').classList.add('w3-hide');
    document.getElementById('infobar').classList.remove('w3-hide-small');
}
function ausblenden(){
    document.getElementById('ausblenden').classList.add('w3-hide');
    document.getElementById('einblenden').classList.remove('w3-hide');
    document.getElementById('infobar').classList.add('w3-hide-small');
}

//Gleichmäßige Verteilung der Centerpanels in lc_start und tc_start
//Siehe hierzu auch .centerpanels in style.css
function centerpanels_anordnung(){
  //Misst die größe des Containers in denen sich die Centerpanels befinden
  var x = document.getElementById("messen").offsetWidth;

  //Berechnet die notwendige Margin für die Centerpanels
  anz = Math.min(4, Math.floor(x/160)); //(gerundete) Anzahl der Centerpanels
  var y = (x - anz*160)/(2*anz)-0.5; //benötigte Margin

  //Margin ändern
  z = document. getElementsByClassName("centerpanels");
  for (let e of z) { e.style.margin = y + "px"; }
}

//Woerterzaehlen für Neuigkeit eintragen und bearbeiten
function woerter_zaehlen(max_woerter = 500) {
  var anzahl = max_woerter - window.document.getElementById('text').value.length;
  document.getElementById("counter").innerHTML = anzahl + ' Zeichen übrig';

  if(anzahl < 30){
      document.getElementById("counter").style.color="red";
  }else{
      document.getElementById("counter").style.color="green";}
  
  if(anzahl < 0){
      document.getElementById("counter").innerHTML = 'Zeichenanzahl wurde überschritten.';
      document.getElementById("submit").style.display="none";
  }else{
      document.getElementById("submit").style.display="block";
  }
}

//Öffnen der Tabs im Kommunikationscenter
function openTab(tabName) {
  var i;
  var x = document.getElementsByClassName("tab");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  document.getElementById(tabName).style.display = "block";
}

//Invertieren der Teamauswahl im Kommunikationscenter
function invert(checkbox_name) {
  checkboxes = document.getElementsByName(checkbox_name);
  for(var i=0, n=checkboxes.length;i<n;i++) {
      if (checkboxes[i].checked == true){
          checkboxes[i].checked = false;
      }else{
          checkboxes[i].checked = true;
      }
  }
}