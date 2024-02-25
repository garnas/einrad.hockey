<?php

use App\Service\Team\TeamValidator;

/**
 * Class Neuigkeit
 */
class Neuigkeit
{

    /**
     * Erstellt einen Neuigkeiten-Eintrag in der Datenbank
     *
     * @param string $titel
     * @param string $text
     * @param string $name
     * @param string $link_jpg
     * @param string $link_pdf
     * @param string $bild_verlinken
     */
    public static function create(string $titel, string $text, string $name, string $link_jpg = '', string $link_pdf = '', string $bild_verlinken = '')
    {
        $sql = "
                INSERT INTO neuigkeiten (titel, inhalt, eingetragen_von, link_jpg, link_pdf, bild_verlinken) 
                VALUES (?, ?, ?, ?, ?, ?)
                ";
        $params = [$titel, $text, $name, $link_jpg, $link_pdf, $bild_verlinken];
        db::$db->query($sql, $params)->log();
    }

    /**
     * Löscht einen Neuigkeiten-Eintrag aus der Datenbank
     *
     * @param $neuigkeiten_id
     */
    public static function delete($neuigkeiten_id): void
    {
        // Bilder und Dokumente der Neuigkeit löschen
        $neuigkeit = self::get_neuigkeiten($neuigkeiten_id);
        $neuigkeit = $neuigkeit[$neuigkeiten_id];

        if (file_exists($neuigkeit['link_jpg'])) unlink($neuigkeit['link_jpg']);
        if (file_exists($neuigkeit['link_pdf'])) unlink($neuigkeit['link_pdf']);

        // Neuigkeiteintrag aus der DB löschen
        $sql = "
                DELETE FROM neuigkeiten
                WHERE neuigkeiten_id = ?
                ";
        db::$db->query($sql, $neuigkeiten_id)->log();

    }

    /**
     * Neuigkeit wird in der DB mit neuen Werten überschrieben
     *
     * @param int $neuigkeiten_id
     * @param string $titel
     * @param string $inhalt
     * @param string $link_jpg
     * @param string $link_pdf
     * @param string $bild_verlinken
     */
    public static function update_neuigkeit(int $neuigkeiten_id, string $titel, string $inhalt, string $link_jpg,
                                            string $link_pdf, string $bild_verlinken = '')
    {
        $sql = "
                UPDATE neuigkeiten 
                SET titel = ?, inhalt = ?, link_jpg = ?, link_pdf = ?, bild_verlinken = ?, zeit = zeit 
                WHERE neuigkeiten_id = '$neuigkeiten_id'
                "; // zeit=zeit, damit der timestamp nicht erneuert wird
        $params = [$titel, $inhalt, $link_jpg, $link_pdf, $bild_verlinken];
        db::$db->query($sql, $params)->log();
    }

    /**
     * Neuigkeiten aus der DB
     *
     * Wenn keine Neuigkeit ausgewählt wird, werden die letzten 10 ausgegeben
     * @param int $neuigkeiten_id
     * @return array
     */
    public static function get_neuigkeiten(int $neuigkeiten_id = 0): array
    {
        if (empty($neuigkeiten_id)) { // Alle Neuigkeiten
            $sql = "
                SELECT * 
                FROM neuigkeiten 
                ORDER BY zeit DESC 
                LIMIT 10
                "; // Es werden max. 10 Neuigkeiten angezeigt
            $neuigkeiten = db::$db->query($sql)->esc()->fetch('neuigkeiten_id');
        } else { // Eine Neuigkeit
            $sql = "
                SELECT * 
                FROM neuigkeiten 
                WHERE neuigkeiten_id = ? 
                ";
            $neuigkeiten = db::$db->query($sql, $neuigkeiten_id)->esc()->fetch('neuigkeiten_id');
        }
        foreach ($neuigkeiten as $key => $neuigkeit) {
            if (
                $neuigkeit['eingetragen_von'] === 'Ligaausschuss'
                || $neuigkeit['eingetragen_von'] === "Öffiausschuss"
            ) {
                $neuigkeiten[$key]['inhalt'] = htmlspecialchars_decode($neuigkeit['inhalt'], ENT_QUOTES);
                $neuigkeiten[$key]['titel'] = htmlspecialchars_decode($neuigkeit['titel'], ENT_QUOTES);
            }
        }
        return $neuigkeiten;
    }

    /**
     * Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
     *
     * @param array $file
     * @param string $target_dir
     * @param int $quality
     * @param int $pix
     * @return false|string
     */
    public static function upload_bild(array $file, string $target_dir = "../uploads/s/", int $quality = 75, int $pix = 1680): false|string
    {
        // Validierung des hochgeladenen Bildes
        if (self::check_error_image($file)) {
            return false;
        }

        // Gibt den Pfad mit neuem unix-time Namen zurück, wo die hochgeladene Datei gespeichert werden soll.
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Speicherpfad des Uploads mit neuem Dateinamen
        $file_dir = $target_dir . date("Y_m_d_H_i_s") . "." . $file_type;

        // Datei wird vom temporären Ordner in den richtigen Ordner verschoben
        if (move_uploaded_file($file["tmp_name"], $file_dir)) {
            // Bild wird kompressiert // Sehr hoher Memory-Bedarf
            self::compress_image($file_dir, $quality, $pix);
        } else {
            Html::error("Bild konnte nicht hochgeladen werden.");
            return false;
        }
        return $file_dir;
    }

    /**
     * Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
     *
     * @param array $file
     * @param string $target_dir
     * @return false|string
     */
    public static function upload_dokument(array $file, string $target_dir = "../uploads/s/"): false|string
    {
        // Validierung des hochgeladenen Bildes
        if (!self::check_pdf($file)) {
            return false;
        }

        // Gibt den Pfad mit neuem unix-time Namen zurück, wo die hochgeladene Datei gespeichert werden soll.
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Speicherpfad des Uploads mit neuem Dateinamen
        $file_dir = $target_dir . date("Y_m_d_H_i_s") . "." . $file_type;

        // Datei wird vom temporären Ordner in den richtigen Ordner verschoben
        if (!move_uploaded_file($file["tmp_name"], $file_dir)) {
            Html::error("PDF konnte nicht hochgeladen werden.");
            return false;
        }
        return $file_dir;
    }

    /**
     * Komprimiert automatisch die Bilder für die Webseite
     *
     * @param string $source Pfad zum Bild
     * @param int $quality
     * @param int $max_pix
     */
    public static function compress_image(string $source, int $quality, int $max_pix): void
    {
        if (!(extension_loaded('gd') && function_exists('gd_info'))) {
            Html::error("Bild konnte nicht kompressiert werden - keine GD-Extension.");
            return;
        }
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        } else {
            Html::error("Bildformat konnte nicht ermittelt werden.");
            return;
        }

        [$width, $height] = $info;
        $ratio = $width / $height;
        // Bild skalieren // hohe Belastung für das PHP memory_limit
        if (max($width, $height) > $max_pix) {
            if ($ratio <= 1) {
                $image = imagescale($image, $max_pix * $ratio, $max_pix);
            } else {
                $image = imagescale($image, $max_pix, $max_pix / $ratio);
            }
        }

        // Fix Orientation, ansonsten werden Hochkantbilder nicht mehr als Hochkant angezeigt, da exif_daten gelöscht werden
        // Achtung: Error-Meldungen werden ignoriert
        $exif = @exif_read_data($source);
        if (!empty($exif['Orientation'])) {
            $image = match ($exif['Orientation']) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        // Bild mit entsprechender Qualität speichern
        imagejpeg($image, $source, $quality);
    }

    /**
     * Test ob es sich um valides Bild handelt es gibt true zurück, wenn ein fehler vorliegt
     *
     * @param array $file
     * @return bool
     */
    public static function check_error_image(array $file): bool
    {
        $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Test auf Filegröße
        if ($file["size"] > 12582912) {
            Html::error("Das Bild ist mit über 11,9 Megabyte zu groß. Der Arbeitsspeicher des Servers reicht nicht aus, um es zu kompressieren");
            return true;
        }

        if (!file_exists($file['tmp_name'])) {
            Html::error("Bild konnte nicht überprüft werden");
            return true;
        }

        // Test ob es ein fake image ist
        $check = getimagesize($file["tmp_name"]);
        if ($check == false) {
            Html::error("Die Datei konnte nicht als Bild identifiziert werden.");
            return true;
        }

        // Test auf richtigen Dateityp
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            Html::error("Für das Bild können nur die Formate JPG, JPEG, PNG & GIF verwendet werden.");
            return true;
        }

        return false;
    }

    /**
     * Test ob es sich um valides PDF handelt
     *
     * @param $file
     * @return bool
     */
    public static function check_pdf($file): bool
    {
        if (!file_exists($file['tmp_name'])) {
            Html::error("PDF konnte nicht überprüft werden");
            return false;
        }

        // Test auf Filegröße
        if ($file["size"] > 3100000) {
            Html::error("Das PDF-Dokument darf nicht größer als drei Megabyte sein.");
            return false;
        }

        $pdfFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Test auf richtigen Dateityp
        if ($pdfFileType != "pdf" && $pdfFileType != "xlsx") {
            Html::error("Ungültiger Dateityp");
            return false;
        }

        return true;
    }

    /**
     * Max Tore Statistik für das Inforboard
     *
     * @param int $saison
     * @return array
     */
    public static function get_statistik_tore(int $saison = Config::SAISON): array
    {
        // Tore Team A
        $sqla = "
                SELECT sum(tore_a) AS tore, team_id_a
                FROM spiele
                INNER JOIN turniere_liga
                ON spiele.turnier_id = turniere_liga.turnier_id
                WHERE tore_a IS NOT NULL
                AND turniere_liga.saison = ?
                GROUP BY team_id_a
                ORDER BY RAND()
                ";
        foreach(db::$db->query($sqla, $saison)->esc()->fetch() as $x) {
            $tore[$x['team_id_a']] = $x['tore'];
        }
        // Addition der Tore Team B
        $sqlb = "
                SELECT sum(tore_b) AS tore, team_id_b
                FROM spiele
                INNER JOIN turniere_liga
                ON spiele.turnier_id = turniere_liga.turnier_id
                WHERE tore_b IS NOT NULL
                AND turniere_liga.saison = ?
                GROUP BY team_id_b
                ORDER BY RAND()
                ";
        foreach(db::$db->query($sqlb, $saison)->esc()->fetch() as $x) {
            if (isset($tore[$x['team_id_b']])) {
                $tore[$x['team_id_b']] += $x['tore'];
            } else {
                $tore[$x['team_id_b']] = $x['tore'];
            }
        }
        arsort($tore);
        return array_slice($tore, 0, 3, true) ?? [];
    }

    public static function darf_verlinken(): bool
    {
        return Helper::$ligacenter || Helper::$oeffentlichkeitsausschuss;
    }

    public static function darf_bearbeiten(string $eingetragen_von): bool
    {
        // LA?
        if (Helper::$ligacenter) {
            return true;
        }

        // Vom Team eingetragen?
        if (
            isset($_SESSION['logins']['team']['name'])
            && $_SESSION['logins']['team']['name'] == $eingetragen_von
        ) {
            return true;
        }

        // Team Teil des Öffis?
        if (
            $eingetragen_von === "Öffentlichkeitsausschuss"
            && Helper::$oeffentlichkeitsausschuss
        ) {
            return true;
        }

        return false;

    }

}