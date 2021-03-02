<!--Navigation für Desktop und Tablet -->
<div class="w3-display-container w3-hide-small">
    
    <!-- Hintergrundbild -->
    <img src="<?= Form::get_hintergrund_bild() ?>" 
         class="<?php if (!isset($_SESSION['logins']['la']) && !isset($_SESSION['logins']['team'])){?>w3-card-4<?php } ?>" 
         alt="Hintergrundbild" 
         style="width:100%; opacity: 0.3;">
    
    <!-- oben links -->
    <div class="w3-display-topleft w3-margin w3-padding-large">
        <!--Logo (nur large) -->
        <div class="w3-hide-medium">
            <a href="<?= Env::BASE_URL ?>/liga/neues.php">
                <img src="<?= Env::BASE_URL ?>/bilder/logo_kurz_small.png" 
                     alt="kurzes Logo" class="w3-image w3-left w3-bar-item" 
                     style="margin-top: 30px; max-width: 200px;">
            </a>
        </div>
    </div>

    <!-- unten links -->
    <div class="w3-display-bottomleft w3-margin w3-padding-large">
        <!--Logo (nur medium) -->
        <div class="w3-hide-large">
            <a href="<?= Env::BASE_URL ?>/liga/neues.php">
                <img src="<?= Env::BASE_URL ?>/bilder/logo_lang_small.png" 
                     class="w3-image w3-left w3-bar-item" 
                     alt="langes Logo" 
                     style="max-width: 70%;">
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
                <?php foreach(Nav::get_sonstiges() as Nav::$link){ ?>
                    <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
                <?php } //end for ?>
            </div>
        </div>

        <!-- Teamcenter -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class="w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">group</i>
                <span style="font-size: 22px">TEAMCENTER</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <?php foreach(Nav::get_teamcenter() as Nav::$link){ ?>
                    <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button <?= Nav::$link[2] ?>"><?= Nav::$link[1] ?></a>
                <?php } //end for ?>
            </div>
        </div>

        <!-- Modus -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">settings</i>
                <span style="font-size: 22px">MODUS</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <?php foreach(Nav::get_modus() as Nav::$link){ ?>
                    <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
                <?php } //end for ?>
            </div>
        </div>

        <!-- Liga -->
        <div class="w3-dropdown-hover w3-right w3-text-primary">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">emoji_events</i>
                <span style="font-size: 22px">LIGA</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <?php foreach(Nav::get_liga() as Nav::$link){ ?>
                    <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
                <?php } //end for ?>
            </div>
        </div>

        <!-- Info -->
        <div class="w3-dropdown-hover w3-right w3-text-primary" style="">
            <a class=" w3-button w3-hover-primary">
                <i style="vertical-align: -18.5%" class="material-icons w3-xlarge">info</i>
                <span style="font-size: 22px">INFO</span>
            </a>
            <div class="w3-dropdown-content w3-bar-block w3-border">
                <?php foreach(Nav::get_info() as Nav::$link){ ?>
                    <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
                <?php } //end for ?>
            </div>
        </div>
    </div>

    <!-- unten rechts -->
    <div class="w3-display-bottomright w3-text-primary w3-margin w3-padding-large w3-large">
        <!-- Logout -->
        <?php if (isset($_SESSION['logins']['team'])){?>
            <a href='<?= Env::BASE_URL ?>/teamcenter/tc_logout.php' class="w3-button w3-right w3-hover-primary">
                <?= Form::icon("logout", 24, 26) ?> Logout
            </a>
        <?php }elseif (isset($_SESSION['logins']['la'])){?>
            <a href='<?= Env::BASE_URL ?>/ligacenter/lc_logout.php' class="w3-button w3-right w3-hover-primary">
                <?= Form::icon("logout", tag:"h3") ?> Logout
            </a>
        <?php } //end if?>

        <!-- Suchfeld -->
        <div style="margin-bottom: 6px;">
            <div class="w3-margin-left w3-margin-right">
                <div role="search" data-ss360="true">
                    <label for="suche_desktop"></label>
                    <input class="searchbox" id="suche_desktop" type="search" placeholder="Suche" />
                    <button class="searchbutton"></button>
                </div>
            </div>
        </div>
    </div>
</div>