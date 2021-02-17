<?php

class SchiriTest
{
    /**
     * Get eine bestimmte Anzahl an zufälligen Fragen einer Kategorie für den Schiritest
     * aus der Datenbank.
     *
     * @param $kategorie // Kategorie aus welcher die Fragen geholt werden sollen
     * @param $anzahl // Anzahl der Fragen die geholt werden sollen
     * @return array // Fragen die zurückgegeben werden
     */
    static function get_fragen(string $kategorie, int $anzahl, int $fragenr=0): array
    {
        if ($kategorie=='*'){
            $sql = "
            SELECT *
            FROM schiri_test
            ORDER BY RAND()
            LIMIT ?
            ";
            $result = dbi::$db->query($sql, $anzahl)->fetch();
        }else{
            $sql = "
            SELECT *
            FROM schiri_test
            WHERE kategorie = ?
            ORDER BY RAND()
            LIMIT ?
            ";
            $result = dbi::$db->query($sql, $kategorie, $anzahl)->fetch();
        }
        if ($fragenr>0){
            $sql = "
            SELECT *
            FROM schiri_test
            WHERE frage_id = ?
            LIMIT ?
            ";
            $result = dbi::$db->query($sql, $fragenr, $anzahl)->fetch();
        }
        //        $result = db::readdb($sql); // Mysqli Objekt
        //        while ($row = mysqli_fetch_assoc($result)) {
        //            $row['richtig'] = preg_split('/[\s#\s]+/', $row['richtig']); // String in ein Array parsen
        //            for ($index = 1; $index <= 6; $index++){ //Index für die Antwortmöglichkeiten
        //                if (!empty($row['antwort_' . $index])){
        //                    $row['antworten'][$index] = $row['antwort_' . $index]; // Wird zum Array hinzugefügt
        //                    unset($row['antwort_' . $index]); // Wird nicht mehr gebraucht
        //                }
        //            }
        //            $fragen[$row['frage_id']] = $row;
        //        }
        foreach ($result as $row) {
            $row['richtig'] = preg_split('/[\s#\s]+/', $row['richtig']); // String in ein Array parsen
            for ($index = 1; $index <= 6; $index++){ // Index für die Antwortmöglichkeiten
                if (!empty($row['antwort_' . $index])){
                    $row['antworten'][$index] = $row['antwort_' . $index]; // Wird zum Array hinzugefügt
                    unset($row['antwort_' . $index]); // Wird nicht mehr gebraucht
                }
            }
            $fragen[$row['frage_id']] = $row;
        }

        return $fragen ?? []; // Rückgabe muss Array sein, falls $fragen nicht definiert ist, wird deswegen ein leeres
    }

    /**
     * Lade Regelwerk aus der Datenbank
     */
    static function get_regelwerk(): array
    {
        $sql = "
            SELECT *
            FROM regelwerk
        ";
//        $result = db::readdb($sql); // Mysqli Objekt
//        while ($row = mysqli_fetch_assoc($result)) {
//            $regeln[$row['regelnummer']] = $row;
//        }
//        return $regeln;
        return dbi::$db->query($sql)->fetch('regelnummer');
    }

    /**
     * Lade eine Regel aus der Datenbank
     */
    static function get_regel(string $nummer0): array
    {
        $regeln = self::get_regelwerk();
        preg_match('/([0-9.]+)([a-z]*)/', $nummer0, $matches);
        $nummer = $matches[1];
        $part = $matches[2];
        $fulltext = $regeln[$nummer]['regeltext'];
        if ($part == ''){
            $text = $fulltext;
        } else {
            $text = '...';
            foreach (str_split($part) as $onepart){
                // U=non-greedy
                // https://www.php.net/manual/de/reference.pcre.pattern.modifiers.php
                preg_match('|<p part="' . $onepart . '">(.*)</p>|U', $fulltext, $textpart);
                $text .= '<br>' . $textpart[1] . '<br>...';
            }
        }
        return array($nummer, $part, $regeln[$nummer]['regeltitel'], $text);
    }

    /**
     * Stimmen die gegebenen Antworten als Array mit den richtigen Antworten aus der DB überein?
     *
     * @param int $frage_id
     * @param array $user_antworten
     * @return bool
     */
    static function validate_frage(int $frage_id, array $user_antworten): bool
    {
        // Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        //        $result = db::readdb($sql);
        //        $richtig = mysqli_fetch_assoc($result)['richtig']; // String, # als Trennzeichen
        $richtig = dbi::$db->query($sql, $frage_id)->fetch_one();

        $richtig = preg_split('/[\s#\s]+/', $richtig); // Array mit den Nummern der richtigen Antwort

        // Vergleich der Arrays $richtig und $antworten
        sort($richtig); // Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        sort($user_antworten);
        if ($richtig == $user_antworten) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Was sind die richtigen Antworten?
     *
     * @param int $frage_id
     * @return array
     */
    static function get_richtig(int $frage_id): array
    {
        // Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        //        $result = db::readdb($sql);
        //        $richtig = mysqli_fetch_assoc($result)['richtig']; // String, # als Trennzeichen
        $richtig = dbi::$db->query($sql, $frage_id)->fetch_one(); // String, # als Trennzeichen

        $richtig = preg_split('/[\s#\s]+/', $richtig); // Array mit den Nummern der richtigen Antwort
        sort($richtig); // Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        return $richtig;
    }

    /**
     * Frage anzeigen
     */
    static function frage_anzeigen(int $frage_id, array $frage)
    { ?>
    <h3 class="w3-bottombar">Schiritest (Frage Nr. <?= $frage_id ?>)</h3>
    <h4><?= $frage['frage'] ?></h4>
    <?php if(!empty($frage['name_video'])){?>
        <!-- Video zur Frage -->
        <div style="max-width: 500px"> <!-- Damit das Video nicht zu groß wird -->
            <video class="w3-image w3-card" src="videos/<?=$frage['name_video']?>"
                   controls playsinline > Video zur Frage
            </video>
        </div>
    <?php } //endif?>
    <?php if(!empty($frage['name_bild'])){?>
        <!-- Bild zur Frage -->
        <div style="max-width: 500px"> <!-- Damit das Bild nicht zu groß wird -->
            <img alt="Bild zur Frage" class="w3-image w3-card"
                 src="bilder/<?=$frage['name_bild']?>">
        </div>
    <?php } //endif?>
    <?php }

} ?>
