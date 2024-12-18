<?php

class nTurnierGeloescht {
    
    private $grund;

    /**
     * TurnierGeloescht constructor.
     */
    public function __construct(bool $esc = true)
    {
        if ($esc) {
            foreach (get_object_vars($this) as $name => $value) {
                $this->$name = db::escape($value);
            }
        }

    }

    /**
     * @return string
     */
    public function get_grund()
    {
        return $this->grund;
    }
    
    /**
     * Liste an gelöschten Turnieren
     *
     * @param int
     * @return nTurnierGeloescht[]
     */
    public static function get_geloescht(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT * 
                FROM turniere_geloescht 
                WHERE saison = ? 
                ORDER BY datum DESC
                ";
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key:'turnier_id');
    }
}