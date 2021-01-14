<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<!-- Teambesprechung -->
<?php if ($spielplan->turnier->details['besprechung'] == 'Ja') { ?>
    <p class="nicht-drucken">
        <i>Alle Teams sollen sich um <?= date('H:i', strtotime($spielplan->turnier->details['startzeit']) - 15 * 60) ?>
            &nbsp;Uhr zu einer
            gemeinsamen Turnierbesprechung einfinden.</i>
    </p>
<?php }//endif?>
<!-- Spielzeiten -->
<p class="w3-text-grey">
    Spielzeit: <?= $spielplan->details['anzahl_halbzeiten'] ?> x <?= $spielplan->details['halbzeit_laenge'] ?>&nbsp;min
    | Puffer: <?= $spielplan->details['pause'] ?>&nbsp;min
</p>


<?php if ($teamcenter or $ligacenter){ ?>
    <form method="post">
        <!-- Formular für die Ergebniseintragung in den Centern erstellen -->
        <p>
            <input type="submit"
                   name="tore_speichern"
                   class="w3-block w3-button w3-tertiary"
                   value="Spiele zwischenspeichern">
        </p>
<?php } // end if ?>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered w3-striped">
        <tr class="w3-primary">
            <th>
                <i class="material-icons">schedule</i>
                <br>
                Zeit
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">sports</i>
                <br>
                Schiri
            </th>
            <th class="w3-hide-large w3-hide-medium pdf-hide">
                <span class="pdf-hide">
                    <i class="material-icons">sports</i>
                    <br>
                    Schiri
                </span>
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">sports_hockey</i>
                <br>
                Spiele
            </th>
            <th class="w3-hide-large w3-hide-medium">
                <span class="pdf-hide">
                    <i class="material-icons">sports_hockey</i>
                    <br>
                    Spiele
                </span>
            </th>
            <th class="w3-hide-small">
                <i class="material-icons">sports_baseball</i>
                <br>
                Tore
            </th>
            <th class="w3-hide-large w3-hide-medium">
                <span class="pdf-hide">
                    <i class="material-icons">sports_baseball</i>
                    <br>Tore
                </span>
            </th>
            <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                <th class="w3-hide-small">
                    <i class="material-icons">priority_high</i>
                    <br>
                    Penalty
                </th>
                <th class="w3-hide-large w3-hide-medium">
                    <span class="pdf-hide">
                        <i class="material-icons">priority_high</i>
                        <br>
                        Penalty
                    </span>
                </th>
            <?php }//endif?>
        </tr>
        <?php foreach ($spielplan->spiele as $spiel_id => $spiel) { ?>
            <tr <?php if (($teamcenter or $ligacenter)
                && !is_null($spiel["tore_a"])
                && !is_null($spiel["tore_b"])
                && !$spielplan->check_penalty_spiel($spiel_id, true)) { ?>
                    class="w3-pale-green"
                <?php }//endif?>
            >
                <td><?= $spiel["zeit"] ?></td>
                <!-- Dekstop -->
                <td class="w3-hide-small">
                    <table class="w3-table w3-centered" style="width: auto; margin: auto;">
                        <tr>
                            <td style="width: 30px; padding:0;">
                                <span class="w3-tooltip" style="cursor: help;">
                                    <i class="w3-text-primary"><?= $spiel["schiri_team_id_b"] ?></i>
                                    <span style="white-space: nowrap; position:absolute;right:30px;top:0"
                                          class="w3-text w3-small w3-primary w3-tag pdf-hide">
                                        <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_b"]]['teamname'] ?>
                                    </span>
                                </span>
                            </td>
                            <td style="width: 30px; padding:0;">|</td>
                            <td style="width: 30px; padding:0;">
                                <span class="w3-tooltip" style="cursor: help;">
                                    <i class="w3-text-primary"><?= $spiel["schiri_team_id_a"] ?></i>
                                    <span style="white-space: nowrap; position:absolute;left:30px;top:0"
                                          class="w3-text w3-small w3-primary w3-tag pdf-hide">
                                        <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_a"]]['teamname'] ?>
                                    </span>
                                </span>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Mobil -->
                <td class="w3-center w3-hide-large w3-hide-medium w3-text-primary">
                    <span class="w3-tooltip pdf-hide">
                        <i><?= $spiel["schiri_team_id_b"] ?></i>
                        <span style="white-space: nowrap; position:absolute;left:0px;bottom:15px"
                              class="w3-text w3-small w3-primary w3-tag">
                            <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_down</i>
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_b"]]['teamname'] ?>
                        </span>
                    </span>
                    <br class="pdf-hide">
                    <span class="w3-tooltip pdf-hide">
                        <i><?= $spiel["schiri_team_id_a"] ?></i>
                        <span style="white-space: nowrap; position:absolute;left:0px;top:15px"
                              class="w3-text w3-small w3-primary w3-tag">
                            <i class="material-icons" style="vertical-align: -30%">keyboard_arrow_up</i>
                            <?= $spielplan->platzierungstabelle[$spiel["schiri_team_id_a"]]['teamname'] ?>
                        </span>
                    </span>
                </td>
                <!-- Dekstop -->
                <td class="w3-hide-small">
                    <table class="w3-table w3-centered" style="white-space: nowrap; width: auto; margin: auto;">
                        <tr>
                            <td class="w3-right-align" style="width: <?= $width_in_px ?>px; padding:0;">
                                <?= $spiel["teamname_a"] ?>
                            </td>
                            <td style="width: 40px; padding:0;">-</td>
                            <td class="w3-left-align" style="width: <?= $width_in_px ?>px; padding:0;">
                                <?= $spiel["teamname_b"] ?>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Mobil -->
                <td class="w3-center w3-hide-large w3-hide-medium" style="white-space: nowrap;">
                    <span class="pdf-hide"><?= $spiel["teamname_a"] ?></span>
                    <br class="pdf-hide">
                    <span class="pdf-hide"><?= $spiel["teamname_b"] ?></span>
                </td>
                <!-- Tore Dekstop -->
                <td class="w3-hide-small">
                    <table class="w3-table w3-centered" style="width: auto; margin: auto;">
                        <tr>
                            <td style="width: 30px; padding:0;">
                                <b><?= $spiel["tore_a"] ?></b>
                            </td>
                            <td style="width: 30px; padding:0;">:</td>
                            <td style="width: 30px; padding:0;">
                                <b><?= $spiel["tore_b"] ?></b>
                            </td>
                        </tr>
                    </table>
                </td>
                <!-- Tore Mobil -->
                <td class="w3-center w3-hide-large w3-hide-medium">
                    <span class="pdf-hide">
                        <?php if ($teamcenter or $ligacenter) { ?>
                            <input id="tore_a[<?= $spiel_id ?>]"
                                   name="tore_a[<?= $spiel_id ?>]"
                                   value='<?= $spiel["tore_a"] ?>'
                                   class='w3-input w3-border w3-round w3-center'
                                   style='padding: 2px; width: 65px; display: inline-block;'
                                   type='number'
                                   autocomplete='off'
                                   min='0'
                                   step='1'
                            >
                        <?php } else { ?>
                            <b><?= $spiel["tore_a"] ?></b>
                        <?php } //end if ?>
                    </span>
                    <br class="pdf-hide">
                    <span class="pdf-hide">
                        <?php if ($teamcenter or $ligacenter) { ?>
                            <input id="tore_b[<?= $spiel_id ?>]"
                                   name="tore_b[<?= $spiel_id ?>]"
                                   value='<?= $spiel["tore_b"] ?>'
                                   class='w3-input w3-border w3-round w3-center'
                                   style='padding: 2px; width: 65px; display: inline-block;'
                                   type='number'
                                   autocomplete='off'
                                   min='0'
                                   step='1'
                            >
                        <?php } else { ?>
                            <b><?= $spiel["tore_b"] ?></b>
                        <?php } //end if ?>
                    </span>
                </td>
                <!-- Penalty -->
                <?php if ($spielplan->check_penalty_anzeigen()) { ?>
                    <!-- Penalty Dekstop -->
                    <td class="w3-hide-small">
                        <table class="w3-table w3-centered" style="width: auto; margin: auto;">
                            <tr>
                                <td class="w3-text-secondary" style="width: 30px; padding:0;">
                                        <b><?= $spiel["penalty_a"] ?></b>
                                </td>
                                <td style="width: 30px; padding:0;">:</td>
                                <td class="w3-text-secondary" style="width: 30px; padding:0;">
                                        <b><?= $spiel["penalty_b"] ?></b>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Penalty Mobil -->
                    <td class="w3-center w3-hide-large w3-hide-medium">
                        <span class="pdf-hide w3-text-secondary">
                            <?php if ($teamcenter or $ligacenter) { ?>
                                <!-- Penalty Formular -->
                                <input id="penalty_a[<?= $spiel_id ?>]"
                                       name="penalty_a[<?= $spiel_id ?>]"
                                       value='<?= $spiel["penalty_a"] ?>'
                                       class='w3-input w3-border w3-round w3-center
                                            <?= !(!is_null($spiel["penalty_a"])
                                                   && !$spielplan->check_penalty_spiel($spiel_id))
                                                   ?: 'w3-secondary' ?>'
                                       style='padding: 2px; width: 65px; display: inline-block;'
                                               <?= (!is_null($spiel["penalty_a"])
                                               || $spielplan->check_penalty_spiel($spiel_id))
                                               ?: 'disabled placeholder = "/"' ?>
                                               type='number'
                                       autocomplete='off'
                                       min='0'
                                       step='1'
                                >
                            <?php } else { ?>
                                <!-- Penaltystand -->
                                <?= $spiel["penalty_a"] ?>
                            <?php } //end if ?>
                        </span>
                        <br class="pdf-hide">
                        <span class="pdf-hide w3-text-secondary">
                            <?php if ($teamcenter or $ligacenter) { ?>
                                <input id="penalty_b[<?= $spiel_id ?>]"
                                       name="penalty_b[<?= $spiel_id ?>]"
                                       value='<?= $spiel["penalty_b"] ?>'
                                       class='w3-input w3-border w3-round w3-center
                                       <?= !(!is_null($spiel["penalty_b"])
                                           && !$spielplan->check_penalty_spiel($spiel_id)) // Penalty ist falsch
                                           ?: 'w3-secondary' ?>'
                                       style='padding: 2px; width: 65px; display: inline-block;'
                                       <?= (!is_null($spiel["penalty_b"])
                                           || $spielplan->check_penalty_spiel($spiel_id)) // Penalty nicht erforderlich
                                           ?: 'disabled placeholder = "/"' ?>
                                       type='number'
                                       autocomplete='off'
                                       min='0'
                                       step='1'
                                >
                            <?php } else { ?>
                                <?= $spiel["penalty_b"] ?>
                            <?php } //end if ?>
                        </span>
                    </td>
                <?php }// endif?>
            </tr>
            <?php if ($get_pause($spiel) > 0) { ?>
                <tr>
                    <td>
                        <?= date("H:i", strtotime($spiel["zeit"]) + $spiel_dauer * 60) ?>
                    </td>
                    <td></td>
                    <td class="w3-center"> <i class="material-icons">schedule</i>
                        <i><?= round($get_pause($spiel) / 60) ?>&nbsp; min Pause</i>
                        <i class="material-icons">schedule</i>
                    </td>
                    <td></td>
                    <?php if($spielplan->check_penalty_anzeigen()){ ?>
                        <td></td>
                    <?php }// end if ?>
                </tr>
            <?php }// endif?>
        <?php }// end foreach?>
    </table>
</div>
<?php if ($teamcenter or $ligacenter){ ?>
    <!-- Formular für die Ergebniseintragung in den Centern erstellen -->
        <p>
            <input type="submit"
                   name="tore_speichern"
                   class="w3-block w3-button w3-tertiary"
                   value="Spiele zwischenspeichern"
            >
        </p>
    </form>
<?php } // end if ?>

<script>
    document.addEventListener("wheel", function(event){
        if(document.activeElement.type === "number"){
            document.activeElement.blur();
        }
    });
</script>

<?php if ($ligacenter or $teamcenter){ ?>
        <!-- Script zum Anzeigen des Formulars beim Ergebniseintragen -->
    <script>
        var elems = document.querySelectorAll(".w3-hide-small");

        [].forEach.call(elems, function(el) {
            el.classList.add("w3-hide");
        });

        var elems = document.querySelectorAll(".w3-hide-medium");

        [].forEach.call(elems, function(el) {
            el.classList.remove("w3-hide-medium");
        });

        var elems = document.querySelectorAll(".w3-hide-large");

        [].forEach.call(elems, function(el) {
            el.classList.remove("w3-hide-large");
        });
    </script>
<?php } ?>