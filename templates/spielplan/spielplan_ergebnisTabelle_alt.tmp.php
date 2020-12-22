<div class="w3-hide-small">
    <!-- Penalty Warnung -->
    <p class="w3-text-secondary"><?= $spielplan->penalty_warning ?? '' ?></p>
    <!-- ABSCHLUSSTABELLE -->
    <h3 class="w3-text-secondary w3-margin-top">Tabelle</h3>
    <?php if (!$spielplan->check_tabelle_einblenden()) { ?>
        <p class="w3-text-grey">Platzierungen und Ligapunkte werden angezeigt, sobald jedes Team mindestens ein Spiel gespielt hat.</p>
    <?php } // endif?>
    <div class="w3-responsive w3-card">
        <table class="w3-table <?= (($teamcenter or $ligacenter) && $spielplan->turnier->details['phase'] == 'ergebnis') ? 'w3-pale-green' : 'w3-striped' ?>">
            <tr class="w3-primary">
                <th class="w3-center">Pl.</th>
                <th>Team</th>
                <th class="w3-center">Spiele</th>
                <th class="w3-center">Punkte</th>
                <th class="w3-center">Tore</th>
                <th class="w3-center">Gegentore</th>
                <th class="w3-center">Differenz</th>
                <th class="w3-center">Ligapunkte</th>
            </tr>
            <?php foreach ($spielplan->platzierungstabelle as $team_id => $x) { ?>
                <tr class="<?= ($x['penalty'] && empty($x['statistik']['penalty_diff'])) ? "w3-secondary" : "" ?>">
                    <td class="w3-center"><?= ($spielplan->check_tabelle_einblenden()) ? $x['platz'] : '--' ?></td>
                    <td><?= $x["teamname"] ?></td>
                    <td class="w3-center"><?= $x['statistik']["spiele"] ?></td>
                    <td class="w3-center"><?= $x['statistik']["punkte"] ?></td>
                    <td class="w3-center"><?= $x['statistik']["tore"] ?></td>
                    <td class="w3-center"><?= $x['statistik']["gegentore"] ?></td>
                    <td class="w3-center"><?= $x['statistik']["tordifferenz"] ?></td>
                    <td class="w3-center"><?= (($x['penalty'] && empty($x['statistik']['penalty_diff'])) or !$spielplan->check_tabelle_einblenden()) ? '--' : $x["ligapunkte"] ?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
</div>
<?= $spielplan->direkter_vergleich_tabellen ?>