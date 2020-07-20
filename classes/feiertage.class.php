<?php //Quelle: https://www.symcon.de/forum/threads/5983-Feiertage-berechnen
// Errechnet das Datum des Ostersonntags für ein gegebenes Jahr
class Feiertage {
    public static function easter($year)
    {
        if($year > 2038 || $year < 1970) {
            return false;
        } else {
            $a = $year % 19;
            $b = $year % 4;
            $c = $year % 7;
            $m = ((8 * ($year / 100) + 13) / 25) - 2;
            $s = ($year / 100) - ($year / 400) - 2;
            $M = (15 + $s - $m) % 30;
            $N = (6 + $s) % 7;
            $d = ($M + 19 * $a) % 30;
            if ($d == 29) {
                $D = 28;
            } elseif ($d == 28 && $a >= 11) {
                $D = 27;
            } else {
                $D = $d;
            }
            $e = (2 * $b + 4 * $c + 6 * $D + $N) % 7;
            $delta = $D + $e + 1;
            $easter = mktime(0, 0, 0, 3, 21, $year) + $delta * (24 * 3600);
            return $easter;
        }
    }

    // Berechnet alle festen und variablen Feiertage eines gegebenen Jahrs.
    // Die Feiertage werden als Array zurückgeliefert, wobei der Key dem
    // Feiertagsnamen entspricht und der Wert dem entsprechenden Zeitstempel.
    public static function finden($year)
    {
        $OneDay = 24 * 60 * 60;
        $easter = self::easter($year);
        if(!$easter) {
            return false;
        } else {
            $advday = date('w', mktime(0, 0, 0, 11, 26, $year));
            $advent = mktime(0, 0, 0, 11, 26, $year) + (($advday == 0 ? 0 : 7 - $advday) * $OneDay);
            $holidays['Neujahr']                   = mktime(0, 0, 0,  1,  1, $year);
            $holidays['Karfreitag']                = $easter - (2 * $OneDay);
            $holidays['Ostermontag']               = $easter + (1 * $OneDay);
            $holidays['Tag der Arbeit']            = mktime(0, 0, 0,  5,  1, $year);
            $holidays['Christi Himmelfahrt']       = $easter + (39 * $OneDay);
            $holidays['Pfingstmontag']             = $easter + (50 * $OneDay);
            $holidays['Tag der deutschen Einheit'] = mktime(0, 0, 0, 10,  3, $year);
            $holidays['1. Weihnachtsfeiertag']     = mktime(0, 0, 0, 12, 25, $year);
            $holidays['2. weihnachtsfeiertag']     = mktime(0, 0, 0, 12, 26, $year);
            array_walk($holidays, 'ResetHours');
            return $holidays;
        }
    }
}

// Beseitigt Stundenanteile in einem UNIX-Zeitstempel
function ResetHours(&$timestamp)
{
    $hour = date('G', $timestamp);
    $timestamp -= ($hour * 3600);
}
function ResetHour($timestamp) 
{
    $hour = date('G', $timestamp);
    $timestamp -= ($hour * 3600);
    return $timestamp;
}
