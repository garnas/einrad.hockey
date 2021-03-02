<!-- Sidebar für Mobile Navigation-->
<div class="w3-sidebar w3-white w3-bar-block" style="opacity: 0.9; display:none;z-index:5; width: 75%; max-width: 360px" id="mySidebar">
    <div class="w3-center w3-text-primary">
        <a href='<?= Env::BASE_URL ?>/liga/neues.php' class='no'>
            <h3>NAVIGATION</h3>
            <img src="<?= Env::BASE_URL ?>/bilder/logo_kurz_small.png" class="w3-image" alt="kleines Logo" style="max-width: 140px">
        </a>
    </div>
    <!-- Searchbox -->
    <div class="w3-panel">
        <div class="w3-margin-left w3-margin-right">
            <div role="search" data-ss360="true">
                <label for="suche_mobil"></label>
                <input class="searchbox" id="suche_mobil" type="search" placeholder="Suche" />
                <button class="searchbutton"></button>
            </div>
        </div>
    </div>

    <!-- Info -->
    <a href="<?= Env::BASE_URL ?>/liga/ueber_uns.php" class="no">
        <h3 class="w3-margin-left w3-text-primary"><i style="vertical-align: -16%" class="material-icons w3-xlarge">info</i> INFO</h3>
    </a>
    <?php foreach(Nav::get_info() as Nav::$link){ ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php } //end for ?>

    <!-- Liga -->
    <div class="w3-text-black">
        <h3 class="w3-margin-left w3-text-primary">
            <?= Html::icon("emoji_events", tag:"h3") ?> LIGA
        </h3>
    </div>
    <?php foreach(Nav::get_liga() as Nav::$link){ ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php } //end for ?>

    <!-- Modus -->
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons">settings</i> MODUS</h3>
    </div>
    <?php foreach(Nav::get_modus() as Nav::$link){ ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php } //end for ?>

    <!-- Teamcenter -->
    <div class="w3-text-primary">
        <a style="text-decoration: none" href="<?= Env::BASE_URL ?>/teamcenter/tc_start.php">
            <h3 class="w3-margin-left"><i style="vertical-align: -20%" class="material-icons w3-xlarge">group</i> TEAMCENTER</h3>
        </a>
    </div>
    <?php foreach(Nav::get_teamcenter() as Nav::$link){ ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button <?= Nav::$link[2] ?>"><?= Nav::$link[1] ?></a>
    <?php } //end for ?>

    <!-- Sonstiges -->
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons w3-xlarge">format_list_bulleted</i> SONSTIGES</h3>
    </div>
    <?php foreach(Nav::get_sonstiges() as Nav::$link){ ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button <?= Nav::$link[2] ?? '' ?>"><?= Nav::$link[1] ?></a>
    <?php } //end for ?>
    <a href="#" class="w3-bar-item w3-button"></a>
</div>

<!-- Sidebar Overlay -->
<div class="w3-overlay" id="myOverlay" onclick="close_sidebar()" style="cursor:pointer;"></div>

<!--Navigation für Smartphones -->
<div class="w3-display-container w3-hide-large w3-hide-medium">
    <!-- Hintergrundbild -->
    <img src="<?= Html::get_hintergrund_bild() ?>" class="<?php if (!isset($_SESSION['logins']['la']) && !isset($_SESSION['logins']['team'])){?>w3-card-4<?php } ?>" alt="Hintergrundbild" style="width:100%; opacity: 0.4;">
    <div class="w3-display-left w3-margin-left">
        <img src="<?= Env::BASE_URL ?>/bilder/logo_lang_small.png" onclick="open_sidebar()" class="w3-image" alt="langes Logo" style="max-width: 80%; vertical-align: 22%; cursor: pointer">
    </div>

    <!-- Burger Menü -->
    <div class="w3-display-right">
        <button onclick="open_sidebar()" class="w3-btn w3-round w3-ripple w3-text-primary">
            <!-- vertical-align 0% stehen lassen, da material-icons vertical-align in style.css verändern -->
            <i class="w3-xxlarge material-icons" style="vertical-align: 0%;">menu</i>
        </button>
    </div>

    <!-- Logout Button -->
    <div class="w3-display-bottomright w3-text-primary w3-large">
        <?php if (isset($_SESSION['logins']['team'])){?>
            <a href='<?= Env::BASE_URL ?>/teamcenter/tc_logout.php' class="w3-button w3-hover-primary"><i class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }elseif (isset($_SESSION['logins']['la'])){?>
            <a href='<?= Env::BASE_URL ?>/ligacenter/lc_logout.php' class="w3-button w3-hover-primary"><i class="material-icons w3-xlarge">block</i> Logout</a>
        <?php }?>
    </div>
</div>