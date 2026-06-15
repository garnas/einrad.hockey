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

<h1 class="w3-text-primary">Deutsche Meisterschaft 2026</h1>

<h3 class="w3-text-secondary">Spielplan zur Deutschen Meisterschaft 2026</h3>
<p>
    <?= HTML::link("https://docs.google.com/spreadsheets/d/e/2PACX-1vQfaSM7O1sYp02dzPGz9jtMU3Zd7n0KInu8IXoTGiU90RpdzfW9e7-VnIVvADL6aQ/pubhtml?gid=96926240&amp;single=true&amp;widget=true&amp;headers=false",
        bezeichnung: "Direkter Link zum Spielplan",
        extern: true,
        icon: "launch") ?>    
</p>

<p>
    <?= HTML::link(Env::BASE_URL . '/dokumente/spielplaene/2026_Deutsche_Meisterschaft_Herne.pdf?version=20260611',
        bezeichnung: "PDF Version des Spielplans",
        extern: true,
        icon: "launch") ?>
</p>

<iframe 
    style="width:100%; height:800px"
    class="w3-border-0"
    src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQfaSM7O1sYp02dzPGz9jtMU3Zd7n0KInu8IXoTGiU90RpdzfW9e7-VnIVvADL6aQ/pubhtml?gid=96926240&amp;single=true&amp;widget=true&amp;headers=false">
</iframe>

<h3 class="w3-text-secondary">Livestream zur Deutschen Meisterschaft 2026</h3>
<p>
    <?= HTML::link("https://www.twitch.tv/einradhockeytv",
        bezeichnung: "Zum Livestream",
        extern: true,
        icon: "launch") ?>    
</p>

<?php include '../../templates/footer.tmp.php';
