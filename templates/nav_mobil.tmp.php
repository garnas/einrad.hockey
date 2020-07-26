<!-- Sidebar für Mobile Navigation-->
<div class="w3-sidebar w3-white w3-bar-block" style="opacity: 0.9; display:none;z-index:5; width: 75%; max-width: 360px" id="mySidebar">
    <div class="w3-center w3-text-primary">
        <a href='../liga/neues.php' class='no'>
            <h3>DEUTSCHE EINRADHOCKEYLIGA</h3>
            <img src="../bilder/logo_kurz_small.png" class="w3-image" alt="kleines Logo" style="max-width: 140px">
        </a>
    </div>
    <a href="../liga/ueber_uns.php" class="no">
        <h3 class="w3-margin-left w3-text-primary"><i style="vertical-align: -16%" class="material-icons w3-xlarge">info</i> INFO</h3>
    </a>
    <a href="../liga/neues.php" class="w3-bar-item w3-button">Neuigkeiten</a>
    <a href="../liga/ueber_uns.php" class="w3-bar-item w3-button">Interesse?</a>
    <a href="../liga/teams.php" class="w3-bar-item w3-button">Teams</a>
    <a href="../liga/ligakarte.php" class="w3-bar-item w3-button">Ligakarte</a>
    <a href="https://forum.einrad.hockey/" class="w3-bar-item w3-button">Forum</a>
    <div class="w3-text-black">
        <h3 class="w3-margin-left w3-text-primary"><i style="vertical-align: -16%" class="material-icons w3-xlarge">emoji_events</i> LIGA</h3>
    </div>
    <a href="../liga/turniere.php" class="w3-bar-item w3-button">Turniere</a>
    <a href="../liga/ergebnisse.php" class="w3-bar-item w3-button">Ergebnisse</a>
    <a href="../liga/teams.php" class="w3-bar-item w3-button">Teams</a>
    <a href="../liga/tabelle.php#meister" class="w3-bar-item w3-button">Meisterschaftstabelle</a>
    <a href="../liga/tabelle.php#rang" class="w3-bar-item w3-button">Rangtabelle</a>
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons">settings</i> MODUS</h3>
    </div>
    <a href="../liga/dokumente.php" class="w3-bar-item w3-button">Dokumente</a>
    <a href="../liga/ligaleitung.php" class="w3-bar-item w3-button">Ligaleitung</a>
    <div class="w3-text-primary">
        <a style="text-decoration: none" href="../teamcenter/tc_start.php">
            <h3 class="w3-margin-left"><i style="vertical-align: -20%" class="material-icons w3-xlarge">group</i> TEAMCENTER</h3>
        </a>
    </div>
    <a href="../teamcenter/tc_login.php" class="w3-bar-item w3-button">Login</a>
    <?php if (!isset($_SESSION['team_id'])){$tc_color = 'w3-text-grey';}?>
    <div class="<?=$tc_color ?? ''?>">
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_start.php">Start</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_turnierliste_anmelden.php">Turnieranmeldung</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_turnier_erstellen.php">Turnier erstellen</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_turnierliste_verwalten.php">Turnier verwalten</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_neuigkeit_eintragen.php">Neuigkeiten eintragen</a>
        <a class="w3-bar-item w3-button" href="../teamcenter/tc_neuigkeit_liste.php">Neuigkeit bearbeiten</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_emails.php">Kontaktcenter</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_teamdaten.php">Teamdaten</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_kader.php">Kader</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_pw_aendern.php">Passwort ändern</a>
        <a class="w3-bar-item w3-button <?=$tc_color?>" href="../teamcenter/tc_logout.php">Logout</a>
    </div>
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons w3-xlarge">format_list_bulleted</i> SONSTIGES</h3>
    </div>
    <a href="../liga/ueber_uns.php" class="w3-bar-item w3-button">Über uns</a>
    <a href="../liga/archiv.php" class="w3-bar-item w3-button">Archiv</a>
    <a href="../ligacenter/lc_login.php" class="w3-bar-item w3-button">Ligacenter</a>
    <a href="../liga/kontakt.php" class="w3-bar-item w3-button">Kontakt</a>
    <a href="../liga/datenschutz.php" class="w3-bar-item w3-button">Datenschutz</a>
    <a href="../liga/impressum.php" class="w3-bar-item w3-button">Impressum</a>
    <a href="#" class="w3-bar-item w3-button"></a>
</div>

<!-- Sidebar Overlay -->
<div class="w3-overlay" id="myOverlay" onclick="close_sidebar()" style="cursor:pointer;"></div>

<!--Navigation für Smartphones -->
<div class="w3-display-container w3-hide-large w3-hide-medium">
    <!-- Hintergrundbild -->
    <img src="<?=$_SESSION['hintergrund']?>" class="<?php if (!isset($_SESSION['la_id']) && !isset($_SESSION['team_id'])){?>w3-card-4<?php } ?>" alt="Hintergrundbild" style="width:100%; opacity: 0.4;">
    <div class="w3-display-left w3-margin-left">
        <img src="../bilder/logo_lang_small.png" onclick="open_sidebar()" class="w3-image" alt="langes Logo" style="max-width: 80%; vertical-align: 22%; cursor: pointer">
    </div>

    <!-- Burger Menü -->
    <div class="w3-display-right">
        <button onclick="open_sidebar()" class="w3-btn w3-text-primary">
            <!-- vertical-align 0% stehen lassen, da material-icons vertical-align in style.css verändern -->
            <i class="w3-xxlarge material-icons" style="vertical-align: 0%;">menu</i>
        </button>
    </div>

    <!-- Logout Button -->
    <div class="w3-display-bottomright w3-text-primary w3-large">
        <?php if (isset($_SESSION['team_id'])){?>
        <a href='../teamcenter/tc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }elseif (isset($_SESSION['la_id'])){?>
        <a href='../ligacenter/lc_logout.php' class="w3-button w3-hover-primary"><i style=""
                class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }?>
    </div>
</div>