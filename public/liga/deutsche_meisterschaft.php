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

<h1 class="w3-text-primary">Deutsche Meisterschaft 2025</h1>

<h3 class="w3-text-secondary">Spielplan zur Deutschen Meisterschaft 2025</h3>
<p>
    <?= HTML::link("https://docs.google.com/spreadsheets/d/e/2PACX-1vQ712b8anQI3ZDXPVFdFEomKgA3zyyNBLT4pT28iJXasOgxWLFeqWNRnVUjQxDlLQ/pubhtml?gid=62217435&single=true",
    bezeichnung: "Direkter Link zum Spielplan",
    extern: true,
    icon: "launch") ?>    
</p>

<iframe 
    style="width:100%; height:800px"
    class="w3-border-0"
    src="https://docs.google.com/spreadsheets/d/e/2PACX-1vQ712b8anQI3ZDXPVFdFEomKgA3zyyNBLT4pT28iJXasOgxWLFeqWNRnVUjQxDlLQ/pubhtml?gid=62217435&single=true">
</iframe>

<h3 class="w3-text-secondary">Livestream zur Deutschen Meisterschaft 2025</h3>

<h4>Samstag, 21. Juni 2025</h4>
<div class="w3-leftbar" style="margin-bottom: 15px;">
    <ul class="w3-ul">
        <li><?=HTML::icon("schedule", class:"w3-text-primary")?> ab ca. 10:00 Uhr</li>
        <li><?=HTML::icon("videocam", class:"w3-text-primary")?> Live auf Twitch</li>
        <li><?=HTML::icon("launch", class:"w3-text-primary")?> <?=HTML::link("https://www.twitch.tv/einradhockeytv", bezeichnung: "Zum Livestream auf Twitch", extern: true, icon: "") ?></li>
    </ul>
</div>

<h4>Sonntag, 22. Juni 2025</h4>
<div class="w3-leftbar" style="margin-bottom: 15px;">
    <ul class="w3-ul">
        <li><?=HTML::icon("schedule", class:"w3-text-primary")?> ab ca. 09:00 Uhr</li>
        <li><?=HTML::icon("videocam", class:"w3-text-primary")?> Weiterhin live auf Twitch</li>
        <li><?=HTML::icon("launch", class:"w3-text-primary")?> <?=HTML::link("https://www.twitch.tv/einradhockeytv", bezeichnung: "Zum Livestream auf Twitch", extern: true, icon: "") ?></li>
    </ul>
</div>

<div class="w3-leftbar" style="margin-bottom: 15px;">
    <ul class="w3-ul">
        <li><?=HTML::icon("schedule", class:"w3-text-primary")?> ab ca. 13:00 Uhr</li>
        <li><?=HTML::icon("videocam", class:"w3-text-primary")?> Wechsel zur Übertragung im MDR</li>
        <li><?=HTML::icon("launch", class:"w3-text-primary")?> <?=HTML::link("https://www.mdr.de/tv/livestreams/event-sendungen/sendung-1036754.html", bezeichnung: "Zum Livestream beim MDR", extern: true, icon: "") ?></li>
    </ul>
</div>

<?php include '../../templates/footer.tmp.php';
