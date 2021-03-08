<?php

/**
 * Class Tabelle
 *
 * Alles zum Anzeigen der Tabelle
 */
class Tabelle
{

    /**
     * Speichert die Erstellten Rangtabellen, damit diese nicht mehrfach erstellt werden müssen.
     * @var array
     */
    public static array $rangtabellen = [];

    /**
     * Übergibt den Spieltag, bis zu welchem Ergebnisse eingetragen worden sind.
     *
     * @param int $saison
     * @return int
     */
    public static function get_aktuellen_spieltag(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT spieltag
                FROM turniere_liga 
                WHERE saison = ?
                AND (art='I' OR art = 'II' OR art='III')
                AND phase != 'ergebnis'
                ORDER BY spieltag
                LIMIT 1
                ";
        return dbi::$db->query($sql, $saison)->fetch_one() ?? 1;
    }

    /**
     * Schaut ob der aktuelle Spieltag live ist, also ein unvollständiger Spieltag, welcher teilweise ausgespielt wurde.
     *
     * @param int $spieltag
     * @param int $saison
     * @return bool
     */
    public static function check_spieltag_live(int $spieltag, $saison = Config::SAISON): bool
    {
        $sql = "
                SELECT phase, count(phase)
                FROM turniere_liga 
                WHERE spieltag = ?
                AND (art = 'I' OR art = 'II' OR art = 'III') 
                AND saison = ?
                GROUP BY phase
                ";
        $result = dbi::$db->query($sql, $spieltag, $saison)->list('count(phase)','phase');
        return (
                isset($result['spielplan']) or isset($result['melde']) or isset($result ['offen'])
                )
                && isset($result['ergebnis']);
    }

    /**
     * True, wenn das Turnierergebnis eingetragen werden darf. Also jedes vorherige Turnier in der Ergebnisphase ist.
     *
     * @param Turnier $turnier
     * @return bool
     */
    public static function check_ergebnis_eintragbar(Turnier $turnier): bool
    {
        if (!in_array($turnier->details['art'], ['I', 'II', 'III', 'final'])) {
            Html::error("Für diesen Turniertyp können keine Ergebnisse eingetragen werden.");
            // TODO ist der Check hier an der besten Stelle?
            return false;
        }
        $sql = "
                SELECT * 
                FROM turniere_liga 
                WHERE spieltag < ? 
                AND spieltag != 0 
                AND (art='I' OR art = 'II' OR art='III') 
                AND saison = ?
                AND phase != 'ergebnis'
                ";
        return dbi::$db->query($sql, $turnier->details['spieltag'], $turnier->details['saison'])->num_rows() === 0;
    }

    /**
     * Gibt die Platzierung eines Teams in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function get_team_rang(int $team_id, NULL|int $spieltag = NULL): ?int
    {
        // Default: Aktueller Spieltag - 1 = Spieltag mit allen eingetragenen Ergebnissen
        $spieltag = $spieltag ?? (self::get_aktuellen_spieltag() - 1);

        // Rangtabelle soll nicht jedes mal neu berechnet werden müssen
        if (!isset(self::$rangtabellen[$spieltag])){
            self::$rangtabellen[$spieltag] = self::get_rang_tabelle($spieltag);
        }
        // Nichtligateam haben den Rang NULL
        return self::$rangtabellen[$spieltag][$team_id]['rang'] ?? NULL;
    }

    /**
     * Gibt den Block eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return string|null
     */
    public static function get_team_block(int $team_id, NULL|int $spieltag = NULL): ?string
    {
        $rang = self::get_team_rang($team_id, $spieltag);
        return self::rang_to_block($rang);
    }

    /**
     * Gibt die Wertung eines Teams auf Grundlage der Platzierung in der Rangtabelle zurück
     *
     * @param int $team_id
     * @param int|null $spieltag
     * @return int|null
     */
    public static function get_team_wertigkeit(int $team_id, null|int $spieltag = null): ?int
    {
        $rang = self::get_team_rang($team_id, $spieltag);
        return self::rang_to_wertigkeit($rang);
    }

    /**
     * Weist dem Platz in der Rangtabelle einen Block zu
     *
     * @param int|null $rang
     * @return string|null
     */
    public static function rang_to_block(?int $rang): ?string
    {
        // Nichtligateam
        if (is_null($rang)) return NULL;

        // Blockzuordnung
        foreach (Config::RANG_TO_BLOCK as $block => $range) {
            if ($range[0] <= $rang && $range[1] >= $rang){
                return $block;
            }
        }
    }

    /**
     * Weist dem Platz in der Rangtabelle eine Wertung zu
     *
     * @param int|null $rang
     * @return int|null
     */
    public static function rang_to_wertigkeit(?int $rang): ?int
    {
        // Nichtligateam
        if (is_null($rang)){
            return NULL;
        }

        // Platz 1 bis 43;
        if (1 <= $rang && 43 >= $rang){
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
    public static function get_all_ergebnisse($saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_ergebnisse.*, teams_liga.teamname , teams_liga.ligateam
                FROM turniere_ergebnisse
                LEFT JOIN teams_liga
                ON teams_liga.team_id = turniere_ergebnisse.team_id
                LEFT JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
                WHERE turniere_liga.saison = $saison
                AND phase = 'ergebnis'
                ORDER BY turniere_liga.datum DESC, platz
                ";
        $result = dbi::$db->query($sql)->esc()->fetch();
        foreach ($result as $ergebnis){
            $return[$ergebnis['turnier_id']][] = $ergebnis;
        }
        return $return ?? [];
    }

    /**
     * Gibt das Array der Meisterschaftstabelle aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function get_meisterschafts_tabelle(int $spieltag, int $saison = Config::SAISON): array
    {

        $sql = "
                SELECT turniere_ergebnisse.ergebnis, turniere_ergebnisse.turnier_id, turniere_liga.datum, 
                turniere_liga.saison, teams_liga.aktiv, teams_liga.teamname, teams_liga.team_id 
                FROM turniere_ergebnisse
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_ergebnisse.team_id
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
                WHERE teams_liga.ligateam = 'Ja'
                AND turniere_liga.art != 'final' 
                AND (turniere_liga.saison = ?) 
                AND (turniere_liga.spieltag <= ?)
                ORDER BY ergebnis DESC, RAND()
                ";
        $result = dbi::$db->query($sql, $saison, $spieltag)->esc()->fetch('team_id');

        $counter = $return = [];
        foreach($result as $team_id => $eintrag){
            if (isset($return[$team_id])) {
                if ($counter[$team_id] <= 5) {
                    $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];
                    $return[$team_id]['string'] .= "+" . Html::link("ergebnisse.php#" . $eintrag['turnier_id'], $eintrag['ergebnis']);
                    $return[$team_id]['summe'] += $eintrag['ergebnis'];
                }
            } else {
                $return[$team_id]['einzel_ergebnisse'] = [];
                $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];
                $return[$team_id]['team_id'] = $team_id;
                $return[$team_id]['teamname'] = $eintrag['teamname'];
                $return[$team_id]['string'] = Html::link("ergebnisse.php#" . $eintrag['turnier_id'], $eintrag['ergebnis']);
                $return[$team_id]['summe'] = $eintrag['ergebnis'];
                $counter[$team_id] = 1;
            }
            $counter[$team_id]++;
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet
        if ($saison == Config::SAISON) {
            $list_of_teamids = Team::get_liste_ids();
            foreach ($list_of_teamids as $team_id) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = Team::id_to_name($team_id); //Ansonsten doppel dbi::escape --> fehler in der Darstellung
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = [0];
                }
            }
        }

        // Hinzufügen der Strafen:
        $strafen = Team::get_strafen();
        foreach ($strafen as $strafe) {
            // Hinzufügen des Sterns
            if (isset($return[$strafe['team_id']]['strafe_stern'])) {
                $return[$strafe['team_id']]['strafe_stern'] .= '*';
            } else {
                $return[$strafe['team_id']]['strafe_stern'] = '*';
            }
            // Addieren der Prozentstrafen
            if ($strafe['verwarnung'] == 'Nein' && !empty($strafe['prozentsatz'])) {
                $return[$strafe['team_id']]['strafe'] = ($return[$strafe['team_id']]['strafe'] ?? 0) + $strafe['prozentsatz'] / 100;
            }
        }
        // Kumulierte Strafe mit der Summe der Turnierergebnisse des Teams verrechnen
        foreach ($return as $team_id => $team) {
            if (isset($team['strafe'])) {
                $return[$team_id]['summe'] = round($return[$team_id]['summe'] * (1 - $team['strafe']));
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_summe" die eine public static function in dieser Klasse Tabelle ist
        uasort($return, ["Tabelle", "sortieren_summe"]);

        // Zuordnen der Plätze
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse']);
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
        return $return;
    }

    /**
     * Gibt die Rangtabelle als Array aus
     *
     * @param int $spieltag
     * @param int $saison
     * @return array
     */
    public static function get_rang_tabelle(int $spieltag, int $saison = Config::SAISON): array
    {

        $ausnahme = ($saison === 26) ? 'OR turniere_liga.saison = 24' : '';

        $sql = "
                SELECT turniere_ergebnisse.ergebnis, turniere_ergebnisse.turnier_id, turniere_liga.datum, 
                turniere_liga.saison, teams_liga.teamname, teams_liga.team_id, turniere_liga.spieltag 
                FROM turniere_ergebnisse
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_ergebnisse.team_id
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_ergebnisse.turnier_id
                WHERE teams_liga.ligateam = 'Ja'
                AND teams_liga.aktiv = 'Ja'
                AND turniere_liga.art != 'final'
                AND (
                    (turniere_liga.spieltag <= ? 
                    AND turniere_liga.saison = ? 
                    OR (turniere_liga.saison = ? - 1)
                    $ausnahme)
                    )
                ORDER BY turniere_liga.saison DESC, turniere_liga.datum DESC";
        $result = dbi::$db->query($sql, $spieltag, $saison, $saison)->esc()->fetch('team_id');
        $return = [];
        $counter = [];

        foreach($result as $team_id => $eintrag) {

            //Farbe des Ergebnisses in der Rangtabelle festlegen.
            $color =  ($eintrag['saison'] != $saison) ? "w3-text-green" : 'w3-text-primary';

            //Verlinkung des Ergebnisses hinzufügen
            $link = "ergebnisse.php?saison=" . $eintrag['saison'] . "#" . $eintrag['turnier_id'];

            //Initialisierung
            if (!isset($return[$team_id])) {
                //Zähler der Ergebnisse (Max 5)
                $counter[$team_id] = 1;
                $return[$team_id]['summe'] = $eintrag['ergebnis'];
                $return[$team_id]['einzel_ergebnisse'] = [];
                $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];

                $return[$team_id]['team_id'] = $team_id; //Wichtig, da bei Sortierung die $eintrag['team_id] überschrieben wird
                $return[$team_id]['teamname'] = $eintrag['teamname'];
                $return[$team_id]['string'] =
                    "<a href='$link' class='no $color w3-hover-text-secondary'>" . $eintrag['ergebnis'] . "</a>";

            } else if ($counter[$team_id] <= 5) {
                $return[$team_id]['einzel_ergebnisse'][] = $eintrag['ergebnis'];
                $return[$team_id]['string'] .=
                    "+<a href='$link' class='no $color w3-hover-text-secondary'>" . $eintrag['ergebnis'] . "</a>";
                $return[$team_id]['summe'] += $eintrag['ergebnis'];
            }
            $counter[$team_id]++;
            $return[$team_id]['avg'] = round($return[$team_id]['summe'] / count($return[$team_id]['einzel_ergebnisse']), 1);
        }

        // Tabelle mit aktiven Teams ohne Ergebnis auffüllen
        // TODO ? In vergangenen Saisons werden nur Teams mit Ergebnissen gelistet, ist das gut so?
        if ($saison == Config::SAISON) {
            $list_of_teamids = Team::get_liste();
            foreach ($list_of_teamids as $team_id => $teamname) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = $teamname;
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['avg'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = [0];
                }
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_avg"
        uasort($return, ["Tabelle", "sortieren_avg"]); //Sortieren nach der static function sortieren_avg in der Klasse Tabelle...

        // Zuordnen der Blöcke
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $rang = 1;
        $zeile_vorher['rang'] = 1;
        $zeile_vorher['summe'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse']);
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
    public static function sortieren_summe(array $value1, array $value2): int
    {
        if ($value1['summe'] < $value2['summe']) return 1;
        if ($value1['summe'] > $value2['summe']) return -1;
        return max($value2['einzel_ergebnisse']) <=> max($value1['einzel_ergebnisse']);
    }

    /**
     * Individuelle Sortierfunktion für die Rangtabelle
     *
     * @param array $value1
     * @param array $value2
     * @return int
     */
    public static function sortieren_avg(array $value1, array $value2): int
    {
        if ($value1['avg'] < $value2['avg']) return 1;
        if ($value1['avg'] > $value2['avg']) return -1;
        return max($value2['einzel_ergebnisse']) <=> max($value1['einzel_ergebnisse']);
    }
}