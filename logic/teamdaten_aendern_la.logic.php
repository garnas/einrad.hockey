<?php

use App\Entity\Team\FreilosGrund;
use App\Repository\Team\TeamRepository;

if (isset($_POST['change_la']) && Helper::$ligacenter) {
    $error = false;
    $neuer_teamname = $_POST['teamname'];
    $passwort = $_POST['passwort'];

    if (
        $neuer_teamname != htmlspecialchars_decode($team->getName())
        && TeamRepository::get()->findByName($neuer_teamname)
    ) {
        Html::error("Der Teamname existiert bereits.");
        $error = true;
    }


    if (!$error) {
        if ($neuer_teamname != $team->getName()) {
            $team->setName($neuer_teamname);
            Html::info("Der Teamname wurde geändert.");
        }
        if (!empty($_POST["freilos_grund"]) && $_POST["freilos_grund"] != "NO_CHANGE") {
            $grund = FreilosGrund::fromName($_POST["freilos_grund"]);
            $saison = (int) $_POST["freilos_saison"];
            $team->addFreilos($grund, $saison);
            Html::info("Freilos wurde hinzugefügt.");
        }
        if (!empty($_POST["freilos_delete"]) && $_POST["freilos_delete"] != "NO_CHANGE") {
            $id = (int) $_POST["freilos_delete"];
            TeamRepository::get()->deleteFreilos($_POST["freilos_delete"], $team);
            Html::info("Freilos wurde gelöscht.");
        }
        if (!empty($passwort)) {
            $team->setPasswort($passwort);
            Html::info("Passwort wurde geändert.");
        }
        TeamRepository::get()->speichern($team);
    }
    Helper::reload("ligacenter/lc_teamdaten_aendern.php?team_id=" . $team->id());

}
