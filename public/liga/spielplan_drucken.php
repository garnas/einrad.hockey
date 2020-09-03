<?php
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link type="text/css" rel="stylesheet" href="../css/w3.css">
        <link type="text/css" rel="stylesheet" href="../css/style.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <meta name="viewport" content="width=273mm, scale=1">
        <title>Spielplan Druckversion</title>
    </head>
    <style>
    body{
        transform: scale(1);
    }
    .w3-card{
        box-shadow:0 0 0 0 rgba(0,0,0,0.16),0 0 0 0 rgba(0,0,0,0.12);
    }
    .w3-responsive{
        display: block;
        overflow-x: visible}
    .druck-bereich {
        height: 386mm;
        width: 273mm;
        padding: 8mm 12mm 8mm 12mm;
        background-color: white;
        border-style: dotted;   
    }
    .drucken-hide {
        display: none;
    }
    @media print {
        .druck-bereich {
            border-style: none;
        }
    }
    .w3-hide-small{
        display: block!important;
    }
    .w3-text-primary,.w3-text-secondary{
        color:black!important;
    }
    </style>
    <script>
        var scale = 'scale(1)';
        document.body.style.webkitTransform = scale;    // Chrome, Opera, Safari
        document.body.style.msTransform = scale;        // IE 9
        document.body.style.transform = scale;     // General
        
    </script>
    <body class="w3-panel">
        <div class='nicht-drucken'>
            <p><a href='spielplan.php?turnier_id=<?=$turnier_id?>' class="w3-text-blue w3-hover-text-red no"><i class="material-icons">reorder</i> Zurück zum Spielplan</a></p>
            <p><button class="w3-button w3-tertiary nicht-drucken" onclick="window.print()">Drucken!</button></p>
        </div>
        <div class="druck-bereich border">
            <div class="w3-right">
                <img src="../bilder/logo_lang_small.png" style="width: 100mm" class="w3-margin-top">
            </div>
            <?php
            include '../../templates/spielplan/spielplan_vorTurnierTabelle.tmp.php';
            include '../../templates/spielplan/spielplan_paarungen.tmp.php';
            ?>
        </div>
    </body>
</html>