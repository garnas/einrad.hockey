<?php

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
        <iframe style="width:100%; height: 340px" src="https://www.youtube-nocookie.com/embed/eaUT9tUrjS4?si=Xkto73gokPx_a5UB" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        <span class="w3-text-grey">Video der Europameisterschaft 2023</span>
    </div>

    <h2 class="w3-text-secondary w3-xxlarge">A-Kader</h2>
    <h3 class="w3-text-primary">Termine</h3>
    <div class="w3-section">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>06. + 07. Februar 2027 (Mörfelden)</li>
            <li>20. + 21. März 2027 (Mörfelden)</li>
            <li><i>Weitere Termine folgen</i></li>

        </ul>
    </div>

    <h3 class="w3-text-primary">Erfolge</h3>
    <div class="w3-section">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2. Platz, A-Turnier, UNICON Steyr 2026</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-primary") ?> 5. Platz, A-Turnier, UNICON Steyr 2026</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-primary") ?> 6. Platz, A-Turnier, UNICON Steyr 2026</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2. Platz, A-Turnier, UNICON Bemidji 2024</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 3. Platz, A-Turnier, EUHC Mörfelden 2023</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2. Platz, A-Turnier, UNICON Grenoble 2022</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 3. Platz, A-Turnier, UNICON Grenoble 2022</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-primary") ?> 5. Platz, A-Turnier, UNICON Grenoble 2022</li>
        </ul>
    </div>
    
    <h3 class="w3-text-primary">Trainerstab</h3>
    <div class="w3-section w3-center">
        <div style="display: flex; justify-content: center; gap: 10px; flex-wrap: wrap;">
            <div>
                <div><img src="<?= Env::BASE_URL ?>/bilder/kader/Def - 31.jpg" alt="Trainer" style="width: 100%; max-width: 200px"></div>
                <div><span class="w3-text-grey" style="width: 100%; max-width: 200px">Jan Schubert</span></div>
            </div>
            <div>
                <div><img src="<?= Env::BASE_URL ?>/bilder/kader/Off - 156.jpg" alt="Trainer" style="width: 100%; max-width: 200px"></div>
                <div><span class="w3-text-grey" style="width: 100%; max-width: 200px">Jan Hohlbein</span></div>
            </div>
        </div>
    </div>

    <div class="w3-section w3-center">
        <a href="mailto:einradhockeykader@gmx.de" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
            <?= Html::icon("mail") ?> E-Mail an den A-Kader
        </a>
    </div>
        
    <hr>
        
    <h2 class="w3-text-secondary w3-xxlarge">B-Kader</h2>
    <h3 class="w3-text-primary">Termine</h3>
    <div class="w3-section">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>10. + 11. Oktober 2026 (Mörfelden)</li>
            <li>07. + 08. November 2026</li>
            <li>12. + 13. Dezember 2026</li>
            <li>23. + 24. Januar 2027</li>
            <li>06. + 07. März 2027</li>
            <li>17. + 18. April 2027</li>
            <li>22. + 23. Mai 2027 (Mörfelden)</li>
        </ul>
    </div>

    <h3 class="w3-text-primary">Erfolge</h3>
    <div class="w3-section">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2. Platz, B-Turnier, UNICON Steyr 2026</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-primary") ?> 5. Platz, B-Turnier, UNICON Steyr 2026</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-tertiary") ?> 1. Platz, B-Turnier, EUHC Mörfelden 2023</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-grey") ?> 2. Platz, B-Turnier, EUHC Mörfelden 2023</li>
            <li><?= Html::icon("emoji_events", class: "w3-text-brown") ?> 3. Platz, B-Turnier, EUHC Mörfelden 2023</li>
        </ul>
    </div>

    <h3 class="w3-text-primary">Trainerstab</h3>
    <div class="w3-section w3-center">
        <div><img src="<?= Env::BASE_URL ?>/bilder/kader/B-Kader-Trainer.jpg" alt="B-Kader-Trainer" style="width: 100%; max-width: 600px"></div>
        <div><i class="w3-text-grey" style="display: block; margin: auto; width: 100%; max-width: 600px">B-Kader-Trainer: Lukas, Max, Maike, Philipp (von links nach rechts)</i></div>
    </div>

    <div class="w3-section w3-center">
        <a href="mailto:nachwuchs@einrad.hockey" class="w3-button w3-ripple w3-round w3-tertiary w3-margin-top">
            <?= Html::icon("mail") ?> E-Mail an den B-Kader
        </a>
    </div>

<?php include '../../templates/footer.tmp.php';
