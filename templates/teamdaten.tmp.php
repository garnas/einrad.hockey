<?php

use App\Service\Team\TeamService;
use App\Service\Team\TeamValidator;
use App\Service\Turnier\TurnierSnippets;

?>
<!-- Link Teamdaten ändern -->
<h1 class="w3-text-primary"><?= Html::icon("group", tag: "h1") ?> <?= e($teamEntity->getName()) ?></h1>
    <p>
        <?= Html::link(
                (Helper::$ligacenter) ? 'lc_teamdaten_aendern.php?team_id=' . $team->id : 'tc_teamdaten_aendern.php',
                Html::icon('create') . ' Team- und Kontaktdaten ändern'
        ) ?>
    </p>
<div class="w3-panel w3-card-4">
    <h2 class="w3-text-primary"><?= Html::icon("image", tag: "h2") ?> Teamfoto</h2>
    <?php if ($teamEntity->getDetails()->getTeamfoto()){?>
        <p>
            <img src="<?= e($teamEntity->getDetails()->getTeamfoto()) ?>" class="w3-card w3-image"
                 alt="<?= e($teamEntity->getName())?>" style="max-height: 360px;">
        </p>
    <?php }else{?>
        <p class="w3-text-grey">Es wurde noch kein Teamfoto hochgeladen.</p>
    <?php } //end if?>
</div>

<div class="w3-panel w3-card-4">
    <h2 id="trikotfarbe" class="w3-text-primary">
        <?= Html::icon("brush", tag: "h2") ?> Trikotfarben
    </h2>

    <div class="w3-row-padding w3-center w3-strech">
        <div class="w3-half">
                <p>
                    1. Trikotfarbe
                </p>
                    <span class="w3-card-4" style="height:70px;width:70px;background-color:<?= empty($team->details['trikot_farbe_1']) ? '#bbb' : $team->details['trikot_farbe_1']?>;border-radius:50%;display:inline-block;">
                        <br><?= $teamEntity->getDetails()->getTrikotFarbe1() ? '' :  Html::icon('not_interested')?>
                    </span>
        </div>

        <div class="w3-half">
                <p>
                    2. Trikotfarbe
                </p>
                    <span class="w3-card-4" style="height:70px;width:70px; background-color:<?= empty($team->details['trikot_farbe_2']) ? '#bbb' : $team->details['trikot_farbe_2'] ?>;border-radius:50%;display:inline-block;">
                        <br><?= $teamEntity->getDetails()->getTrikotFarbe2() ? '' :  Html::icon('not_interested')?>
                    </span>
        </div>
    </div>
    <p class="w3-text-grey">
        Eure Trikotfarben werden im Spielplan angezeigt. Sie helfen anderen Teams bei der Wahl ihrer Trikots und Zuschauern dein Team zu identifizieren.
        Die 1. Trikotfarbe wird in den Spielplänen bevorzugt.
    </p>
</div>
<h2 class="w3-text-primary"><?= Html::icon("info", tag: "h2") ?> Teamdaten</h2>
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-striped">
        <tr>
            <th class="w3-primary" style="width: 140px">Teamname</th>
            <td><b><?= e($teamEntity->getName()) ?></b></td>
        </tr>
        <tr>
            <th class="w3-primary">Team ID</th>
            <td><?= e($teamEntity->id()) ?></td>
        </tr>
        <tr>
            <th class="w3-primary">Freilose</th>
            <td>
                <?=$teamEntity->getFreilose()?>
                <?php if(TeamValidator::hasSchiriFreilosErhalten($teamEntity)): ?>
                    <span class="w3-text-green">
                        <?= Html::icon("check") ?>
                        (Schirifreilos erhalten am <?= $teamEntity->getZweitesFreilos()->format("d.m.Y") ?>)
                    </span>
                <?php else: ?>
                    <span class="w3-text-grey">
                         - dein Team hat noch kein Freislos für zwei Schiedsrichter erhalten.
                    </span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th class="w3-primary">Gesetzte Freilose</th>
            <td>
                <?php if (TeamService::getGesetzteFreilose($teamEntity)->isEmpty()): ?>
                    --
                <?php endif; ?>
                <?php foreach (TeamService::getGesetzteFreilose($teamEntity) as $anmeldung): ?>
                    <p>
                        Freilos gesetzt am <?= $anmeldung->getFreilosGesetztAm()->format("d.m.Y") ?> für das Turnier
                        <?= TurnierSnippets::ortDatumBlock($anmeldung->getTurnier()) ?>
                        <?php if(TeamService::isFreilosRecyclebar($anmeldung)): ?>
                            <br>
                            <span class="w3-text-green">Dieses Freilos könnt ihr auf Antrag beim Ligaausschuss zurückerhalten</span>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th class="w3-primary">Erstellte Turniere</th>
            <td>
                <?php if (TeamService::getEingetrageneTurniere($teamEntity)->isEmpty()): ?>
                    --
                <?php endif; ?>
                <?php foreach (TeamService::getEingetrageneTurniere($teamEntity) as $turnier): ?>
                    <p>
                         Turnier in <?= TurnierSnippets::ortDatumBlock($turnier) ?> eingetragen am
                         <?= $turnier->getErstelltAm()->format("d.m.Y") ?>
                        <?php if(TeamService::isAusrichterFreilosBerechtigt($turnier)
                            && !$turnier->isCanceled()): ?>
                            <br>
                            <span class="w3-text-green">Für dieses Turnier könnt ihr nach dem Turnier ein Freilos
                                erhalten (insgesamt 2x möglich)</span>
                        <?php endif; ?>
                    </p>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th class="w3-primary">Ligavertreter</th>
            <td><?= e($teamEntity->getDetails()->getLigavertreter()) ?></td>
        </tr>
        <tr>
            <th class="w3-primary" style="width: 140px">PLZ</th>
            <td><?= e($teamEntity->getDetails()->getPlz()) ?></td>
        </tr>
        <tr>
            <th class="w3-primary">Ort</th>
            <td><?= e($teamEntity->getDetails()->getOrt()) ?></td>
        </tr>
        <tr>
            <th class="w3-primary">Verein</th>
            <td><?= e($teamEntity->getDetails()->getVerein()) ?></td>
        </tr>
        <tr>
            <th class="w3-primary">Homepage</th>
            <td><?= Html::link(e($teamEntity->getDetails()->getHomepage())) ?></td>
        </tr>
    </table>
</div>

<h2 class="w3-text-primary"><?= Html::icon("mail", tag: "h2") ?> Kontaktdaten</h2>
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-striped">
        <tr>
            <th class="w3-primary">Email</th>
            <th class="w3-primary w3-center">Auf Webseite anzeigen?</th>
            <th class="w3-primary w3-center">Infomails erhalten?</th>
        </tr>
        <?php foreach($emails as $email){?>
            <tr>
                <td><?=e($email['email'])?></td>
                <td class='w3-center'><?=e($email['public'])?></td>
                <td class='w3-center'><?=e($email['get_info_mail'])?></td>
            </tr>
        <?php }?>
    </table>
</div>

<!-- Link Teamdaten ändern -->
<p>
    <a href="<?=(Helper::$ligacenter) ? 'lc_teamdaten_aendern.php?team_id=' . $team->id : 'tc_teamdaten_aendern.php'?>"
       class="w3-button w3-block w3-secondary">
        <?= Html::icon("create") ?> Team- und Kontaktdaten ändern
    </a>
</p>
