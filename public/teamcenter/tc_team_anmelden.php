<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

//Check ob das Team über fünf Spieler verfügt
if (count(Spieler::get_teamkader($_SESSION['logins']['team']['id'])) < 5){
    Html::info('Bitte tragt euren Teamkader ein, um euch zu Turnieren anmelden zu können.');
    header('Location: ../teamcenter/tc_kader.php');
    die();
}

//Turnierobjekt erstellen
$turnier_id = (int) @$_GET['turnier_id'];
$turnier = new Turnier ($turnier_id);

//Existiert das Turnier?
if (empty($turnier->details)){
    Helper::not_found("Turnier wurde nicht gefunden.");
}
if (strtotime($turnier->details['datum']) < time()) {
    trigger_error("Das Turnier liegt in der Vergangenheit.", E_USER_ERROR);
}

if ($turnier->details['art'] == 'spass'){
    $kontakt = new Kontakt ($turnier->details['ausrichter']);
    $email = $kontakt->get_emails();
    Html::notice("Anmeldung zu Spass-Turnieren erfolgt über den Ausrichter: "
        . Html::mailto($email, Team::id_to_name($turnier->details['ausrichter'])), esc:false);
    header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->id);
    die();
}

$akt_team = new Team($_SESSION['logins']['team']['id']);
if ($turnier->check_team_angemeldet($_SESSION['logins']['team']['id'])){
    $team_angemeldet = true;
}else{
    $team_angemeldet = false;
}

//Turnieranmeldungen bekommen
$anmeldungen = $turnier->get_anmeldungen();

//Abmeldung möglich bis Freitag abend zwei Wochen vor dem Spieltag. Das Bedeutet zwei Wochen nach dem Loszeitpunkt:
$abmelden_moeglich_bis = LigaBot::time_offen_melde($turnier->details['datum']) + 2*7*24*60*60;

/////////////Formularauswertung der Turnieranmeldung/////////////

//Reguläres Anmelden
if (isset($_POST['anmelden'])){
    $error = false;
    if (!($turnier->details['art'] == 'I' or $turnier->details['art'] == 'II' or $turnier->details['art'] == 'III')){
        Html::error ("Anmeldungen zu Turnieren dieses Typs sind im Teamcenter nicht möglich.");
        $error = true;
    }
    //Test schon angemeldet
    if ($team_angemeldet){
        Html::error ("Dein Team ist schon zum Turnier angemeldet");
        $error = true;
    }
    //Richtige Phase
    if ($turnier->details['phase'] == 'spielplan'){
        Html::error ("Das Turnier befindet sich bereits in der Spielplanphase. Anmeldung nur noch über den Ligaausschuss: " . Html::mailto(Env::LAMAIL));
        $error = true;
    }
    if ($turnier->details['phase'] == 'ergebnis'){
        Html::error ("Das Turnier ist schon in der Ergebnisphase. Melde dich bei " .Html::mailto(Env::LAMAIL));
        $error = true;
    }
    //Test Teamblock
    /* Enfällt mit Modusänderung
    if (!$akt_turnier->check_team_block($_SESSION['logins']['team']['id'])){
        Form::error ("Falscher Teamblock");
        $error = true;
    }
    */
    if (!$error){
        //Position auf der Warteliste
        $pos = 0;
        //Richtige Liste finden
        if ($turnier->details['phase'] == 'offen'){
            $liste='melde';
        }
        if ($turnier->details['phase'] == 'melde'){
            $liste='spiele';
            if (!($turnier->check_team_block($_SESSION['logins']['team']['id']))){
                Html::info ("Dein Team wurde auf der Warteliste angemeldet, da der Turnierblock ("
                    . $turnier->details['tblock'] . ") nicht zu deinem Teamblock ("
                    . $_SESSION['logins']['team']['block'] . ") passt");
                $liste = 'warte';
                $anzahl_warteliste = count($anmeldungen['warte']);
                $pos = $anzahl_warteliste+1;
            }elseif ($turnier->get_anzahl_freie_plaetze() <= 0){
                Html::info ("Dein Team wurde auf der Warteliste angemeldet, da das Turnier voll ist");
                $liste = 'warte';
                $anzahl_warteliste = count($anmeldungen['warte']);
                $pos = $anzahl_warteliste + 1;
            }
        }
        //Team anmelden
        if ($turnier->check_doppel_anmeldung($_SESSION['logins']['team']['id'])){
            Html::error("Dein Team ist bereits auf einer Spiele-Liste am gleichen Kalendertag");
        }else{
            $turnier->anmelden($_SESSION['logins']['team']['id'], $liste, $pos);
            $turnier->log("Anmeldung: ". $_SESSION['logins']['team']['name'] ."\r\nTeamblock: ".$_SESSION['logins']['team']['block']. " Turnierblock: " . $turnier->details['tblock'] . "\r\nListe: $liste (WartePos: $pos)", $_SESSION['logins']['team']['name']);
            Html::info ("Dein Team wurde zum Turnier angemeldet");
            header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id']);
            die();
        }
    }else{
        Html::error ("Dein Team wurde nicht angemeldet.");
    }
}

//Freilos setzen
if (isset($_POST['freilos'])){
    $error = false;
    if (!($turnier->details['art'] == 'I' or $turnier->details['art'] == 'II' or $turnier->details['art'] == 'III')){
        Html::error ("Anmeldungen zu Turnieren dieses Typs sind im Teamcenter nicht möglich.");
        $error = true;
    }
    if ($turnier->get_liste($_SESSION['logins']['team']['id']) == 'spiele'){
        Html::error ("Dein Team ist schon auf der Spielen-Liste");
        $error = true;
    }elseif ($turnier->check_doppel_anmeldung($_SESSION['logins']['team']['id'])){
        Html::error("Dein Team ist bereits auf einer Spiele-Liste am gleichen Kalendertag");
        $error = true;
    }
    if ($akt_team->get_freilose() <= 0){
        Html::error ("Du hast kein Freilos mehr");
        $error = true;
    }
    if ($turnier->details['phase'] == 'spielplan'){
        Html::error ("Das Turnier befindet sich bereits in der Spielplanphase. Anmeldung nur noch über den Ligaausschuss: " . Html::mailto(Env::LAMAIL));
        $error = true;
    }
    if ($turnier->details['phase'] == 'ergebnis'){
        Html::error ("Das Turnier ist schon in der Ergebnisphase. Melde dich bei " .Html::mailto(Env::LAMAIL));
        $error = true;
    }
    if ($turnier->details['phase'] == 'melde' && $turnier->check_team_block($_SESSION['logins']['team']['id'])){
        Html::error ("Dein Team würde auch ohne Freilos auf die Spielen-Liste gesetzt werden.");
        $error = true;
    }
    if (!$turnier->check_team_block_freilos($_SESSION['logins']['team']['id'])){
        Html::error ("Turnierblock stimmt nicht. Freilose können nur für Turniere mit höheren oder passenden Block gesetzt werden.");
        $error = true;
    }
    if ($turnier->get_anzahl_freie_plaetze() <= 0 ){
        Html::error ("Die Spielen-Liste ist schon voll");
        $error = true;
    }
    if (!$error){
        $turnier->abmelden($_SESSION['logins']['team']['id']);
        $turnier->freilos($_SESSION['logins']['team']['id']);
        Html::info ("Dein Team wurde zum Turnier angemeldet");
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }else{
        Html::error ("Dein Team wurde nicht angemeldet.");
    }
}

//Team abmelden
if (isset($_POST['abmelden']) && isset($_SESSION['logins']['team'])){
    $error = false;
    $liste = $turnier->get_liste($_SESSION['logins']['team']['id']);
    if (empty($liste)){
        $error = true;
        Html::error ("Dein Team ist momentan nicht zum Turnier angemeldet");
    }
    if (time() > $abmelden_moeglich_bis && $liste == 'spiele'){
        $error = true;
        Html::error ("Abmeldungen von der Spielen-Liste sind nur bis Freitag 23:59 zwei Wochen vor dem Turnier möglich. Bitte nehmt via Email Kontakt mit dem Ligaausschuss auf: " .Html::mailto(Env::LAMAIL). "");
    }
    if (!$error){

        $turnier->abmelden($_SESSION['logins']['team']['id']);

        if ($turnier->get_anzahl_freie_plaetze() > 0 && $turnier->details['phase'] == 'melde')
            $turnier->spieleliste_auffuellen();

        if ($liste == 'warte' && $turnier->details['phase'] != 'offen')
            $turnier->warteliste_aktualisieren();

        Html::info ("Dein Team wurde erfolgreich abgemeldet");
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }
}

// Für Abschlussturnier bewerben
if (isset($_POST['bewerben'])){
    if ($turnier->details['art'] != 'final'){
        Html::error ("Anmeldung fehlgeschlagen.");
        $error = true;
    }
    if ($team_angemeldet){
        Html::error ("Dein Team ist schon angemeldet.");
        $error = true;
    }
    if (!$error){
        $turnier->anmelden($_SESSION['logins']['team']['id'],'melde',0);
        Html::info("Deine Meldung wurde erfolgreich entgegen genommmen.");
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }
}

// Hinweis Live-Spieltag
if (Tabelle::check_spieltag_live(Tabelle::get_aktuellen_spieltag())){
    Html::notice("Für den aktuelle Spieltag (ein Spieltag ist immer ein ganzes Wochenende) wurden noch nicht alle Ergebnisse eingetragen. Für die Turnieranmeldung gilt immer der Teamblock des letzten vollständigen Spieltages: "
        . Html::link("../liga/tabelle.php?spieltag=" . ($akt_spieltag - 1) . "#rang", "Spieltag " . ($akt_spieltag - 1)));
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">Turnieranmeldung</h2>
<h3 class="w3-text-grey"><?=$turnier->details['tname'] ?: 'Turnier'?> in <?=$turnier->details['ort']?>, <?=strftime("%d.%m.%Y (%A)", strtotime($turnier->details['datum']))?>(<?=$turnier->details['tblock']?>)</h3>

<!-- Weiterführende Links -->
<?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id'],
    "Turnierdetails",
    false,
    "keyboard_arrow_left") ?>
<?= Html::link('../liga/tc_turnierliste_anmelden.php?turnier_id=' . $turnier->details['turnier_id'],
    "Turnieranmeldeliste",
    false,
    "keyboard_arrow_right") ?>

<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-card w3-container">
    <form method='post'>
        <h3 class="w3-text-primary">Listen</h3>

        <!--Spielen-Liste-->
        <div class="w3-section">
            <p class="w3-text-grey w3-border-bottom w3-border-grey">Spielen-Liste</p>
            <p>
                <?php if (!empty($anmeldungen['spiele'])){?>
                    <?php foreach ($anmeldungen['spiele'] as $team){?>
                        <?php if ($team['teamname'] == $_SESSION['logins']['team']['name']) {$team['teamname'] = "<span class='w3-text-green'><b>".$team['teamname']."</b></span>";}?>
                        <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span>
                        <br>
                    <?php }//end foreach?>
                <?php }else{ ?><i>leer</i><?php } //endif?>
            </p>
        </div>
        <!--Meldeliste-->
        <div class="w3-section">
            <?php if (!empty($anmeldungen['melde'])){?>
                <p class="w3-text-grey w3-border-bottom w3-border-grey">Meldeliste</p>
                    <?php foreach ($anmeldungen['melde'] as $team){?>
                        <?php if ($team['teamname'] == $_SESSION['logins']['team']['name']) {$team['teamname'] = "<span class='w3-text-yellow'><b>".$team['teamname']."</b></span>";}?>
                        <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span>
                        <br>
                    <?php }//end foreach?>
            <?php } //endif?>
        </div>

        <!--Warteliste-->
        <div class="w3-section">
            <?php if (!empty($anmeldungen['warte'])){?>
                <p class="w3-opacity w3-border-bottom w3-border-black">Warteliste</p>
                <?php foreach ($anmeldungen['warte'] as $team){?>
                <?php if ($team['teamname'] == $_SESSION['logins']['team']['name']) {$team['teamname'] = "<span class='w3-text-primary'><b>".$team['teamname']."</b></span>";}?>
                <?=$team['position_warteliste'] . ". " . $team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span>
                <br>
                <?php }//end foreach?>
            <?php } //endif?>
        </div>
        <p>Freie Plätze: <?=$turnier->details['plaetze'] - count(($anmeldungen['spiele'] ?? array()))?> von <?=$turnier->details['plaetze']?></p>
        <p class="w3-small w3-text-primary">Phase: <?=$turnier->details['phase']?></p>
    </form>

    <!-- An- und Abmeldung -->
    <?php if ($turnier->details['art'] == 'final'){?>
        <form class="" method="post">
            <p>
                <input type='submit' class='w3-button w3-margin-bottom w3-block w3-tertiary <?php if ($team_angemeldet){?>w3-opacity<?php } //end if?>' name='bewerben' value='Melden für Abschlussturnier'>
                <span class="w3-text-grey"><i class="material-icons">info</i>Wir wollen auf dem Abschlussturnier spielen bzw. wir wären bereit nachzurücken.</span>
            </p>
        </form>
    <?php }else{?>
        <form class="" method="post">
            <p>
                <input type='submit' class='<?php if ($team_angemeldet){?>w3-opacity<?php } //endif?> w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='anmelden' value='Anmelden <?php if (!$turnier->check_team_block($_SESSION['logins']['team']['id']) && $turnier->details['phase'] == 'melde'){?>(Warteliste)<?php } //endif?>'>
            </p>
        </form>
        <form method="post" onsubmit="return confirm('Freilose setzen dein Team direkt auf die Spielen-Liste. Beim Übergang in die Meldephase wirst du auf die Warteliste gesetzt, wenn dein Teamblock höher ist als der Turnierblock. Das Freilos wird euch dann erstattet.');">
            <p>
                <input type='submit'
                    class='w3-button w3-margin-bottom w3-block w3-tertiary
                    <?php if (($turnier->get_liste($_SESSION['logins']['team']['id']) == 'spiele') or
                                !$turnier->check_team_block_freilos($_SESSION['logins']['team']['id']) or
                                $akt_team->get_freilose() <= 0){?>w3-opacity
                                <?php }//endif?>'
                    name='freilos' value='Freilos setzen (<?=$akt_team->get_freilose()?> vorhanden)'>
            </p>
        </form>
    <?php } //endif?>
        <form method="post" onsubmit="return confirm('Dein Team wird vom Turnier abgemeldet werden.');">
            <p><input type='submit' class='<?php if (!$team_angemeldet){?>w3-opacity<?php } //endif?> w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='abmelden' value='Abmelden'></p>
            <p class="w3-text-grey">Abmeldung von der Spielen-Liste ist möglich bis <?=strftime("%A, %d.%m.%Y %H:%M", $abmelden_moeglich_bis-1)?>&nbsp;Uhr</p>
        </form>
    </div>

<?php include '../../templates/footer.tmp.php';