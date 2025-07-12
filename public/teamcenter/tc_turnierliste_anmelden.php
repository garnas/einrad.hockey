<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Entity\Turnier\Turnier;
use App\Service\Team\TeamService;
use App\Service\Turnier\TurnierSnippets;
use App\Service\Turnier\TurnierLinks;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$team_id = $_SESSION['logins']['team']['id'];

$turniere = TurnierRepository::getKommendeTurniere();

if ($turniere->isEmpty()) {
  Html::notice(
    'Zurzeit sind keine Turniere ausgeschrieben.',
    esc: false
  );
}

// Einfärben wenn schon angemeldet
$rowColor = static function (Turnier $turnier) use ($teamEntity) {
    if (TeamService::isAufSetzliste($teamEntity, $turnier)) {
        return 'w3-pale-green';
    }
    if (TeamService::isAufWarteliste($teamEntity, $turnier) && $turnier->isWartePhase()) {
        return 'w3-pale-yellow';
    }
    if (TeamService::isAufWarteliste($teamEntity, $turnier) && $turnier->isSetzPhase()) {
        return 'w3-pale-blue';
    }
    return '';
};

// LAYOUT
include '../../templates/header.tmp.php';?>

<h2 class="w3-text-primary" style='display: inline;'>Turnieranmeldung und -abmeldung</h2>
<!-- Trigger/Open the Modal -->
<p>
    <button onclick="document.getElementById('id01').style.display='block'" class="w3-button w3-text-primary">
        <?= Html::icon("help") ?> Legende
    </button>
</p>
<!-- The Modal -->
<div id="id01" class="w3-modal">
  <div class="w3-modal-content" style="max-width:400px">
    <div class="w3-container w3-card-4 w3-border w3-border-black">
      <span onclick="document.getElementById('id01').style.display='none'"
      class="w3-button w3-display-topright">&times;</span>
        
        <h3>Legende:</h3>
        <p>
        Reihen:<br>
        <span class="w3-pale-green">Auf Setzliste<br></span>
        <span class="w3-pale-blue">Auf Warteliste in der Setzphase<br></span>
        <span class="w3-pale-yellow">Auf Warteliste in der Wartephase<br></span>
        <br>
        <i><span class="w3-text-green">(Block)</span>: Anmeldung möglich</i><br>
        <i><span class="w3-text-yellow">(Block)</span>: Freilos möglich</i><br>
        <i><span class="w3-text-red">(Block)</span>: Falscher Block</i><br>
        <br>
        <i><span class="w3-text-green">frei</span>: Plaetze auf der Setzliste frei</i><br>
        <i><span class="w3-text-red">voll</span>: Setzliste ist voll</i><br>
        <i><span class="w3-text-gray">geschlossen</span>: Anmeldung nicht mehr möglich</i><br>
        </p>
    </div>
  </div>
</div>

<script>
    // Get the modal
    let modal = document.getElementById('id01');

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    }
</script>

<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-bordered w3-striped w3-centered" style="white-space: nowrap">
        <tr class='w3-primary'>
            <th>Status</th>
            <th>Datum</th>
            <th>Ort</th>
            <th>Block</th>
            <th><i>Phase<i></th>
            <th><i>Ausrichter<i></th>

        </tr>
        <?php foreach ($turniere as $turnier): ?>
            <tr class="<?= isset($rowColor) ? $rowColor($turnier) : '' ?>">
                <td><?= TurnierSnippets::status($turnier) ?></td>
                <td><?= $turnier->getDatum()->format("d.m.y") ?></td>
                <td><?= e($turnier->getDetails()->getOrt()) ?></td>
                <td><?= TurnierSnippets::blockColor($turnier, $teamEntity) ?></td>
                <td><?= TurnierSnippets::phase($turnier) ?></td>
                <td><?= e($turnier->getAusrichter()->getName()) ?></i></td>
            </tr>
            <tr>
                <td colspan="4" style="white-space:normal;" class="w3-left-align">
                    <?= implode("<br>", TurnierLinks::getLinksAnmeldeListe($turnier)) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php include '../../templates/footer.tmp.php';