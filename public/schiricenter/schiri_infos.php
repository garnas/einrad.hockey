<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
$DEBUGMODUS = true;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Schiedsrichter-Infos | Deutsche Einradhockeyliga";
Html::$content = "Infos für Schiedsrichter und alle, die es werden wollen.";
include '../../templates/header.tmp.php';
?>

<?php if (isset($DEBUGMODUS)) { # Start Debug Modus ?>
    <P><div class="w3-ul w3-pale-red w3-bordered">
        DEBUGGING:
        <?=Html::link("uebungstest_l_debug.php", "Übungstest (Lehrgang)",
                      false, false)?> /
        <?=Html::link("uebungstest_j_debug.php", "Übungstest (Junior)",
                      false, false)?> /
        <?=Html::link("uebungstest_b_debug.php", "Übungstest (Basis)",
                      false, false)?> /
        <?=Html::link("uebungstest_f_debug.php", "Übungstest (Fortgeschrittene)",
                      false, false)?>
    </div>
<?php } # Ende Debug Modus ?> 

<h1 class="w3-text-primary">Infos für Schiedsrichter und alle, die es
    werden wollen</h1>

<UL>
    <LI>Es gibt immer einen schriftlichen Test, und dann eine praktische Prüfung.</LI>
    <LI>Der schriftliche Test gilt bis ???.</LI>
    <LI>Die Schirilizenz gilt für ??? Jahre.</LI>
    <LI>Teams mit mindestens ??? Schiris haben folgende Vorteile: ???</LI>
    <LI>(...hier noch mehr Infos hinzufügen...)</LI>
    <LI>(alle Infos gelten für die Saison 2021/2022)</LI>
</UL>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Multiple-Choice Übungstests
            (mit den Fragen aus den echten Tests)</b></td>
        </tr>
        <tr>
            <th class="w3-primary-3"><?=Html::link("uebungstest_l.php",
                "Übungstest (Lehrgang)", false, false)?></th>
            <td>Dieser Test eignet sich für einen Schirilehrgang. Es
            wird immer 1 Frage gestellt, und auf der nächsten Seite
            kommt die Auflösung. Es ist auch möglich, per ID eine
            gezielte Frage auszuwählen.</tr>
        </tr>
        <tr>
            <th class="w3-primary-3"><?=Html::link("uebungstest_j.php",
                "Übungstest (Junior)", false, false)?></th>
            <td>Dies ist ein kompletter Test (<?=array_sum(SchiriTest::anzahl_J)?> Fragen)
            als Vorbereitung auf die Junior-Schiriprüfung.</tr>
        </tr>
        <tr>
            <th class="w3-primary-3"><?=Html::link("uebungstest_b.php",
                "Übungstest (Basis)", false, false)?></th>
            <td>Dies ist ein kompletter Test (<?=array_sum(SchiriTest::anzahl_B)?> Fragen)
            als Vorbereitung auf die Basis-Schiriprüfung.</tr>
        </tr>
        <tr>
            <th class="w3-primary-3"><?=Html::link("uebungstest_f.php",
                "Übungstest (Fortgeschrittene)", false, false)?></th>
            <td>Dies ist ein kompletter Test
            (<?=array_sum(SchiriTest::anzahl_F)?> Fragen) als
            Vorbereitung auf die Schiriprüfung für Fortgeschrittene.</tr>
        </tr>
    </table>
</div>

<P>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-bordered">
        <tr>
            <td class="w3-primary" colspan="2"><b>Weitere Infos</b></td>
        </tr>
        <tr>
            <th class="w3-primary-3"><?=Html::link(Nav::LINK_REGELN,
                                                   'Regelwerk', false, false)?></th>
            <td>Hier gibt es die kompletten Regeln der deutschen
                Einradhockeyliga als pdf.</tr>
        </tr>
    </table>
</div>

<h1 class="w3-text-primary">Fragen?</h1>

<p>Wer Einradhockeyschiedsrichter werden möchte, kann sich jederzeit an
den Schiedsrichterausschuss wenden: <?=Html::mailto(Env::SCHIRIMAIL)?></p>
        
<?php include '../../templates/footer.tmp.php';
