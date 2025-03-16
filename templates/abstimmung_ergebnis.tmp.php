<?php 
function get_sum($arr) {
    $sum = 0;
    foreach ($arr as $key => $value) {
        $sum += $value;
    }

    return $sum == 0 ? 1 : $sum;
}
use App\Repository\Team\TeamRepository;
$teams = TeamRepository::get()->activeLigaTeams();
$stimmen = Abstimmung::get_anzahl_stimmen();

$ergebnisse = Abstimmung::get_ergebnisse();
$massnahmen = array_slice($ergebnisse, 0, 6);
$weiteres = array_slice($ergebnisse, 6, 1);
$option = array_slice($ergebnisse, 7, 1);

$massnahme_text = [
    0 => 'Enthaltung',
    1 => '1 - weniger wichtig',
    5 => '5 - sehr wichtig'
];

$massnahme_key = [
    'Uebernachtungskosten' => 'Übernachtungskosten',
    'Aufwandsentschaedigung' => 'Aufwandsentschädigung'
]

?>

<h2 class="w3-large w3-text-secondary">Beteiligung</h2>
<p class="w3-xxlarge w3-text-primary"><?=round($stimmen / count($teams) * 100)?>%</p>

<h2 class="w3-large w3-text-secondary">Bitte bewertet die folgenden Fördermaßnahmen nach ihrer Wichtigkeit für die Liga:</h2>
<?php foreach ($massnahmen as $key => $massnahme):?>
    <p class="w3-panel w3-leftbar w3-border-bottom w3-border-primary w3-text-primary"><?=$massnahme_key[$key] ?? $key?></p>
    <table class="w3-table w3-striped w3-border-left w3-border-primary">
        <?php for ($idx = 5; $idx >= 0; $idx--):?>
            <tr>
                <td class="w3-right-align"><?=round(($massnahme[$idx] ?? 0) / get_sum($massnahme) * 100)?>%</td>
                <td><?=$massnahme_text[$idx] ?? $idx?></td>
            </tr>
        <?php endfor;?>
    </table>
<?php endforeach;?>


<h2 class="w3-large w3-text-secondary">Seid Ihr damit einverstanden, dass die jährlichen Beiträge der Mitglieder der Liga für Fördermaßnahmen eingesetzt werden, die zur Förderung der deutschen Einradhockeyliga und des Sports beitragen?</h2>
<table class="w3-table w3-striped w3-border-left w3-border-primary">
    <tr>
        <td class="w3-right-align"><?=round(($option['option']['Ja'] ?? 0) / get_sum($option['option']) * 100)?>%</td>
        <td>Ja</td>
    </tr>
    <tr>
        <td class="w3-right-align"><?=round(($option['option']['Nein'] ?? 0) / get_sum($option['option']) * 100)?>%</td>
        <td>Nein</td>
    </tr>
    <tr>
        <td class="w3-right-align"><?=round(($option['option']['Keine_Angabe'] ?? 0) / get_sum($option['option']) * 100)?>%</td>
        <td>Keine Angabe</td>
    </tr>
</table>