<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';

$alle_teamdaten = TeamRepository::get()->activeLigaTeams();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Teamliste | Deutsche Einradhockeyliga";
Html::$content = "Liste der Teams der Deutschen Einradhockeyliga mit Teamfoto und Kontaktmöglichkeit.";
include '../../templates/header.tmp.php';
?>

    <script src="<?= Env::BASE_URL ?>/javascript/jquery.min.js?v=20250825"></script>
    
    <script>
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV > div").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>

    <h1 class='w3-text-primary w3-border-bottom w3-border-grey'>
        Ligateams
        <span class="w3-right w3-hide-small">Saison <?= Html::get_saison_string() ?></span>
    </h1>
    <!-- Legende -->
    <p>
        <span class="w3-right w3-text-primary">
          <?= Html::icon("home") ?>&nbsp;Homepage
          <?= Html::icon("group") ?>&nbsp;Teamfoto
          <?= Html::icon("mail") ?>&nbsp;Email
        </span>
    </p>
    <br class="w3-hide-large w3-hide-medium">
    <p><?= Html::link("ligakarte.php", 'Ligakarte aller Teams', true, 'place') ?></p>

    <!-- Team suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Html::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;"
                                          type="text" placeholder="Team suchen">
    </div>

    <!-- Teams Tabelle -->
<div id="myDIV">

    <?php foreach ($alle_teamdaten as $team): ?>
        <div id="<?= $team->id() ?>" class="w3-card-4 w3-margin-bottom">
            
            <!-- Teamname -->
            <div class="w3-container w3-primary w3-padding w3-large">
                <?= $team->getName() ?>
            </div>

            <!-- Infos -->
            <div class="w3-container w3-padding">
                <div><?= $team->getDetails()->getLigavertreter() ?></div>
                <div><?= $team->getDetails()->getVerein() ?></div>
                <div><?= $team->getDetails()->getPlz() ?> <?= $team->getDetails()->getOrt() ?></div>
            </div>

            <!-- Links -->
            <div class="w3-bar w3-light-grey">
                <?php if ($team->getDetails()->getHomepage()): ?>
                    <div class="w3-bar-item"><?= Html::Link($team->getDetails()->getHomepage(), "", true, "home") ?></div>
                <?php endif; ?>
                <?php if ($team->getDetails()->getTeamfoto()): ?>
                    <div class="w3-bar-item"><?= Html::Link($team->getDetails()->getTeamfoto(), "", true, "group") ?></div>
                <?php endif; ?>
                <?php if ((new Kontakt($team->id()))->get_emails('public')): ?>
                    <div class="w3-bar-item"><?= Html::mailto((new Kontakt($team->id()))->get_emails('public'), '')?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<?php include '../../templates/footer.tmp.php';
