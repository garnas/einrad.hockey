<!--Navigation für Desktop und Tablet -->
<div class="w3-display-container w3-hide-small" style="width: 100%;">
    
    <!-- Hintergrundbild -->
    <img src="<?=$_SESSION['hintergrund']?>" class="<?php if (!isset($_SESSION['la_id']) && !isset($_SESSION['team_id'])){?>w3-card-4<?php } ?>" alt="Hintergrundbild" style="width:100%; opacity: 0.3;">
    
    <!-- oben links -->
    <div class="w3-display-topleft w3-margin w3-padding-large">
        <!--Logo (nur large) -->
        <div class="w3-hide-medium">
            <a href="../liga/neues.php">
                <img src="../bilder/logo_kurz_small.png" alt="kurzes Logo" class="w3-image w3-left w3-bar-item" style="margin-top: 30px; max-width: 200px;">
            </a>
        </div>
    </div>

    <!-- unten links -->
    <div class="w3-display-bottomleft w3-margin w3-padding-large">
        <!--Logo (nur medium) -->
        <div class="w3-hide-large">
            <a href="../liga/neues.php">
                <img src="../bilder/logo_lang_small.png" class="w3-image w3-left w3-bar-item" alt="langes Logo" style="max-width: 70%;">
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
                <a href="../liga/ueber_uns.php" class="w3-bar-item w3-button">Über uns</a>
                <a href="../liga/archiv.php" class="w3-bar-item w3-button">Archiv</a>
                <a href="../ligacenter/lc_login.php" class="w3-bar-item w3-button">Ligacenter</a>
                <a href="../liga/kontakt.php" class="w3-bar-item w3-button">Kontakt</a>
                <a href="../liga/datenschutz.php" class="w3-bar-item w3-button">Datenschutz</a>
                <a href="../liga/impressum.php" class="w3-bar-item w3-button">Impressum</a>
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
                    <a href="../teamcenter/tc_login.php" class="w3-bar-item w3-button">Login</a>
                <?php } // endif?>
                <?php if (!isset($_SESSION['team_id'])){ $tc_color="w3-text-grey";?><?php } // endif?>
                <div class="<?=$tc_color ?? ''?>">
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_start.php">Start</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_challenge.php">km-Challenge</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_turnierliste_anmelden.php">Turnieranmeldung</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_turnier_erstellen.php">Turnier erstellen</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_turnierliste_verwalten.php">Eigene Turniere</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_neuigkeit_eintragen.php">Neuigkeiten eintragen</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_neuigkeit_liste.php">Neuigkeit bearbeiten</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_kontaktcenter.php">Kontaktcenter</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_teamdaten.php">Teamdaten</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_kader.php">Kader</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_pw_aendern.php">Passwort ändern</a>
                    <a class="w3-bar-item w3-button" href="../teamcenter/tc_logout.php">Logout</a>
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
                <a href="../liga/dokumente.php" class="w3-bar-item w3-button">Dokumente</a>
                <a href="../liga/ligaleitung.php" class="w3-bar-item w3-button">Ligaleitung</a>
            </div>
        </div>

        <!-- Liga -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">emoji_events</i>
                <span style="font-size: 22px">LIGA</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="../liga/challenge.php" class="w3-bar-item  w3-button">km-Challenge</a>
                <a href="../liga/turniere.php" class="w3-bar-item  w3-button">Turniere</a>
                <a href="../liga/ergebnisse.php" class="w3-bar-item w3-button">Ergebnisse</a>
                <a href="../liga/teams.php" class="w3-bar-item w3-button">Teams</a>
                <a href="../liga/tabelle.php#meister" class="w3-bar-item w3-button">Meisterschaftstabelle</a>
                <a href="../liga/tabelle.php#rang" class="w3-bar-item w3-button">Rangtabelle</a>
            </div>
        </div>

        <!-- Info -->
        <div class="w3-dropdown-hover w3-right w3-text-primary" style="">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">info</i>
                <span style="font-size: 22px">INFO</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <a href="../liga/neues.php" class="w3-bar-item w3-button">Neuigkeiten</a>
                <a href="../liga/ueber_uns.php" class="w3-bar-item w3-button">Interesse?</a>
                <a href="../liga/teams.php" class="w3-bar-item w3-button">Teams</a>
                <a href="../liga/ligakarte.php" class="w3-bar-item w3-button">Ligakarte</a>
                <a href="https://forum.einrad.hockey/" class="w3-bar-item w3-button">Forum</a>
            </div>
        </div>
    </div>

    <!-- unten rechts -->
    <div class="w3-display-bottomright w3-text-primary w3-margin w3-padding-large w3-large">

        <!-- Logout -->
        <?php if (isset($_SESSION['team_id'])){?>
        <a href='../teamcenter/tc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }elseif (isset($_SESSION['la_id'])){?>
        <a href='../ligacenter/lc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php } //end if?>
    </div>
</div>