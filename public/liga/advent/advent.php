<?php

// Logik
require_once '../../../init.php';
$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;

$directory = 'doors/';
$files = scandir($directory);
$files = array_filter($files, function($file) {
    return preg_match('/^\d{2}-.*\.php$/', $file);
});
$files = array_values($files);

$fileMap = [];
foreach ($files as $file) {
    preg_match('/^(\d{2})-/', $file, $matches);
    $id = (int)$matches[1];
    $fileMap[$id] = $file;
}

$doors = [];
$today = (int)date('d');
$month = (int)date('m');
$hour = (int)date('H');

$number_of_doors = 24;
$main_month = 12;

for ($i = 1; $i <= $number_of_doors; $i++) {

    $isPast = ($month === $main_month && $i < $today);
    $isToday = ($month === $main_month && $i == $today);
    $isFuture = ($month !== $main_month || $i > $today);

    $doors[] = [
        "id" => $i,
        "file" => isset($fileMap[$i]) ? $directory . $fileMap[$i] : 'working.php',
        "past" => $isPast,
        "today" => $isToday,
        "future" => $isFuture
    ];
}
shuffle($doors);

// Layout
Html::$titel = "Adventskalender | Deutsche Einradhockeyliga";
Html::$content = "Adventskalender der Deutschen Einradhockeyliga für das Jahr 2025.";

include '../../../templates/header.tmp.php'; ?>

<link type="text/css" rel="stylesheet" href="style.css?20251127">
<link type="text/css" rel="stylesheet" href="colors.css?20251127">

<h1 class="w3-text-primary">Adventskalender</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<div class="flex-container">
    <?php foreach ($doors as $door): ?>
        <?php if ( $door['past'] ): ?>
            <a href="<?=$door['file']?>" class="no flex-item w3-round-xlarge panel-done w3-xlarge">
                <?=$door['id']?>
            </a>
        <?php elseif ( $door['today'] ):?>
            <a href="<?=$door['file']?>" class="no flex-item w3-round-xlarge panel w3-xlarge">
                <?=$door['id']?>
            </a>
        <?php else:?>
            <a href="error.php" class="no flex-item w3-round-xlarge panel w3-xlarge">
                <?=$door['id']?>
            </a>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<?php include '../../../templates/footer.tmp.php'; ?>