<?php
class Ligaleitung {
    //Ligaausschuss
    public static function get_all_la(): array
    {
        $sql="SELECT r_name, team_id, email FROM ausschuss_liga ORDER BY RAND()";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return);
    }
    //Technikausschuss
    public static function get_all_tk(): array
    {
        $sql="SELECT r_name, team_id FROM ausschuss_technik";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return);
    }
    //Schiriausschuss
    public static function get_all_sa(): array
    {
        $sql="SELECT r_name, team_id FROM ausschuss_schiri";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return);
    }
    //Öffentlichkeitsausschuss
    public static function get_all_oa(): array
    {
        $sql="SELECT r_name, team_id FROM ausschuss_oeffi";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return,$x);
        }
        return db::escape($return);
    }
    //Liste der Schiriausbilder
    public static function get_all_ausbilder(): array
    {
        $sql="SELECT vorname, nachname, team_id FROM spieler WHERE schiri = 'Ausbilder/in'";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)){
            array_push($return, $x);
        }
        return db::escape($return);
    }
    public static function get_la_id($name): int
    {
        $sql = "SELECT ligaausschuss_id  FROM ausschuss_liga WHERE login_name = '$name'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return $result['ligaausschuss_id'] ?? 0;
    }

    public static function get_la_password ($la_id): string
    {
        $sql = "SELECT passwort FROM ausschuss_liga WHERE ligaausschuss_id = '$la_id'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return $result['passwort'] ?? '';
    }

    public static function get_la_name ($la_id): string
    {
        $sql = "SELECT r_name  FROM ausschuss_liga WHERE ligaausschuss_id = '$la_id'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result['r_name']);
    }

    public static function set_la_password ($la_id, $passwort)
    {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        $sql = "UPDATE ausschuss_liga SET passwort = '$passwort_hash' WHERE  ligaausschuss_id = '$la_id'";
        db::write($sql);
    }

    /*
    public static function create_new_la ($login_name, $name, $passwort, $email, $team_id)
    {
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);
        $sql = "INSERT INTO ausschuss_liga(login_name, r_name, passwort, email, team_id) VALUES ('$login_name','$name','$passwort', '$email', '$team_id')";
        db::write($sql);
    }
    */
}

