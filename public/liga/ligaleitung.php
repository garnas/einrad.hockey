<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$las = Ligaleitung::get_all_la();
$alle_ausbilder = Ligaleitung::get_all_ausbilder();
$tks = Ligaleitung::get_all_tk();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Ligaleitung | Deutsche Einradhockeyliga";
$content = "Übersicht über die Ausschüsse der Deutschen Einradhockeyliga, welche die Liga leiten.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-grey">Ligaleitung der Saison <?=Form::get_saison_string()?></h1>

<!--Liga -->
<h2 class="w3-border-bottom w3-text-primary">Ligaausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach ($las as $la){?>
        <tr>
            <td><a class="w3-text-primary w3-hover-text-secondary no" href="mailto:<?=$la['email']?>"><i class="material-icons">mail</i></a> <?=$la['r_name']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($la['team_id']) ?: 'Ehrenamtlich'?>)</i></td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
    
<p>Der Ligaausschuss besteht aus sechs Mitgliedern und dient als Ansprechpartner der Deutschen Einradhockeyliga. Er ist verantwortlich für die Weiterentwicklung der Liga und für den reibungslosen Saisonablauf. Wenn es mehr Bewerber als Ligaausschussplätze gibt, wird er jeweils gegen Ende der Saison von allen Ligateams gewählt.</p>

<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::LAMAIL)?></p>

<p class="w3-margin" style="max-width: 666px">
    <a href="../bilder/ligaausschuss2020.jpg" class="">
        <img src="../bilder/ligaausschuss2020.jpg" style="" alt="Ligaausschuss 2020" class="w3-image w3-card-4">
    </a>
    Das Bild des Ligaausschusses der Saison 2020.
</p>

<!-- Technik -->
<h2 class="w3-border-bottom w3-text-primary">Technikausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach ($tks as $tk){?>
        <tr>
            <td><?=$tk['r_name']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($tk['team_id']) ?: 'Ehrenamtlich'?>)</i>
            </td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Der Technikausschuss ist verantwortlich für die Instandhaltung und Weiterentwicklung der IT der Deutschen Einradhockeyliga. Er wird durch den Ligaausschuss bestimmt. Interessenten können sich jederzeit beim Ligaausschuss melden.</p>
<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::TECHNIKMAIL)?></p>

<!-- Schiri -->
<h2 class="w3-border-bottom w3-text-primary">Schiedsrichterausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach ($alle_ausbilder as $ausbilder){?>
        <tr>
            <td><?=$ausbilder['vorname'] . ' ' . $ausbilder['nachname']?></td>
            <td><i
                    class="w3-text-primary">(<?=Team::teamid_to_teamname ($ausbilder['team_id']) ?: 'Ehrenamtlich'?>)</i>
            </td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Der Schiedsrichterausschuss ist für die Aus- und Weiterbildung von Schiedsrichtern in der Deutschen Einradhockeyliga verantwortlich. Er wird durch den Ligaausschuss bestimmt.</p>
<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::SCHIRIMAIL)?></p>

<?php include '../../templates/footer.tmp.php';