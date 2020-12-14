<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Ligaleitung | Deutsche Einradhockeyliga";
$content = "Übersicht über die Ausschüsse der Deutschen Einradhockeyliga, welche die Liga leiten.";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-grey">Ligaleitung der Saison <?=Saison::get_saison_string()?></h1>
<p class="w3-text-grey">Die Liga wird von den hier aufgelisteten Gremien geleitet. Wenn du auch in einem der Ausschüsse die Einradhockeyliga weiterentwickeln möchtest, kannst du dich jederzeit beim Ligaauschuss melden.</p> 
<!--Liga -->
<h2 class="w3-border-bottom w3-text-primary">Ligaausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (Ligaleitung::get_all_la() as $la){?>
        <tr>
            <td><?=Form::mailto($la['email'], ' ')?><?=$la['r_name']?></td>
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
        <?php foreach (Ligaleitung::get_all_tk() as $tk){?>
        <tr>
            <td><?=$tk['r_name']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($tk['team_id']) ?: 'Ehrenamtlich'?>)</i>
            </td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Der Technikausschuss ist verantwortlich für die Instandhaltung und Weiterentwicklung der IT der Deutschen Einradhockeyliga.</p>
<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::TECHNIKMAIL)?></p>

<!-- Öffntlichkeits-Ausschuss -->
<h2 class="w3-border-bottom w3-text-primary">Öffentlichkeitsausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
    <?php foreach (LigaLeitung::get_all_oa() as $oa){?>
        <tr>
            <td><?=$oa['r_name']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($oa['team_id']) ?: 'Ehrenamtlich'?>)</i></td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Der Öffentlichkeitsausschuss ist relativ neu und noch nicht offiziell im Ligamodus verankert. Er soll den Einradhockeysport nach außen hin präsentieren und sich um unsere Socialmedia-Accounts kümmern.</p>
<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::OEFFIMAIL)?></p>

<!-- Schiri-Ausschuss -->
<h2 class="w3-border-bottom w3-text-primary">Schiedsrichterausschuss</h2>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
    <?php foreach (LigaLeitung::get_all_sa() as $sa){?>
        <tr>
            <td><?=$sa['r_name']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($sa['team_id']) ?: 'Ehrenamtlich'?>)</i></td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Der Schiedsrichterausschuss ist für die Organisation der Aus- und Weiterbildung der Schiedsrichter in der Deutschen Einradhockeyliga verantwortlich.</p>
<p class="w3-text-grey">Schreib uns an: <?=Form::mailto(Config::SCHIRIMAIL)?></p>
<h3 class="w3-border-bottom w3-text-primary">Schiedsrichterausbilder</h3>
<div class="w3-responsive">
    <table class="w3-leftbar w3-container w3-margin-left w3-border-tertiary" style="white-space: nowrap">
        <?php foreach (Ligaleitung::get_all_ausbilder() as $ausbilder){?>
        <tr>
            <td><?=$ausbilder['vorname'] . ' ' . $ausbilder['nachname']?></td>
            <td><i class="w3-text-primary">(<?=Team::teamid_to_teamname ($ausbilder['team_id']) ?: 'Ehrenamtlich'?>)</i>
            </td>
        </tr>
        <?php } //end foreach?>
    </table>
</div>
<p>Dank unserer Ausbilder haben wir zurzeit <?=Spieler::get_anz_schiris()?> Schiedsrichter in der Deutschen Einradhockeyliga.</p>

<?php include '../../templates/footer.tmp.php';