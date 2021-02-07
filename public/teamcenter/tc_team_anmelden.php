<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

//Check ob das Team über fünf Spieler verfügt
if (count(Spieler::get_teamkader($_SESSION['team_id'])) < 5){
    Form::affirm('Bitte tragt euren Teamkader ein, um euch zu Turnieren anmelden zu können.');
    header('Location: ../teamcenter/tc_kader.php');
    die();
}

//Turnierobjekt erstellen
$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier ($turnier_id);

//Existiert das Turnier?
if (empty($akt_turnier->details)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../teamcenter/tc_turnierliste_anmelden.php');
    die();
}
if (strtotime($akt_turnier->details['datum']) < Config::time_offset()){
    Form::error ("Das Turnier liegt in der Vergangenheit.");
    header('Location: ../teamcenter/tc_turnierliste_anmelden.php');
    die();
}

if ($akt_turnier->details['art'] == 'spass'){
    $kontakt = new Kontakt ($akt_turnier->details['ausrichter']);
    $email = $kontakt->get_emails();
    Form::attention("Anmeldung zu Spass-Turnieren erfolgt über den Ausrichter: " . Form::mailto($email, Team::teamid_to_teamname($akt_turnier->details['ausrichter'])));
    header('Location: ../liga/turnier_details.php?turnier_id=' . $akt_turnier->id);
    die();
}

$akt_team = new Team($_SESSION['team_id']);
if ($akt_turnier->check_team_angemeldet($_SESSION['team_id'])){
    $team_angemeldet = true;
}else{
    $team_angemeldet = false;
}

//Turnieranmeldungen bekommen
$anmeldungen = $akt_turnier->get_anmeldungen();

//Abmeldung möglich bis Freitag abend zwei Wochen vor dem Spieltag. Das Bedeutet zwei Wochen nach dem Loszeitpunkt:
$abmelden_moeglich_bis = LigaBot::time_offen_melde($akt_turnier->details['datum']) + 2*7*24*60*60;

/////////////Formularauswertung der Turnieranmeldung/////////////

//Reguläres Anmelden
if (isset($_POST['anmelden'])){
    $error = false;
    if (!($akt_turnier->details['art'] == 'I' or $akt_turnier->details['art'] == 'II' or $akt_turnier->details['art'] == 'III')){
        Form::error ("Anmeldungen zu Turnieren dieses Typs sind im Teamcenter nicht möglich.");
        $error = true;
    }
    //Test schon angemeldet
    if ($team_angemeldet){
        Form::error ("Dein Team ist schon zum Turnier angemeldet");
        $error = true;
    }
    //Richtige Phase
    if ($akt_turnier->details['phase'] == 'spielplan'){
        Form::error ("Das Turnier befindet sich bereits in der Spielplanphase. Anmeldung nur noch über den Ligaausschuss: " . Form::mailto(Config::LAMAIL));
        $error = true;
    }
    if ($akt_turnier->details['phase'] == 'ergebnis'){
        Form::error ("Das Turnier ist schon in der Ergebnisphase. Melde dich bei " .Form::mailto(Config::LAMAIL));
        $error = true;
    }
    //Test Teamblock
    /* Enfällt mit Modusänderung
    if (!$akt_turnier->check_team_block($_SESSION['team_id'])){
        Form::error ("Falscher Teamblock");
        $error = true;
    }
    */
    if (!$error){
        //Position auf der Warteliste
        $pos = 0;
        //Richtige Liste finden
        if ($akt_turnier->details['phase'] == 'offen'){
            $liste='melde';
        }
        if ($akt_turnier->details['phase'] == 'melde'){
            $liste='spiele';
            if (!($akt_turnier->check_team_block($_SESSION['team_id']))){
                Form::affirm ("Dein Team wurde auf der Warteliste angemeldet, da der Turnierblock (" . $akt_turnier->details['tblock'] . ") nicht zu deinem Teamblock (" . $_SESSION['teamblock'] . ") passt");
                $liste = 'warte';
                $anzahl_warteliste = count($anmeldungen['warte']);
                $pos = $anzahl_warteliste+1;
            }elseif ($akt_turnier->get_anzahl_freie_plaetze() <= 0){
                Form::affirm ("Dein Team wurde auf der Warteliste angemeldet, da das Turnier voll ist");
                $liste = 'warte';
                $anzahl_warteliste = count($anmeldungen['warte']);
                $pos = $anzahl_warteliste + 1;
            }
        }
        //Team anmelden
        if ($akt_turnier->check_doppel_anmeldung($_SESSION['team_id'])){
            Form::error("Dein Team ist bereits auf einer Spiele-Liste am gleichen Kalendertag");
        }else{
            $akt_turnier->team_anmelden($_SESSION['team_id'], $liste, $pos);
            $akt_turnier->log("Anmeldung: ". $_SESSION['teamname'] ."\r\nTeamblock: ".$_SESSION['teamblock']. " Turnierblock: " . $akt_turnier->details['tblock'] . "\r\nListe: $liste (WartePos: $pos)", $_SESSION['teamname']);
            Form::affirm ("Dein Team wurde zum Turnier angemeldet");
            header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $akt_turnier->details['turnier_id']);
            die();
        }
    }else{
        Form::error ("Dein Team wurde nicht angemeldet.");
    }
}

//Freilos setzen
if (isset($_POST['freilos'])){
    $error = false;
    if (!($akt_turnier->details['art'] == 'I' or $akt_turnier->details['art'] == 'II' or $akt_turnier->details['art'] == 'III')){
        Form::error ("Anmeldungen zu Turnieren dieses Typs sind im Teamcenter nicht möglich.");
        $error = true;
    }
    if ($akt_turnier->get_liste($_SESSION['team_id']) == 'spiele'){
        Form::error ("Dein Team ist schon auf der Spielen-Liste");
        $error = true;
    }elseif ($akt_turnier->check_doppel_anmeldung($_SESSION['team_id'])){
        Form::error("Dein Team ist bereits auf einer Spiele-Liste am gleichen Kalendertag");
        $error = true;
    }
    if ($akt_team->get_freilose() <= 0){
        Form::error ("Du hast kein Freilos mehr");
        $error = true;
    }
    if ($akt_turnier->details['phase'] == 'spielplan'){
        Form::error ("Das Turnier befindet sich bereits in der Spielplanphase. Anmeldung nur noch über den Ligaausschuss: " . Form::mailto(Config::LAMAIL));
        $error = true;
    }
    if ($akt_turnier->details['phase'] == 'ergebnis'){
        Form::error ("Das Turnier ist schon in der Ergebnisphase. Melde dich bei " .Form::mailto(Config::LAMAIL));
        $error = true;
    }
    if ($akt_turnier->details['phase'] == 'melde' && $akt_turnier->check_team_block($_SESSION['team_id'])){
        Form::error ("Dein Team würde auch ohne Freilos auf die Spielen-Liste gesetzt werden.");
        $error = true;
    }
    if (!$akt_turnier->check_team_block_freilos($_SESSION['team_id'])){
        Form::error ("Turnierblock stimmt nicht. Freilose können nur für Turniere mit höheren oder passenden Block gesetzt werden.");
        $error = true;
    }
    if ($akt_turnier->get_anzahl_freie_plaetze() <= 0 ){
        Form::error ("Die Spielen-Liste ist schon voll");
        $error = true;
    }
    if (!$error){
        $akt_turnier->abmelden($_SESSION['team_id']);
        $akt_turnier->freilos($_SESSION['team_id']);
        $akt_turnier->log("Freilos: ". $_SESSION['teamname'] ."\r\nTeamblock: ".$_SESSION['teamblock']. " Turnierblock: " . $akt_turnier->details['tblock'] . "\r\nListe: spiele\r\n(WartePos: 0)", $_SESSION['teamname']);
        Form::affirm ("Dein Team wurde zum Turnier angemeldet");
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $akt_turnier->details['turnier_id']);
        die();
    }else{
        Form::error ("Dein Team wurde nicht angemeldet.");
    }
}

//Team abmelden
if (isset($_POST['abmelden']) && isset($_SESSION['team_id'])){
    $error = false;
    $liste = $akt_turnier->get_liste($_SESSION['team_id']);
    if (empty($liste)){
        $error = true;
        Form::error ("Dein Team ist momentan nicht zum Turnier angemeldet");
    }
    if (Config::time_offset() > $abmelden_moeglich_bis && $liste == 'spiele'){
        $error = true;
        Form::error ("Abmeldungen von der Spielen-Liste sind nur bis Freitag 23:59 zwei Wochen vor dem Turnier möglich. Bitte nehmt via Email Kontakt mit dem Ligaausschuss auf: " .Form::mailto(Config::LAMAIL). "");
    }
    if (!$error){
        if ($liste == 'warte' && $akt_turnier->details['phase'] != 'offen'){
            $warteliste_neu = true;
        }else{
            $warteliste_neu = false;
        }
        $akt_turnier->abmelden($_SESSION['team_id']);
        Form::affirm ("Dein Team wurde erfolgreich abgemeldet");
        if ($akt_turnier->get_anzahl_freie_plaetze() > 0 && $akt_turnier->details['phase'] == 'melde'){
            $akt_turnier->spieleliste_auffuellen();
        }
        if ($warteliste_neu){
            $akt_turnier->warteliste_aktualisieren();
        }
        $akt_turnier->log("Abmeldung: ". $_SESSION['teamname'] ."\r\nvon Liste: " . $liste, $_SESSION['teamname']);
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $akt_turnier->details['turnier_id']);
        die();
    }
}

//Für Abschlussturnier bewerben
if (isset($_POST['bewerben'])){
    if ($akt_turnier->details['art'] != 'final'){
        Form::error ("Anmeldung fehlgeschlagen.");
        $error = true;
    }
    if ($team_angemeldet){
        Form::error ("Dein Team ist schon angemeldet.");
        $error = true;
    }
    if (!$error){
        $akt_turnier->team_anmelden($_SESSION['team_id'],'melde',0);
        $akt_turnier->log("Bewerbung für Spielplatz:\r\n". $_SESSION['teamname'] ."\r\nauf Liste: melde", $_SESSION['teamname']);
        header('Location: ../teamcenter/tc_team_anmelden.php?turnier_id=' . $akt_turnier->details['turnier_id']);
        die();
    }
}

//Hinweis Live-Spieltag
$akt_spieltag = Tabelle::get_aktuellen_spieltag();
if (Tabelle::check_spieltag_live($akt_spieltag)){
    Form::attention(
        "Für den aktuelle Spieltag (ein Spieltag ist immer ein ganzes Wochenende) wurden noch nicht alle Ergebnisse eingetragen. Für die Turnieranmeldung gilt immer der Teamblock des letzten vollständigen Spieltages: "
        . Form::link("../liga/tabelle.php?spieltag=" . ($akt_spieltag - 1) . "#rang", "Spieltag " . ($akt_spieltag - 1)));
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">Turnieranmeldung</h2>
<h3 class="w3-text-grey"><?=$akt_turnier->details['tname'] ?: 'Turnier'?> in <?=$akt_turnier->details['ort']?>, <?=strftime("%d.%m.%Y (%A)", strtotime($akt_turnier->details['datum']))?> (<?=$akt_turnier->details['tblock']?>)</h3>

<a href='../liga/turnier_details.php?turnier_id=<?=$akt_turnier->details['turnier_id']?>' class="w3-text-hover-secondary w3-text-blue no">
    <i class="material-icons">keyboard_arrow_left</i>Turnierdetails
</a>
<a href='../teamcenter/tc_turnierliste_anmelden.php?turnier_id=<?=$akt_turnier->details['turnier_id']?>' class="w3-right w3-text-hover-secondary w3-text-blue no">
    Turnieranmeldeliste<i class="material-icons">keyboard_arrow_right</i>
</a>
<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-card w3-container">
    <form method='post'>
        <h3 class="w3-text-primary">Listen</h3>

        <!--Spielen-Liste-->
        <p class="w3-text-grey w3-border-bottom w3-border-grey">Spielen-Liste</p> 
        <p>
            <?php if (!empty($anmeldungen['spiele'])){?>
                <?php foreach ($anmeldungen['spiele'] as $team){?>
                    <?php if ($team['teamname'] == $_SESSION['teamname']) {$team['teamname'] = "<span class='w3-text-green'><b>".$team['teamname']."</b></span>";}?>
                    <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span>
                    <br>
                <?php }//end foreach?>
            <?php }else{ ?><i>leer</i><?php } //endif?> 
        </p>
        <!--Meldeliste-->
        <p>
            <?php if (!empty($anmeldungen['melde'])){?>
                <p class="w3-text-grey w3-border-bottom w3-border-grey">Meldeliste</p> 
                    <?php foreach ($anmeldungen['melde'] as $team){?>
                        <?php if ($team['teamname'] == $_SESSION['teamname']) {$team['teamname'] = "<span class='w3-text-yellow'><b>".$team['teamname']."</b></span>";}?>
                        <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span>
                        <br>
                    <?php }//end foreach?>
            <?php } //endif?>
        </p>
        
        <!--Warteliste-->
        <p>
            <?php if (!empty($anmeldungen['warte'])){?>
                <p class="w3-opacity w3-border-bottom w3-border-black">Warteliste</p> 
                <?php foreach ($anmeldungen['warte'] as $team){?>
                <?php if ($team['teamname'] == $_SESSION['teamname']) {$team['teamname'] = "<span class='w3-text-blue'><b>".$team['teamname']."</b></span>";}?>
                <?=$team['position_warteliste'] . ". " . $team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span>
                <br>
                <?php }//end foreach?>
            <?php } //endif?> 
        </p>
        <p>Freie Plätze: <?=$akt_turnier->details['plaetze'] - count(($anmeldungen['spiele'] ?? array()))?> von <?=$akt_turnier->details['plaetze']?></p>
        <p class="w3-small w3-text-primary">Phase: <?=$akt_turnier->details['phase']?></p>
    </form>

    <!-- An- und Abmeldung -->
    <?php if ($akt_turnier->details['art'] == 'final'){?>
        <form class="" method="post">
            <p>
                <input type='submit' class='w3-button w3-margin-bottom w3-block w3-tertiary <?php if ($team_angemeldet){?>w3-opacity<?php } //end if?>' name='bewerben' value='Bewerben'>
                <span class="w3-text-grey"><i class="material-icons">info</i>Wir wollen auf dem Abschlussturnier spielen bzw. wir wären bereit nachzurücken.</span>
            </p>
        </form>
    <?php }else{?>
        <form class="" method="post">
            <p>
                <input type='submit' class='<?php if ($team_angemeldet){?>w3-opacity<?php } //endif?> w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='anmelden' value='Anmelden <?php if (!$akt_turnier->check_team_block($_SESSION['team_id']) && $akt_turnier->details['phase'] == 'melde'){?>(Warteliste)<?php } //endif?>'>
            </p>
        </form>
        <form method="post" onsubmit="return confirm('Freilose setzen dein Team direkt auf die Spielen-Liste. Beim Übergang in die Meldephase wirst du auf die Warteliste gesetzt, wenn dein Teamblock höher ist als der Turnierblock. Das Freilos wird euch dann erstattet.');">
            <p>
                <input type='submit' 
                    class='w3-button w3-margin-bottom w3-block w3-tertiary  
                    <?php if (($akt_turnier->get_liste($_SESSION['team_id']) == 'spiele') or
                                !$akt_turnier->check_team_block_freilos($_SESSION['team_id']) or 
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
</div>

<?php include '../../templates/footer.tmp.php';