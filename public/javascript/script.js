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

//Infos aus/einblenden
function modal(id){
  var x = document.getElementById(id);
  if (window.getComputedStyle(x).display === "none") {
      x.style.display = "block";
  }else{
      if (window.getComputedStyle(x).display === "block") {
          x.style.display = "none";
      }
  }
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
function woerter_zaehlen(max_woerter = 500, id_textarea = 'text', id_counter = 'counter') {
  var anzahl = max_woerter - window.document.getElementById(id_textarea).value.length;
  document.getElementById(id_counter).innerHTML = anzahl + ' Zeichen übrig';

  if(anzahl < 30){
      document.getElementById(id_counter).style.color = "red";
  }else{
      document.getElementById(id_counter).style.color = "green";}
  
  if(anzahl < 0){
      document.getElementById(id_counter).innerHTML = 'Zeichenanzahl wurde überschritten.';
      //document.getElementById("submit").style.display="none";
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
    let checkboxes = document.getElementsByName(checkbox_name);
  for(var i=0, n=checkboxes.length;i<n;i++) {
      if (checkboxes[i].checked === true){
          checkboxes[i].checked = false;
      }else{
          checkboxes[i].checked = true;
      }
  }
}

//Countdown
function countdown(countDownDate, countdown_id){
    // Set the date we're counting down to
    countDownDate = countDownDate * 1000; // JS Unix in Milliseconds

    // Update the count down every 1 second
    var x = setInterval(function() {

    // Get today's date and time
    var now = new Date().getTime();

    // Find the distance between now and the count down date
    var distance = countDownDate - now;

    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

    if (days <= 0){
    days = '00'
    }else if (days < 10){
    days = "0" + days
    }
    if (hours < 10){hours = "0" + hours}
    if (minutes < 10){minutes = "0" + minutes}
    if (seconds < 10){seconds = "0" + seconds}

    // Display the result in the element with id="demo"
    document.getElementById(countdown_id + "_days").innerHTML = days;
    document.getElementById(countdown_id + "_hours").innerHTML = hours;
    document.getElementById(countdown_id + "_minutes").innerHTML = minutes;
    document.getElementById(countdown_id + "_seconds").innerHTML = seconds;

    /*    "<span class='w3-center' style='display: inline-block'>" +  + "<span style='display: block' class='w3-small w3-text-black'>Tage</span></span>"
    + "<span class='w3-center' style='display: inline-block'>" + hours + "<span style='display: block' class='w3-small'>Stunden</span></span>"
    + "<span class='w3-center' style='display: inline-block'>" + minutes + "<span style='display: block' class='w3-small'>Minuten</span></span>"
    + "<span class='w3-center' style='display: inline-block'>" + seconds + "<span style='display: block' class='w3-small'>Sekunden</span></span>"*/

    // If the count down is finished, write some text
    if (distance < 0) {
    clearInterval(x);
    document.getElementById(countdown_id).style.display = "none";
    }
    }, 1000);

}

class SortableTable {
    constructor(tableNode) {
        this.tableNode = tableNode;

        this.columnHeaders = tableNode.querySelectorAll('thead th');

        this.sortColumns = [];

        for (var i = 0; i < this.columnHeaders.length; i++) {
            var ch = this.columnHeaders[i];
            var buttonNode = ch.querySelector('button');
            if (buttonNode) {
                this.sortColumns.push(i);
                buttonNode.setAttribute('data-column-index', i);
                buttonNode.addEventListener('click', this.handleClick.bind(this));
            }
        }

        this.optionCheckbox = document.querySelector(
            'input[type="checkbox"][value="show-unsorted-icon"]'
        );

        if (this.optionCheckbox) {
            this.optionCheckbox.addEventListener(
                'change',
                this.handleOptionChange.bind(this)
            );
            if (this.optionCheckbox.checked) {
                this.tableNode.classList.add('show-unsorted-icon');
            }
        }
    }

    setColumnHeaderSort(columnIndex) {
        if (typeof columnIndex === 'string') {
            columnIndex = parseInt(columnIndex);
        }

        for (var i = 0; i < this.columnHeaders.length; i++) {
            var ch = this.columnHeaders[i];
            var buttonNode = ch.querySelector('button');
            if (i === columnIndex) {
                var value = ch.getAttribute('aria-sort');
                if (value === 'descending') {
                    ch.setAttribute('aria-sort', 'ascending');
                    this.sortColumn(
                        columnIndex,
                        'ascending',
                        ch.classList.contains('num')
                    );
                } else {
                    ch.setAttribute('aria-sort', 'descending');
                    this.sortColumn(
                        columnIndex,
                        'descending',
                        ch.classList.contains('num')
                    );
                }
            } else {
                if (ch.hasAttribute('aria-sort') && buttonNode) {
                    ch.removeAttribute('aria-sort');
                }
            }
        }
    }

    sortColumn(columnIndex, sortValue, isNumber) {
        function compareValues(a, b) {
            if (sortValue === 'ascending') {
                if (a.value === b.value) {
                    return 0;
                } else {
                    if (isNumber) {
                        return a.value - b.value;
                    } else {
                        return a.value < b.value ? -1 : 1;
                    }
                }
            } else {
                if (a.value === b.value) {
                    return 0;
                } else {
                    if (isNumber) {
                        return b.value - a.value;
                    } else {
                        return a.value > b.value ? -1 : 1;
                    }
                }
            }
        }

        if (typeof isNumber !== 'boolean') {
            isNumber = false;
        }

        var tbodyNode = this.tableNode.querySelector('tbody');
        var rowNodes = [];
        var dataCells = [];

        var rowNode = tbodyNode.firstElementChild;

        var index = 0;
        while (rowNode) {
            rowNodes.push(rowNode);
            var rowCells = rowNode.querySelectorAll('th, td');
            var dataCell = rowCells[columnIndex];

            var data = {};
            data.index = index;
            data.value = dataCell.textContent.toLowerCase().trim();
            if (isNumber) {
                data.value = parseFloat(data.value);
            }
            dataCells.push(data);
            rowNode = rowNode.nextElementSibling;
            index += 1;
        }

        dataCells.sort(compareValues);

        // remove rows
        while (tbodyNode.firstChild) {
            tbodyNode.removeChild(tbodyNode.lastChild);
        }

        // add sorted rows
        for (var i = 0; i < dataCells.length; i += 1) {
            tbodyNode.appendChild(rowNodes[dataCells[i].index]);
        }
    }

    /* EVENT HANDLERS */

    handleClick(event) {
        var tgt = event.currentTarget;
        this.setColumnHeaderSort(tgt.getAttribute('data-column-index'));
    }

    handleOptionChange(event) {
        var tgt = event.currentTarget;

        if (tgt.checked) {
            this.tableNode.classList.add('show-unsorted-icon');
        } else {
            this.tableNode.classList.remove('show-unsorted-icon');
        }
    }
}

// Initialize sortable table buttons
window.addEventListener('load', function () {
    var sortableTables = document.querySelectorAll('table.sortable');
    for (var i = 0; i < sortableTables.length; i++) {
        new SortableTable(sortableTables[i]);
    }
});