<?php
use Diegobanos\Glicko2\Rating\Rating;
use Diegobanos\Glicko2\Result\Result;
use Diegobanos\Glicko2\Glicko2;

class Ranking
{
    public int $turnier_id;
    public int $spiel_id;
    public int $team_id_a;
    public int $team_id_b;
    public int $schiri_team_id_a;
    public int $schiri_team_id_b;
    public ?int $tore_a;
    public ?int $tore_b;
    public ?int $penalty_a;
    public ?int $penalty_b;

    public ?float $rating_a;
    public ?float $rating_a_deviation;
    public ?float $rating_a_volatility;
    public ?float $rating_b;
    public ?float $rating_b_deviation;
    public ?float $rating_b_volatility;
    public ?float $delta_a;
    public ?float $delta_a_deviation;
    public ?float $delta_a_volatility;
    public ?float $delta_b;
    public ?float $delta_b_deviation;
    public ?float $delta_b_volatility;
    public string $datum;

    const RATING_DEFAULT = 1500;
    const DEVIATION_DEFAULT = 300;
    const VOLATILITY_DEFAULT = 0.1;

    const TOTAL_SEASONS_FOR_CALC = 2;

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

    public static function calculate_score(Ranking $ranking): void
    {
        $rating_a = self::get_old_ranking($ranking->team_id_a, $ranking);
        $ranking->rating_a = $rating_a->getRating();
        $ranking->rating_a_deviation = $rating_a->getRatingDeviation();
        $ranking->rating_a_volatility = $rating_a->getVolatility();

        $rating_b = self::get_old_ranking($ranking->team_id_b, $ranking);
        $ranking->rating_b = $rating_b->getRating();
        $ranking->rating_b_deviation = $rating_b->getRatingDeviation();
        $ranking->rating_b_volatility = $rating_a->getVolatility();

        if ($ranking->tore_a == $ranking->tore_b) {
            $result_a = new Result($rating_b, 0.5);
            $result_b = new Result($rating_a, 0.5);
        } elseif ($ranking->tore_a > $ranking->tore_b) {
            $result_a = new Result($rating_b, 1);
            $result_b = new Result($rating_a, 0);
        } else {
            $result_a = new Result($rating_b, 0);
            $result_b = new Result($rating_a, 1);
        }

        $glicko2 = new Glicko2;

        $rating_a_new = $glicko2->calculateRating($rating_a, [$result_a]);
        $ranking->delta_a = $rating_a_new->getRating() - $rating_a->getRating();
        $ranking->delta_a_deviation = $rating_a_new->getRatingDeviation() - $rating_a->getRatingDeviation();
        $ranking->delta_a_volatility = $rating_a_new->getVolatility() - $rating_a->getVolatility();

        $rating_b_new = $glicko2->calculateRating($rating_b, [$result_b]);
        $ranking->delta_b = $rating_b_new->getRating() - $rating_b->getRating();
        $ranking->delta_b_deviation = $rating_b_new->getRatingDeviation() - $rating_b->getRatingDeviation();
        $ranking->delta_b_volatility = $rating_b_new->getVolatility() - $rating_b->getVolatility();

    }

    /**
     * @return Ranking[]
     */
    public static function get_all_spiele(): array
    {
        $sql = "
                SELECT s.*, t.datum
                FROM spiele s
                INNER JOIN turniere_liga t ON s.turnier_id = t.turnier_id
                WHERE s.tore_a IS NOT NULL AND s.tore_b IS NOT NULL
                ORDER BY t.datum, s.turnier_id, s.spiel_id 
                "
        ;
        return db::$db->query($sql)->fetch_objects(__CLASS__);
    }

    public static function persist_delta(Ranking $ranking): void
    {
        $sql = "
            UPDATE spiele
            SET delta_a = $ranking->delta_a,
                delta_b = $ranking->delta_b,
                delta_a_deviation = $ranking->delta_a_deviation,
                delta_b_deviation = $ranking->delta_b_deviation,
                delta_a_volatility = $ranking->delta_a_volatility,
                delta_b_volatility = $ranking->delta_b_volatility
            WHERE turnier_id = $ranking->turnier_id AND spiel_id = $ranking->spiel_id
        ";
        db::$db->query($sql)->log();
    }

    public static function get_old_ranking($team_id, Ranking $ranking): Rating
    {
        $sql = "
            SELECT SUM(delta_a) as rating, SUM(delta_a_deviation) as deviaton, SUM(delta_a_volatility) as volatility
            FROM spiele s
            INNER JOIN turniere_liga t ON s.turnier_id = t.turnier_id
            WHERE s.team_id_a = $team_id 
              AND t.datum < (SELECT datum FROM turniere_liga st WHERE st.turnier_id = $ranking->turnier_id)
              AND t.canceled = 0
              AND t.art != 'spass'
              AND t.phase = 'ergebnis'
              AND t.saison > ?
        ";
        $sum_a = db::$db->query($sql, Config::SAISON - self::TOTAL_SEASONS_FOR_CALC)->fetch_row();
        $sql = "
            SELECT SUM(delta_b) as rating, SUM(delta_b_deviation) as deviaton, SUM(delta_b_volatility) as volatility
            FROM spiele s
            INNER JOIN turniere_liga t ON s.turnier_id = t.turnier_id
            WHERE s.team_id_b = $team_id 
              AND t.datum < (SELECT datum FROM turniere_liga st WHERE st.turnier_id = $ranking->turnier_id)
              AND t.canceled = 0
              AND t.art != 'spass'
              AND t.phase = 'ergebnis'
              AND t.saison > ?
        ";
        $sum_b = db::$db->query($sql, Config::SAISON - self::TOTAL_SEASONS_FOR_CALC)->fetch_row();
        $rating = self::RATING_DEFAULT + $sum_a["rating"] + $sum_b["rating"];
        $deviation = self::DEVIATION_DEFAULT + $sum_a["deviaton"] + $sum_b["deviaton"];
        $volatility = self::VOLATILITY_DEFAULT + $sum_a["volatility"] + $sum_b["volatility"];

        return new Rating(rating: $rating, ratingDeviation: $deviation, volatility: $volatility);
    }

    public static function get_rank(int $team_id) {
        $sql = "
            SELECT SUM(delta_a)
            FROM spiele s
            INNER JOIN turniere_liga t ON s.turnier_id = t.turnier_id
            WHERE s.team_id_a = $team_id 
              AND t.canceled = 0
              AND t.art != 'spass'
              AND t.phase = 'ergebnis'
              AND t.saison > ?
        ";
        $sum_a = db::$db->query($sql, Config::SAISON - self::TOTAL_SEASONS_FOR_CALC)->fetch_one();
        $sql = "
            SELECT SUM(delta_b)
            FROM spiele s
            INNER JOIN turniere_liga t ON s.turnier_id = t.turnier_id
            WHERE s.team_id_b = $team_id 
              AND t.canceled = 0
              AND t.art != 'spass'
              AND t.phase = 'ergebnis'
              AND t.saison > ?
        ";
        $sum_b = db::$db->query($sql, Config::SAISON - self::TOTAL_SEASONS_FOR_CALC)->fetch_one();
        return self::RATING_DEFAULT + $sum_a + $sum_b;
    }

    public static function get_rank_turnier(int $team_id, int $turnier_id)
    {
        $sql = "
            SELECT SUM(delta_a)
            FROM spiele
            WHERE team_id_a = $team_id AND turnier_id = $turnier_id;
        ";
        $sum_a = db::$db->query($sql)->fetch_one();
        $sql = "
            SELECT SUM(delta_b)
            FROM spiele
            WHERE team_id_b = $team_id AND turnier_id = $turnier_id;
        ";
        $sum_b = db::$db->query($sql)->fetch_one();
        return $sum_a + $sum_b;
    }

}
