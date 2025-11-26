<?php

namespace App\Service\Neuigkeit;

class FileService    
{

    /**
     * Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
     *
     * @param array $file
     * @param string $target_dir
     * @return false|string
     */
    public static function uploadPDF(array $file, string $target_dir = "../uploads/s/"): false|string
    {
        // Validierung des hochgeladenen Bildes
        if (!self::checkPDF($file)) {
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
     * Test ob es sich um valides PDF handelt
     *
     * @param $file
     * @return bool
     */
    public static function checkPDF($file): bool
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
     * Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
     *
     * @param array $file
     * @param string $target_dir
     * @param int $quality
     * @param int $pix
     * @return false|string
     */
    public static function uploadImage(array $file, string $target_dir = "../uploads/s/", int $quality = 75, int $pix = 1680): false|string
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
}