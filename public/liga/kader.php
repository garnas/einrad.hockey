<?php
require_once '../../init.php';

Html::$titel = "Kader | Deutsche Einradhockeyliga";
Html::$content = "Infos über die verschiedenen Kader im Einradsport";
include '../../templates/header.tmp.php';
?>

    <h1 class="w3-text-primary">Nationalkader</h1>
    <p>
        Seit seiner Gründung im September 2021 bilden die stärksten Einradhockeyspieler und -spielerinnen Deutschlands
        den Nationalkader. Sie treffen sich in regelmäßigen Abständen zu Trainingslagern und repräsentieren die deutsche
        Liga bei internationalen Veranstaltungen wie Welt- und Europameisterschaften auf höchstem Niveau. Aus der bereits
        bestehenden Nachwuchsförderung wurde ein Jahr später im September 2022 der B-Nationalkader aufgebaut. Er bietet
        nachkommenden Talenten und speziell den jüngeren Spielern und Spielerinnen die Möglichkeit der Weiterentwicklung
        und den perspektivischen Aufstieg in den deutschen Nationalkader. Seit Sommer 2023 ist auch der B-Nationalkader
        bei internationalen Wettbewerben vertreten.
    </p>

    <h3 class="w3-text-primary">Erfolge (Nationalkader)</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>2024 Weltmeisterschaft UNICON Bemidji, A-Turnier, 2. Platz</li>
            <li>2023 Europameisterschaft EUHC Mörfelden, A-Turnier, 3. Platz</li>
            <li>2022 Weltmeisterschaft UNICON Grenoble, A-Turnier, 2. und 3. Platz</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">Erfolge (B-Kader)</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>2023 Europameisterschaft EUHC Mörfelden, B-Turnier, 1., 2. und 3. Platz</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">Termine für die Trainingslager der Saison 2024/25, B-Kader</h3>
    <div class="w3-container">
        <ul class="w3-ul w3-leftbar w3-border-tertiary">
            <li>26. + 27. Oktober 2024 (Sichtungstraining) in Mörfelden</li>
            <li>09. + 10. November 2024 in Mörfelden</li>
            <li>18. + 19. Januar 2025</li>
            <li>08. + 09. Februar 2025 in Augustdorf</li>
            <li>29. + 30. März 2025 in Mörfelden</li>
            <li>03. + 04. Mai 2025 in Remscheid</li>
        </ul>
    </div>
    <h3 class="w3-text-primary">Termine für die Trainingslager der Saison 2024/25, A-Kader</h3>
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

    <h3 class="w3-text-primary">Trainerstab</h3>
    <p>
        Nationalkader: Jan Holhbein, Jan Schubert, Kontakt: <?=Html::mailto("einradhockeykader@gmx.de")?>
        <br>B-Kader: Max Oles, Philipp Gross, Maike Oles, Kontakt: <?=Html::mailto("nachwuchs@einrad.hockey")?>
    </p>

<?php include '../../templates/footer.tmp.php';
