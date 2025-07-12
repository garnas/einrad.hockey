<?php

class Archiv_Spielplan_JgJ extends Spielplan_JgJ {
    /**
     * Berechnet und fügt die Ligapunkte in die Platzierungstabelle ein.
     */
    public function set_ligapunkte(): void
    {        
        $reverse_tabelle = array_reverse($this->platzierungstabelle, true);
        $ligapunkte = 0;
        foreach ($reverse_tabelle as $team_id => $eintrag) {
            $ligapunkte += $this->platzierungstabelle[$team_id]['wertigkeit'];
            $this->platzierungstabelle[$team_id]['ligapunkte'] = round($ligapunkte * $this->details['faktor']);
        }
    }
}
