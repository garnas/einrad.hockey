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

<h1 class="w3-text-primary">Deutsche Meisterschaft 2025 in Dresden</h1>

<h3 class="w3-text-secondary">Spielplan</h3>
<iframe 
    style="width:100%; height:800px"
    class="w3-border-0"
    src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQ712b8anQI3ZDXPVFdFEomKgA3zyyNBLT4pT28iJXasOgxWLFeqWNRnVUjQxDlLQ/pubhtml?gid=62217435&single=true">
</iframe>

<p>
    <?= HTML::link("https://docs.google.com/spreadsheets/d/e/2PACX-1vQ712b8anQI3ZDXPVFdFEomKgA3zyyNBLT4pT28iJXasOgxWLFeqWNRnVUjQxDlLQ/pubhtml?gid=62217435&single=true",
    bezeichnung: "Direkter Link zum Spielplan",
    extern: true,
    icon: "launch") ?>    
</p>
<p class="w3-text-grey w3-small">
    Hinweis: Um neue Ergebnisse einsehen zu können, muss der Spielplan aktualisiert werden. Dafür bitte die entsprechende Seite neu laden.
</p>

<h3 class="w3-text-secondary">Livestream</h3>
<p>Für die Deutsche Meisterschaft wird es einen Livestream geben. Der Link wird dann hier veröffentlicht.</p>
<!-- <p>
    <iframe src="https://player.twitch.tv/?channel=holzmichel41&parent=einrad.hockey" frameborder="0" allowfullscreen="true" scrolling="no"
            width="100%"></iframe>
</p> -->

<?php include '../../templates/footer.tmp.php';