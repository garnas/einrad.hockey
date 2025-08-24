<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Entity\Team\FreilosGrund;
use App\Entity\Team\Kontakt;
use App\Entity\Team\nTeam;
use App\Entity\Team\TeamDetails;
use App\Repository\Team\TeamRepository;

require_once "../../init.php";
require_once "../../logic/session_la.logic.php";

// Formularauswertung
if (isset($_POST["team_erstellen"])) {
    $error = false;
    $teamname = $_POST["teamname"];
    $passwort = $_POST["passwort"];
    $email = $_POST["email"];
    $mitFreilos = $_POST["mit_freilos"] ?? "Nein";

    //Felder dürfen nicht leer sein
    if (empty($teamname) || empty($email) || empty($passwort)) {
        Html::error("Bitte alle Felder ausfüllen");
        $error = true;
    }

    //Email wird auf gültigkeit überprüft
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        Html::error("Ungültige Email");
        $error = true;
    }

    //Nichtligateams bekommen ein Stern hinter ihrem Namen, damit sie nicht Teamnamen für Ligateams wegnehmen.
    if (!empty(Team::name_to_id($teamname))) {
        Html::error("Der Teamname existiert bereits");
        $error = true;
    }

    //Team wird erstellt
    if (!$error) {
        $team = (new nTeam())
            ->setName($teamname)
            ->setLigateam("Ja")
            ->setPasswort($passwort);
        $kontakt = (new Kontakt())
            ->setTeam($team)
            ->setEmail($email)
            ->setPublic("Nein")
            ->setGetInfoMail("Nein");
        $team->addEmail($kontakt);
        $team->setDetails((new TeamDetails())->setTeam($team));
        if ($mitFreilos === "Ja") {
            $team->addFreilos(grund: FreilosGrund::NEUES_LIGATEAM);
        }
        TeamRepository::get()->speichern($team);

        Html::info("Das Team \"" . e($teamname) . "\" wurde erfolgreich erstellt. Email: " . e($email) . " Passwort: " . e($passwort));
        Helper::reload("/liga/teams.php");
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include "../../templates/header.tmp.php";
?>

    <div class="w3-card-4 w3-panel">
        <form method="post">
            <h3>Neues Ligateam</h3>

            <label class="w3-text-primary" for="teamname">Teamname:</label><br>
            <input required class="w3-input w3-border w3-border-primary" type="text" id="teamname" value="<?= $_POST["teamname"] ?? "" ?>" name="teamname">
            <p>
                <label class="w3-text-primary" for="passwort">Passwort:</label><br>
                <input required class="w3-input w3-border w3-border-primary" type="text" id="passwort" value="<?= $_POST["passwort"] ?? "" ?>" name="passwort">
            </p>
            <p>
                <label class="w3-text-primary" for="email">E-Mail:</label><br>
                <input required class="w3-input w3-border w3-border-primary" type="email" id="email" value="<?= $_POST["email"] ?? "" ?>" name="email">
            </p>
            <p>
                <input class="w3-check" type="checkbox" id="mit_freilos" name="mit_freilos" checked value="Ja">
                <label for="mit_freilos" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer">Das neue Team soll direkt ein Freilos erhalten</label>
            </p>
            <p>
                <input class="w3-button w3-block w3-secondary" type="submit" name="team_erstellen" value="Team erstellen">
            </p>
        </form>
    </div>

    <div class="w3-card-4 w3-panel">
        <p>
            <a class="w3-button w3-block w3-primary" href="lc_start.php">
                <i class="material-icons">chevron_left</i>
                Zurück
                <i class="material-icons" style="visibility: hidden">chevron_right</i>
            </a>
        </p>
    </div>

<?php include "../../templates/footer.tmp.php";