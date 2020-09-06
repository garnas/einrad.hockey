<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //auth

$centerpanels = array(
  array("name" => "Turnier-anmeldung", "link" => "tc_turnierliste_anmelden.php", "farbe" => "w3-primary"),
  array("name" => "Eigene Turniere", "link" => "tc_turnierliste_verwalten.php", "farbe" => "w3-primary"),
  array("name" => "Turnier erstellen", "link" => "tc_turnier_erstellen.php", "farbe" => "w3-primary"),
  array("name" => "Kontaktcenter", "link" => "tc_kontaktcenter.php", "farbe" => "w3-tertiary"),
  array("name" => "Neuigkeit erstellen", "link" => "tc_neuigkeit_eintragen.php", "farbe" => "w3-tertiary"),
  array("name" => "Neuigkeit bearbeiten", "link" => "tc_neuigkeit_liste.php", "farbe" => "w3-tertiary"),
  array("name" => "Teamdaten", "link" => "tc_teamdaten.php", "farbe" => "w3-green"),
  array("name" => "Teamkader", "link" => "tc_kader.php", "farbe" => "w3-green"),
  array("name" => "Passwort ändern", "link" => "tc_pw_aendern.php", "farbe" => "w3-grey"),
  array("name" => "Logout", "link" => "tc_logout.php", "farbe" => "w3-grey"),
);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$page_width = '580px';
include '../../templates/header.tmp.php';
?>

<h1 class='w3-center w3-text-primary'>Hallo <?=$_SESSION['teamname']?>!</h1>
<div id="messen" class=""> <!-- Misst die Fensterbreite des Browsers, um die Centerpanels gleichmäßig verteilen zu können -->
  <div id="apps" class="w3-content">
    <div class="w3-row">

      <?php foreach ($centerpanels as $centerpanel) {?>
        <a class="" href="<?=$centerpanel['link']?>">
          <div class="w3-col <?=$centerpanel['farbe']?> w3-round-xxlarge w3-hover-opacity w3-display-container centerpanels">
            <div class="w3-display-middle w3-large w3-center">
              <?=$centerpanel['name']?>
            </div>
          </div>
        </a>
      <?php } //end foreach?>

    </div> <!-- w3-row -->
  </div> <!-- apps -->
</div> <!-- messen -->

<script>
  //setzt die Margin der centerpanels-Quadrate so, dass sie immer gleich verteilt sind
  window.addEventListener("resize", centerpanels_anordnung);
  centerpanels_anordnung();
</script>

<?php include '../../templates/footer.tmp.php';?>

