<!-- Auswahl der Emailadressen -->
<h1 class="w3-text-primary">Kontaktcenter</h1>
<p class="w3-text-grey">Das Kontaktcenter kann dazu verwendet werden, um andere Teams Emails zu senden.</p>

<!-- Javascript für die Tabs zu finden in script.js -->
<?php if (empty($emails)){?>
    <p>
        <button style="width: 300px;" class="w3-tertiary w3-button" onclick="openTab('turniere')">Turnierwahl</button>
        <i class="w3-hide-small">Alle Teams welche auf den Listen eines Turniers stehen anschreiben</i>
    <p>
    <p>
        <button style="width: 300px;" class="w3-tertiary w3-button" onclick="openTab('teams')">Teamwahl</button>
        <i class="w3-hide-small">Teams auswählen, welche man anschreiben möchte</i>
    <p>
    <p>
        <form method="post">
            <input type="submit" style="width: 300px;" class="w3-button w3-tertiary" value="Rundmail" name="rundmail">
            <i class="w3-hide-small">Rundmail an alle Teams in der Liga verschicken</i>
        </form>
    <p>
<?php } //end if?>

<p>
    <a style="width: 300px" href="<?php if($ligacenter){?>lc_emails.php<?php }elseif($teamcenter){?>tc_emails.php<?php } //end if?>" class="w3-tertiary w3-button">Zurücksetzen</a>
    <i class="w3-hide-small">Ausgewählte Emails zurücksetzen</i>
<p>

<!-- Turnierauswahl -->
<div id="turniere" class="tab" style="display:none">
<h2 class="w3-bottombar w3-text-primary">Turnier wählen</h2>
    <form method="POST">
        <p>
            <label class="w3-text-primary" for="turnier">Turnier wählen</label>
            <select required class="w3-select w3-border w3-border-primary" name="turnier_id" id="turnier" onchange="this.form.submit()">
                <option disabled <?php if(empty($_GET['turnier_id'])){?>selected<?php }?>></option>
                <?php foreach ($turniere as $turnier_id => $turnier){?>
                    <option <?php if($turnier_id == ($_GET['turnier_id'] ?? '')){?>selected<?php }?> value="<?=$turnier_id?>">
                    <?=$turnier['datum'] .' '.$turnier['ort']. ' (' . $turnier['tblock'] . ')'?>
                    </option>
                <?php } //end foreach?>
            </select>
        <p>
        <!--<p>
            <input type="submit" value="Turnier auswählen" class="w3-button w3-tertiary">
        </p>-->
    </form>
</div>

<!-- Teamauswahl -->
<div id="teams" class="tab" style="display:none">
    <h2 class="w3-bottombar w3-text-primary">Teams wählen</h2>
    <form method="post">
        <div class="w3-row">
            <?php foreach ($teams as $team){?>
                <div class="w3-col s12 m6">
                    <input style="cursor: pointer;" class="w3-check" type="checkbox" id="email<?=$team['team_id']?>" name="team[]" value="<?=$team['team_id']?>">
                    <label style="cursor: pointer; color: red;" class="w3-text-primary w3-hover-text-secondary" for="email<?=$team['team_id']?>"><?=$team['teamname']?> (<?=Tabelle::get_team_block($team['team_id'], $akt_spieltag - 1)?>)</label>
                </div>
            <?php } //end foreach?>
        </div>
        <p>
            <a style="cursor: pointer;" class="w3-text-primary w3-hover-text-secondary" onclick="invert('team[]')"><i class="material-icons">invert_colors</i> Auswahl umkehren</a>
        </p>
        <p>
            <input type="submit" class="w3-button w3-tertiary" style="cursor: pointer;" value="Teams auswählen" name="teams_emails">
        </p>
    </form>
</div>