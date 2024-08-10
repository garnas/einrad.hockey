<?php

use App\Repository\Team\TeamRepository;

if (isset($_POST['change_la']) && Helper::$ligacenter) {
    $error = false;
    $neuer_teamname = $_POST['teamname'];
    $freilose = (int)$_POST['freilose'];
    $passwort = $_POST['passwort'];

    if (
        !empty(Team::name_to_id($neuer_teamname))
        && $neuer_teamname != htmlspecialchars_decode($team->getName())
    ) {
        Html::error("Der Teamname existiert bereits.");
        $error = true;
    }


    if (!$error) {
        if ($neuer_teamname != $team->getName()) {
            $team->setName($neuer_teamname);
            Html::info("Der Teamname wird geändert");
        }
        if (!empty($_POST["freilos_grund"])) {
            $team->addFreilos($_POST["freilos_grund"]);
            Html::info("Freilos wird hinzugefügt");
        }
        if (!empty($passwort)) {
            $team->setPasswort($passwort);
            Html::info("Passwort wird geändert");
        }
        TeamRepository::get()->speichern($team);
    }
    Helper::reload("ligacenter/lc_teamdaten_aendern.php?team_id=" . $team->id());

}
