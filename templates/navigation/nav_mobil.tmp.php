<!-- Sidebar für Mobile Navigation-->
<div class="w3-sidebar w3-white w3-bar-block" style="opacity: 0.9; display:none;z-index:5; width: 75%; max-width: 360px" id="mySidebar">
    <div class="w3-center w3-text-primary">
        <a href='<?= Env::BASE_URL ?>/liga/neues.php' class='no'>
            <img src="<?= Env::BASE_URL ?>/bilder/logo_kurz.png"
                 class="w3-image w3-margin-top"
                 alt="Logo der Deutschen Einradhockeyliga"
                 style="max-width: 200px">
        </a>
    </div>

    <!-- Info -->
    <a href="<?= Env::BASE_URL ?>/liga/neues.php" class="no">
        <h3 class="w3-margin-left w3-text-primary"><i style="vertical-align: -16%" class="material-icons">info</i> INFO</h3>
    </a>
    <?php foreach (Nav::get_info() as Nav::$link): ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php endforeach; ?>

    <!-- Liga -->
    <div class="w3-text-black">
        <h3 class="w3-margin-left w3-text-primary">
            <?= Html::icon("emoji_events", tag: "h3") ?> LIGA
        </h3>
    </div>
    <?php foreach (Nav::get_liga() as Nav::$link): ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php endforeach; ?>

    <!-- Teamcenter -->
    <?php if (isset($_SESSION['logins']['team'])): ?>
        <div class="w3-text-primary">
            <a style="text-decoration: none" href="<?= Env::BASE_URL ?>/teamcenter/tc_login.php">
                <h3 class="w3-margin-left"><i style="vertical-align: -20%" class="material-icons">group</i> TEAMCENTER</h3>
            </a>
        </div>
        <?php foreach (Nav::get_teamcenter() as Nav::$link): ?>
            <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Modus -->
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons">settings</i> MODUS</h3>
    </div>
    <?php foreach (Nav::get_modus() as Nav::$link): ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php endforeach; ?>

    <!-- Berichte -->
    <div class="w3-text-primary">
        <h3 class="w3-margin-left"><i style="vertical-align: -16%" class="material-icons">article</i> BERICHTE</h3>
    </div>
    <?php foreach (Nav::get_berichte() as Nav::$link): ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php endforeach; ?>

    <!-- Organisation -->
    <div class="w3-text-primary">
        <a style="text-decoration: none" href="<?= Env::BASE_URL ?>/teamcenter/tc_login.php">
            <h3 class="w3-margin-left"><i style="vertical-align: -20%" class="material-icons">group</i> ORGA</h3>
        </a>
    </div>
    <?php foreach (Nav::get_organisation() as Nav::$link): ?>
        <a href="<?= Nav::$link[0] ?>" class="w3-bar-item w3-button"><?= Nav::$link[1] ?></a>
    <?php endforeach; ?>

</div>

<!-- Sidebar Overlay -->
<div class="w3-overlay" id="myOverlay" onclick="close_sidebar()" style="cursor:pointer;"></div>

<!--Navigation für Smartphones -->
<div class="w3-display-container">
    <!-- Hintergrundbild -->
    <img src="<?= Html::get_hintergrund_bild() ?>" class="<?php if (!isset($_SESSION['logins']['la']) && !isset($_SESSION['logins']['team'])) {?>w3-card-4<?php } ?>" alt="Hintergrundbild" style="width:100%; opacity: 0.4;">
    <div class="w3-display-left w3-margin-left">
        <img src="<?= Env::BASE_URL ?>/bilder/logo_lang.png" 
            onclick="open_sidebar()" 
            class="w3-image" 
            alt="Logo der Deutschen Einradhockeyliga mit Schriftzug" 
            style="max-width: 50%; vertical-align: 22%; cursor: pointer">
    </div>

    <!-- Burger Menü -->
    <div class="w3-display-right">
        <button onclick="open_sidebar()" class="w3-btn w3-round w3-ripple w3-text-primary">
            <!-- vertical-align 0% stehen lassen, da material-icons vertical-align in style.css verändern -->
            <i class="w3-xxlarge material-icons" style="vertical-align: 0;">menu</i>
        </button>
    </div>

    <!-- Logout Button -->
    <div class="w3-display-bottomright w3-text-primary w3-large">
        <?php if (isset($_SESSION['logins']['team'])): ?>
            <a href='<?= Env::BASE_URL ?>/teamcenter/tc_logout.php' class="w3-button w3-hover-primary"><i class="material-icons w3-xlarge">block</i> Logout</a>
        <?php elseif (isset($_SESSION['logins']['la'])): ?>
            <a href='<?= Env::BASE_URL ?>/ligacenter/lc_logout.php' class="w3-button w3-hover-primary"><i class="material-icons w3-xlarge">block</i> Logout</a>
        <?php endif; ?>
    </div>
</div>