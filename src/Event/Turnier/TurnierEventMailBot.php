<?php

namespace App\Event\Turnier;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;
use App\Service\Team\TeamValidator;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;
use Env;
use Kontakt;
use MailBot;

class TurnierEventMailBot
{
    /**
     * Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn ein neues Turnier eingetragen wird
     *
     * @param Turnier $turnier
     */
    public static function mailNeuesTurnier(Turnier $turnier): void
    {
        if ($turnier->isLigaturnier()) {

            $teams = TeamRepository::get()->activeLigaTeams();

            foreach ($teams as $team) {
                if (
                    TurnierService::isSetzBerechtigt($turnier, $team)
                    && !TeamValidator::isAmKalenderTagAufSetzliste($turnier, $team)
                    && $turnier->getAusrichter()->id() != $team->id()
                ) {
                    $betreff = "Neues " . $turnier->getBlock() . "-Turnier in " . $turnier->getDetails()->getOrt();
                    ob_start();
                        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_neues_turnier.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                    $inhalt = ob_get_clean();

                    $emails = (new Kontakt ($team->id()))->get_emails('info');
                    MailBot::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }

    public static function mailCanceled(Turnier $turnier)
    {
        $emails = TurnierService::getEmails($turnier);
        $betreff = "Abgesagt: " . $turnier->getBlock() . "-Turnier in " . $turnier->getDetails()->getOrt();
        foreach ($emails as $email) {
            $emailAdressen[] = $email->getEmail();
        }
        $emailAdressen[] = Env::LAMAIL;
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_turnier_canceled.tmp.php");
        $inhalt = ob_get_clean();

        MailBot::add_mail($betreff, $inhalt, $emailAdressen);
        MailBot::mail_bot($betreff);
    }

    /**
     * Erstellt eine Mail in der Datenbank an Teams, welche in von der Warteliste aufgerückt sind
     *
     * @param Turnier $turnier
     * @param nTeam $team
     */
    public static function mailWarteZuSetzliste(Turnier $turnier, nTeam $team): void
    {
        $betreff = "Setzliste: " . TurnierSnippets::ortDatumBlock($turnier, false);
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_warte_zu_spiele.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $akt_kontakt = new Kontakt ($team->id());
        $emails = $akt_kontakt->get_emails('info');
        MailBot::add_mail($betreff, $inhalt, $emails);
    }

    public static function mailDoppelAnmeldung(Turnier $turnier, nTeam $team): void
    {
        $betreff = "Abgemeldet: " . TurnierSnippets::ortDatumBlock($turnier, false);
        ob_start();
        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
        include(Env::BASE_PATH . "/templates/mails/mail_doppelt_anmeldung.tmp.php");
        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $akt_kontakt = new Kontakt ($team->id());
        $emails = $akt_kontakt->get_emails('info');
        MailBot::add_mail($betreff, $inhalt, $emails);
    }

    /**
     * Erstellt eine Mail in der Datenbank an alle vom Losen betroffenen Teams
     *
     * @param Turnier $turnier
     */
    public static function mailGelost(Turnier $turnier): void
    {
        if ($turnier->isLigaturnier()) {
            $anmeldungen = $turnier->getListe();
            foreach ($anmeldungen as $anmeldung) {
                if (!$anmeldung->getTeam()->isLigaTeam()) {
                    continue;
                }

                $betreff =  TurnierSnippets::translate($anmeldung->getListe())
                    . ": "
                    . TurnierSnippets::ortDatumBlock($turnier, false);
                ob_start();
                    include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                    include(Env::BASE_PATH . "/templates/mails/mail_gelost.tmp.php");
                    include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                    $inhalt = ob_get_clean();
                    $emails = (new Kontakt ($anmeldung->getTeam()->id()))->get_emails('info');
                MailBot::add_mail($betreff, $inhalt, $emails);
            }
        }
    }

    /**
     * Schreibt eine Mail in die Datenbank an alle spielberechtigten Teams, wenn es (zum Übergang zur Meldephase) noch
     * freie Plätze gibt.
     *
     * @param Turnier $turnier
     */
    public static function mailPlaetzeFrei(Turnier $turnier): void
    {
        if ($turnier->isLigaturnier() && TurnierService::hasFreieSetzPlaetze($turnier)) {
            $teams = TeamRepository::get()->activeLigaTeams();
            foreach ($teams as $team) {
                // Noch Plätze frei
                if (
                    !TeamService::isAngemeldet($team, $turnier)
                    && TurnierService::isSetzBerechtigt($turnier, $team)
                    && TeamValidator::isAmKalenderTagAufSetzliste($turnier, $team)
                ) {
                    $betreff = "Freie Plätze: " . TurnierSnippets::ortDatumBlock($turnier, false);
                    ob_start();
                        include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_plaetze_frei.tmp.php");
                        include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
                        $inhalt = ob_get_clean();
                    $emails = (new Kontakt ($team->id()))->get_emails('info');
                    MailBot::add_mail($betreff, $inhalt, $emails);
                }
            }
        }
    }
}