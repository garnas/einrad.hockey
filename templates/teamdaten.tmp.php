<?php if (isset($team->details)){ //Wird nur angezeigt, wenn daten zum ausfüllen übertragen worden sind?>
<!-- Link Teamdaten ändern -->
<h1 class="w3-text-primary"><?= Form::icon("group", tag: "h1") ?> <?=$team->details['teamname']?></h1>
    <p>
        <?= Form::link(
                ($ligacenter) ? 'lc_teamdaten_aendern.php?team_id=' . $team->id : 'tc_teamdaten_aendern.php',
                Form::icon('create') . ' Team- und Kontaktdaten ändern') ?>
        </>
    </p>
<div class="w3-panel w3-card-4">
    <h2 class="w3-text-primary"><?= Form::icon("image", tag: "h2") ?> Teamfoto</h2>
    <?php if (!empty($team->details['teamfoto'])){?>
        <p>
            <img src="<?=$team->details['teamfoto']?>" class="w3-card w3-image" alt="<?=$team->details['teamname']?>" style="max-height: 360px;">
        </p>
    <?php }else{?>
        <p class="w3-text-grey">Es wurde noch kein Teamfoto hochgeladen.</p>
    <?php } //end if?>
</div>

<div class="w3-panel w3-card-4">
    <h2 id="trikotfarbe" class="w3-text-primary">
        <?= Form::icon("brush", tag: "h2") ?> Trikotfarben
    </h2>

    <div class="w3-row-padding w3-center w3-strech">
        <div class="w3-half">
                <p>
                    1. Trikotfarbe
                </p>
                    <span class="w3-card-4" style="height:70px;width:70px;background-color:<?= empty($team->details['trikot_farbe_1']) ? '#bbb' : $team->details['trikot_farbe_1']?>;border-radius:50%;display:inline-block;">
                        <br><?= (empty($team->details['trikot_farbe_1'])) ? Form::icon('not_interested') : '' ?>
                    </span>
        </div>

        <div class="w3-half">
                <p>
                    2. Trikotfarbe
                </p>
                    <span class="w3-card-4" style="height:70px;width:70px; background-color:<?= empty($team->details['trikot_farbe_2']) ? '#bbb' : $team->details['trikot_farbe_2'] ?>;border-radius:50%;display:inline-block;">
                        <br><?= (empty($team->details['trikot_farbe_2'])) ? Form::icon('not_interested') : '' ?>
                    </span>
        </div>
    </div>
    <p class="w3-text-grey">
        Eure Trikotfarben werden im Spielplan angezeigt. Sie helfen anderen Teams bei der Wahl ihrer Trikots und Zuschauern dein Team zu identifizieren.
    </p>
</div>
<h2 class="w3-text-primary"><?= Form::icon("info", tag: "h2") ?> Teamdaten</h2>
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-striped">
        <tr>
            <th class="w3-primary" style="width: 140px">Teamname</th>
            <td><b><?=$team->details['teamname']?></b></td>
        </tr>
        <tr>
            <th class="w3-primary">Team ID</th>
            <td><?=$team->details['team_id']?></td>
        </tr>
        <tr>
            <th class="w3-primary">Freilose</th>
            <td><?=$team->details['freilose']?></td>
        </tr>
        <tr>
            <th class="w3-primary">Ligavertreter</th>
            <td><?=$team->details['ligavertreter']?></td>
        </tr>
        <tr>
            <th class="w3-primary" style="width: 140px">PLZ</th>
            <td><?=$team->details['plz']?></td>
        </tr>
        <tr>
            <th class="w3-primary">Ort</th>
            <td><?=$team->details['ort']?></td>
        </tr>
        <tr>
            <th class="w3-primary">Verein</th>
            <td><?=$team->details['verein']?></td>
        </tr>
        <tr>
            <th class="w3-primary">Homepage</th>
            <td><?=Form::link($team->details['homepage'], $team->details['homepage'], true)?></td>
        </tr>
    </table>
</div>
<h2 class="w3-text-primary"><?= Form::icon("mail", tag: "h2") ?> Kontaktdaten</h2>
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-striped">
        <tr>
            <th class="w3-primary">Email</th>
            <th class="w3-primary w3-center">Auf Webseite anzeigen?</th>
            <th class="w3-primary w3-center">Infomails erhalten?</th>
        </tr>
        <?php foreach($emails as $email){?>
            <tr>
                <td><?=$email['email']?></td>
                <td class='w3-center'><?=$email['public']?></td>
                <td class='w3-center'><?=$email['get_info_mail']?></td>
            </tr>
        <?php }?>
    </table>
</div>

<!-- Link Teamdaten ändern -->
<p>
    <a href="<?=($ligacenter) ? 'lc_teamdaten_aendern.php?team_id=' . $team->id : 'tc_teamdaten_aendern.php'?>"
       class="w3-button w3-block w3-secondary">
        <?= Form::icon("create") ?> Team- und Kontaktdaten ändern
    </a>
</p>
<?php } //ende if?>
