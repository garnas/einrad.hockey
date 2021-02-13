<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
<div class="w3-responsive w3-card w3-section">
    <table class="w3-table w3-striped w3-centered" style="white-space: nowrap;">
        <tr class="w3-primary">
            <th><i class="material-icons">info_outline</i><br>Team-ID</th>
            <th><i class="material-icons">group</i><br>Team</th>
            <th><i class="material-icons">reorder</i><br>Block</th>
            <th><i class="material-icons">arrow_circle_up</i><br>Wertigkeit</th>
            <?php if($spielplan->turnier->details['phase'] != 'ergebnis') { ?>
                <th><span class="pdf-hide"><i class="material-icons">accessibility</i><br>Trikots</span></th>
            <?php } //endif?>
            <th><i class="material-icons">account_circle</i><br>Ligavertreter</th>
            <th><span class="pdf-hide"><i class="material-icons">help_outline</i><br>Kontakt</span></th>
        </tr>
        <?php foreach ($spielplan->teamliste as $team_id => $team) { ?>
            <tr>
                <td><?= $team_id ?></td>
                <td><?= $team["teamname"] ?></td>
                <td><?= $team["tblock"] ?></td>
                <td><?= $team["wertigkeit"] ?></td>
                <?php if ($spielplan->turnier->details['phase'] != 'ergebnis') { ?>
                    <td>
                        <span class="pdf-hide">
                            <?php if (!empty($team['trikot_farbe_1'])){ ?>
                                <span class="w3-card-4" style="height:14px;width:14px; background-color:<?= $team['trikot_farbe_1']?>;border-radius:50%;display:inline-block;"></span>
                            <?php } // end if ?>
                            <?php if (!empty($team['trikot_farbe_2'])){ ?>
                                <span class="w3-card-4" style="height:14px;width:14px; background-color:<?= $team['trikot_farbe_2']?>;border-radius:50%;display:inline-block;"></span>
                            <?php } // end if ?>
                        </span>
                    </td>
                <?php } // end if ?>
                <td><?= $team["ligavertreter"] ?></td>
                <td>
                    <span class="pdf-hide">
                        <?=Form::mailto((new Kontakt($team_id))->get_emails('public'),'E-Mail')?>
                    </span>
                </td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>

<?php if (in_array('NL', array_column($spielplan->teamliste, 'tblock'))) { ?>
    <span class="w3-text-grey">* Nichtligateam</span>
<?php } //endif?>
