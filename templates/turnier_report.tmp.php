<h1 class="w3-text-primary"><?=date("d.m.Y", strtotime($akt_turnier->daten['datum']))?> <?=$akt_turnier->daten['ort']?> <i>(<?=$akt_turnier->daten['tblock']?>)</i></h1>
<h2 class="w3-text-grey"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">article</i> Turnier-Report</h2>

<!-- LINK TURNIERREPORT -->
<p><?=Form::link('../liga/spielplan.php?turnier_id=' . $turnier_id, '<i class="material-icons">reorder</i> Zum Spielplan')?></p>

<!-- Ausbilder -->
<?php if (!empty($ausbilder_liste)){?>
    <h2 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">school</i> Schiedsrichter-Ausbilder</h2>
    <ul class='w3-ul w3-margin-left w3-leftbar w3-border-tertiary'>
        <?php foreach ($ausbilder_liste as $spieler){?>
            <li><?=$spieler['vorname'] . ' ' . $spieler['nachname']?> (<i><?=Team::teamid_to_teamname($spieler['team_id'])?></i>)</li>
        <?php }//end foreach?>
    </ul>
<?php }//endif?>

<!-- Kader -->
<h2 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">groups</i> Kader und Schiedsrichter</h2>
<?php foreach ($kader_array as $team_id => $kader){?>
    <ul class='w3-ul w3-margin-left w3-leftbar w3-border-tertiary'>
        <li class="w3-hover-tertiary" style="cursor: pointer;" onclick="openTab('<?=$team_id?>')"><?=Team::teamid_to_teamname($team_id)?></li>
    </ul>
<?php }//end foreach?>
<?php foreach ($kader_array as $team_id => $kader){?> 
    <div id="<?=$team_id?>" class="tab" style="display:none; max-width: 600px">
        <?php if(!empty($kader)){?>
            <h3><?=Team::teamid_to_teamname($team_id)?></h3>
            <div class="w3-responsive w3-card">
                <table class="w3-table w3-striped">
                    <tr class="w3-primary">
                        <th>Spieler-ID</th>
                        <th>Spieler</th>
                        <th>Schiri</th>
                    </tr>
                    <?php foreach ($kader as $spieler_id => $spieler){?>
                        <tr class="<?php if(!empty($spieler['schiri'])){?>w3-pale-green<?php } //endif?>">
                            <td><?=$spieler_id?></td>
                            <td><?=$spieler['vorname'] . ' ' .  mb_substr($spieler['nachname'],0,1, "utf-8") . '.'?></td>
                            <td><?php if (!empty($spieler['schiri'])){?><i class="material-icons">check_circle</i> <?=Saison::get_saison_string($spieler['schiri'])?><?php } //endif?></td>
                        </tr>
                    <?php }//end foreach?>
                </table>
            </div>
        <?php } //endif?>
        <?php if(!Team::is_ligateam($team_id)){?><p class="w3-text-grey">Nichtligateams haben keinen zugewiesenen Kader.</p><?php }//endif?>
    </div>
<?php }//end foreach?>

<!-- Spielerausleihe -->
<h2 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">accessibility</i> Spielerausleihe</h2>
<?php if(!empty($spieler_ausleihen)){?>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Name</th>
                <th>Aufnehmendes Team</th>
                <th>Abgebendes Team</th>
                <?php if($change_tbericht){ ?>
                    <th>Löschen</th>
                <?php }//endif?>
            </tr>
            <?php foreach ($spieler_ausleihen as $ausleihe){?>
                <tr>
                    <td><?=$ausleihe['spieler']?></td>
                    <td><?=$ausleihe['team_auf']?></td>
                    <td><?=$ausleihe['team_ab']?></td>
                    <?php if($change_tbericht){ ?>
                        <td><form method="post"><input type="submit" class="w3-button w3-text-primary" value="X" name="del_ausleihe_<?=$ausleihe['ausleihe_id']?>"></form></td>
                    <?php }//endif?>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
<?php }else{?>
    <p><span class="w3-text-grey">Es sind keine Spielerausleihen eingetragen.</span></p>
<?php }//endif?>

<!-- Spielerausleihe hinzufügen -->
<?php if($change_tbericht){ ?> 
    <button onclick="document.getElementById('modal_ausleihe').style.display='block'" class="w3-section w3-button w3-tertiary">Spielerausleihe hinzufügen</button>
    <div id="modal_ausleihe" class="w3-modal">
        <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
            <span onclick="document.getElementById('modal_ausleihe').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
            <h2 class="w3-text-primary">Spielerausleihe hinzufügen</h2>
            <p>
                <label for="ausleihe_name">Spieler</label>
                <input required class="w3-input w3-border w3-border-primary" type="text" name="ausleihe_name" id="ausleihe_name"></input>   
            </p>
            <p>
                <label for="ausleihe_team_auf">Aufnehmendes Team</label>
                <select required name="ausleihe_team_auf" id="ausleihe_team_auf" class="w3-select w3-input w3-border w3-border-primary">
                    <option selected disabled>--</option>
                    <?php foreach($teams as $team){?>
                        <option><?=$team['teamname']?></option>
                    <?php } //end foreach?>
                </select>
            </p>
            <p>
                <label for="ausleihe_team_ab">Abgebendes Team</label>
                <input class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" type="text" list="teams" id="ausleihe_team_ab" name="ausleihe_team_ab" required>
                    <?=Form::datalist_teams()?>
            </p>
            <p>
                <input type="submit" value="Hinzufügen" name="new_ausleihe" class="w3-button w3-tertiary">
            </p>
        </form>
    </div>
<?php }//endif?>

<!-- Zeitstrafen -->
<h2 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">schedule</i> Zeitstrafen</h2>
<?php if(!empty($zeitstrafen)){?>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Dauer</th>
                <th>Spieler</th>  
                <th>Spielpaarung</th>
                <?php if($change_tbericht){ ?>
                    <th>Löschen</th>
                <?php }//endif?>
            </tr>
            <?php foreach ($zeitstrafen as $zeitstrafe){?>
                <tr>
                    <td><?=$zeitstrafe['dauer']?></td>
                    <td><?=$zeitstrafe['spieler']?></td>
                    <td><?=$zeitstrafe['team_a']?> : <?=$zeitstrafe['team_b']?></td>
                    <?php if($change_tbericht){ ?>
                        <td><form method="post"><input type="submit" class="w3-button w3-text-primary" value="X" name="del_zeitstrafe_<?=$zeitstrafe['zeitstrafe_id']?>"></form></td>
                    <?php }//endif?>
                </tr>
                <tr>
                    <td colspan="5"><span class="w3-text-secondary">Grund: </span><?=nl2br($zeitstrafe['grund'])?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
<?php }else{?>
    <p><span class="w3-text-grey">Es sind keine Zeitstrafen eingetragen.</span></p>
<?php }//endif?>

<!-- Zeitstrafe hinzufügen -->
<?php if($change_tbericht){ ?>
    <button onclick="document.getElementById('modal_zeitstrafe').style.display='block'" class="w3-section w3-button w3-tertiary">Zeitstrafe hinzufügen</button>
    <div id="modal_zeitstrafe" class="w3-modal">
        <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
            <span onclick="document.getElementById('modal_zeitstrafe').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
            <h2 class="w3-text-primary">Zeitstrafe hinzufügen</h2>
            <p>
                <label for="zeitstrafe_spieler">Spieler</label>
                <input type="text" placeholder="Name eingeben" class="w3-input w3-border w3-border-primary" list="spielerliste" id="zeitstrafe_spieler" name="zeitstrafe_spieler">
                    <datalist id="spielerliste">
                        <?php
                        foreach ($spieler_liste as $spieler_id => $spieler){ ?>
                            <option value='<?=$spieler['vorname'] . ' ' .  mb_substr($spieler['nachname'],0,1, "utf-8") . '.' . ' | ' . Team::teamid_to_teamname($spieler['team_id'])?>'>
                        <?php } //end foreach ?>
                    </datalist>
            </p>
            <p>
                <label for="zeitstrafe_dauer">Dauer</label>
                <select name="zeitstrafe_dauer" id="zeitstrafe_dauer" class="w3-select w3-input w3-border w3-border-primary">
                    <option>2 min</option>
                    <option>5 min</option>
                    <option>Gesamtes Spiel</option>
                </select>
            </p>
            <p>
                <label for="zeitstrafe_team_a">Spielpaarung</label>
                <select id="zeitstrafe_team_a" name="zeitstrafe_team_a" class="w3-select w3-input w3-border w3-border-primary">
                    <option disabled selected>--</option>
                    <?php foreach($teams as $team){?>
                        <option><?=$team['teamname']?></option>
                    <?php } //end foreach?>
                </select>
                <label for="zeitstrafe_team_b" class="w3-text-grey">versus</label>
                <select id="zeitstrafe_team_b" name="zeitstrafe_team_b" class="w3-select w3-input w3-border w3-border-primary">
                    <option disabled selected>--</option>
                    <?php foreach($teams as $team){?>
                        <option><?=$team['teamname']?></option>
                    <?php } //end foreach?>
                </select>
            </p>
            <p>
                <label for="zeitstrafe_bericht">Grund <i>(kurz)</i></label>
                <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(300, 'zeitstrafe_bericht','zeitstrafe_counter');" maxlength="300" rows="3" id="zeitstrafe_bericht" name="zeitstrafe_bericht" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
                <p id="zeitstrafe_counter"></p>
                <span class="w3-text-grey">Eine detailierte und neutrale Beschreibung der Situation sollte gegebenenfalls in den Turnierbericht aufgenommen werden.</span>  
            </p>
            <p>
                <input type="submit" name="new_zeitstrafe" value="Hinzufügen" class="w3-button w3-tertiary">
            </p>
        </form>
    </div>
<?php }//endif?>

<!-- Turnierbericht -->
<h2 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -20%" class="material-icons">info</i> Turnierbericht</h2>
<?php if($change_tbericht){ ?>
    <form method="post">
        <p>
            <input <?php if($tbericht->kader_check()){?>checked<?php }//endif?> class="w3-check" value="kader_checked" type="checkbox" name="kader_check" id="kader_check"></input>
            <label for="kader_check" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer;"> Kader wurden kontrolliert</label>           
        </p>
        <p>
            <label for="spieler_id">Turnierbericht</label>
            <textarea class="w3-input w3-border w3-border-primary" onkeyup="woerter_zaehlen(1500, 'turnierbericht', 'turnierbericht_counter');" maxlength="1500" placeholder="Wie war das Turnier? Besondere Vorkomnisse werden hier vermerkt." rows="12" id="turnierbericht" name="turnierbericht"><?=stripcslashes($_POST['text'] ?? '')?><?=$tbericht->get_turnier_bericht()?></textarea>
            <p id="turnierbericht_counter"><p>
        </p>
        <input type="submit" value="Speichern" name="set_turnierbericht" class="w3-button w3-tertiary">
    </form>
<?php }else{?>
    <p><?=$tbericht->get_turnier_bericht() ?: '<p class="w3-text-grey">Es ist kein Turnierbericht vorhanden.</p>'?></p>
<?php }//endif?>

<script>
    // Get the modal
    var modal1 = document.getElementById('modal_ausleihe');
    var modal2 = document.getElementById('modal_zeitstrafe');
    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal1) {
            modal1.style.display = "none";
        }
        if (event.target == modal2) {
            modal2.style.display = "none";
        }
    }
</script>