<?php

namespace App\Event\Turnier;

use App\Entity\Turnier\Turnier;
use App\Service\Team\TeamService;
use App\Service\Turnier\TurnierService;
use App\Repository\Team\TeamRepository;
use MailBot;
use Env;
use Kontakt;

class TurnierEventMailBot
{
    /**
     * Erstellt eine Mail in der Datenbank an alle spielberechtigten Teams, wenn ein neues Turnier eingetragen wird
     *
     * @param Turnier $turnier
     */
    public static function mailNeuesTurnier(Turnier $turnier): void
    {
        if (TurnierService::isLigaTurnier($turnier)) {

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
}