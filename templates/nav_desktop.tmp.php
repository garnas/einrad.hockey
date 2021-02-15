<!--Navigation für Desktop und Tablet -->
<div class="w3-display-container w3-hide-small">
    
    <!-- Hintergrundbild -->
    <img src="<?= Form::get_hintergrund_bild() ?>" class="<?php if (!isset($_SESSION['la_id']) && !isset($_SESSION['team_id'])){?>w3-card-4<?php } ?>" alt="Hintergrundbild" style="width:100%; opacity: 0.3;">
    
    <!-- oben links -->
    <div class="w3-display-topleft w3-margin w3-padding-large">
        <!--Logo (nur large) -->
        <div class="w3-hide-medium">
            <a href="<?= Env::BASE_URL ?>/liga/neues.php">
                <img src="<?= Env::BASE_URL ?>/bilder/logo_kurz_small.png" alt="kurzes Logo" class="w3-image w3-left w3-bar-item" style="margin-top: 30px; max-width: 200px;">
            </a>
        </div>
    </div>

    <!-- unten links -->
    <div class="w3-display-bottomleft w3-margin w3-padding-large">
        <!--Logo (nur medium) -->
        <div class="w3-hide-large">
            <a href="<?= Env::BASE_URL ?>/liga/neues.php">
                <img src="<?= Env::BASE_URL ?>/bilder/logo_lang_small.png" class="w3-image w3-left w3-bar-item" alt="langes Logo" style="max-width: 70%;">
            </a>
        </div>
    </div>

    <!-- oben rechts -->
    <div class="w3-display-topright w3-margin w3-padding-large w3-large">

        <!-- Sonstiges -->
        <div class="w3-dropdown-hover w3-right w3-text-primary" style="background-color: transparent;">
            <a class="w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">format_list_bulleted</i>
                <span style="font-size: 22px">SONSTIGES</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="<?= Env::BASE_URL ?>/liga/ueber_uns.php" class="w3-bar-item w3-button">Über uns</a>
                <a href="<?= Env::BASE_URL ?>/liga/archiv.php" class="w3-bar-item w3-button">Archiv</a>
                <a href="<?= Env::BASE_URL ?>/ligacenter/lc_login.php" class="w3-bar-item w3-button">Ligacenter</a>
                <a href="<?= Env::BASE_URL ?>/liga/kontakt.php" class="w3-bar-item w3-button">Kontakt</a>
                <a href="<?= Env::BASE_URL ?>/liga/datenschutz.php" class="w3-bar-item w3-button">Datenschutz</a>
                <a href="<?= Env::BASE_URL ?>/liga/impressum.php" class="w3-bar-item w3-button">Impressum</a>
            </div>
        </div>

        <!-- Teamcenter -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class="w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">group</i>
                <span style="font-size: 22px">TEAMCENTER</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <?php if (!isset($_SESSION['team_id'])){?>
                    <a href="<?= Env::BASE_URL ?>/teamcenter/tc_login.php" class="w3-bar-item w3-button">Login</a>
                <?php } // endif?>
                <?php if (!isset($_SESSION['team_id'])){ $tc_color="w3-text-grey";?><?php } // endif?>
                <div class="<?=$tc_color ?? ''?>">
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_start.php">Start</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_abstimmung.php">Abstimmung</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_turnierliste_anmelden.php">Turnieranmeldung</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_turnier_erstellen.php">Turnier erstellen</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_turnierliste_verwalten.php">Eigene Turniere</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_neuigkeit_eintragen.php">Neuigkeiten eintragen</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_neuigkeit_liste.php">Neuigkeit bearbeiten</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_kontaktcenter.php">Kontaktcenter</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_teamdaten.php">Teamdaten</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_kader.php">Kader</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_pw_aendern.php">Passwort ändern</a>
                    <a class="w3-bar-item w3-button" href="<?= Env::BASE_URL ?>/teamcenter/tc_logout.php">Logout</a>
                </div>
            </div>
        </div>

        <!-- Modus -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">settings</i>
                <span style="font-size: 22px">MODUS</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="<?= Env::BASE_URL ?>/liga/dokumente.php" class="w3-bar-item w3-button">Dokumente</a>
                <a href="<?= Env::BASE_URL ?>/liga/ligaleitung.php" class="w3-bar-item w3-button">Ligaleitung</a>
            </div>
        </div>

        <!-- Liga -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">emoji_events</i>
                <span style="font-size: 22px">LIGA</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="<?= Env::BASE_URL ?>/liga/jubilaeum.php" class="w3-bar-item  w3-button">25 Jahre Liga</a>
                <a href="<?= Env::BASE_URL ?>/liga/turniere.php" class="w3-bar-item  w3-button">Turniere</a>
                <a href="<?= Env::BASE_URL ?>/liga/ergebnisse.php" class="w3-bar-item w3-button">Ergebnisse</a>
                <a href="<?= Env::BASE_URL ?>/liga/teams.php" class="w3-bar-item w3-button">Teams</a>
                <a href="<?= Env::BASE_URL ?>/liga/tabelle.php#meister" class="w3-bar-item w3-button">Meisterschaftstabelle</a>
                <a href="<?= Env::BASE_URL ?>/liga/tabelle.php#rang" class="w3-bar-item w3-button">Rangtabelle</a>
            </div>
        </div>

        <!-- Info -->
        <div class="w3-dropdown-hover w3-right w3-text-primary" style="">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">info</i>
                <span style="font-size: 22px">INFO</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="<?= Env::BASE_URL ?>/liga/neues.php" class="w3-bar-item w3-button">Neuigkeiten</a>
                <a href="<?= Env::BASE_URL ?>/liga/ueber_uns.php" class="w3-bar-item w3-button">Interesse?</a>
                <a href="<?= Env::BASE_URL ?>/liga/teams.php" class="w3-bar-item w3-button">Teams</a>
                <a href="<?= Env::BASE_URL ?>/liga/ligakarte.php" class="w3-bar-item w3-button">Ligakarte</a>
                <a href="https://forum.einrad.hockey/" class="w3-bar-item w3-button">Forum</a>
            </div>
        </div>
    </div>

    <!-- unten rechts -->
    <div class="w3-display-bottomright w3-text-primary w3-margin w3-padding-large w3-large">
        <div style="margin-bottom: 6px;">
            <section role="search" data-ss360="true">
                <input class="searchbox" type="search" placeholder="Suche" />
                <button class="searchbutton"/>
            </section>
        </div>
        <!-- Logout -->
        <?php if (isset($_SESSION['team_id'])){?>
        <a href='<?= Env::BASE_URL ?>/teamcenter/tc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }elseif (isset($_SESSION['la_id'])){?>
        <a href='<?= Env::BASE_URL ?>/ligacenter/lc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php } //end if?>
    </div>
</div>