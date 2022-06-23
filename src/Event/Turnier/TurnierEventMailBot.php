<?php

namespace App\Event\Turnier;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;
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
                    TurnierService::isSpielBerechtigt($turnier, $team)
                    && !TeamService::isAmKalenderTagAufSetzliste($turnier->getDatum(),$team)
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
            ob_start();
                include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
                include(Env::BASE_PATH . "/templates/mails/mail_turnier_canceled.tmp.php");
                include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
            $inhalt = ob_get_clean();

            MailBot::add_mail($betreff, $inhalt, $emailAdressen);
        }
    }

    /**
     * Erstellt eine Mail in der Datenbank an Teams, welche in von der Warteliste aufgerückt sind
     *
     * @param Turnier $turnier
     * @param nTeam $team
     */
    public static function mailWarteZuSetzliste(Turnier $turnier, nTeam $team): void
    {
        $betreff = "Setzliste: " . TurnierSnippets::ortDatumBlock($turnier);
        ob_start();
            include(Env::BASE_PATH . "/templates/mails/mail_anfang.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_warte_zu_spiele.tmp.php");
            include(Env::BASE_PATH . "/templates/mails/mail_ende.tmp.php");
        $inhalt = ob_get_clean();
        $akt_kontakt = new Kontakt ($team->id());
        $emails = $akt_kontakt->get_emails('info');
        MailBot::add_mail($betreff, $inhalt, $emails);
    }
}