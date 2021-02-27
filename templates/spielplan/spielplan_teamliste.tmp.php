<!-- TEAMLISTE -->
<h2 class="w3-text-secondary w3-margin-top">Teamliste</h2>
<div class="w3-section">
    <div class="w3-responsive w3-card-4">
        <table class="w3-table w3-striped w3-centered" style="white-space: nowrap;">
            <tr class="w3-primary">
                <th><?= Form::icon("info_outline") ?><br>Team-ID</th>
                <th><?= Form::icon("group") ?><br>Team</th>
                <th><?= Form::icon("reorder") ?><br>Block</th>
                <th class="w3-hide-small"><?= Form::icon("arrow_circle_up") ?><br>Wertigkeit</th>
            </tr>
            <?php foreach ($spielplan->teamliste as $team_id => $team) { ?>
                <tr>
                    <td><?= $team_id ?></td>
                    <td><?= $team["teamname"] ?></td>
                    <td><?= $team["tblock"] ?></td>
                    <td class="w3-hide-small"><?= $team["wertigkeit"] ?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
    <?php if (in_array('NL', array_column($spielplan->teamliste, 'tblock'), true)) { ?>
        <span class="w3-text-grey">* Nichtligateam</span>
    <?php } //endif?>
    <!-- Modal-Button -->
    <span class="w3-button pdf-hide w3-text-primary"
          onclick="document.getElementById('teamliste_details').style.display='block'">
        <?= Form::icon("info") ?> Details anzeigen
    </span>

</div>

<!-- Modal -->
<div id="teamliste_details" class="w3-modal">
    <div class="w3-modal-content w3-card-4">
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped w3-centered" style="white-space: nowrap;">
                <tr class="w3-primary">
                    <th><?= Form::icon("info_outline") ?><br>Team-ID</th>
                    <th><?= Form::icon("group") ?><br>Team</th>
                    <th><?= Form::icon("reorder") ?><br>Block</th>
                    <th><?= Form::icon("arrow_circle_up") ?><br>Wertigkeit</th>
                    <?php if ($spielplan->turnier->details['phase'] !== 'ergebnis') { ?>
                        <th><span class="pdf-hide"><?= Form::icon("invert_colors") ?><br>Trikots</span></th>
                    <?php } //endif?>
                    <th><span class="pdf-hide"><?= Form::icon("account_circle") ?><br>Ligavertreter</span></th>
                    <th><span class="pdf-hide"><?= Form::icon("help_outline") ?><br>Kontakt</span></th>
                </tr>
                <?php foreach ($spielplan->teamliste as $team_id => $team) { ?>
                    <tr>
                        <td><?= $team_id ?></td>
                        <td><?= $team["teamname"] ?></td>
                        <td><?= $team["tblock"] ?></td>
                        <td><?= $team["wertigkeit"] ?></td>
                        <?php if ($spielplan->turnier->details['phase'] !== 'ergebnis') { ?>
                            <td>
                                <span class="pdf-hide">
                                    <?= Form::trikot_punkt($team['trikot_farbe_1'], $team['trikot_farbe_2']) ?>
                                </span>
                            </td>
                        <?php } // end if ?>
                        <td>
                            <span class="pdf-hide"><?= $team["ligavertreter"] ?></span>
                        </td>
                        <td>
                            <span class="pdf-hide">
                                <?= Form::mailto((new Kontakt($team_id))->get_emails('public'), 'E-Mail') ?>
                            </span>
                        </td>
                    </tr>
                <?php }//end foreach ?>
            </table>
        </div>
    </div>
</div>
<script>
    // When the user clicks anywhere outside of the modal, close it
    window.addEventListener("click", function (event) {
        if (event.target === document.getElementById('teamliste_details')) {
            document.getElementById('teamliste_details').style.display = "none";
        }
    });
</script>