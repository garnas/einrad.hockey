<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$saison = (isset($_GET['saison'])) ? (int)$_GET['saison'] : Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Saisontermine | Deutschen Einradhockeyliga";
Html::$content = "Die wichtigsten Termine der Deutschen Einradhockeyliga.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary">Termine</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<h1 class="w3-text-primary w3-margin-top" id='saisontermine'>Saisontermine</h1>
<div class="w3-responsive w3-section">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Saison 2025/2026</b></td>
        </tr>
        <tr>
            <td class="w3-primary-3">Reguläre Spielzeit</td>
            <td>16. August 2025 - 31. Mai 2026</td>
        </tr>
        <tr>
            <td class="w3-primary-3">B/C-Finalwochenende</td>
            <td>13. Juni 2026 + 14. Juni 2026</td>
        </tr>
        <tr>
            <td class="w3-primary-3">Deutsche Meisterschaft</td>
            <td>20. Juni 2026 + 21. Juni 2026</td>
        </tr>
    </table>
</div>

<div class="w3-responsive w3-section">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Saison 2026/2027</b></td>
        </tr>
        <tr>
            <td class="w3-primary-3">Reguläre Spielzeit</td>
            <td>15. August 2026 - 30. Mai 2027</td>
        </tr>
        <tr>
            <td class="w3-primary-3">B/C-Finalwochenende</td>
            <td>12. Juni 2027 + 13. Juni 2027</td>
        </tr>
        <tr>
            <td class="w3-primary-3">Deutsche Meisterschaft</td>
            <td>19. Juni 2027 + 20. Juni 2027</td>
        </tr>
    </table>
</div>

<h1 class="w3-text-primary w3-margin-top" id='lawahlen'>Ligaausschusswahlen</h1>
<div class="w3-responsive w3-section">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Saison 2025/2026</b></td>
        </tr>
        <tr>
            <td class="w3-primary-3">Bewerbungsfrist</td>
            <td>01. Februar 2026</td>
        </tr>
        <tr>
            <td class="w3-primary-3">Ligaausschusswahlen</td>
            <td>15. Februar 2026 - 01. März 2026</td>
        </tr>
    </table>
</div>

<h1 class="w3-text-primary w3-margin-top" id='kader'>Kadertermine</h1>
Die Termine der beiden Nationalkader sind hier einsehbar: <span><?= Html::link("kader.php", " Termine des A- und B-Kaders", false, "") ?></span>
<?php

include Env::BASE_PATH . '/templates/footer.tmp.php';