<?php

/**
 *
 */
class nSpieler
{
    public int $spieler_id;
    public ?int $team_id;
    public ?String $teamname;
    public ?int $letzte_saison;
    public ?string $vorname;
    public ?string $nachname;
    public ?int $jahrgang;
    public ?string $geschlecht;
    public ?int $schiri = NULL;
    public ?string $junior = NULL;
    public ?string $timestamp;

    public bool $error = false;



    /**
     * Spieler constructor.
     */
    public function __construct($esc = true)
    {
        if ($esc) {
            foreach (get_object_vars($this) as $name => $value) {
                $this->$name = db::escape($value);
            }
        }
    }

    /**
     * @param int $id
     * @return nSpieler
     */
    public static function get(int $id): nSpieler
    {
        $sql = "
                SELECT spieler.* , teams_liga.teamname
                FROM spieler 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE spieler.spieler_id = ?
                ";
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return db::$db->query($sql, $id)->fetch_object(__CLASS__) ?? new nSpieler();
    }

    public static function get_all(): array
    {
        $sql = "
                SELECT spieler.* , teams_liga.teamname
                FROM spieler 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                ";
        return db::$db->query($sql)->fetch_objects(__CLASS__, key:'spieler_id');
    }

    /**
     * @param int $team_id
     * @param int $saison
     * @return nSpieler[]
     */
    public static function get_kader(int $team_id, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT spieler.* , teams_liga.teamname
                FROM spieler 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE spieler.team_id = ?
                AND spieler.letzte_saison = ?
                ";
        return db::$db->query($sql, $team_id, $saison)->fetch_objects(__CLASS__, key:'spieler_id');
    }

    public function get_vorname(): String
    {
        return $this->vorname;
    }

    /**
     * @param string $vorname
     * @return nSpieler
     */
    public function set_vorname(string $vorname): nSpieler
    {
        $this->vorname = $vorname;
        return $this;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->spieler_id;
    }

    /**
     * @return int
     */
    public function get_team_id(): int
    {
        return $this->team_id;
    }

    public function get_team(): string
    {
        return $this->teamname ?? '';
    }

    /**
     * @param int|null $team_id
     * @return nSpieler
     */
    public function set_team_id(?int $team_id): nSpieler
    {
        if (!isset($this->team_id)) {
            $this->team_id = 0;
        }
        if (Team::is_ligateam($team_id)) {
            if ($this->team_id !== $team_id) {
                $this->set_timestamp();
            }
            $this->team_id = $team_id;
            $this->teamname = Team::id_to_name($team_id);

        } else {
            $this->error = true;
            Html::error("Die Team-ID konnte keinem Ligateam zugeordnet werden.");
        }

        return $this;
    }

    /**
     * @param string $teamname
     * @return nSpieler
     */
    public function set_teamname(string $teamname): nSpieler
    {
        $team_id = Team::name_to_id($teamname);
        if ($team_id != $this->team_id) {
            $this->set_timestamp();
        }
        $this->set_team_id($team_id);

        return $this;
    }

    /**
     * @param bool $short
     * @return string|null
     */
    public function get_nachname(bool $short = false): ?string
    {
        if ($short) {
            return mb_substr($this->nachname,0,1, "utf-8") . '.';
        }
        return $this->nachname;
    }

    /**
     * @param string $nachname
     * @return nSpieler
     */
    public function set_nachname(string $nachname): nSpieler
    {
        $this->nachname = $nachname;
        return $this;
    }

    /**
     * @param bool $short
     * @return string
     */
    public function get_name(bool $short = false): string
    {
        return $this->get_vorname() . ' ' . $this->get_nachname($short);
    }


    /**
     * @return int|null
     */
    public function get_jahrgang(): ?int
    {
        return $this->jahrgang;
    }

    /**
     * @param string|null $jahrgang
     * @return nSpieler
     */
    public function set_jahrgang(?string $jahrgang): nSpieler
    {
        if ($jahrgang < 1900 || $jahrgang > (int)date("Y")) {
            $this->error = true;
            HTML::error("Ungültiges Jahrgangsformat - (JJJJ ist das richtige Format)");
        }

        $this->jahrgang = $jahrgang;
        return $this;

    }

    /**
     * @return string|null
     */
    public function get_geschlecht(): ?string
    {
        return $this->geschlecht;
    }

    /**
     * @param string|null $geschlecht
     * @return nSpieler
     */
    public function set_geschlecht(?string $geschlecht): nSpieler
    {
        $this->geschlecht = $geschlecht;
        return $this;
    }

    public function get_schiri(): string
    {
        if (empty($this->schiri)) {
            return '';
        }
        return Html::get_saison_string($this->schiri);
    }

    public function get_schiri_as_html(): string
    {
        if (empty($this->schiri)) {
            return '';
        }
        $saison_text = Html::get_saison_string($this->schiri);
        $junior = ($this->junior === 'Ja') ? "<i class='w3-text-grey'>junior</i>" : "";
        $ausbilder = ($this->check_ausbilder()) ? "<i class='w3-text-grey'>Ausbilder/in</i>" : "";
        if ($this->schiri >= Config::SAISON) {
            $icon = Html::icon("check_circle_outline");
            return "<span class='w3-text-green'>$icon $saison_text $junior $ausbilder</span>";
        } else {
            $icon = Html::icon("block");
            return "<span class='w3-text-grey'><s>$icon $saison_text $junior</s> $ausbilder</span>";
        }
    }

    /**
     * @param int|null $schiri
     * @return nSpieler
     */
    public function set_schiri(?int $schiri): nSpieler
    {
        $this->schiri = $schiri;
        return $this;
    }

    /**
     * @return string|null
     */
    public function get_junior(): ?string
    {
        return $this->junior;
    }

    /**
     * @param string|null $junior
     * @return nSpieler
     */
    public function set_junior(?string $junior): nSpieler
    {
        if ($junior === "Ja") {

            $junior_bis = (int)date("Y", strtotime(Config::SAISON_ANFANG)) - 16;
            if ($this->jahrgang >= $junior_bis) {
                $this->junior = $junior;
            } else {
                Html::error("Der Spieler ist zu alt für den Junior-Schiri.");
                $this->error = true;
            }

        } else {

            $this->junior = NULL;

        }

        return $this;

    }

    /**
     * @return string|null
     */
    public function get_timestamp(): ?string
    {
        return $this->timestamp;
    }

    public function set_timestamp(): nSpieler
    {
        $this->timestamp = date('Y-m-d H:i:s');
        return $this;
    }

    public function speichern(bool $new = false): bool
    {

        if ($this->error) {
            return false;
        }

        // Es handelt sich um einen Neu-Eintrag
        if ($new) {

            // Es wird getestet, ob der Spieler bereits existiert:
            $check = "
                SELECT spieler_id, letzte_saison, team_id
                FROM spieler 
                WHERE vorname = ? 
                AND nachname = ?
                AND jahrgang = ? 
                AND geschlecht = ?
                ";
            $params = [$this->vorname, $this->nachname, $this->jahrgang, $this->geschlecht];
            $result = db::$db->query($check, $params)->fetch_row();



            if (isset($result['spieler_id'])) {

                $this->spieler_id = $result['spieler_id'];

                if ($result['letzte_saison'] === Config::SAISON) {
                    Html::error("Der Spieler ist bereits im Kader für folgendes Team gemeldet: " . Team::id_to_name($result['team_id'])
                        . "<br> Bitte wende dich an den Ligaausschuss (" . Html::mailto(Env::LAMAIL) . ")",
                        esc:false);
                    return false;
                }
                if ($result['team_id'] !== $this->team_id) {
                    Html::info("Der Spieler wurde vom Team '" . Team::id_to_name($result['team_id']) . "' übernommen.");
                    $spieler = self::get($result['spieler_id']);
                    $spieler
                        ->set_team_id($this->team_id)
                        ->set_letzte_saison(Config::SAISON)
                        ->speichern();
                    return true;
                }

            } else { // Spieler existierte vorher nicht

                $new_entry = "INSERT INTO spieler VALUES ()";
                db::$db->query($new_entry)->log();
                $this->spieler_id = db::$db->get_last_insert_id();

            }


        }

        $update = "
                UPDATE spieler
                SET team_id = ?,
                    vorname = ?,
                    nachname = ?,
                    jahrgang = ?,
                    geschlecht = ?,
                    schiri = ?,
                    junior = ?,
                    letzte_saison = ?,
                    timestamp = ?
                WHERE spieler_id = $this->spieler_id
                ";
        $params = [
            $this->team_id,
            $this->vorname,
            $this->nachname,
            $this->jahrgang,
            $this->geschlecht,
            $this->schiri,
            $this->junior,
            $this->letzte_saison,
            $this->timestamp
            ];

        db::$db->query($update, $params)->log();

        return true;
    }

    public function delete(): void
    {
        $sql = "
                DELETE FROM spieler 
                WHERE spieler_id = ?
                ";
        db::$db->query($sql, $this->spieler_id)->log();
    }

    /**
     * @return String
     */
    public function get_letzte_saison(): String
    {
        if (is_null($this->letzte_saison)) {
            return '';
        }

        return Html::get_saison_string($this->letzte_saison);
    }

    /**
     * @param int|null $saison
     * @return nSpieler
     */
    public function set_letzte_saison(?int $saison): nSpieler
    {
        if (strtotime(Config::SAISON_ENDE) + 24 * 60 * 60 < time() && !Helper::$ligacenter) {
            Html::error("Spieler können nur bis zum Ende der regulären Saison hinzugefügt werden.");
            $this->error = true;
            return $this;
        }

        if (!isset($this->letzte_saison)) {
            $this->letzte_saison = 0;
        }

        if (
            $this->letzte_saison !== $saison
            && $saison === Config::SAISON
        ) {
            $this->set_timestamp();
        }

        $this->letzte_saison = $saison;
        return $this;
    }



    public function check_ausbilder(): bool
    {
        $sql = "
                SELECT * 
                FROM ligaleitung 
                WHERE spieler_id = $this->spieler_id 
                AND funktion = 'schiriausbilder'
                ";
        return db::$db->query($sql)->affected_rows() > 0;
    }
}