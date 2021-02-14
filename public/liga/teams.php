<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$alle_teamdaten = Team::get_teamdata_all_teams();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$titel = "Teamliste | Deutsche Einradhockeyliga";
Config::$content = "Liste der Teams der Deutschen Einradhockeyliga mit Teamfoto und Kontaktmöglichkeit.";
include '../../templates/header.tmp.php';
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        //Turnierergebnisse filtern
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV tr").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <h1 class='w3-text-primary w3-border-bottom w3-border-grey'>
        Ligateams
        <span class="w3-right w3-hide-small">Saison <?= Form::get_saison_string() ?></span>
    </h1>
    <!-- Legende -->
    <p>
        <span class="w3-right w3-text-primary">
          <?= Form::icon("home") ?>&nbsp;Homepage
          <?= Form::icon("group") ?>&nbsp;Teamfoto
          <?= Form::icon("mail") ?>&nbsp;Email
        </span>
    </p>
    <br class="w3-hide-large w3-hide-medium">
    <p><?= Form::link("ligakarte.php", 'Ligakarte aller Teams', true, 'place') ?></p>

    <!-- Team suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Form::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;"
                                                   type="text" placeholder="Team suchen">
    </div>

    <!-- Teams Tabelle -->
    <div id="myDIV" class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th></th>
                <th style="white-space: nowrap;"><?= Form::icon('groups')?> Teamname</th>
                <th style="white-space: nowrap;"><?= Form::icon('room')?> Ort</th>
                <th style="white-space: nowrap;"><?= Form::icon('outlined_flag')?> Verein</th>
                <th style="white-space: nowrap;"><?= Form::icon('account_circle')?> Ligavertreter</th>
                <th class="w3-center" style="white-space: nowrap;"><?= Form::icon('invert_colors')?> Farben</th>
            </tr>
            <?php foreach ($alle_teamdaten as $team) { ?>
                <tr id='<?= $team['team_id'] ?>'>
                    <!-- Icons -->
                    <td style='white-space: nowrap;' class="w3-right-align">
                        <?= Form::Link($team['homepage'] ?? '', "", true, "home")?>
                        <?= Form::Link($team['teamfoto'] ?? '', "", true, "group")?>
                        <?= Form::mailto((new Kontakt($team['team_id']))->get_emails('public'), '')?>
                    </td>
                    <!-- Text -->
                    <td style='white-space: nowrap;'><?= $team['teamname'] ?></td>
                    <td><?= $team['plz'] ?>&nbsp;<?= $team['ort'] ?></td>
                    <td><?= $team['verein'] ?></td>
                    <td><?= $team['ligavertreter'] ?></td>
                    <td class="w3-center" style="white-space: nowrap;"><?= Form::trikot_punkt($team['trikot_farbe_1'], $team['trikot_farbe_2']) ?></td>
                </tr>
            <?php } //Ende foreach?>
        </table>
    </div>

<?php include '../../templates/footer.tmp.php';