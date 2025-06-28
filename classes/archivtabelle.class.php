<?php

/**
 * Class Tabelle
 *
 * Alles zum Anzeigen der Tabelle
 */
class ArchivTabelle extends Tabelle
{
    /**
     * Gibt die Templates für die Meisterschaftstabelle zurück
     *
     * @param string $saison
     * @return array
     */
    public static function get_meisterschafts_tabelle_templates(int $saison = Config::SAISON): array
    {
        return [
            'desktop' => 'templates/archiv/tabellen/desktop_meistertabelle.tmp.php',
            'mobil' => 'templates/archiv/tabellen/mobil_meistertabelle.tmp.php',
        ];
    }

    public static function get_meisterschafts_tabelle(int $spieltag, int $saison = Config::SAISON): array
    {
        $sql = "
            WITH tournaments as (
                SELECT te.team_id, teams.teamname, te.turnier_id, tl.datum, te.ergebnis, td.ort, tl.tblock, te.platz, dense_rank() over (PARTITION BY te.team_id order by te.ergebnis DESC) AS `rank`
                FROM turniere_ergebnisse te
                INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
                INNER JOIN teams_liga teams ON teams.team_id = te.team_id
                INNER JOIN turniere_details td ON td.turnier_id = te.turnier_id
                WHERE teams.ligateam = 'Ja'
                AND tl.art != 'final' 
                AND (tl.saison = ?) 
                AND (tl.spieltag <= ?)
            ), num_of_teams as (
                SELECT turnier_id, count(*) as teilnehmer
                FROM turniere_ergebnisse te
                GROUP BY turnier_id
            )

            SELECT team_id, teamname, t.turnier_id, datum, ergebnis, ort, tblock, platz, teilnehmer
            FROM tournaments t
            INNER JOIN num_of_teams n ON n.turnier_id = t.turnier_id
            WHERE `rank` <= 5 ORDER BY team_id, ergebnis DESC
         ";
        $result = db::$db->query($sql, $saison, $spieltag)->esc()->fetch();
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
            $list_of_teamids = Team::get_liste_ids();
            shuffle($list_of_teamids);
            foreach ($list_of_teamids as $team_id) {
                if (!array_key_exists($team_id, $return)) {
                    $return[$team_id] = [];
                    $return[$team_id]['teamname'] = Team::id_to_name($team_id); //Ansonsten doppel dbi::escape --> fehler in der Darstellung
                    $return[$team_id]['team_id'] = $team_id;
                    $return[$team_id]['string'] = '';
                    $return[$team_id]['summe'] = 0;
                    $return[$team_id]['einzel_ergebnisse'] = [0];
                    $return[$team_id]['details'] = [];
                    $return[$team_id]['hat_strafe'] = false;
                }
            }
        }

        // Hinzufügen der Strafen:
        $strafen = Team::get_strafen($saison);
        foreach ($strafen as $strafe) {
            # Ist die Strafe überhaupt in den Ergebnissen enthalten?
            if (!isset($return[$strafe['team_id']])) {
                continue;
            }

            $return[$strafe['team_id']]['hat_strafe'] = true;
            
            // Addieren der Prozentstrafen
            if ($strafe['verwarnung'] == 'Nein' && !empty($strafe['prozentsatz'])) {
                $return[$strafe['team_id']]['strafe'] = ($return[$strafe['team_id']]['strafe'] ?? 0) + $strafe['prozentsatz'] / 100;
            }
        }

        // Kumulierte Strafe mit der Summe der Turnierergebnisse des Teams verrechnen
        foreach ($return as $team_id => $team) {
            if (isset($team['strafe'])) {
                $return[$team_id]['summe'] = round($team['summe'] * (1 - $team['strafe']));
            }
        }

        // Nach Summe der Ergebnisse sortieren mit der Funktion "sortieren_summe" die eine public static function in dieser Klasse Tabelle ist
        uasort($return, ["Tabelle", "sortieren_summe"]);

        // Zuordnen der Plätze
        // Teams mit gleicher Summe und gleichem höchsten Einzelergebnis bekommen den selben Platz
        $platz = 1;
        $zeile_vorher['platz'] = 1;
        $zeile_vorher['summe'] = 0;
        $zeile_vorher['max_einzel'] = 0;
        foreach ($return as $key => $zeile) {
            $zeile['max_einzel'] = max($zeile['einzel_ergebnisse'] ?? [0]);
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

        if ($saison !== Config::SAISON) {
            foreach ($return as $team_id => $team) {
                $return[$team_id]['teamname'] = Team::id_to_name($team_id, $saison);
            }
        }

        return $return;
    }
}