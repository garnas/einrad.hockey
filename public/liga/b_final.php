<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Spielplan B-Meisterschaft';
Html::$content = 'Aktuelle Ergebnisse der B-Meisterschaft';
include '../../templates/header.tmp.php';
?>

    <!-- Archiv -->
    <h1 class="w3-text-primary">Spielplan B-Meisterschaft</h1>
    <p class="w3-text-grey w3-hide-medium w3-hide-large"><?=Html::icon("info")?> Für Gruppe-B-Spiele nach rechts wischen</p>
    <iframe style="width:100%;height:800px;" class="archiv w3-border-0"
            src="https://docs.google.com/spreadsheets/d/e/2PACX-1vSsMJDE3BnroknCV3bUs2vtqGcnEJRNxMV5UCSJmJoU_7UpS27vlID-sZ-sKqnz8Aq5zT1WjhEt0bAz/pubhtml?gid=746281655&amp;single=true&amp;widget=false&amp;headers=false"></iframe>

<?php include '../../templates/footer.tmp.php';