<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //auth

$centerpanels = array(
  array("name" => "Turniere verwalten", "link" => "lc_turnierliste.php", "farbe" => "w3-primary"),
  array("name" => "Turnier erstellen", "link" => "lc_turnier_erstellen.php", "farbe" => "w3-primary"),
  array("name" => "Neuigkeit eintragen", "link" => "lc_neuigkeit_eintragen.php", "farbe" => "w3-primary"),
  array("name" => "Neuigkeit bearbeiten", "link" => "../liga/neues.php", "farbe" => "w3-primary"),
  array("name" => "Teams-Übersicht", "link" => "lc_teams_uebersicht.php", "farbe" => "w3-tertiary"),
  array("name" => "Team erstellen", "link" => "lc_team_erstellen.php", "farbe" => "w3-tertiary"),
  array("name" => "Teamdaten verwalten", "link" => "lc_teamdaten.php", "farbe" => "w3-tertiary"),
  array("name" => "Teamstrafen", "link" => "lc_teamstrafe.php", "farbe" => "w3-tertiary"),
  array("name" => "Teamkader verwalten", "link" => "lc_kader.php", "farbe" => "w3-tertiary"),
  array("name" => "Spieler verwalten", "link" => "lc_spieler_aendern.php", "farbe" => "w3-tertiary"),
  array("name" => "Emails verschicken", "link" => "lc_emails.php", "farbe" => "w3-green"),
  array("name" => "Passwort ändern", "link" => "lc_pw_aendern.php", "farbe" => "w3-secondary"),
  array("name" => "Admin", "link" => "lc_admin.php", "farbe" => "w3-secondary"),
  array("name" =>"Logout", "link" => "lc_logout.php", "farbe" => "w3-secondary"),
);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$page_width = "580px";
include '../../templates/header.tmp.php';
?>

<h1 class='w3-center w3-text-primary'>Hallo <?=ligaleitung::get_la_name($_SESSION['la_id']);?>!</h1>
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
  </div> <!-- w3-apps -->
</div> <!-- w3-messen -->

<script>
  //setzt die Margin der centerpanels-Quadrate so, dass sie immer gleich verteilt sind
  window.addEventListener("resize", centerpanels_anordnung);
  centerpanels_anordnung();
</script>

<?php include '../../templates/footer.tmp.php';?>

