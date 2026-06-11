<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Spielplan Deutsche Meisterschaft';
Html::$content = 'Spielplan Deutsche Meisterschaft';
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">B-Meisterschaft 2026</h1>

<h3 class="w3-text-secondary">Spielplan zur B-Meisterschaft 2026</h3>
<p>
    <?= HTML::link("https://docs.google.com/spreadsheets/d/e/2PACX-1vTwuJGv8xoYntn6VsS2N3YAR2z1H2roF1QGVGMrbEIZAu8EnvTAZ1B50RHwA51CKw/pubhtml?gid=961046260&amp;single=true&amp;widget=true&amp;headers=false",
        bezeichnung: "Direkter Link zum Spielplan",
        extern: true,
        icon: "launch") ?>
</p>


<p>
    <?= HTML::link(Env::BASE_URL . '/dokumente/spielplaene/2026_B-Meisterschaft_Moerfelden.pdf?version=20260611',
        bezeichnung: "PDF Version des Spielplans",
        extern: true,
        icon: "launch") ?>
</p>


<iframe
    style="width:100%; height:800px"
    class="w3-border-0"
    src="https://docs.google.com/spreadsheets/d/e/2PACX-1vTwuJGv8xoYntn6VsS2N3YAR2z1H2roF1QGVGMrbEIZAu8EnvTAZ1B50RHwA51CKw/pubhtml?gid=961046260&amp;single=true&amp;widget=true&amp;headers=false">
</iframe>


<h3 class="w3-text-secondary">Livestream zur B-Meisterschaft 2026</h3>
<p><i>Informationen folgen</i></p>


<?php include '../../templates/footer.tmp.php';
