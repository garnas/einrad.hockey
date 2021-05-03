<?php

/**
 * Class Kontakt
 *
 * Klasse für die E-Mail-Adressen-Verwaltung der Teams
 */
class Kontakt
{
    /**
     * Eindeutige in der Datenbank hinterlegte Team-ID
     * @var int
     */
    public int $team_id;

    /**
     * Kontakt constructor.
     *
     * @param int $team_id
     */
    public function __construct(int $team_id)
    {
        $this->team_id = $team_id;
    }

    /**
     * Get alle Emails für eine Rundmail
     *
     * Array der E-Mail-Adressen
     * @return array
     */
    public static function get_emails_rundmail(): array
    {
        $sql = "
                SELECT DISTINCT teams_kontakt.email
                FROM teams_kontakt
                INNER JOIN teams_liga
                ON teams_liga.team_id = teams_kontakt.team_id 
                WHERE teams_liga.aktiv = 'Ja'
                ";
        return db::$db->query($sql)->esc()->list('email');
    }

    /**
     * Get E-Mailadresse der Teams auf einem Turnier
     *
     * @param $turnier_id
     * @return array
     */
    public static function get_emails_turnier(int $turnier_id): array
    {
        $sql = "
                SELECT teams_kontakt.email, teams_liga.teamname 
                FROM teams_kontakt
                INNER JOIN teams_liga
                ON teams_liga.team_id = teams_kontakt.team_id
                INNER JOIN turniere_liste
                ON turniere_liste.team_id = teams_kontakt.team_id
                WHERE teams_liga.aktiv = 'Ja' 
                AND turniere_liste.turnier_id = ?
                ";

        $return['emails'] = $return['teamnamen'] = [];
        // Doppelte Teamnamen und Email-Adressen filtern
        foreach (db::$db->query($sql, $turnier_id)->esc()->fetch() as $x){
            if (!in_array($x['teamname'], $return['teamnamen'])) {
                $return['teamnamen'][] = $x['teamname'];
            }
            if (!in_array($x['email'], $return['emails'])){
                $return['emails'][] = $x['email'];
            }
        }
        return $return;
    }

    /**
     * Erstellt einen neuen Teamkontakteintrag in der Datenbank
     * @param string $email
     * @param string $public
     * @param string $infomail
     */
    public function set_email(string $email, string $public, string $infomail): void
    {
        $email = strtolower($email);
        $sql = "
                INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) 
                VALUES ($this->team_id, ?, ?, ?)
                ";
        db::$db->query($sql, [$email, $public, $infomail])->log();
    }

    /**
     * Get alle Emails des erstellten Teamkontaktes
     *
     * @param string $scope $scope = 'info => Nur Emails für automatische Infomails
     *                      $scope = 'public' => Nur öffentliche Emails
     * @return array
     */
    public function get_emails(string $scope = ''): array
    {
        $and_clause = match ($scope) {
            '' => '',
            'public' => "AND public = 'Ja'",
            'info' => "AND get_info_mail = 'Ja'"
        };
        $sql = "
                SELECT email
                FROM teams_kontakt 
                WHERE team_id = $this->team_id
                " . $and_clause;
        return db::$db->query($sql)->esc()->list('email');
    }

    /**
     * Get alle verfügbaren Emails eines Teams in einem Array mit allen Infos aufgeschlüsselt
     *
     * Funktioniert nicht mit Form::mailto()
     *
     * @return array der Form [teams_kontakt_id][email, public, get_info_mail]
     */
    public function get_emails_with_details(): array
    {
        $sql = "
                SELECT * 
                FROM teams_kontakt 
                WHERE team_id = $this->team_id
                ";
        return db::$db->query($sql)->esc()->fetch('teams_kontakt_id');
    }

    /**
     * Set public, also ob eine E-Mail-Adresse öffentlich angezeigt werden will
     *
     * @param $teams_kontakt_id
     * @param $value
     */
    public function set_public(int $teams_kontakt_id, string $value): void
    {
        $sql = "
                UPDATE teams_kontakt 
                SET public = ? 
                WHERE teams_kontakt_id = ?
                AND team_id = $this->team_id
                ";
        db::$db->query($sql, [$value, $teams_kontakt_id])->log();

    }

    /**
     * Set get_info_mail, also ob eine E-Mail-Adresse automatische Infomails bekommen will
     *
     * @param $teams_kontakt_id
     * @param $value
     */
    public function set_info(int $teams_kontakt_id, string $value): void
    {
        $sql = "
                UPDATE teams_kontakt 
                SET get_info_mail = ?
                WHERE teams_kontakt_id = ?
                AND team_id = $this->team_id
                ";
        db::$db->query($sql, [$value, $teams_kontakt_id])->log();
    }

    /**
     * Löscht eine E-Mail-Adresse aus der Datenbank
     *
     * @param $teams_kontakt_id
     * @return bool
     */
    public function delete_email(int $teams_kontakt_id): bool
    {
        if (count($this->get_emails()) > 1) {
            $sql = "
                    DELETE FROM teams_kontakt 
                    WHERE teams_kontakt_id = ?
                    AND team_id = $this->team_id
                    ";
            db::$db->query($sql, $teams_kontakt_id)->log();
            return true;
        }
        return false;
    }
}