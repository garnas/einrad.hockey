<?php # -*- php -*-

class SchiriTest
{

    #-------------------------------------------------------------------------

    # Get eine bestimmte Anzahl an zufälligen Fragen einer Kategorie für den Schiritest
    # aus der Datenbank.
    # @param $LJBF      # Lehrgang, Junior, Basis oder Fortgeschrittene?
    # @param $kategorie # Kategorie aus welcher die Fragen geholt werden sollen
    # @param $anzahl    # Anzahl der Fragen die geholt werden sollen
    # @param $fragenr   # Nummer einer bestimmten Frage, die geholt werden soll
    # @return array     # Fragen die zurückgegeben werden
    public static function get_fragen(string $LJBF, string $kategorie,
                                      int $anzahl, int $fragenr = 0): array
    {
        if ($kategorie === '*') {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE INSTR(LJBF, ?) > 0
                ORDER BY RAND()
                LIMIT ?
                ";
            $result = db::$db->query($sql, $LJBF, $anzahl)->fetch();
        } else {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE INSTR(LJBF, ?) > 0 AND kategorie = ?
                ORDER BY RAND()
                LIMIT ?
                ";
            $result = db::$db->query($sql, $LJBF, $kategorie, $anzahl)->fetch();
        }
        if ($fragenr > 0) {
            $sql = "
                SELECT *
                FROM schiri_test
                WHERE frage_id = ?
                LIMIT ?
                ";
            $result = db::$db->query($sql, $fragenr, $anzahl)->fetch();
        }
        foreach ($result as $row) {
            # String in ein Array parsen:
            $row['richtig'] = preg_split('/[\s#\s]+/', $row['richtig']);
            for ($index = 1; $index <= 6; $index++) { # Index für die Antwortmöglichkeiten
                if (!empty($row['antwort_' . $index])) {
                    # zum Array "row" hinzufügen:
                    $row['antworten'][$index] = $row['antwort_' . $index];
                    unset($row['antwort_' . $index]); # wird nicht mehr gebraucht
                }
            }
            $fragen[$row['frage_id']] = $row;
        }

        return $fragen ?? []; # Rückgabe muss Array sein (leer falls $fragen undefiniert)
    }

    #-------------------------------------------------------------------------

    # Lade Regelwerk aus der Datenbank
    public static function frage_anzeigen(int $index, array $frage): void
    {
        echo '<h3 class="w3-topbar">Frage Nr. ' . $index . '</h3>';
        echo '<h4>' . $frage['frage'] . '</h4>';
        if (!empty($frage['name_video'])) { # Video zur Frage:
            echo '<div style="max-width: 500px">';
            echo '<video class="w3-image w3-card" src="videos/' . $frage['name_video'] . '"';
            echo '       controls playsinline> Video zur Frage </video></div>';
        }
        if (!empty($frage['name_bild'])) { # Bild zur Frage:
            echo '<div style="max-width: 500px">';
            echo '<img alt="Bild zur Frage" class="w3-image w3-card"';
            echo '     src="bilder/' . $frage['name_bild'] . '"></div>';
        }
    }

    #-------------------------------------------------------------------------

    # Lade eine Regel aus der Datenbank
    public static function antworten_anzeigen(int $frage_id, array $frage): void
    {
        foreach ($frage['antworten'] as $index => $antwort) { ?>
            <p>
                <input name="abgabe[<?= $frage_id ?>][<?= $index ?>]"
                       value="<?= $index ?>"
                       id="<?= $frage_id . '*' . $index ?>"
                       type="checkbox"
                       class="w3-check"
                       style="cursor: pointer;">
                <label for="<?= $frage_id . '*' . $index ?>"
                       class="w3-hover-text-primary" style="cursor: pointer;">
                    <?= $antwort ?>
                </label>
            </p>
        <?php } # end foreach antworten
    }

    #-------------------------------------------------------------------------

    # Stimmen die gegebenen Antworten als Array mit den richtigen
    # Antworten aus der DB überein?
    #
    # @param int $frage_id
    # @param array $user_antworten
    # @return bool
    public static function auswertung_anzeigen(int $frage_id, array $frage): void
    {
        $richtig = self::get_richtig($frage_id);
        foreach ($frage['antworten'] as $index => $antwort) {
            $antwort_user = isset($_POST['abgabe'][$frage_id][$index]);
            if ($antwort_user) { # diese Antwort angeklickt?
                echo '<p><i class="material-icons">check_circle_outline</i>';
            } else {
                echo '<p><i class="material-icons">radio_button_unchecked</i>';
            }
            $antwort_richtig = in_array($index, $richtig);
            if ($antwort_user xor $antwort_richtig) { # richtig beantwortet?
                echo '<span class="w3-text-red">';
                echo '<i class="material-icons">thumb_down</i></span>';
            } else {
                echo '<span class="w3-text-green">';
                echo '<i class="material-icons">thumb_up</i></span>';
            }
            if ($antwort_richtig) { # ist diese Antwort richtig?
                echo '<b>' . $antwort . '</b></p>';
            } else {
                echo '<span class="w3-text-grey"><s><i>' . $antwort .
                    '</i></s></span></p>';
            }
        }
        $antworten_user = $_POST['abgabe'][$frage_id] ?? []; # leer, wenn keine Antwort
        if (self::validate_frage($frage_id, $antworten_user)) {
            echo '<h3 class="w3-border-bottom w3-text-green">' .
                Html::icon("thumb_up", class:"md-36") .
                'Alles korrekt beantwortet!</h3>';
        } else {
            echo '<h3 class="w3-border-bottom w3-text-red">' .
                Html::icon("thumb_down", class: "md-36") .
                'Da war etwas falsch!</h3>';
        }
        echo '<p><b>Erklärung: </b>' . $frage['erklaerung'] . '</p>';
        if (!empty($frage['erklaerung_video'])) { # Video zur Frage:
            echo '<div style="max-width: 500px"><video class="w3-image w3-card"' .
                ' src="videos/' . $frage['erklaerung_video'] . '" controls playsinline>' .
                '</video></div>';
        }
        if (!empty($frage['erklaerung_bild'])) { # Bild zur Frage:
            echo '<div style="max-width: 500px">' .
                '<img alt="Bild zur Frage" class="w3-image w3-card"' .
                ' src="bilder/' . $frage['erklaerung_bild'] . '"></div>';
        }

        $regelnr = $frage['regelnr'];
        if (empty($regelnr)) {
            Html::message('notice', '(Keine Regelnummer für diese Frage)');
        } else {
            foreach (preg_split('/[\s#\s]+/', $regelnr) as $regelnr1) {
                [$nr, $part, $titel, $text] = self::get_regel($regelnr1);
                if (empty($nr)) {
                    Html::message('error',
                        'Regel |' . $regelnr1 . '| nicht in der Datenbank.');
                } else {
                    Html::message('info',
                        $text, 'Offizielle Regel ' . $nr . ": " . $titel, esc: false);
                }
            }
        }
    }

    #-------------------------------------------------------------------------

    # Was sind die richtigen Antworten?
    #
    # @param int $frage_id
    # @return array
    public static function get_richtig(int $frage_id): array
    {
        # Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        $richtig = db::$db->query($sql, $frage_id)->fetch_one(); # String, # als Trennzeichen
        # Array mit den Nummern der richtigen Antwort:
        $richtig = preg_split('/[\s#\s]+/', $richtig);
        sort($richtig); # Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        return $richtig;
    }

    #-------------------------------------------------------------------------

    # Frage anzeigen:
    public static function validate_frage(int $frage_id, array $user_antworten): bool
    {
        # Antworten aus der Datenbank lesen
        $sql = "
            SELECT richtig
            FROM schiri_test
            WHERE frage_id = ?
        ";
        # $result = db::readdb($sql);
        # $richtig = mysqli_fetch_assoc($result)['richtig']; # String, # als Trennzeichen
        $richtig = db::$db->query($sql, $frage_id)->fetch_one();
        # Array mit den Nummern der richtigen Antwort:
        $richtig = preg_split('/[\s#\s]+/', $richtig);
        # Vergleich der Arrays $richtig und $antworten
        sort($richtig); # Sortieren, damit beide Arrays die gleiche Reihenfolge haben
        sort($user_antworten);
        return $richtig == $user_antworten;
    }

    #-------------------------------------------------------------------------

    # Antwortmöglichkeiten anzeigen:

    public static function get_regel(string $nummer0): array
    {
        $regeln = self::get_regelwerk();
        preg_match('/([0-9.]+)([a-z]*)/', $nummer0, $matches);
        $nummer = $matches[1];
        $part = $matches[2];
        $fulltext = $regeln[$nummer]['regeltext'];
        if (empty($part)) {
            $text = $fulltext;
        } else {
            $text = '...';
            foreach (str_split($part) as $onepart) {
                # U=non-greedy
                # https://www.php.net/manual/de/reference.pcre.pattern.modifiers.php
                preg_match('|<p part="' . $onepart . '">(.*)</p>|U', $fulltext, $textpart);
                $text .= '<br>' . $textpart[1] . '<br>...';
            }
        }
        return [$nummer, $part, $regeln[$nummer]['regeltitel'], $text];
    }

    #-------------------------------------------------------------------------

    # Auswertung anzeigen

    public static function get_regelwerk(): array
    {
        $sql = "
            SELECT *
            FROM regelwerk
        ";
        return db::$db->query($sql)->fetch('regelnummer');
    }

    #-------------------------------------------------------------------------

}
