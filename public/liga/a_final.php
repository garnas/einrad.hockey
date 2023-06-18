<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Spielplan Deutsche Meisterschaft';
Html::$content = 'Aktuelle Ergebnisse der B-Meisterschaft';
include '../../templates/header.tmp.php';
?>

    <!-- Archiv -->
    <h1 class="w3-text-primary">Spielplan Deutsche Meisterschaft</h1>
<h2>Livestream</h2>
<p>
    <iframe src="https://player.twitch.tv/?channel=holzmichel41&parent=einrad.hockey" frameborder="0" allowfullscreen="true" scrolling="no" height="378" width="620"></iframe>
</p>
<p>
    <?= HTML::link("https://docs.google.com/spreadsheets/d/e/2PACX-1vQWCCtPxRlVZ6EAh6TSxoAGa0Lc0alfo2iqjWBz0rmGoM1deUl5hYPMORPAnn9gCq67eysaWUjGlqSK/pubhtml",
    bezeichnung: "Direkter Link zum Spielplan",
    extern: true,
    icon: "launch") ?>
</p>
    <iframe style="width:100%;height:800px" class="archiv w3-border-0"
            src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQWCCtPxRlVZ6EAh6TSxoAGa0Lc0alfo2iqjWBz0rmGoM1deUl5hYPMORPAnn9gCq67eysaWUjGlqSK/pubhtml?gid=460300906&amp;single=true&amp;widget=false&amp;headers=false"></iframe>
<?php include '../../templates/footer.tmp.php';