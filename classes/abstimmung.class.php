<?php

class Abstimmung {

    public $beginn_der_abstimmung = "2020-12-28 10:00:00";
    public $ende_der_abstimmung = "2021-01-31 18:00:00";

    // Überprüfung, ob das Team bereits abgestimmt hat
    function get_team($crypt) {
        $sql = '
        SELECT * 
        FROM abstimmung 
        WHERE crypt = "'. $crypt . '"
        ';

        $result = db::readdb($sql);
        $data = mysqli_fetch_assoc($result);
        
        // Check, ob Team bereits abgestimmt hat
        // true -> Das Team hat noch nie abgestimmt; false -> Das Team hat bereits einmal abgestimmt
        if (empty($data)) {
            return true;
        } else {
            return false;
        }
    }

    function get_crypt($key, $teamname, $iv) {
        $cipher = "aes-128-gcm";
        if (in_array($cipher, openssl_get_cipher_methods())) {
            # Alternativ kann man auch mit einem Datum Arbeiten (das müsste dann aber gespeichert werden)
            # Oder man gibt einfach eine Zahl vor
            $ciphertext = openssl_encrypt($teamname, $cipher, $key, $options=0, $iv, $tag);
        }
        return $ciphertext;
    }

    // Stimmt ein Team ab, wird die Stimme getrennt von der team_id gespeichert
    static function add_stimme($value, $crypt) {
        $sql = '
        INSERT INTO `abstimmung` (`id`, `value`, `crypt`) 
        VALUES (NULL, "' . $value . '", "' . $crypt . '")
        ';
        
        db::writedb($sql);
    
        return true;
    }

    static function update_stimme($value, $crypt) {
        $sql = '
        UPDATE `abstimmung` 
        SET `value` = "' . $value . '"
        WHERE `crypt` = "' . $crypt . '"
        ';
        
        db::writedb($sql);
    
        return true;
    }

    function get_ergebnisse($min = 6) {
        $sql = '
        SELECT value, COUNT(value) AS stimmen 
        FROM `abstimmung_ergebnisse`
        GROUP BY value
        ';

        $result = db::readdb($sql);

        $ergebnisse = array();
        $anzahl_stimmen = 0;

        while($row = mysqli_fetch_assoc($result)) {
            $ergebnisse = array_merge($ergebnisse, array($row['value'] => $row['stimmen']));
            $anzahl_stimmen = $anzahl_stimmen + $row['stimmen'];
        }
        
        if ($anzahl_stimmen < $min) {
            foreach($ergebnisse as $möglichkeit => $stimmen) {
                $ergebnisse[$möglichkeit] = 0;
            }
        }

        $ergebnisse = array_merge($ergebnisse, array('gesamt' => $anzahl_stimmen));
        db::debug($ergebnisse);

        return $ergebnisse;
    }
}