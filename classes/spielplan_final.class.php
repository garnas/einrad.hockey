<?php


class spielplan_final
{

    public nTurnier $turnier;
    private string $topOrBottom;

    public const FINAL_TOP_ID = 1011;
    public const FINAL_BOTTOM_ID = 1010;


    public function __construct(nTurnier $turnier)
    {
        $this->turnier = $turnier;
        $this->topOrBottom = self::topOrBottom($this->turnier->turnier_id);
    }

    public static function topOrBottom(int $turnier_id): string
    {
        if ($turnier_id === self::FINAL_BOTTOM_ID) {
            return 'bottom';
        }
        if ($turnier_id === self::FINAL_TOP_ID) {
            return 'top';
        }
        trigger_error("TopOrBottom Fehler Final", E_USER_ERROR);
    }

    public static function routeToFinalSpielplan(int $turnier_id): void
    {
        if ($turnier_id === self::FINAL_TOP_ID || $turnier_id === self::FINAL_BOTTOM_ID){
            Helper::reload('/liga/spielplan_finale.php', '?turnier_id=' . $turnier_id);
        }
    }

    public function get_spielplan(): Spielplan_JgJ
    {
        $spielplan = new Spielplan_JgJ($this->turnier);

        $startzeit = strtotime($this->turnier->get_startzeit());
        $spielzeit = (
                $spielplan->details["anzahl_halbzeiten"]
                * $spielplan->details["halbzeit_laenge"]
                + $spielplan->details["puffer"]
            ) * 60; // In Sekunden für Unixzeit

        $spiele = $spielplan->spiele;
        foreach($spiele as $spiel_id => $spiel){
            if ($spiel_id < 7) {
                $spiele[$spiel_id]['zeit'] = "Vortag";
            } else {
                $spiele[$spiel_id]["zeit"] = date("H:i", $startzeit);
                $startzeit += $spielzeit + $spielplan->get_pause($spiel_id) * 60;
            }
        }

        $spielplan->spiele = $spiele;
        $offset = ($this->topOrBottom == 'bottom') ? 3 : 0;
        $spielplan->teamliste[$spiele[7]['team_id_a']]->tblock= (3 + $offset) . ". Gruppe A";
        $spielplan->teamliste[$spiele[7]['team_id_b']]->tblock= (1 + $offset) . ". Gruppe B";
        $spielplan->teamliste[$spiele[8]['team_id_a']]->tblock= (1 + $offset) . ". Gruppe A";
        $spielplan->teamliste[$spiele[8]['team_id_b']]->tblock= (3 + $offset) . ". Gruppe B";
        $spielplan->teamliste[$spiele[9]['team_id_a']]->tblock= (2 + $offset) . ". Gruppe A";
        $spielplan->teamliste[$spiele[9]['team_id_b']]->tblock= (2 + $offset) . ". Gruppe B";

        uasort($spielplan->teamliste, static function ($team_a, $team_b) {
            return ($team_a->tblock <=> $team_b->tblock);
        });

        if ($this->topOrBottom === "bottom") {
            foreach($spielplan->platzierungstabelle as $team_id => $team) {
                $spielplan->platzierungstabelle[$team_id]['platz'] += 6;
            }
        }
        return $spielplan;
    }
}
