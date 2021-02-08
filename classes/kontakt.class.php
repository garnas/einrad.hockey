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
     * @param int $team_id
     */
    function __construct(int $team_id)
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
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $return[] = $x['email'];
        }
        return db::escape($return ?? []);
    }

    /**
     * Get E-Mailadresse der Teams auf einem Turnier
     *
     * @param $turnier_id
     * @return array
     */
    public static function get_emails_turnier($turnier_id): array
    {
        // distinct email funktioniert nicht, da sonst Teamnamen fehlen oder doppelt vorkommen.
        $sql = "
                SELECT teams_kontakt.email, teams_liga.teamname 
                FROM teams_kontakt
                INNER JOIN teams_liga
                ON teams_liga.team_id = teams_kontakt.team_id
                INNER JOIN turniere_liste
                ON turniere_liste.team_id = teams_kontakt.team_id
                WHERE teams_liga.aktiv = 'Ja' 
                AND turniere_liste.turnier_id = '$turnier_id'
                ";

        $result = db::readdb($sql);
        $return['emails'] = $return['teamnamen'] = [];
        while ($x = mysqli_fetch_assoc($result)) {
            if (!in_array($x['teamname'], $return['teamnamen'])) {
                $return['teamnamen'][] = $x['teamname'];
            }
            if (!in_array($x['email'], $return['emails'])) {
                $return['emails'][] = $x['email'];
            }
        }
        return db::escape($return);
    }

    /**
     * Erstellt einen neuen Teamkontakteintrag in der Datenbank
     * @param string $email
     * @param string $public
     * @param string $infomail
     */
    function create_new_team_kontakt(string $email, string $public, string $infomail)
    {
        $team_id = $this->team_id;
        $email = strtolower($email);
        $sql = "
                INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) 
                VALUES ('$team_id','$email','$public','$infomail')
                ";
        db::writedb($sql);
    }

    /**
     * Get alle Emails des erstellten Teamkontaktes
     *
     * @param string $scope $scope = 'info => Nur Emails für automatische Infomails
     *                      $scope = 'public' => Nur öffentliche Emails
     * @return array
     */
    function get_emails(string $scope = ''): array
    {
        $team_id = $this->team_id;
        $and_clause = match ($scope) {
            '' => '',
            'public' => "AND public = 'Ja'",
            'info' => "AND get_info_mail = 'Ja'"
        };
        $sql = "
                SELECT email
                FROM teams_kontakt 
                WHERE team_id = $team_id
                " . $and_clause;
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $return[] = $x['email'];
        }
        return db::escape($return ?? []);
    }

    /**
     * Get alle verfügbaren Emails eines Teams in einem Array mit allen Infos aufgeschlüsselt
     *
     * Funktioniert nicht mit Form::mailto()
     *
     * @return array der Form [teams_kontakt_id][email, public, get_info_mail]
     */
    function get_emails_with_details(): array
    {
        $sql = "
                SELECT * 
                FROM teams_kontakt 
                WHERE team_id = $this->team_id
                ";
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['teams_kontakt_id']] = $x;
        }
        return db::escape($return ?? []);
    }

    /**
     * Set public, also ob eine E-Mail-Adresse öffentlich angezeigt werden will
     *
     * @param $teams_kontakt_id
     * @param $value
     */
    function set_public($teams_kontakt_id, $value)
    {
        $sql = "
                UPDATE teams_kontakt 
                SET public = '$value' 
                WHERE teams_kontakt_id = '$teams_kontakt_id'
                ";
        db::writedb($sql);
    }

    /**
     * Set get_info_mail, also ob eine E-Mail-Adresse automatische Infomails bekommen will
     *
     * @param $teams_kontakt_id
     * @param $value
     */
    function set_info($teams_kontakt_id, $value)
    {
        $sql = "
                UPDATE teams_kontakt 
                SET get_info_mail = '$value'
                WHERE teams_kontakt_id = '$teams_kontakt_id'
                ";
        db::writedb($sql);
    }

    /**
     * Löscht eine E-Mail-Adresse aus der Datenbank
     *
     * @param $teams_kontakt_id
     * @return bool
     */
    function delete_email($teams_kontakt_id): bool
    {
        if (count($this->get_emails()) > 1) {
            $sql = "DELETE FROM teams_kontakt WHERE teams_kontakt_id = '$teams_kontakt_id'";
            db::writedb($sql);
            return true;
        }
        return false;
    }
}