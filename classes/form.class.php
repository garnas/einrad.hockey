<?php
class Form
{
    //Fehlermeldungem werden in einer $_SESSION Variable gespeichert
    public static function error($string)
    {
        //Falls $_SESSION noch nicht gesetzt wurde, wird sie als array deklariert
        if (!isset($_SESSION['e_messages'])) {
            $_SESSION['e_messages'] = array();
        }
        //argument wird dem array $_SESSION['e_messages'] hinzugefügt
        array_push($_SESSION['e_messages'], $string);
    }

    //Analog zur error funktion
    public static function affirm($string)
    {
        if (!isset($_SESSION['a_messages'])) {
            $_SESSION['a_messages'] = array();
        }
        array_push($_SESSION['a_messages'], $string);
    }

    //Analog zur error funktion
    public static function attention($string)
    {
        if (!isset($_SESSION['w_messages'])) {
            $_SESSION['w_messages'] = array();
        }
        array_push($_SESSION['w_messages'], $string);
    }

    //Hinweise werden ins Html-Dokument geschrieben
    public static function schreibe_attention($message, $caption = 'Hinweis')
    { ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-yellow w3-pale-yellow'>
            <h3><?=$caption?></h3>
            <p><?=$message?></p>
        </div>
    <?php }

    //Errors werden ins Html-Dokument geschrieben
    public static function schreibe_error($message, $caption = 'Fehler')
    { ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-red w3-pale-red'>
            <h3><?=$caption?></h3>
            <p><?=$message?></p>
        </div>
    <?php }

    //Infos werden ins Html-Dokument geschrieben
    public static function schreibe_affirm($message, $caption = 'Info')
    { ?>
        <div class='w3-card w3-panel w3-leftbar w3-border-green w3-pale-green'>
            <h3><?=$caption?></h3>
            <p><?=$message?></p>
        </div>
    <?php }

    //Meldungen aus $_SESSION werden ins Html-Dokument geschrieben
    public static function schreibe_meldungen()
    {   
        //Hinweise
        if (isset($_SESSION['w_messages'])) {
            foreach ($_SESSION['w_messages'] as $message) {
                Self::schreibe_attention($message);
            }
            unset($_SESSION['w_messages']);
        }
        //Fehler
        if (isset($_SESSION['e_messages'])) {
            foreach ($_SESSION['e_messages'] as $message) {
                Self::schreibe_error($message);
            }
            unset($_SESSION['e_messages']);
        }
        //Info
        if (isset($_SESSION['a_messages'])) {
            foreach ($_SESSION['a_messages'] as $message) {
                Self::schreibe_affirm($message);
            }
            unset($_SESSION['a_messages']);
        }
    }

    //Erstellt eine HTML-Datalist aller Ligateams.
    public static function link($link, $bezeichnung = '', $extern = false)
    {
        if (empty($bezeichnung)) {
            $bezeichnung = $link;
        }
        if ($extern) {
            $new_tab = 'target="_blank" rel="noopener noreferrer"';
        } else {
            $new_tab = '';
        }
        return "<a href='$link' class='no w3-text-primary w3-hover-text-secondary' style='white-space: nowrap;' $new_tab>$bezeichnung</a>";
    }

    //Erstellt eine HTML-Datalist aller Ligateams.
    public static function datalist_teams()
    {
        $return = "<datalist id='teams'>";
        $liste = Team::list_of_all_teams();
        foreach ($liste as $teamname) {
            $return .= "<option value='$teamname'>";
        }
        $return .= "</datalist>";
        return $return;
    }

    //Erststellt anklickbare Email-Adressen
    public static function mailto($email, $name = '')
    {
        if (is_array($email)) {
            $email = implode(',', $email);
        }
        if (empty($name)) {
            $name = $email;
        }
        return "<a href='mailto:$email' class='no w3-text-blue w3-hover-text-secondary' style='white-space: nowrap;'><i class='material-icons'>mail</i> $name</a>";
    }

    //Funktion für Saisonumstellung auf eine Saison über zwei Jahre
    //0 = Saison 1995
    public static function get_saison_string($saison = Config::SAISON)
    {
        //Sollte zum Beispiel ein String übergeben werden, dann wird genau dieser String auch wieder rausgeworfen.
        if (!is_numeric($saison)) {
            return $saison;
        }
        if ($saison == 25) {
            return "2020 (Corona-Saison)";
        }
        if ($saison > 25) {
            $saison_jahr = 1994 + $saison;
            $saison_jahr_next = $saison_jahr + 1;
            return substr($saison_jahr, -2) . "/" . substr($saison_jahr_next, -2);
        }
        return 1995 + $saison;
    }
    
    public static function countdown($date, $id = 'countdown'){
        ?>
            <script>countdown('<?=date("Y-m-d\TH:i:s", strtotime($date))?>', '<?=$id?>')</script>
            <div id='countdown' class="w3-xlarge w3-text-primary" style='white-space: nowrap;'>
                <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_days'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Tage</span>
                </span>
                <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_hours'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Stunden</span>
                </span>
                <span class="w3-center w3-margin-right" style="display: inline-block">
                    <span id='countdown_minutes'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Minuten</span>
                </span>
                <span class="w3-center" style="display: inline-block">
                    <span id='countdown_seconds'>--</span>
                    <span class="w3-small w3-text-grey" style="display: block">Sekunden</span>
                </span>
            </div>
        <?php
    }

    public static function progressBar($stand, $ende) {
        ?>  
            <div id='bar'>
                <div id='progress'>
                </div>
            </div>
            <script>progressBar(<?=$stand?>, <?=$ende?>)</script>
        <?php
    }

}