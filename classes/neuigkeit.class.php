<?php

class Neuigkeit {

    public static function create_neuigkeit ($titel,$text,$name, $link_jpg ='', $link_pdf = '', $bild_verlinken = '')
    {
        $sql="INSERT INTO neuigkeiten (titel, inhalt, eingetragen_von, link_jpg, link_pdf, bild_verlinken) VALUES ('$titel','$text','$name','$link_jpg','$link_pdf', '$bild_verlinken')";
        db::writedb($sql);
    }

    public static function delete_neuigkeit ($neuigkeiten_id)
    {   
        //Bilder und Dokumente der Neuigkeit löschen
        $neuigkeit = self::get_neuigkeiten($neuigkeiten_id);
        $neuigkeit = $neuigkeit[$neuigkeiten_id];
        if (file_exists($neuigkeit['link_jpg'])){unlink ($neuigkeit['link_jpg']);}
        if (file_exists($neuigkeit['link_pdf'])){unlink ($neuigkeit['link_pdf']);}

        //Neuigkeiteintrag aus der DB löschen
        $sql="DELETE FROM `neuigkeiten` WHERE neuigkeiten_id='$neuigkeiten_id'";
        db::writedb($sql);
    }

    public static function update_neuigkeit($neuigkeiten_id, $titel, $text, $link_jpg ='', $link_pdf = '', $bild_verlinken = '')
    {
        $sql="UPDATE neuigkeiten SET titel='$titel', inhalt='$text', link_jpg='$link_jpg', link_pdf='$link_pdf', bild_verlinken = '$bild_verlinken', zeit=zeit WHERE neuigkeiten_id = '$neuigkeiten_id'"; //zeit=zeit, damit der timestamp nicht erneuert wird
        db::writedb($sql);
    }

    public static function get_neuigkeiten ($neuigkeiten_id = 'neuigkeiten_id')
    {
        //Wenn kein Argument überliefert wird, werden alle Neuigkeiten herausgegeben
        $sql="SELECT * FROM neuigkeiten WHERE neuigkeiten_id = $neuigkeiten_id ORDER BY zeit DESC LIMIT 10"; //Es werden max. 10 Neuigkeiten angezeigt
        //db::debug($sql);
        $result = db::readdb($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            if ($x['eingetragen_von'] != 'Ligaausschuss'){
                $x = db::escape($x);
            }
            $return[$x['neuigkeiten_id']] = $x;
        }
        return $return; //Array //Escape in Funktion, nicht bei Ligaausschuss als Autor
    }

    //Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
    public static function upload_image($file, $target_dir = "../uploads/s/", $quality = 75, $pix = 1680)
    {
        //Validierung des hochgeladenen Bildes
        if (self::check_error_image($file)){
            return false;
        }
        //Gibt den Pfad mit neuem unix-time Namen zurück, wo die hochgeladene Datei gespeichert werden soll.
        $file_type = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        //Speicherpfad des Uploads mit neuem Dateinamen
        $file_dir = $target_dir . date("Y_m_d_H_i_s") . "." . $file_type;

        //Datei wird vom temporären Ordner in den richtigen Ordner verschoben
        if (move_uploaded_file($file["tmp_name"], $file_dir)) {
            //Bild wird kompressiert //Sehr hoher Memory Bedarf...
            self::compress_image($file_dir, $quality, $pix);
        }else{
            Form::error("Bild konnte nicht hochgeladen werden.");
            return false;
        }
        return $file_dir;
    }

    //Speichert die Datei im als standard im Upload-Ordner und benennt sie nach Unix-Timestamp um
    public static function upload_pdf($file, $target_dir = "../uploads/s/")
    {
        //Validierung des hochgeladenen Bildes
        if (self::check_error_pdf($file)){
            return false;
        }
        //Gibt den Pfad mit neuem unix-time Namen zurück, wo die hochgeladene Datei gespeichert werden soll.
        $file_type = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        //Speicherpfad des Uploads mit neuem Dateinamen
        $file_dir = $target_dir . date("Y_m_d_H_i_s") . "." . $file_type;

        //Datei wird vom temporären Ordner in den richtigen Ordner verschoben
        if (!move_uploaded_file($file["tmp_name"], $file_dir)) {
            Form::error("PDF konnte nicht hochgeladen werden.");
            return false;
        }
        return $file_dir;
    }

    //Verkleinert automatisch die Bilder für die Webseite
    public static function compress_image($source, $quality, $max_pix)
    {
        $info = getimagesize($source);

        if ($info['mime'] == 'image/jpeg'){ 
          $image = imagecreatefromjpeg($source);
        }
        elseif ($info['mime'] == 'image/gif'){ 
          $image = imagecreatefromgif($source);
        }
        elseif ($info['mime'] == 'image/png'){ 
          $image = imagecreatefrompng($source);
        }

        $width = $info[0];
        $height = $info[1];
        $ratio = $width / $height;
        //Bild skalieren //hohe Belastung für das PHP memory_limit
        if (max($width, $height) > $max_pix){
            if ($ratio <= 1){
                $image=imagescale($image, $max_pix * $ratio, $max_pix);
            }else{
                $image=imagescale($image, $max_pix, $max_pix / $ratio);
            }
        }

        //Fix Orientation, ansonsten werden Hochkantbilder nicht mehr als Hochkant angezeigt, da exif_daten gelöscht werden
        //Achtung: Error-Meldungen werden ignoriert
        $exif = @exif_read_data($source);
        if (!empty($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;
    
                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;
    
                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }
        }

        //Bild mit entsprechender Qualität speichern
        imagejpeg($image, $source, $quality);
    }

    //Test ob es sich um valides Bild handelt es gibt true zurück, wenn ein fehler vorliegt
    public static function check_error_image($file)
    {
        $imageFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));

        //Test auf Filegröße
        if ($file["size"] > 12582912) {
            Form::error("Das Bild ist mit über 11,9 Megabyte zu groß. Der Arbeitsspeicher des Servers reicht nicht aus, um es zu kompressieren");
            return true;
        }

        if (!file_exists($file['tmp_name'])){
            Form::error("Bild konnte nicht überprüft werden");
            return true;
        }

        //Test ob es ein fake image ist
        $check = getimagesize($file["tmp_name"]);
        if($check == false) {
            Form::error("Die Datei konnte nicht als Bild identifiziert werden.");
            return true;
        }

        //Test auf richtigen Dateityp
        if(!in_array($imageFileType, array("jpg", "jpeg", "png", "gif"))) {
            Form::error("Für das Bild können nur die Formate JPG, JPEG, PNG & GIF verwendet werden.");
            return true;
        }
        
        return false;
    }

    //Test ob es sich um valides PDF handelt
    public static function check_error_pdf($file)
    {
        if (!file_exists($file['tmp_name'])){
            Form::error("PDF konnte nicht überprüft werden");
            return true;
        }

        //Test auf Filegröße
        if ($file["size"] > 3100000) {
            Form::error("Das PDF-Dokument darf nicht größer als drei Megabyte sein.");
            return true;
        }

        $pdfFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        //Test auf richtigen Dateityp
        if($pdfFileType != "pdf" && $pdfFileType != "xlsx") {
            Form::error("Ungültiger Dateityp");
            return true;
        }
        
        return false;
    }

    public static function get_statistik($saison = Config::SAISON)
    {
        //Findet die drei Teams, welche die meisten Turniere bisher gespielt haben
        //Sortiert nach Zufall bei gleichstand
        $sql = "SELECT teams_liga.teamname, count(*) as gespielt 
            FROM turniere_liste 
            INNER JOIN turniere_liga 
            ON turniere_liste.turnier_id = turniere_liga.turnier_id 
            INNER JOIN teams_liga 
            ON teams_liga.team_id = turniere_liste.team_id 
            WHERE teams_liga.aktiv = 'JA' 
            AND turniere_liga.saison = '$saison' 
            AND turniere_liste.liste = 'spiele' 
            AND turniere_liga.phase = 'ergebnis' 
            GROUP BY teams_liga.teamname 
            ORDER BY gespielt desc, rand()
            LIMIT 3";
        $result = db::readdb($sql);
        $return['max_turniere'] = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return['max_turniere'], $x);
        }
        return db::escape($return); //Array 
    }
    public static function get_statistik_tore($saison = Config::SAISON){
        //Tore Team A
        $sqla = "SELECT sum(tore_a) AS tore, team_id_a
        FROM spiele
        INNER JOIN turniere_liga 
        ON spiele.turnier_id = turniere_liga.turnier_id 
        WHERE tore_a IS NOT NULL
        AND turniere_liga.saison = '$saison'
        GROUP BY team_id_a
        ORDER BY RAND()";
        $result = db::readdb($sqla);
        while ($x = mysqli_fetch_assoc($result)){
            $tore[$x['team_id_a']] = $x['tore'];
        }
        //Addition der Tore Team B
        $sqlb = "SELECT sum(tore_b) AS tore, team_id_b
        FROM spiele
        INNER JOIN turniere_liga 
        ON spiele.turnier_id = turniere_liga.turnier_id 
        WHERE tore_b IS NOT NULL
        AND turniere_liga.saison = '$saison'
        GROUP BY team_id_b
        ORDER BY RAND()";
        $result = db::readdb($sqlb);
        while ($x = mysqli_fetch_assoc($result)){
            if (isset($tore[$x['team_id_b']])){
                $tore[$x['team_id_b']] += $x['tore'];
            }else{
                $tore[$x['team_id_b']] = $x['tore'];
            }
        }
        arsort($tore);
        return array_slice($tore, 0, 3, true);
    }
}