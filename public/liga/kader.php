<?php

use App\Service\Team\SpielerService;

require_once '../../init.php';

Html::$titel = "Kader | Deutsche Einradhockeyliga";
Html::$content = "Infos über die verschiedenen Kader im Einradsport";
include '../../templates/header.tmp.php';
?>

    <h1 class="w3-text-primary">Nationalkader</h1>
    <p>
        Seit seiner Gründung im September 2021 bilden die stärksten Einradhockeyspieler und -spielerinnen Deutschlands
        den A-Kader. Sie treffen sich in regelmäßigen Abständen zu Trainingslagern und repräsentieren die deutsche
        Liga bei internationalen Veranstaltungen wie Welt- und Europameisterschaften auf höchstem Niveau.
    </p>
    <p>Aus der bereits bestehenden Nachwuchsförderung wurde ein Jahr später im September 2022 der B-Kader aufgebaut. Er bietet
        nachkommenden Talenten und speziell den jüngeren Spielern und Spielerinnen die Möglichkeit der Weiterentwicklung
        und den perspektivischen Aufstieg in den deutschen Kader. Seit Sommer 2023 ist auch der B-Kader
        bei internationalen Wettbewerben vertreten.
    </p>
    <div class="w3-center">
        <iframe  style="width:100%; height: 340px" src="https://www.youtube-nocookie.com/embed/eaUT9tUrjS4?si=Xkto73gokPx_a5UB" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        <span class="w3-text-grey">Video der Europameisterschaft 2023</span>
    </div>

    <h3 class="w3-text-primary">A-Kader: Erfolge</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2024 Weltmeisterschaft UNICON Bemidji, A-Turnier, 2. Platz</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 2023 Europameisterschaft EUHC Mörfelden, A-Turnier, 3. Platz</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2022 Weltmeisterschaft UNICON Grenoble, A-Turnier, 2. Platz</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 2022 Weltmeisterschaft UNICON Grenoble, A-Turnier, 3. Platz</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">B-Kader: Erfolge</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li><?= Html::icon("emoji_events", class: "w3-text-tertiary") ?> 2023 Europameisterschaft EUHC Mörfelden, B-Turnier, 1. Platz</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2023 Europameisterschaft EUHC Mörfelden, B-Turnier, 2. Platz</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 2023 Europameisterschaft EUHC Mörfelden, B-Turnier, 3. Platz</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">A-Kader: Termine für die Trainingslager der Saison 2024/25</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>12. + 13. Oktober 2024 in Remscheid</li>
            <li>09. + 10. November 2024 in Bottrop</li>
            <li>11. + 12. Januar 2025 in Bottrop</li>
            <li>01. + 02. März 2025 in Mörfelden</li>
            <li>17. + 18. Mai 2025 in Meerbusch</li>
            <li>07. + 08. Juni 2025 in Mörfelden</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">B-Kader: Termine für die Trainingslager der Saison 2024/25</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>26. + 27. Oktober 2024 (Sichtungstraining) in Mörfelden</li>
            <li>09. + 10. November 2024 in Mörfelden</li>
            <li>18. Januar 2025 in Münster (bei Dieburg)</li>
            <li>08. + 09. Februar 2025 in Augustdorf</li>
            <li>29. + 30. März 2025 in Mörfelden</li>
            <li>03. + 04. Mai 2025 in Remscheid</li>
        </ul>
    </div>


    <h3 class="w3-text-primary">Trainerstab</h3>
    <p>
        A-Kader - Kontakt: <?=Html::mailto("einradhockeykader@gmx.de")?>
    </p>
    <div class="w3-container w3-row-padding">
        <div style="max-width: 200px" class="w3-col l6 m6 s6">
            <img src="<?= Env::BASE_URL ?>/bilder/kader/Def - 31.jpg" alt="Trainer" class="w3-image w3-card-4">
            <span>Jan Schubert</span>
        </div>
        <div style="max-width: 200px" class="w3-col l6 m6 s6">
            <img src="<?= Env::BASE_URL ?>/bilder/kader/Off - 156.jpg" alt="Trainer" class="w3-image w3-card-4">
            <span>Jan Hohlbein</span>
        </div>
    </div>
    <p>
        B-Kader - Kontakt: <?=Html::mailto("nachwuchs@einrad.hockey")?>
    </p>
    <div class="w3-container w3-row-padding">
        <div style="max-width: 200px"  class="w3-col l4 m4 s6">
            <img src="<?= Env::BASE_URL ?>/bilder/kader/Def - 548.jpg" alt="Trainer" class="w3-image w3-card-4">
            <span>Maike Oles</span>
        </div>
        <div style="max-width: 200px" class="w3-col l4 m4 s6">
            <img src="<?= Env::BASE_URL ?>/bilder/kader/Def - 874.jpg" alt="Trainer" class="w3-image w3-card-4">
            <span>Philipp Gross</span>
        </div>
        <div style="max-width: 200px" class="w3-col l4 m4 s6">
            <img src="<?= Env::BASE_URL ?>/bilder/kader/Def - 245.jpg" alt="Trainer" class="w3-image w3-card-4">
            <span>Max Oles</span>
        </div>
    </div>
<?php include '../../templates/footer.tmp.php';
