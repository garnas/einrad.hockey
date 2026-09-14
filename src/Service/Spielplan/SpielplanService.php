<?php

namespace App\Service\Spielplan;

use App\Entity\Spielplan\SpielplanDetails;
use App\Entity\Team\nTeam;
use App\Entity\Turnier\Spiel;
use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use App\Repository\Spielplan\SpielplanRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierService;
use Html;

/**
 * Lifecycle eines automatischen JgJ-Spielplans: Erstellung aus einer Vorlage,
 * Löschung und Ergebniseintragung.
 *
 * Portiert aus Spielplan::spielplan_erstellen() / fill_vorlage() / delete() /
 * get_vorlage() / set_tore().
 */
final class SpielplanService
{
    /** Team-Anzahl => Name der Standard-Spielplanvorlage */
    private const VORLAGEN = [
        4 => '4er_jgj_default',
        5 => '5er_jgj_default',
        6 => '6er_jgj_default',
        7 => '7er_jgj_default',
        8 => '8er_jgj_default',
    ];

    public static function hatSpielplan(int $turnierId): bool
    {
        return SpielplanRepository::get()->hatSpielplan($turnierId);
    }

    /**
     * Welche Spielplanvorlage ist für das Turnier maßgeblich?
     *
     * - manuell hochgeladener Spielplan  -> keine Vorlage (null)
     * - bereits gesetzte Vorlage         -> diese
     * - sonst                            -> Standardvorlage nach Team-Anzahl
     */
    public static function ermittleVorlage(Turnier $turnier, ?int $anzahlTeams = null): ?SpielplanDetails
    {
        if (!empty($turnier->getSpielplanDatei())) {
            return null;
        }
        if ($turnier->getSpielplanVorlage() !== null) {
            return $turnier->getSpielplanVorlage();
        }

        $anzahlTeams ??= count(TurnierService::getSpielenliste($turnier));
        $name = self::VORLAGEN[$anzahlTeams] ?? null;

        return $name === null ? null : SpielplanRepository::get()->getDetails($name);
    }

    /**
     * Erstellt einen JgJ-Spielplan in der DB. Meldungen laufen über {@see Html::error()}.
     *
     * @return bool erfolgreich erstellt?
     */
    public static function erstellen(Turnier $turnier): bool
    {
        $anzahlTeams = count(TurnierService::getSpielenliste($turnier));
        $fehler = false;

        if ($turnier->isLigaturnier() && $turnier->getPhase() !== 'setz') {
            Html::error("Das Turnier muss in der Setzphase sein.");
            $fehler = true;
        }
        if ($turnier->isLigaturnier() && ($anzahlTeams < 3 || $anzahlTeams > 8)) {
            Html::error("Falsche Anzahl an Teams. Nur 4er - 8er Jeder-gegen-Jeden Spielpläne können erstellt werden.");
            $fehler = true;
        }
        if (!empty($turnier->getSpielplanDatei())) {
            Html::error("Spielplan konnte nicht erstellt werden. Es existiert ein manuell hochgeladener Spielplan.");
            $fehler = true;
        }

        return $fehler ? false : self::fuelleVorlage($turnier);
    }

    /**
     * Löscht einen automatisch erstellten Spielplan und setzt das Turnier zurück in die Setzphase.
     */
    public static function loeschen(Turnier $turnier): void
    {
        if ($turnier->getSpielplanVorlage() !== null) {
            $turnier->setSpielplanVorlage(null);
            TurnierRepository::get()->speichern($turnier);
        }

        if (!self::hatSpielplan($turnier->id())) {
            return;
        }

        SpielplanRepository::get()->loescheSpiele($turnier);
        $turnier->getLogService()->addLog("Automatischer JgJ-Spielplan gelöscht.");
        $turnier->setPhase('setz');
        TurnierRepository::get()->speichern($turnier);
    }

    /**
     * Schreibt ein Spielergebnis. Nicht eingetragene Werte werden als {@see null} gespeichert
     * (damit sie nicht als 0:0 gewertet werden).
     */
    public static function speichereTore(
        Turnier $turnier,
        int $spielId,
        ?int $toreA,
        ?int $toreB,
        ?int $penaltyA,
        ?int $penaltyB,
    ): void {
        $spiel = SpielplanRepository::get()->getSpiel($turnier, $spielId);
        if ($spiel === null) {
            return;
        }

        $spiel->setToreA($toreA)->setToreB($toreB)->setPenaltyA($penaltyA)->setPenaltyB($penaltyB);
        SpielplanRepository::get()->flush();
    }

    private static function fuelleVorlage(Turnier $turnier): bool
    {
        if (self::hatSpielplan($turnier->id())) {
            Html::error("Es existiert bereits ein Spielplan");
            return false;
        }

        // Teamliste 1-indexiert für die Vorlagenpaarungen
        $teamliste = array_values(TurnierService::getSpielenliste($turnier));
        array_unshift($teamliste, null);
        unset($teamliste[0]);

        $vorlage = self::ermittleVorlage($turnier);
        if ($vorlage === null) {
            Html::error("Es konnte keine Spielplanvorlage ermittelt werden.");
            return false;
        }

        $paarungen = SpielplanRepository::get()->getVorlagePaarungen($vorlage);
        if (empty($paarungen)) {
            Html::error("Es konnte keine Spielreihenfolge aus dem Spielplan ermittelt werden");
            return false;
        }

        $manager = DoctrineWrapper::manager();
        $team = static fn(int $position): nTeam => $manager->getReference(
            nTeam::class,
            $teamliste[$position]['team_id'],
        );

        $spiele = [];
        foreach ($paarungen as $paarung) {
            $spiele[] = (new Spiel())
                ->setTurnier($turnier)
                ->setSpielId($paarung->getSpielId())
                ->setTeamA($team($paarung->getTeamA()))
                ->setTeamB($team($paarung->getTeamB()))
                ->setSchiriTeamA($team($paarung->getSchiriA()))
                ->setSchiriTeamB($team($paarung->getSchiriB()))
                ->setToreA(null)
                ->setToreB(null)
                ->setPenaltyA(null)
                ->setPenaltyB(null);
        }
        SpielplanRepository::get()->speichereSpiele($spiele);

        $turnier->getLogService()->addLog("Automatischer Jgj-Spielplan erstellt.");
        $turnier->setPhase('spielplan');
        $turnier->setSpielplanVorlage($manager->getReference(SpielplanDetails::class, $vorlage->getSpielplan()));
        TurnierRepository::get()->speichern($turnier);

        return true;
    }
}
