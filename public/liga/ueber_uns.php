<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Über uns | Deutsche Einradhockeyliga";
$content = "Infos und aktuelle Daten der Deutsche Einradhockeyliga für Interessierte. Erster Schritt zum Gründen eines Teams.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Interesse?</h1>
<p>Jeder Einradhockeybegeisterte soll in der Deutschen Einradhockeyliga die Möglichkeit haben, unser Hobby in einem sportlichen Rahmen auszuüben. Unsere Liga hat maßgeblich zur Verbreitung unserer Sportart beigetragen und ist in ihrer Art und Konstanz weltweit einzigartig.</p>

<div class="w3-large w3-margin-left w3-panel w3-leftbar w3-border-tertiary">
    <p><i>Wer immer Einradhockey spielen möchte, ist herzlich willkommen!</i></p>
</div>

<h3 class="w3-text-grey">Ligadaten</h3>
<div class="w3-container">
    <ul class="w3-ul w3-leftbar w3-border-tertiary">
        <li><?=count(Team::list_of_all_teams())?> Teams mit <?=Spieler::count_spieler()?> Spieler</li>
        <li><?=count(Config::BLOCK)?> Spielstärken (<?=implode(", ", Config::BLOCK)?>)</li>
        <li>Saison: <?=Config::SAISON_ANFANG . ' - ' . Config::SAISON_ENDE?></li>
        <li><?=Config::LIGAGEBUEHR?> Ligagebühr</li>
    </ul>
</div>

<h2 class="w3-text-primary">Mitspielen!</h2>
<p>Die Liga bietet durch ihre Vielfalt an Teams und durch unterschiedliche Turniertypen einen Anreiz für Spieler aller Spielstärken. Auf unseren Turnieren spielen jeweils fünf bis acht Teams gegeneinander. Ein neues Team kann sich während der Saison jederzeit anmelden, aber auch als Nichtligateam probeweise an einem Turnier teilnehmen.</p>

<div class="w3-container">
    <ul class="w3-ul w3-leftbar w3-border-tertiary">
        <li><?=Form::link(Config::LINK_REGELN_KURZ, 'Die wichtigsten Regeln')?></li>
        <li><a href="ligakarte.php" class="no w3-text-blue w3-hover-text-secondary">Deutschlandkarte aller Ligateams</a></li>
        <li><a href="teams.php" class="no w3-text-blue w3-hover-text-secondary">Kontaktliste aller Ligateams</a></li>
    </ul>
</div>

<h2 class="w3-text-primary">Team gründen.</h2>
<p>Jedes Team besteht aus mindestens fünf Einradfahrern. Es gibt keine Alters- oder Geschlechtseinteilung und Teams jeder geographischen Herkunft sind zugelassen. Ein Ligateam anzumelden geht jederzeit und schnell:</p>
<div class="w3-container">
    <ul class="w3-ul w3-primary w3-card">
        <li>1. <?=Config::LAMAIL?> anschreiben (Teamname, Ligavertreter, Email-Adresse angeben)</li>
        <li>2. <?=Config::LIGAGEBUEHR?> Ligagebühr überweisen</li>
        <li>3. Teamcenter-Login erhalten</li>
        <li>4. Im Teamcenter zu Turnieren anmelden</li>
    </ul>
</div>

<h2 class="w3-text-primary">Fragen?</h2>
<p>Bei Interesse an einer Teilnahme am Spiebetrieb sowie für alle weiteren Fragen steht der Ligaausschuss jederzeit zur Verfügung.</p>

<div class="w3-container">
    <ul class="w3-ul w3-leftbar w3-border-tertiary">
        <li><?=Form::link(Config::LINK_FORUM, 'Forum')?></li>
        <li><?=Form::link('dokumente.php', 'Modus & Regeln')?></li>
        <li><?=Form::link('ligaleitung.php', 'Ligaleitung')?></li>
    </ul>
</div>

<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::LAMAIL)?></p>
        
<?php include '../../templates/footer.tmp.php';