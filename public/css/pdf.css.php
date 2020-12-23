/* Diese Css-Datei wird für die Erstellung von PDFs verwendet */

html{box-sizing:border-box}*,*:before,*:after{box-sizing:inherit}
/* Extract from normalize.css by Nicolas Gallagher and Jonathan Neal git.io/normalize */
html{-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}
/* End extract */

html,body{font-family:Verdana,sans-serif;font-size:<?=$font_size?>px;line-height:1}html{overflow-x:hidden}

h1{font-size:30px}h2{font-size:30px}h3{font-size:24px}h4{font-size:20px}h5{font-size:18px}h6{font-size:16px}.w3-serif{font-family:serif}

h1,h2,h3,h4,h5,h6{font-family:"Segoe UI",Arial,sans-serif;font-weight:400;margin:10px 0}.w3-wide{letter-spacing:4px}

.w3-table {border-collapse:collapse;border-spacing:0;width:100%;display:table}.w3-table-all{border:1px solid #ccc}

.w3-hoverable tbody tr:hover,.w3-ul.w3-hoverable li:hover{background-color:#ccc}.w3-centered tr th,.w3-centered tr td{text-align:center}
.w3-table td,.w3-table th,.w3-table-all td,.w3-table-all th{padding:<?=$padding?> 4px;display:table-cell;vertical-align:middle}
.w3-table th:first-child,.w3-table td:first-child,.w3-table-all th:first-child,.w3-table-all td:first-child{padding-left:16px}
.w3-center {text-align:center}
.w3-left-align{text-align:left!important}
.w3-right-align{text-align:right!important}

/*Primärfarbe*/
.w3-primary,.w3-hover-primary:hover{color:#ffffff!important;background-color:#6c7fbe!important}
.w3-text-primary,.w3-hover-text-primary:hover{color:#6c7fbe!important}
.w3-border-primary,.w3-hover-border-primary:hover{border-color:#6c7fbe!important}

.w3-text-grey {color:#757575!important}
.w3-text-secondary {color:#FB775C!important}

/*Extract aus style.css*/
.w3-responsive{
    display: block;
    overflow-x: visible}
.pdf-hide {
    display: none!important;
}

.w3-hide-small{
    display: block;
}
.w3-hide-large{
    display: none!important;
}
.w3-hide-medium{
    display: none!important;
}

.w3-table {
    page-break-inside: avoid;
}

tr:nth-child(even) {
    background-color: #f1f1f1;
}

th {
    color: white;
    font-weight: normal;
}

td > * {
    vertical-align: middle;
}

.pdfvert {
    vertical-align: middle;
}

h1 {
  font-size:30px;
}

.no { /*Keine unterstrichene Links*/
    text-decoration: none;
}

.w3-right{
    float:right!important
}

.material-icons {
    display: none; /* Können nicht angezeigt werden */
}