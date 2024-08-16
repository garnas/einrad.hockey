<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Entity\Team\FreilosGrund;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php';

$teams = TeamRepository::get()->activeLigaTeams();
foreach ($teams as $team) {
    $vorher = $team->getOffeneFreilose()->count();
    if (TeamService::handleSchiriFreilos($team, false)) {
        Html::info("Schirifreilos hinzugefügt für ".  $team->getName());
    }
    $delta = $team->getFreiloseOld() - $team->getOffeneFreilose()->count();
    if ($delta < 0) {
        Html::error("SCHIRIFREILOS " . $team->getName());
    }
    if ($delta > 0){
        foreach(range(1, $delta) as $index) {
            echo "$index of " . $delta . "<br>";
            $team->addFreilos(FreilosGrund::SONSTIGES, Config::SAISON - 1);
            Html::info("Sonstiges Freilos hinzugefügt für " .  $team->getName());
        }
        if ($team->getFreiloseOld() != $team->getOffeneFreilose()->count()) {
            Html::error($team->getName());
            Html::error($team->getFreiloseOld());
            Html::error($team->getOffeneFreilose()->count());
        }
    }
    TeamRepository::get()->speichern($team);
}
$turnier = TurnierRepository::get()->turnier(1217);

$angels = TeamRepository::get()->team(994);
$angels->addFreilos(FreilosGrund::SONSTIGES, Config::SAISON - 1);
$angels->getNextFreilos()->setzen($turnier);
TeamRepository::get()->speichern($angels);

$allergiker = TeamRepository::get()->team(933);
$allergiker->getNextFreilos()->setzen($turnier);
TeamRepository::get()->speichern($allergiker);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Impressum | Deutsche Einradhockeyliga";
Html::$content = "Das Impressum der Deutschen Einradhockeyliga findet sich hier.";
include '../../templates/header.tmp.php'; ?>

<?php include '../../templates/footer.tmp.php';