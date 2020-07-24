<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth
//$myfile = fopen("wegbeschreibungen/weg004.dat", "r") or die("Unable to open file!");
//$string = readfile("wegbeschreibungen/weg004.dat");
//$string = fread($myfile,filesize("wegbeschreibungen/weg004.dat"));
//db::debug($string);
//db::debug(explode('.ADRESSE=_',$string,));

function getLineWithString($fileName, $str) {
    $lines = file($fileName);
    $i=0;
    foreach ($lines as $lineNumber => $line) {
        $i++;
        if (strpos($line, $str) !== false) {
            return $i;
        }
    }
    return -1;
}
//$string = file("wegbeschreibungen/weg004.dat")[3];
//db::debug($string);
//db::debug($string);

//Neue DB-Verbindung
unset($verbindung_zur_datenbank);
$verbindung = new db("db_test_alt");

$sql="SELECT turniere.turnierID, turniere_details.wegbeschreibung 
    FROM `turniere_details` 
    INNER JOIN turniere 
    ON turniere.turnierID = turniere_details.turnierID 
    WHERE turniere.datum > '2020-05-31'
    AND geloescht = 'N'";

$result = db::readdb($sql);
while ($x =  mysqli_fetch_assoc($result)){
    
    $array = array('hallenname' => ".ORT=_", 'strasse' => ".ADRESSE=_", 'plz' => ".PLZ=_",'ort' => ".STADT=_");
    foreach ($array as $key => $search){
        $file = "wegbeschreibungen/" . $x['wegbeschreibung'];
        $line = getLineWithString($file, $search);
        $string = file($file)[$line];
        $string = mb_convert_encoding($string, "UTF-8", "ISO-8859-1");
        //db::debug($search .': ' . $string);
        $adressen[$x['turnierID']][$key] = $string;
    }
}
db::debug($adressen);

//Verbindung trennen und Verbindung zur neuen db aufbauen
unset($verbindung);
$verbindung = new db("db_test_neu");
foreach ($adressen as $turnier_id => $adresse){
    $sql = 'UPDATE turniere_details 
                SET hallenname = \''.trim($adresse['hallenname']).'\', 
                    strasse = \''.trim($adresse['strasse']).'\', 
                    plz = \''.trim($adresse['plz']).'\',
                    ort = \''.trim($adresse['ort']).'\'
                WHERE turnier_id = \''.$turnier_id.'\'';
    //db::writedb($sql);
    db::debug($sql);
}
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

include '../../templates/header.tmp.php';
?>

<?php include '../../templates/footer.tmp.php';