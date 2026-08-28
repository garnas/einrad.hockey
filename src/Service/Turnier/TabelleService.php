<?php

namespace App\Service\Turnier;

use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TabelleRepository;
use Config;

/**
 * Class TabelleService
 *
 * Alles zum Anzeigen der Tabelle
 */
class TabelleService
{
    /**
     * Speichert die erstellten Rangtabellen, damit diese nicht mehrfach erstellt werden müssen.
     */
    private static array $cacheRangtabellen = [];
    private static array $cacheMeisterschaftstabelle;

    /**
     * Übergibt den Spieltag, der als naechstes gespielt wird
     *
     * @param int $saison
     * @return int
     */
    public static function getAktuellenSpieltag(int $saison = Config::SAISON): int
    {
        return TabelleRepository::get()->getMaxSpieltag($saison);
    }

    /**
     * Wenn alle Turniere in der Ergebnisphase sind, dann ist der Spieltag beendet.
     * @param int $spieltag
     * @return bool
     */
    public static function isSpieltagBeendet(int $spieltag): bool
    {
        $phasen = TabelleRepository::get()->getPhasenBySpieltag($spieltag);
        return $phasen == ["ergebnis"];
    }

    /**
     * Schaut ob der aktuelle Spieltag live ist, also ein unvollständiger Spieltag, welcher teilweise ausgespielt wurde.
     *
     * @param int $spieltag
     * @param int $saison
     * @return bool
     */
    public static function checkSpieltagLive(int $spieltag, int $saison = Config::SAISON): bool
    {
        $phasen = TabelleRepository::get()->getPhasenBySpieltag($spieltag, $saison, );
        return (
            in_array('spielplan', $phasen, true) || in_array('melde', $phasen, true) || in_array('offen', $phasen, true)
        )
                && in_array('ergebnis', $phasen, true);
    }

    /**
     * Gibt die Platzierung eines Teams in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @param int $saison
     * @return int|null
     */
    public static function getTeamRang(int $team_id, ?int $spieltag = null, int $saison = Config::SAISON): ?int
    {
        // Default: Aktueller Spieltag - 1 = Spieltag mit allen eingetragenen Ergebnissen
        $spieltag ??= (self::getAktuellenSpieltag($saison) - 1);

        // Rangtabelle soll nicht jedes mal neu berechnet werden müssen
        if (!isset(self::$cacheRangtabellen[$spieltag])) {
            self::$cacheRangtabellen[$spieltag] = self::getRangTabelle($spieltag, $saison);
        }
        // Nichtligateam haben den Rang NULL
        return self::$cacheRangtabellen[$spieltag][$team_id]['rang'] ?? null;
    }

    /**
     * Gibt die Platzierung eines Teams in der Meisterschaftstabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function getTeamMeisterPlatz(int $team_id, ?int $spieltag = null): ?int
    {
        // Default: Aktueller Spieltag - 1 = Spieltag mit allen eingetragenen Ergebnissen
        $spieltag ??= (self::getAktuellenSpieltag() - 1);
        if (!isset(self::$cacheMeisterschaftstabelle)) {
            self::$cacheMeisterschaftstabelle = self::getMeisterschaftsTabelle($spieltag);
        }
        // Nichtligateam haben den Platz NULL
        return self::$cacheMeisterschaftstabelle[$team_id]['platz'] ?? null;
    }

    /**
     * Gibt den Block eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return string|null
     */
    public static function getTeamBlock(int $team_id, ?int $spieltag = null): ?string
    {
        $rang = self::getTeamRang($team_id, $spieltag);
        return self::rangToBlock($rang);
    }

    /**
     * Gibt die Wertung eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function getTeamWertigkeit(int $team_id, ?int $spieltag = null, int $saison = Config::SAISON): ?int
    {
        $rang = self::getTeamRang($team_id, $spieltag, $saison);
        return self::rangToWertigkeit($rang);
    }

    /**
     * Weist dem Platz in der Rangtabelle einen Block zu
     *
     * @param int|null $rang
     * @return string|null
     */
    public static function rangToBlock(?int $rang): ?string
    {
        // Nichtligateam
        if (null === $rang) {
            return null;
        }

        // Blockzuordnung
        foreach (Config::RANG_TO_BLOCK as $block => $range) {
            if ($range[0] <= $rang && $range[1] >= $rang) {
                return $block;
            }
        }
        trigger_error("Aus der Rangtabelle konnte kein Block abgeleitet werden.", \E_USER_ERROR);
    }

    /**
     * Weist dem Platz in der Rangtabelle eine Wertung zu
     *
     * @param int|null $rang
     * @return int|null
     */
    public static function rangToWertigkeit(?int $rang): ?int
    {
        // Nichtligateam
        if (null === $rang) {
            return null;
        }

        // Platz 1 bis 43;
        if (1 <= $rang && 43 >= $rang) {
            return round(250 * 0.955 ** ($rang - 1));
        }

        // Platz 44 bis Rest
        return max([round(250 * 0.955 ** (43) * 0.97 ** ($rang - 1 - 43)), 15]);
    }

    /**
     * Get alle Ergebnisse der Saison
     *
     * @param int $saison
     * @return array
     */
    public static function getAllErgebnisse(int $saison = Config::SAISON): array
    {
        $ergebnisse = TabelleRepository::get()->getErgebnisseBySaison($saison);

        $return = [];
        foreach ($ergebnisse as $ergebnis) {
            $turnierId = $ergebnis->getTurnier()->id();
            $team = $ergebnis->getTeam();

            $return[$turnierId][] = [
                'turnier_ergebnis_id' => $ergebnis->getTurnierErgebnisId(),
                'ergebnis' => $ergebnis->getErgebnis(),
                'platz' => $ergebnis->getPlatz(),
                'saison_uebernahme_verhindern' => $ergebnis->isSaisonUebernahmeVerhindern(),
                'turnier_id' => $turnierId,
                'team_id' => $team->id(),
                'teamname' => $team->getName($saison),
                'ligateam' => $team->isLigaTeam() ? 'Ja' : 'Nein',
            ];
        }

        return $return;
    }

    public static function getMeisterschaftsTabelleTemplates(int $saison = Config::SAISON): array
    {
        return [
            'desktop' => 'templates/tabellen/desktop_meisterschaftstabelle.tmp.php',
            'mobil' => 'templates/tabellen/mobil_meisterschaftstabelle.tmp.php',
        ];
    }

    public static function getRangTabelleTemplates(int $saison = Config::SAISON): array
    {
        return [
            'desktop' => 'templates/tabellen/desktop_rangtabelle.tmp.php',
            'mobil' => 'templates/tabellen/mobil_rangtabelle.tmp.php',
        ];
    }

    /**
     * Gibt das Array der Meisterschaftstabelle aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function getMeisterschaftsTabelle(int $spieltag, int $saison = Config::SAISON): array
    {
        $result = TabelleRepository::get()->getMeisterschaftsRohdaten($spieltag, $saison);

        $return = [];
        foreach ($result as $eintrag) {
            $team_id = $eintrag['team_id'];

            if (!isset($return[$team_id])) {
                $return[$team_id]['team_id'] = $team_id;
                $return[$team_id]['teamname'] = $eintrag['teamname'];
                $return[$team_id]['einzel_ergebnisse'] = [];
                $return[$team_id]['details'] = [];
                $return[$team_id]['summe'] = 0;
                $return[$team_id]['hat_strafe'] = false;
            }
            $return[$team_id]['summe'] += $eintrag['ergebnis'];
            $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];
            $return[$team_id]['details'][] = $eintrag;
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet
        if ($saison == Config::SAISON) {
            $teamsActive = TeamRepository::get()->activeLigaTeams();
            foreach ($teamsActive as $team) {
                if (!array_key_exists($team->id(), $return)) {
                    $return[$team->id()] = [];
                    $return[$team->id()]['teamname'] = $team->getName();
                    $return[$team->id()]['team_id'] = $team->id();
                    $return[$team->id()]['string'] = '';
                    $return[$team->id()]['summe'] = 0;
                    $return[$team->id()]['einzel_ergebnisse'] = [0];
                    $return[$team->id()]['details'] = [];
                    $return[$team->id()]['hat_strafe'] = false;
                }
            }
        }

        // Hinzufügen der Strafen:
        $strafen = TeamRepository::get()->getStrafenBySaison($saison);
        foreach ($strafen as $strafe) {
            # Ist die Strafe überhaupt in den Ergebnissen enthalten?
            if (!isset($return[$strafe->getTeam()->id()])) {
                continue;
            }

            $return[$strafe->getTeam()->id()]['hat_strafe'] = true;

            // Addieren der Prozentstrafen
            if ($strafe->isStrafe() && !empty($strafe->getProzentsatz())) {
                $return[$strafe->getTeam()->id()]['strafe'] = ($return[$strafe->getTeam()->id()]['strafe'] ?? 0) + $strafe->getProzentsatz() / 100;
            }
        }

        // Kumulierte Strafe mit der Summe der Turnierergebnisse des Teams verrechnen
        foreach ($return as $team_id => $team) {
            if (isset($team['strafe'])) {
                $return[$team_id]['summe'] = round($team['summe'] * (1 - $team['strafe']));
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortierenSumme" die eine public static function in dieser Klasse TabelleService ist
        uasort($return, [self::class, "sortierenSumme"]);

        // Zuordnen der Plätze
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        $zeile_vorher['max_einzel'] = 0;
        foreach ($return as $key => $zeile) {
            $anzahl_ergebnisse = count($zeile['einzel_ergebnisse'] ?? []);
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse'] ?? [0]);

            if ($anzahl_ergebnisse < 4) {
                $return[$key]['platz'] = null;
            } else {
                if (
                    $zeile_vorher['summe'] === $zeile['summe']
                    && $zeile_vorher['max_einzel'] === $zeile['max_einzel']
                ) {
                    $return[$key]['platz'] = $zeile_vorher['platz'];
                } else {
                    $return[$key]['platz'] = $platz;
                }
                $zeile_vorher['summe'] = $zeile['summe'];
                $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
                $zeile_vorher['platz'] = $return[$key]['platz'];
                $platz++;
            }

        }

        if ($saison !== Config::SAISON) {
            foreach ($return as $team_id => $team) {
                $return[$team_id]['teamname'] = TeamRepository::get()->team($team_id)?->getName($saison);
            }
        }

        return $return;
    }

    /**
     * Gibt die Rangtabelle als Array aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function getRangTabelle(int $spieltag, int $saison = Config::SAISON): array
    {
        $result = TabelleRepository::get()->getRangRohdaten($spieltag, $saison);
        $return = [];

        foreach ($result as $row) {
            $team_id = $row['team_id'];

            if (!isset($return[$team_id])) {
                $return[$team_id]['team_id'] = $team_id; //Wichtig, da bei Sortierung die $row['team_id] überschrieben wird
                $return[$team_id]['teamname'] = $row['teamname'];
                $return[$team_id]['summe'] = 0;
                $return[$team_id]['ergebnisse'] = [];
                $return[$team_id]['details'] = [];
            }

            $return[$team_id]['summe'] += $row['ergebnis'];
            $return[$team_id]['ergebnisse'][] = $row['ergebnis'];
            $return[$team_id]['details'][] = $row;
            $return[$team_id]['avg'] = round($return[$team_id]['summe'] / count($return[$team_id]['ergebnisse']), 1);
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // TODO ? In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet, ist das gut so?
        if ($saison == Config::SAISON) {
            $list_of_teamids = [];
            foreach (TeamRepository::get()->activeLigaTeams() as $ligateam) {
                $list_of_teamids[$ligateam->id()] = $ligateam->getName();
            }
            foreach ($list_of_teamids as $team_id => $teamname) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = $teamname;
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['avg'] = 0;
                    $return[$team_id]['ergebnisse'] = [0];
                    $return[$team_id]['details'] = [];
                }
            }
        }
        if ($saison !== Config::SAISON) {
            foreach ($return as $team_id => $team) {
                $return[$team_id]['teamname'] = TeamRepository::get()->team($team_id)?->getName($saison);
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortierenAvg"
        uasort($return, [self::class, "sortierenAvg"]); //Sortieren nach der static function sortierenAvg in der Klasse TabelleService...

        // Zuordnen der Blöcke
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $rang = 1;
        $zeile_vorher['rang'] = 1;
        $zeile_vorher['summe'] = 0;
        $zeile_vorher['max_einzel'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['ergebnisse']);
            if (
                $zeile_vorher['summe'] == $zeile['summe']
                && $zeile_vorher['max_einzel'] == $zeile['max_einzel']
            ) {
                $return[$key]['rang'] = $zeile_vorher['rang'];
            } else {
                $return[$key]['rang'] = $rang;
            }
            $zeile_vorher['summe'] = $zeile['summe'];
            $zeile_vorher['max_einzel'] = $zeile['max_einzel'];
            $zeile_vorher['rang'] = $return[$key]['rang'];
            $rang++;
        }
        return $return;
    }

    /**
     * Individuelle Sortierfunktion für die Meisterschaftstabelle für usort
     *
     * @param array $value1
     * @param array $value2
     * @return int
     */
    public static function sortierenSumme(array $value1, array $value2): int
    {
        if ($value1['summe'] !== $value2['summe']) {
            return $value2['summe'] <=> $value1['summe'];
        }

        $max1 = max($value1['einzel_ergebnisse'] ?? [0]);
        $max2 = max($value2['einzel_ergebnisse'] ?? [0]);

        if ($max1 !== $max2) {
            return $max2 <=> $max1;
        }

        // Nur für stabile Reihenfolge, beeinflusst nicht die Platzvergabe
        return $value1['team_id'] <=> $value2['team_id'];
    }

    /**
     * Individuelle Sortierfunktion für die Rangtabelle
     *
     * @param array $value1
     * @param array $value2
     * @return int
     */
    public static function sortierenAvg(array $value1, array $value2): int
    {
        if ($value1['avg'] < $value2['avg']) {
            return 1;
        }
        if ($value1['avg'] > $value2['avg']) {
            return -1;
        }
        return max($value2['ergebnisse']) <=> max($value1['ergebnisse']);
    }
}
