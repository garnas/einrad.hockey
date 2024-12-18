<?php

require '../../init.php';
require'../../logic/session_team.logic.php';

use App\Repository\Turnier\TurnierRepository;
use App\Service\Form\FormLogicTeam;
use App\Service\Team\NLTeamValidator;
use App\Service\Turnier\TurnierSnippets;

$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

//Existiert das Turnier?
if ($turnier === null){
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

if (isset($_POST['nl_anmelden'])){
    FormLogicTeam::nlTeamAnmelden($turnier);
}


Html::$titel = "Nichtligateam anmelden | Teamcenter";
include '../../templates/header.tmp.php'; ?>
    <div class="w3-panel w3-card-4">
        <h3 class="w3-text-primary"><?= TurnierSnippets::ortWochentagDatumBlock($turnier) ?></h3>
        <?= TurnierSnippets::getListen($turnier) ?>
    </div>
    <div class="w3-panel w3-card-4">
        <h3 class="w3-text-primary">Nichtligateam anmelden</h3>
        <div class="w3-container">
            <ul class="w3-ul w3-leftbar w3-border-tertiary">
                <li>
                    Achtung: Abmeldung des Nichtligateams nur über den Ligaausschuss mit Einverständnis des
                    Nichtligateams möglich.
                </li>
                <li>In der Wartephase kann einmal ein Nichtligateam direkt auf die Setzliste angemeldet werden.</li>
                <li>
                    Es können beliebig viele Nichtligateams angemeldet werden. Allerdings können maximal drei
                    Nichtligateams auf der Setzliste stehen und es müssen immer mindestens vier Plätze auf der Setzliste
                    frei sein für Ligateams. Der Rest wird auf der Warteliste angemeldet.
                </li>
            </ul>
        </div>
        <form method="post">
            <p>
                <label for="nl_teamname" class='w3-text-primary'>Teamname</label><br>
                <input required type="text" class="w3-input w3-border w3-border-primary"
                       placeholder="Nichtligateam eingeben" id="nl_teamname" name="nl_teamname">
            </p>
            <?php if (NLTeamValidator::isValidNLAnmeldungListe($turnier, 'warteliste')): ?>
                <p>
                    <input type="radio" class="w3-radio" name="nl_liste" value="warteliste" id="warteliste" checked="checked">
                    <label for="warteliste"
                           class='w3-text-primary w3-hover-text-secondary'
                           style="cursor: pointer;"
                    >
                            auf die Warteliste
                    </label>
                </p>
            <?php endif; ?>
            <?php if (NLTeamValidator::isValidNLAnmeldungListe($turnier, 'setzliste')): ?>
                <p>
                    <input type="radio"
                           class="w3-radio"
                           name="nl_liste"
                           value="setzliste"
                           id="setzliste"
                           <?= $turnier->isSetzPhase() ? "checked='checked'" : "" ?>
                    >
                    <label for="setzliste"
                           class='w3-text-primary w3-hover-text-secondary'
                           style="cursor: pointer;"
                    >
                            auf die Setzliste <?= ($turnier->isWartePhase() ? "(1x möglich)" : "") ?>
                    </label>
                </p>
            <?php endif; ?>
            <p>
                <input type='submit' class='w3-button w3-margin-bottom w3-tertiary' name='nl_anmelden' value='Anmelden'>
            </p>
        </form>
    </div>
<?php include '../../templates/footer.tmp.php';