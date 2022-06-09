<?php

class spielplan_final
{
    private Spielplan_JgJ $lilienthal;
    private Spielplan_JgJ $thedinghausen;
    private array $gruppeA;
    private array $gruppeB;
    public nTurnier $turnier;
    private string $topOrBottom;

    public function __construct(nTurnier $turnier)
    {
        $this->turnier = $turnier;
        $this->topOrBottom = self::topOrBottom($this->turnier->turnier_id);
    }

    public static function topOrBottom(int $turnier_id): string
    {
        if ($turnier_id === 1010) {
            return 'bottom';
        }
        return 'top';
    }

    public function delete() {
        $sql = "DELETE FROM spiele WHERE turnier_id = ?";
        db::$db->query($sql, $this->turnier->get_turnier_id());
        $sql = "DELETE FROM turniere_liste WHERE turnier_id = ?";
        db::$db->query($sql, $this->turnier->get_turnier_id());
    }

    public function createAndPersist() {
        // Get Top 3 Thedinghausen, Lilienthal
        $thedinghausenTurnier = nTurnier::get(1007);
        $lilienthalTurnier = nTurnier::get(1008);
        $this->thedinghausen = new Spielplan_JgJ($thedinghausenTurnier);
        $this->lilienthal = new Spielplan_JgJ($lilienthalTurnier);

        if ($this->topOrBottom == 'top') {
            $this->gruppeA = $this->getTeamIdsTop3($this->thedinghausen);
            $this->gruppeB = $this->getTeamIdsTop3($this->lilienthal);
        } elseif ($this->topOrBottom  == 'bottom') {
            $this->gruppeA = $this->getTeamIdsBottom3($this->thedinghausen);
            $this->gruppeB = $this->getTeamIdsBottom3($this->lilienthal);
        } else{
            trigger_error("TSFD", E_USER_ERROR);
        }

        $this->teamsAnmelden();
        $this->addSpieleVortag();
        $this->addSpiele();
    }

    private function teamsAnmelden()
    {
        $team_ids = array_merge($this->gruppeA, $this->gruppeB);
        foreach ($team_ids as $team_id){
            $this->turnier->set_team($team_id, 'spiele');
        }
    }

    private function getTeamIdsTop3(Spielplan_JgJ $spielplan)
    {
        $top3 = array_slice($spielplan->platzierungstabelle, 0, 3, true);
        return array_keys($top3);
    }

    private function getTeamIdsBottom3(Spielplan_JgJ $spielplan)
    {
        $bottom3 = array_slice($spielplan->platzierungstabelle, 3, 3, true);
        return array_keys($bottom3);
    }

    private function getSpielIdsVortag(Spielplan_JgJ $spielplan): array
    {
        $team_ids = array_merge($this->gruppeA, $this->gruppeB);
        return $spielplan->get_spiel_ids($team_ids);
    }

    private function getSpieleVortag() {
        $spiel_ids[$this->lilienthal->turnier_id] = $this->getSpielIdsVortag($this->lilienthal);
        $spiel_ids[$this->thedinghausen->turnier_id] = $this->getSpielIdsVortag($this->thedinghausen);
        $spiele = [];

        $newId = 1;
        foreach ($spiel_ids as $turnier_id => $ids) {
            $sql = "
                SELECT spiel_id, team_id_a, t1.teamname AS teamname_a, team_id_b, t2.teamname AS teamname_b,
                schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b
                FROM spiele AS sp
                INNER JOIN teams_liga as t1 on t1.team_id = sp.team_id_a
                INNER JOIN teams_liga as t2 on t2.team_id = sp.team_id_b
                WHERE turnier_id = ?
                AND spiel_id IN (?,?,?)
                AND team_id_a = t1.team_id
                AND team_id_b = t2.team_id
                ";
            $result = db::$db->query($sql, $turnier_id, $ids[0], $ids[1], $ids[2] )->esc()->fetch();
            foreach ($result as $spiel) {
                $spiel["zeit"] = "Vortag";
                $spiel["spiel_id"] = $newId;
                $spiele[$newId++] = $spiel;
            }
        }
        return $spiele;
    }

    private function addSpieleVortag(){
        $vortag = $this->getSpieleVortag();
        foreach ($vortag as $spiel) {
            $sql = "
                    INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b,
                                        tore_a, tore_b, penalty_a, penalty_b)
                    VALUES (?,?,?,?,?,?,?,?,?,?)
                    ";
            $params = [
                $this->turnier->turnier_id,
                $spiel['spiel_id'],
                $spiel['team_id_a'],
                $spiel['team_id_b'],
                $spiel['schiri_team_id_a'],
                $spiel['schiri_team_id_b'],
                $spiel['tore_a'],
                $spiel['tore_b'],
                $spiel['penalty_a'],
                $spiel['penalty_b'],
            ];
            db::$db->query($sql, $params)->log();
        }
    }

    private function addSpiele()
    {
        $spiel_paarungen = [
            [3, 1],
            [1, 3],
            [2, 2],
            [3, 3],
            [2, 1],
            [1, 2],
            [2, 3],
            [3, 2],
            [1, 1]
        ];
        $schiri_paarungen = [
            [1, 3],
            [3, 1],
            [3, 3],
            [2, 2],
            [1, 2],
            [2, 1],
            [3, 2],
            [2, 3],
            [2, 2]
        ];

        $spiel_id = 7;
        foreach ($spiel_paarungen as $key => $paarung) {
            $sql = "
                    INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b)
                    VALUES (?,?,?,?,?,?)
                    ";
            $params = [
                $this->turnier->turnier_id,
                $spiel_id++,
                $this->gruppeA[$paarung[0]-1],
                $this->gruppeB[$paarung[1]-1],
                $this->gruppeA[$schiri_paarungen[$key][0]-1],
                $this->gruppeB[$schiri_paarungen[$key][1]-1],
            ];
            db::$db->query($sql, $params)->log();
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

        $spiel_paarungen = [
            [3, 1],
            [1, 3],
            [2, 2],
            [3, 3],
            [2, 1],
            [1, 2],
            [2, 3],
            [3, 2],
            [1, 1]
        ];

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