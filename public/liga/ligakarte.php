<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$koordinaten = LigaKarte::get_all_team_koordinaten();

//Doppelte PLZ Einträge werden zusammengefasst
$koordinaten_hilf = $koordinaten;
$array = $array_hilf = array();
foreach ($koordinaten as $keya => $teama){
    $latlnga = array($teama['LAT'], $teama['Lon']);
    $doppelt = false;
    unset($koordinaten_hilf[$keya]);

    foreach ($koordinaten_hilf as $keyb => $teamb){
        $latlngb = array($teamb['LAT'], $teamb['Lon']);
        if ($latlnga == $latlngb){
            array_push($array_hilf,$teamb);
            $teama['teamname'] .= "</h5><h5 class=\"w3-text-primary\">" . $teamb['teamname'];
            unset($koordinaten_hilf[$keyb]);
            $doppelt = true;
        }
    }
    if (!in_array($teama,$array_hilf)){
    array_push($array,$teama);
    }
}

//Teamgesuche Formularauswertung
if (isset($_POST['eintragen'])){
  if (empty($_POST['name']) or empty($_POST['kontakt']) or empty($_POST['plz']) or empty($_POST['ort'])){
    Form::error("Bitte Formular vollständig ausfüllen");
    $error = true;
  }

  $lonlat = LigaKarte::plz_to_lonlat($_POST['plz']);
  if(empty($lonlat)){
    Form::error("Deine eingegebene Postleitzahl wurde nicht gefunden.");
    $error = true;
  }

  if(!$error){
    LigaKarte::gesuch_eintragen_db($_POST['plz'],$_POST['ort'],$lonlat['LAT'],$lonlat['Lon'],$_POST['name'],$_POST['kontakt']);
    Form::affirm("Dein Gesuch wurde eingetragen");
    header("Location: ligakarte.php");
    die();
  }
}
$gesuche = LigaKarte::get_all_gesuche();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = 'Ligakarte | Deutsche Einradhockeyliga';
$content = "Deutschlandkarte der Deutschen Einradhockeyliga, in der alle Teams mit ihren Einradfahrern eingetragen sind.";
include '../../templates/header.tmp.php';
?>

<h1 class='w3-border-bottom w3-text-primary'>Karte der Ligateams<span class="w3-right w3-hide-small"><?=Form::get_saison_string()?></span></h1>
<p>Es spielen zurzeit <?=count(Team::list_of_all_teams())?> Teams in der Deutschen Einradhockeyliga. <a href="teams.php" class="no w3-text-blue w3-hover-text-secondary">Hier</a> findest du eine Liste aller Teams mit ihrer hinterlegten E-Mail-Adresse.</p>
  
<p><i>If your team resides outside of Germany, please contact <?=Form::mailto(Config::TECHNIKMAIL)?> to be included in the map.</i></p>

<div class='w3-card-4' style='height: 70vh; width: 100%; max-height: 800px; margin: auto;' id="map">
</div>

<h2 class='w3-border-bottom w3-text-primary'>Mitspieler suchen</h2>
<p>Du kannst einen Eintrag in die Ligakarte erstellen, um Einradhockeyspieler in deiner Umgebung zu finden. Der Eintrag wird ein Jahr lang angezeigt.<p>
<button onclick="document.getElementById('gesuch_formular').style.display='block'" class="w3-button w3-tertiary">Mitspielergesuch eintragen</button>
<div id="gesuch_formular" class="w3-modal">
  <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content" autocomplete="off" style="max-width: 400px" 
  onsubmit="return confirm('Alle Daten die du hier eingeben hast werden gespeichert und ein Jahr lang hier veröffentlicht. Wende dich an <?=Config::LAMAIL?> um deinen Eintrag zu löschen.');">
    <span onclick="document.getElementById('gesuch_formular').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
    <h3 class="w3-text-primary">Mitspielergesuch eintragen</h3>
    <p><i>Hinweis: Alle hier eingebenen Daten werden ein Jahr lang veröffentlicht. Schreibe <?=Form::mailto(Config::LAMAIL)?> an, um deinen Eintrag zu löschen.</i></p>
    <p>
      <label for="kontakt" class="w3-text-primary">Name</label>
      <input required type="text" class="w3-input w3-border-primary" name="name" id="name" value="<?=$_POST['name'] ?? ''?>">
    </p>
    <p>
      <label for="kontakt" class="w3-text-primary">PLZ</label>
      <input required type="number" class="w3-input  w3-border-primary" name="plz" id="plz" value="<?=$_POST['plz'] ?? ''?>">
    </p>
    <p>
      <label for="kontakt" class="w3-text-primary">Ort</label>
      <input required type="text" class="w3-input  w3-border-primary" name="ort" id="ort" value="<?=$_POST['ort'] ?? ''?>">
    </p>
    <p>
      <label for="kontakt" class="w3-text-primary">Kontakt (E-Mail, Handy o. ä.)</label>
      <input required type="text" class="w3-input  w3-border-primary" name="kontakt" id="kontakt" value="<?=$_POST['kontakt'] ?? ''?>">
    </p>
    <p>
      <input type="submit" class="w3-button w3-block w3-tertiary" value="Eintragen" name="eintragen">
    </p>
  </form>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAwgrtZFHGafGYgopQSVoxGrBOBxVdcaUE&callback=initMap">
</script>

<script>
function initMap() {
  var mitte = {lat: 51.23, lng: 10.47};
  var map = new google.maps.Map(document.getElementById('map'), {
    zoom: 5.5,
    center: mitte
  });

  //Ligateams eintragen
  <?php foreach ($array as $team){?>

    var contentString<?=$team['team_id']?> = 
    '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h5 class="w3-text-primary"><?=$team['teamname']?></h5>'+
        '<div class="w3-bottombar"></div>'+
            '<p><?=$team['plz'] . " " .  $team['ort']?></p>'+
            '<a href="teams.php" class="no w3-text-blue w3-hover-text-tertiary">Zur Kontaktliste</a>'+
        '</div>';

    var infowindow<?=$team['team_id']?> = new google.maps.InfoWindow({
    content: contentString<?=$team['team_id']?>
    });

    var marker<?=$team['team_id']?> = new google.maps.Marker({
    position: {<?='lat: ' . $team['LAT'] . ', lng: ' . $team['Lon'] ?>},
    map: map,
    title: '<?=$team['verein'] ?: $team['ort']?>'
    });

    marker<?=$team['team_id']?>.addListener('click', function() {
    infowindow<?=$team['team_id']?>.open(map, marker<?=$team['team_id']?>);
    });

  <?php } //endforeach?>

  //Gesuche Eintragen
  <?php foreach ($gesuche as $gesuch){?>

    var scontentString<?=$gesuch['gesuch_id']?> = 
    '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h5 class="w3-bottombar w3-border-tertiary">Mitspieler gesucht!</h5>'+
            '<p>Name: <?=$gesuch['r_name']?></p>'+
            '<p>Kontakt: <?=$gesuch['kontakt']?></p>'+
            '<p>Ort: <?=$gesuch['plz'] . ' ' . $gesuch['ort']?></p>'+
        '</div>';

    var sinfowindow<?=$gesuch['gesuch_id']?> = new google.maps.InfoWindow({
    content: scontentString<?=$gesuch['gesuch_id']?>
    });
    
    //Bild welches der Marker haben soll
    var image = '../bilder/tennisball.gif';

    var smarker<?=$gesuch['gesuch_id']?> = new google.maps.Marker({
    position: {<?='lat: ' . $gesuch['LAT'] . ', lng: ' . $gesuch['Lon'] ?>},
    map: map,
    title: '<?=$gesuch['r_name']?>',
    //label: 'S',
    icon: image
    });

    smarker<?=$gesuch['gesuch_id']?>.addListener('click', function() {
    sinfowindow<?=$gesuch['gesuch_id']?>.open(map, smarker<?=$gesuch['gesuch_id']?>);
    });

  <?php } //endforeach?>

} //Javascript
</script>

<?php include '../../templates/footer.tmp.php';?>