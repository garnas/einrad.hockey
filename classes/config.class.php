<?php 
class Config {
    
    //Fürs Debugging kann hier die Zeit verändert werden, mit der die Ligalogik arbeitet
    //Dies hat keine Auswirkung auf Benennung der Uploads und auf die Log-Dateien
    public static function time_offset()
    {
        return time() + 0; // Offset in Sekunden: 31 Tage: time() + 31*24*60*60
    }
    
    ///////////////////////////////////////////////////////////////
    ///////////////////////////Saison Jahr/////////////////////////
    ///////////////////////////////////////////////////////////////

    const SAISON = '26'; //Saison Nummer Saison 1 = Saison 1995;
    const SAISON_ANFANG = '15.08.2020';
    const SAISON_ENDE = '31.10.2021';

    ///////////////////////////////////////////////////////////////
    ////////////////////////SQL Zugangsdaten///////////////////////
    ///////////////////////////////////////////////////////////////

    const HOST_NAME = 'localhost';
    const DATABASE = 'neue_db';
    const USER_NAME = 'root';
    const PASSWORD = '';

    ///////////////////////////////////////////////////////////////
    /////////////////////////Absolute Links////////////////////////
    ///////////////////////////////////////////////////////////////

    const LINK_FORUM = 'https://forum.einrad.hockey/';
    const LINK_ARCHIV = 'https://archiv.einrad.hockey/archiv/index.html';
    const LINK_INSTA = 'https://www.instagram.com/einradhockeyde/';
    const LINK_FACE = 'https://www.facebook.com/DeutscheEinradhockeyliga';
    
    ///////////////////////////////////////////////////////////////
    ////////////////////////Dokumenten-Links///////////////////////
    ///////////////////////////////////////////////////////////////

    const LINK_MODUS = '../dokumente/modus20-21_entwurf.pdf';
    const LINK_REGELN = '../dokumente/regeln2020.pdf';
    const LINK_MODUS_KURZ = '../dokumente/modus2020_zusammenfassung.pdf';
    const LINK_REGELN_KURZ = '../dokumente/wichtige_regeln2020.pdf';
    const LINK_MODUS_KURZ_ENG = '../dokumente/league_mode2020_summary.pdf';
    const LINK_REGELN_IUF = '../dokumente/iuf-rulebook-2019.pdf';
    const LINK_TURNIER = '../dokumente/turniermodi2020.pdf';
    const LINK_DSGVO = '../dokumente/datenschutz-hinweise.pdf';

    ///////////////////////////////////////////////////////////////
    //////////////////////////Mailadressen/////////////////////////
    ///////////////////////////////////////////////////////////////

    const LAMAIL = 'liga@einrad.hockey';
    const LAMAIL_ANTWORT = 'la2021@einrad.hockey';
    const TECHNIKMAIL = 'technik@einrad.hockey';
    const SCHIRIMAIL = 'schiri@einrad.hockey';

    ///////////////////////////////////////////////////////////////
    ///////////////////////Mailversand Server//////////////////////
    ///////////////////////////////////////////////////////////////

    const ACTIVATE_EMAIL = false; //Bei True, werden Emails tatsächlich versendet, bei false wird db::debug($mailer) ausgeführt
    const SMTP_HOST = 'HOST';
    const SMTP_USER = 'EMAIL';
    const SMTP_PW = 'PW';
    const SMTP_PORT = 666;

    ///////////////////////////////////////////////////////////////
    ///////////////////////////Teamblöcke//////////////////////////
    ///////////////////////////////////////////////////////////////
    //Für die Block und Wertzuordnung in der Rangtabelle siehe Tabelle::platz_to_block und Tabelle::platz_to_wertigkeit
    //Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein

    //Mögliche Team-Blöcke
    const BLOCK = array('A','AB','BC','CD','DE','EF','F');

    //Mögliche Turnier-Blöcke
    const BLOCK_ALL =  array("ABCDEF",'AB','ABC','BC','BCD','CD','CDE','DE','DEF','EF','F');
    
    //Ligagebühr
    const LIGAGEBUEHR = "30&nbsp;€";
}