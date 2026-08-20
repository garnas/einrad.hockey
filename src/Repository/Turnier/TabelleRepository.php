<?php

namespace App\Repository\Turnier;

use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurnierErgebnis;
use App\Repository\DoctrineWrapper;
use App\Repository\TraitSingletonRepository;
use Config;
use Doctrine\DBAL\Exception;

/**
 * Datenzugriff für die Rang- und Meisterschaftstabelle.
 */
class TabelleRepository
{
    use TraitSingletonRepository;

    private function __construct() {}

    public function getMaxSpieltag(int $saison = Config::SAISON): int
    {
        $result = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('MAX(t.spieltag) + 1')
            ->from(Turnier::class, 't')
            ->andWhere('t.saison = :saison')
            ->andWhere('t.art IN (:arten)')
            ->andWhere('t.phase = :phase')
            ->setParameter('saison', $saison)
            ->setParameter('arten', ['I', 'II', 'III'])
            ->setParameter('phase', 'ergebnis')
            ->getQuery()
            ->getSingleScalarResult();

        return $result === null ? 1 : (int) $result;
    }

    /**
     * Gibt die an einem Spieltag vorkommenden Phasen zurück.
     *
     * @param string[] $arten
     * @return string[]
     */
    public function getPhasenBySpieltag(int $spieltag, int $saison = Config::SAISON): array
    {
        $query = DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('DISTINCT t.phase')
            ->from(Turnier::class, 't')
            ->andWhere('t.spieltag = :spieltag')
            ->andWhere('t.art IN (:arten)')
            ->andWhere('t.saison = :saison')
            ->andWhere('t.canceled = false')
            ->setParameter('spieltag', $spieltag)
            ->setParameter('arten', ['I', 'II', 'III'])
            ->setParameter('saison', $saison)
            ->getQuery()->getArrayResult();
        return array_column($query, 'phase');
    }

    /**
     * @return TurnierErgebnis[]
     */
    public function getErgebnisseBySaison(int $saison): array
    {
        return DoctrineWrapper::manager()
            ->createQueryBuilder()
            ->select('e', 'team', 't')
            ->from(TurnierErgebnis::class, 'e')
            ->innerJoin('e.team', 'team')
            ->innerJoin('e.turnier', 't')
            ->andWhere('t.saison = :saison')
            ->andWhere('t.phase = :phase')
            ->orderBy('t.datum', 'DESC')
            ->addOrderBy('e.platz', 'ASC')
            ->setParameter('saison', $saison)
            ->setParameter('phase', 'ergebnis')
            ->getQuery()
            ->getResult();
    }

    /**
     * Liefert für die Meisterschaftstabelle je Team die bis zu 4 besten Turnierergebnisse.
     *
     * Verwendet natives SQL über die Doctrine-DBAL-Verbindung, da Fensterfunktionen
     * (ROW_NUMBER() OVER (PARTITION BY ...)) von Doctrine DQL nicht unterstützt werden.
     *
     * @param int $spieltag
     * @param int $saison
     * @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function getMeisterschaftsRohdaten(int $spieltag, int $saison): array
    {
        $sql = "
                WITH tournaments AS (
                    SELECT
                        te.team_id,
                        teams.teamname,
                        te.turnier_id,
                        tl.datum,
                        te.ergebnis,
                        td.ort,
                        tl.tblock,
                        te.platz,
                        ROW_NUMBER() OVER (
                            PARTITION BY te.team_id
                            ORDER BY te.ergebnis DESC, tl.datum DESC, te.turnier_id DESC
                        ) AS rn
                    FROM turniere_ergebnisse te
                    INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
                    INNER JOIN teams_liga teams ON teams.team_id = te.team_id
                    INNER JOIN turniere_details td ON td.turnier_id = te.turnier_id
                    WHERE teams.ligateam = 'Ja'
                      AND tl.art != 'final'
                      AND (tl.saison = ?)
                      AND (tl.spieltag <= ?)
                ),
                num_of_teams AS (
                    SELECT turnier_id, COUNT(*) AS teilnehmer
                    FROM turniere_ergebnisse
                    GROUP BY turnier_id
                )

                SELECT
                    t.team_id,
                    t.teamname,
                    t.turnier_id,
                    t.datum,
                    t.ergebnis,
                    t.ort,
                    t.tblock,
                    t.platz,
                    teilnehmer
                FROM tournaments t
                JOIN num_of_teams n ON n.turnier_id = t.turnier_id
                WHERE rn <= 4
                ORDER BY team_id, ergebnis DESC, datum DESC;
         ";

        $result = DoctrineWrapper::manager()
            ->getConnection()
            ->executeQuery($sql, [$saison, $spieltag])
            ->fetchAllAssociative();

        return $result;
    }

    /**
     * Liefert für die Rangtabelle je Team die bis zu 5 letzten Turnierergebnisse inkl. Saisonübernahme.
     *
     * Verwendet natives SQL über die Doctrine-DBAL-Verbindung, da Fensterfunktionen
     * (ROW_NUMBER() OVER (PARTITION BY ...)) von Doctrine DQL nicht unterstützt werden.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRangRohdaten(int $spieltag, int $saison): array
    {
        $ausnahme = match ($saison) {
            26 => 'OR tl.saison = 24',
            27 => 'OR tl.saison = 24 OR tl.saison = 25',
            default => '',
        };

        $sql = "
            WITH tournaments as (
                SELECT te.team_id, teams.teamname, te.turnier_id, tl.saison, tl.datum, te.ergebnis, te.platz, tl.tblock, td.ort, row_number() over (PARTITION BY te.team_id order by tl.datum DESC) AS `turnier_rang`
                FROM turniere_ergebnisse te
                INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
                INNER JOIN teams_liga teams ON teams.team_id = te.team_id
                INNER JOIN turniere_details td ON td.turnier_id = te.turnier_id
                WHERE teams.ligateam = 'Ja'
                AND teams.aktiv = 'Ja'
                AND tl.art != 'final'
                AND ((tl.spieltag <= ? AND tl.saison = ?) OR tl.saison = ? - 1 $ausnahme)
                AND not te.saison_uebernahme_verhindern
            ), num_of_teams as (
                SELECT turnier_id, count(*) as teilnehmer
                FROM turniere_ergebnisse te
                GROUP BY turnier_id
            )

            SELECT t.saison, t.turnier_id, t.datum, t.team_id, t.teamname, t.platz, t.ergebnis, t.ort, t.tblock, t.saison, n.teilnehmer
            FROM tournaments t
            LEFT JOIN num_of_teams n ON n.turnier_id = t.turnier_id
            WHERE `turnier_rang` <= 5
            ORDER BY t.datum DESC
        ";

        return DoctrineWrapper::manager()
            ->getConnection()
            ->executeQuery($sql, [$spieltag, $saison, $saison])
            ->fetchAllAssociative();
    }

}
