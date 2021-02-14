<!-- Auswahl der Emailadressen -->
<h1 class="w3-text-primary">Kontaktcenter</h1>
<p class="w3-text-grey">Das Kontaktcenter kann dazu verwendet werden, um anderen Teams Emails zu senden.</p>

<!-- Javascript für die Tabs zu finden in script.js -->
<?php
if (empty($_SESSION[$list_id]['emails'])){?>
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
<?php }else{?>
    <form method="post">
        <p>
            <input type="submit" name="reset" class="w3-secondary w3-button" value="Emails zurücksetzen">
        <p>
    </form>
<?php } //end if?>

<!-- Turnierauswahl -->
<div id="turniere" class="tab" style="display:none">
<h2 class="w3-bottombar w3-text-primary">Turnier wählen</h2>
    <form method="post">
        <p>
            <input style="cursor: pointer;" class="w3-check" type="checkbox" id="la" name="la" value="la">
            <label style="cursor: pointer; color: red;" class="w3-text-primary w3-hover-text-secondary" for="la"><b>Ligaausschuss anschreiben</b></label>
        </p>
        <p>
            <label class="w3-text-primary" for="turnier">Turnier wählen</label>
            <select required class="w3-select w3-border w3-border-primary" name="turnier_id" id="turnier" onchange="this.form.submit()">
                <option disabled <?php if(empty($_GET['turnier_id'])){?>selected<?php }?>></option>
                <?php foreach ($turniere as $turnier_id => $turnier){?>
                    <option <?php if($turnier_id == ($_GET['turnier_id'] ?? '')){?>selected<?php }?> value="<?=$turnier_id?>">
                    <?=$turnier['datum'] .' '.$turnier['ort']. ' ' . $turnier['tname'] . ' (' . $turnier['tblock'] . ')'?>
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
            <p>
                <input style="cursor: pointer;" class="w3-check" type="checkbox" id="la_team" name="la" value="la">
                <label style="cursor: pointer; color: red;" class="w3-text-primary w3-hover-text-secondary" for="la_team"><b>Ligaausschuss anschreiben</b></label>
            </p>
            <?php foreach ($teams as $team){?>
                <div class="w3-col s12 m6">
                    <input style="cursor: pointer;" class="w3-check" type="checkbox" id="email<?=$team['team_id']?>" name="team[]" value="<?=$team['team_id']?>">
                    <label style="cursor: pointer; color: red;" class="w3-text-primary w3-hover-text-secondary" for="email<?=$team['team_id']?>"><?=$team['teamname']?> (<?=Tabelle::get_team_block($team['team_id'], $akt_spieltag - 1)?>)</label>
                </div>
            <?php } //end foreach?>
        </div>
        <p>
            <div style="cursor: pointer;" class="no w3-text-primary w3-hover-text-secondary" onclick="invert('team[]'); invert('la');"><i class="material-icons">invert_colors</i> Auswahl umkehren</div>
        </p>
        <p>
            <input type="submit" class="w3-button w3-tertiary" style="cursor: pointer;" value="Teams auswählen" name="teams_emails">
        </p>
    </form>
</div>

<!-- Anzeige des Formulars für den Emailversand -->
<?php if (!empty($_SESSION[$list_id]['emails'])){ ?>
    <div class="w3-card-4 w3-panel">
        <h2 class="w3-text-primary">Kontaktformular: <?=$_SESSION[$list_id]['type']?></h2>
        <form method="post" onsubmit="return confirm('Soll die Email wirklich abgeschickt werden?')">
            <p class=""><b><i class=material-icons>mail</i> Absender</b></p>
            <p><?= $from ?></p>
            <p class=""><b><i class=material-icons>mail</i> Empfänger <?php if(Config::$ligacenter){?>(<?=$anzahl_emails?>)<?php }//end if?></b></p>
            <p>
                <div class="w3-row"><i>
                    <?php foreach($tos as $to){?>
                       <div class="w3-col m6 s12">
                            <?=$to?><br>
                        </div>
                    <?php }//end foreach?>
                </i><div class="w3-row">
            </p>
            <?php if(Config::$ligacenter){ ?>
                <p><b>+ BCC:</b> <?=Env::LAMAIL_ANTWORT?></p>
            <?php } //endif?>
            <p>
                <label class="" for="betreff"><b><i class="material-icons">label_outline</i> Betreff</b></label>
                <input class="w3-input w3-border w3-border-primary" type="text" id="betreff" name="betreff" value="<?=$_POST['betreff'] ?? ''?>" required>
            </p>
            <p>
                <label class="" for="text"><b><i class="material-icons">subject</i> Text</b></label>
                <textarea class="w3-input w3-border w3-border-primary" rows="10" type="text" id="text" name="text" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
            </p>
            <?php if(Config::$teamcenter){ ?>
                <p class="w3-text-green">Es wird ebenfalls eine Email an dein Team gesendet, falls ihr nicht schon auf der Empfängerliste steht.</p>
            <?php } //endif?>
            <?php if ($anzahl_emails > $grenze_bcc){?>
                <p class="w3-text-green">Hinweis: Da mehr als <?=$grenze_bcc?> Email-Adressen angeschrieben werden, werden alle im BCC angeschrieben. 
            <?php } //end if?>
            <p>
                <input type="submit" class="w3-secondary w3-round w3-ripple w3-button" name="send_mail" value="Senden">
            </p>
        </form>
    </div>
<?php } //end if