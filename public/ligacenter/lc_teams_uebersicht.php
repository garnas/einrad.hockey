<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

$teams = Team::get_teams();
$max_schiris = $max_spieler = $teams_mit_zwei_schiris = $teams_zweites_freilos_erhalten = 0;
foreach ($teams as $team_id => $team){
    $genug_schiris = false;
    $kader = nSpieler::get_kader($team_id, Config::SAISON);
    $kader_alt = nSpieler::get_kader($team_id, Config::SAISON - 1) + nSpieler::get_kader($team_id, Config::SAISON - 2);


    $teams[$team_id]['kader'] = count($kader);
    $teams[$team_id]['kader_alt'] = count($kader_alt);

    $teams[$team_id]['schiris'] = $teams[$team_id]['schiris_alt'] = 0;

    //Schiris zählen:
    foreach ($kader as $spieler){
        if ($spieler->schiri >= Config::SAISON){
            $teams[$team_id]['schiris']++;
            if ($teams[$team_id]['schiris'] >= 2){ $genug_schiris = true;}
        }
    }
    foreach ($kader_alt as $spieler){
        if ($spieler->schiri >= Config::SAISON){
            $teams[$team_id]['schiris_alt']++;
        }
    }

    $max_schiris += $teams[$team_id]['schiris'];
    $max_spieler += $teams[$team_id]['kader'];

    if ($genug_schiris){
        ++$teams_mit_zwei_schiris;
    }
    if (Team::static_check_schiri_freilos_erhalten($team['zweites_freilos'])){
        ++$teams_zweites_freilos_erhalten;
    }
}

//Hinzufügen des zweiten Freiloses für zwei Schiris zu Saisonbeginn
//$team_liste = '';
//if (isset($_POST['zweites_freilos'])){
//    foreach ($teams as $team_id => $team){
//        if ($team['schiris'] >= 2){
//            Team::add_freilos($team_id);
//            $betreff = 'Zweites Freilos';
//            $text =
//                "<html>Hallo " . $team['teamname'] . ","
//                ."<br><br>da ihr zwei ausgebildete Schiedsrichter im Kader eingetragen habt, wurde euch euer zweites Freilos gutgeschrieben."
//                ."<br><br>Wir wünschen euch eine schöne Saison " . Html::get_saison_string() . "!"
//                ."<br><br>Eure Einradhockeyliga</html>";
//            $akt_kontakt = new Kontakt ($team_id);
//            $adressaten = $akt_kontakt->get_emails();
//            MailBot::add_mail($betreff, $text, $adressaten);
//            $team_liste .= "<br>" . $team['teamname'];
//        }
//    }
//    Html::info("Freilose vergeben an:<br>" . $team_liste);
//    header('Location: lc_teams_uebersicht.php');
//    die();
//}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
<h1 class="w3-text-primary">Übersicht Ligateams</h1>
<span class="w3-text-grey">Saison <?=Html::get_saison_string()?></span>

<!-- Infobox -->
<div style="font-weight: bold;">
    <p>&sum; Spieler: <span class="w3-text-green"><?=$max_spieler?></span></p>
    <p>&sum; Schiris: <span class="w3-text-green"><?=$max_schiris?></span></p>
    <p>&sum; Teams: <span class="w3-text-green"><?=count($teams)?></span></p>
    <p>&sum; Teams mit zwei oder mehr Schiris: <span class="w3-text-green"><?=$teams_mit_zwei_schiris?></span></p>
    <p>&sum; Teams, die ihr Freilos für zwei Schiris erhalten haben: <span class="w3-text-green"><?=$teams_zweites_freilos_erhalten?></span></p>
</div>

<!-- Button 2. Freilos 
<form class="w3-section" method='post'>
    <input disabled type="submit" name="zweites_freilos" class="w3-button w3-secondary" value="Zweites Freilos vergeben">
    <span class="w3-text-grey">Es werden Mails an die betroffenen Teams versendet<br></span>
</form>-->
<p><i>Legende:<br>
    <span class="w3-pale-green">Freilos für zwei Schiris erhalten</span>
    <br>
    <span class="w3-pale-red">keine fünf Spieler im Kader</span>
</i></p>

<!-- Tabelle -->
<div class="w3-responsive w3-card">
    <table style="white-space: nowrap" class="w3-table w3-striped w3-centered">
        <tr class=w3-primary>
            <th>Team ID</th>
            <th>Teamname</th>
            <th>Freilose</th>
            <th>2-Schiri-Freilos</th>
            <th>Kader</th>
            <th>Schiris</th>
            <th>Kader (alt)</th>
            <th>Schiris (alt)</th>
        </tr>
        <?php foreach ($teams as $team_id => $team){?>
            <tr class="w3-center
                        <?php if(Team::static_check_schiri_freilos_erhalten($team['zweites_freilos'])){ ?>
                            w3-pale-green
                        <?php }elseif($team['kader'] < 5){?>
                            w3-pale-red
                        <?php }//endif?>">
                <td><?=$team['team_id']?></td>
                <td><?=Html::link('lc_kader.php?team_id='. $team_id, $team['teamname'])?></td>
                <td><?=$team['freilose']?></td>
                <td><?=$team['zweites_freilos']?></td>
                <td><?=$team['kader']?></td>
                <td><?=$team['schiris']?></td>
                <td class="w3-text-grey"><?=$team['kader_alt']?></td>
                <td class="w3-text-grey"><?=$team['schiris_alt']?></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
<?php include '../../templates/footer.tmp.php';