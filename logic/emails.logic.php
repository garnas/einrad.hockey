<?php
$turniere = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."'");

$akt_spieltag = Tabelle::get_aktuellen_spieltag();
$teams = Tabelle::get_rang_tabelle($akt_spieltag); //Sortierung nach Rangtabelle

//Formularauswertung

//Emailauswahl
if (isset($_POST['turnier_id']) && is_numeric($_POST['turnier_id'])){
    $array = Kontakt::get_emails_turnier($_POST['turnier_id']);
    $emails = $array['emails'];
    $teamnamen = $array['teamnamen'];
    Form::attention("Achtung: Nichtligateams müssen seperat angeschrieben werden!");
}

if (isset($_POST['rundmail'])){
    $emails = Kontakt::get_emails_rundmail();
    $teamnamen = Team::list_of_all_teams();
    $rundmail = true;
}

if (isset($_POST['teams_emails'])){
    $emails = array();
    $teamnamen = array();
    foreach (($_POST['team'] ?? array()) as $team_id){
        $akt_team = new Kontakt($team_id);
        $team_emails = $akt_team->get_emails();
        foreach ($team_emails as $email){
            if (!in_array($email,$emails)){
                array_push($emails,$email);
            }
        }
        array_push($teamnamen, Team::teamid_to_teamname($team_id));
    }
}