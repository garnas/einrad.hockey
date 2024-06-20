<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Saisontermine | Deutschen Einradhockeyliga";
Html::$content = "Die wichtigsten Termine der Deutschen Einradhockeyliga.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<h1 class="w3-text-primary" id='saisontermine'>Saisontermine</h1>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Saison 2024/2025</b></td>
        </tr>
        <tr>
            <th class="w3-primary-3">Reguläre Spielzeit</th>
            <td>17. August 2024 – 01. Juni 2025</td>
        </tr>
        <tr>
            <th class="w3-primary-3">B/C-Finalwochenende</th>
            <td>14. + 15. Juni 2025</td>
        </tr>
        <tr>
            <th class="w3-primary-3">Deutsche Meisterschaft</th>
            <td>21. + 22. Juni 2025</td>
        </tr>
        <tr>
            <td class="w3-primary" colspan="2"><b>Saison 2024/2025</b></td>
        </tr>
        <tr>
            <th class="w3-primary-3">Reguläre Spielzeit</th>
            <td>16. August 2025 – 31. Mai 2026</td>
        </tr>
        <tr>
            <th class="w3-primary-3">B/C-Finalwochenende</th>
            <td>13. Juni 2026 + 14. Juni 2026</td>
        </tr>
        <tr>
            <th class="w3-primary-3">Deutsche Meisterschaft</th>
            <td>20. Juni 2026 + 21. Juni 2026</td>
        </tr>
    </table>
</div>

<h1 class="w3-text-primary w3-margin-top" id='lawahlen'>Ligaausschusswahlen</h1>
<p>Die nächsten Ligaausschusswahlen finden regulär an folgenden Terminen statt:</p>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-bordered">
        <tr>
            <th class="w3-primary-3">Bewerbungsfrist</th>
            <td>28. Januar 2024</td>
        </tr>
        <tr>
            <th class="w3-primary-3">Ligaausschusswahlen</th>
            <td>12. Februar 2024 - 25. Februar 2024</td>
        </tr>
    </table>
</div>

<!--<h1 class="w3-text-primary w3-margin-top">Zukünftige Saisontermine</h1>-->
<!--<p>Für die kommenden Saisons sind bereits diese Termine festgelegt:</p>-->

<!--<div class="w3-responsive w3-card w3-margin-top">-->
<!--    <table class="w3-table w3-bordered">-->
<!--        <tr>-->
<!--            <td class="w3-primary" colspan="2"><b>Saison 2023/2024</b></td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <th class="w3-primary-3">Reguläre Spielzeit</th>-->
<!--            <td>14. August 2023 – 26. Mai 2024</tr>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <th class="w3-primary-3">Finalwochenende I</th>-->
<!--            <td>08. Juni 2024 – 09. Juni 2024</td>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <th class="w3-primary-3">Finalwochenende II</th>-->
<!--            <td>15. Juni 2024 – 16. Juni 2024</td>-->
<!--        </tr>-->
<!--    </table>-->
<!--</div>-->

<?php

include Env::BASE_PATH . '/templates/footer.tmp.php';