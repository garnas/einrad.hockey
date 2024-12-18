<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Todo eigene Funktion
$fortschritt = round(100 * (time() - strtotime(Config::SAISON_ANFANG)) / (strtotime(Config::SAISON_ENDE) - strtotime(Config::SAISON_ANFANG)));
$tage = round((strtotime(Config::SAISON_ANFANG) - time()) / (24 * 60 * 60));

$neuigkeiten = Neuigkeit::get_neuigkeiten();

$turniere = nTurnier::get_turniere_kommend();
$anz_next_turniere = count($turniere);
$next_turniere = array_slice($turniere, 0, 4);

$turniere = nTurnier::get_turniere_ergebnis($asc = false);
$anz_last_turniere = count($turniere);
$last_turniere = array_slice($turniere, 0, 4);

// Zuordnen der Farben für 1. 2. 3. Platz der Statistiken
$colors = ["w3-text-tertiary", "w3-text-grey", "w3-text-brown"];
$icons = ["looks_one", "looks_two", "looks_3"];

$statistik['max_gew'] = Stats::get_gew_spiele_team();
$statistik['max_turniere'] = Stats::get_turniere_team();
$statistik['ges_tore'] = Stats::get_tore_anzahl();
$statistik['ges_spiele'] = Stats::get_spiele_anzahl();
$statistik['spielminuten'] = Stats::get_spielminuten_anzahl();

// Zeitanzeige der Neuigkeiteneinträge verschönern
foreach ($neuigkeiten as $neuigkeiten_id => $neuigkeit) { //Todo in get_neuikgeiten rein
    $delta_zeit = (time() - strtotime($neuigkeit['zeit'])) / (60 * 60); //in Stunden
    if ($delta_zeit < 24) {
        $zeit = ($delta_zeit <= 1.5) ? "gerade eben" : "vor " . round($delta_zeit) . " Stunden";
    } elseif ($delta_zeit < 7 * 24) {
        $zeit = ($delta_zeit <= 1.5 * 24) ? "vor einem Tag" : "vor " . round($delta_zeit / 24) . " Tagen";
    } else {
        $zeit = date("d.m.Y", strtotime($neuigkeit['zeit']));
    }
    $neuigkeiten[$neuigkeiten_id]['zeit'] = $zeit;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Neuigkeiten | Deutsche Einradhockeyliga";
Html::$content = "Hier findet man die Neuigkeiteneinträge des Ligaausschusses und der Teams der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php'; ?>

    <!-- Links (u. a zum Ein- und Ausblenden der Infobar bei Mobils) -->
    <div class="w3-hide-large w3-hide-medium">
        <span id="einblenden"
                class="w3-left w3-left-align w3-hide-large w3-hide-medium w3-hover-text-secondary w3-text-primary"
                onclick="einblenden()"
                style="width:50%;"
        >
            <?= Html::icon("visibility") ?> Infobar
        </span>
        <span id="ausblenden"
                class="w3-left w3-left-align w3-hide w3-hide-large w3-hide-medium w3-hover-text-secondary w3-text-primary"
                onclick="ausblenden()"
                style="width:50%;"
        >
            <?= Html::icon("visibility_off") ?> Infobar
        </span>

        <a href="ueber_uns.php" class="w3-hover-text-secondary w3-text-primary no w3-right w3-right-align" style="width:50%;">
            <?= Html::icon("help_outline") ?> Über uns
        </a>
    </div>


    <!-- Responsive Container -->
    <div class="w3-row-padding w3-stretch">
        <!-- Infobar -->
        <div class="w3-col l4 m5 w3-hide-small" id="infobar">

            <!-- Interesse -->
            <div class="w3-panel w3-card-4 w3-responsive w3-round w3-bottombar">
                <div class="w3-stretch w3-container w3-primary w3-hover-tertiary">
                    <a href='ueber_uns.php' class="no">
                        <h3><?= Html::icon("help_outline", tag: "h1") ?> Interesse</h3>
                    </a>
                </div>
                <p>Die Einradhockeyliga steht jedem Einradhockeybegeisterten offen!</p>
                <iframe width="100%" height="100%" src="https://www.youtube-nocookie.com/embed/eaUT9tUrjS4?si=Xkto73gokPx_a5UB" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                <p><?= Html::link("ueber_uns.php", " Mehr Infos", false, "info") ?></p>
            </div>

            <!-- Anstehende Turniere -->
            <div class="w3-panel w3-card-4 w3-bottombar  w3-responsive w3-round">
                <div class="w3-stretch w3-container w3-primary w3-hover-tertiary">
                    <a href='turniere.php' class="no">
                        <h3><?= Html::icon("event", tag: "h2") ?> Turniere</h3>
                    </a>
                </div>

                <?php if (empty($next_turniere)) { ?>
                    <p class="w3-text-grey">Es sind keine Turniere eingetragen.</p>
                <?php } //end if?>
                <?php foreach ($next_turniere as $turnier): ?>
                    <p class="w3-text-dark-gray">
                        <?= date("d.m", strtotime($turnier->get_datum())) ?>
                        <?= Html::link(
                            'turnier_details.php?turnier_id=' . $turnier->get_turnier_id(),
                            $turnier->get_ort(),
                            false,
                            "open_in_new") ?>
                        <i>(<?= $turnier->get_art() == 'spass' ? "Spaß" : $turnier->get_tblock() ?>)</i>
                    </p>
                <?php endforeach;?>
            </div>

            <!-- Ergebnisse -->
            <div class="w3-panel w3-card-4 w3-bottombar  w3-responsive w3-round">
                <div class="w3-stretch w3-container w3-primary w3-hover-tertiary">
                    <a href='ergebnisse.php' class="no">
                        <h3><?= Html::icon("sports_hockey", tag: "h2") ?> Ergebnisse</h3>
                    </a>
                </div>

                <?php if (empty($last_turniere)): ?>
                    <p class="w3-text-grey">
                        Es liegen keine Ergebnisse vor.
                    </p>
                <?php endif;?>
                <?php foreach ($last_turniere as $turnier): ?>
                    <p class="w3-text-dark-gray">
                        <?= date("d.m", strtotime($turnier->get_datum())) ?>
                        <?= Html::link(
                            'ergebnisse.php#' . $turnier->get_turnier_id(),
                            $turnier->get_ort(),
                            false,
                            'open_in_new') ?>
                        <i>(<?= $turnier->get_tblock() ?>)</i>
                    </p>
                <?php endforeach;?>
            </div>

            <!-- Statistik -->
            <div class="w3-panel w3-card-4 w3-bottombar  w3-responsive w3-round">
                <div class="w3-stretch w3-container w3-primary w3-hover-tertiary">
                    <a href='statistik.php' class="no">
                        <h3><?= Html::icon("insert_chart_outlined", tag: "h2") ?> Statistik</h3>
                    </a>
                </div>
                <span class="w3-text-grey w3-small">Saison <?= Html::get_saison_string() ?></span>

                <!-- Allgemeine Statistik -->
                <div class="w3-section">
                    <div class="w3-responsive">
                        <table class="w3-table w3-bordered">
                            <tr class="w3-bottombar w3-text-grey w3-large w3-border-primary">
                                <td colspan="3"><?= Html::icon("insert_chart_outlined") ?> Allgemein</td>
                            </tr>
                            <tr>
                                <td class="w3-text-primary"><?= Html::icon("check") ?></td>
                                <td><?= $anz_last_turniere ?></td>
                                <td class="w3-small">gespielte Turniere</td>
                            </tr>
                            <tr>
                                <td class="w3-text-primary"><?= Html::icon("double_arrow") ?></td>
                                <td><?= $anz_next_turniere ?></td>
                                <td class="w3-small">anstehende Turniere</td>
                            </tr>
                            <tr>
                                <td class="w3-text-primary"><?= Html::icon("sports_baseball") ?></td>
                                <td><?= $statistik['ges_tore'] ?></td>
                                <td class="w3-small">Tore</td>
                            </tr>
                            <tr>
                                <td class="w3-text-primary"><?= Html::icon("sports_hockey") ?></td>
                                <td><?= $statistik['ges_spiele'] ?></td>
                                <td class="w3-small">Spiele</td>
                            </tr>
                            <tr>
                                <td class="w3-text-primary"><?= Html::icon("schedule") ?></td>
                                <td style="white-space: nowrap;"><?= $statistik['spielminuten'] ?></td>
                                <td class="w3-small">Spielminuten</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Wer hat am meisten Turniere gespielt? -->
                <?php if (!empty($statistik['max_turniere'])): ?>
                    <div class="w3-section">
                        <div class="w3-responsive">
                            <table class="w3-table w3-centered w3-bordered">
                                <tr class="w3-bottombar w3-text-grey w3-large w3-border-primary">
                                    <td><?= Html::icon("leaderboard") ?></td>
                                    <td>Turnierspieler</td>
                                    <td><?= Html::icon("assistant_photo") ?></td>
                                </tr>
                                <?php $i = 0;
                                foreach ($statistik['max_turniere'] as $team): ?>
                                    <tr class="<?= $colors[$i] ?>">
                                        <td><?= Html::icon($icons[$i++]) ?></td>
                                        <td style="white-space: nowrap;" class="w3-small"><?= $team['teamname'] ?></td>
                                        <td><?= $team['gespielt'] ?></td>
                                    </tr>
                                <?php endforeach;?>
                            </table>
                        </div>
                        <span class="w3-text-grey w3-small">
                        <?= Html::icon("assistant_photo") ?> Anzahl gespielter Turniere
                    </span>
                    </div>
                <?php endif; ?>

                <!-- Wer hat am meisten Spiele gewonnen? -->
                <?php if (!empty($statistik['max_gew'])): ?>
                    <div class="w3-section">
                        <div class="w3-responsive">
                            <table class="w3-table w3-centered w3-bordered">
                                <tr class="w3-bottombar w3-text-grey w3-large w3-border-primary">
                                    <td><?= Html::icon("leaderboard") ?></td>
                                    <td>Spielgewinner</td>
                                    <td><?= Html::icon("sports_hockey") ?></td>
                                </tr>
                                <?php $i = 0;
                                foreach ($statistik['max_gew'] as $team): ?>
                                    <tr class="<?= $colors[$i] ?>">
                                        <td><?= Html::icon($icons[$i++]) ?></td>
                                        <td style="white-space: nowrap;" class="w3-small"><?= $team['teamname'] ?></td>
                                        <td><?= $team['siege'] ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                        <span class="w3-text-grey w3-small">
                        <?= Html::icon("sports_hockey") ?> Anzahl gewonnener Spiele
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Links -->
            <div class="w3-panel w3-card-4 w3-bottombar  w3-responsive w3-round">
                <div class="w3-stretch w3-container w3-primary">
                    <h3><?= Html::icon("public", tag: "h2") ?> Links</h3>
                </div>
                <p class="w3-text-grey w3-border-top w3-border-grey"><?= Html::icon("bookmark") ?> Ligen</p>
                <p><?= Html::link(Nav::LINK_SWISS, " Schweizer Einradhockeyliga", true, "link") ?></p>
                <p><?= Html::link(Nav::LINK_AUSTRALIA, " Australische Einradhockeyliga", true, "link") ?></p>
                <p><?= Html::link(Nav::LINK_FRANCE, " Französische Einradbasketballliga", true, "link") ?></p>

                <p class="w3-text-grey w3-border-top w3-border-grey"><?= Html::icon("bookmark") ?> Verbände</p>
                <p><?= Html::link(Nav::LINK_EV, " Einradverband Deutschland", true, "link") ?></p>
                <p><?= Html::link(Nav::LINK_EV_SH, " Einradverband Schleswig-Holstein", true, "link") ?></p>
                <p><?= Html::link(Nav::LINK_EV_BY, " Einradverband Bayern", true, "link") ?></p>

                <p class="w3-text-grey w3-border-top w3-border-grey"><?= Html::icon("bookmark") ?> Föderation</p>
                <p><?= Html::link(Nav::LINK_IUF, " International Unicycle Federation", true, "link") ?></p>
            </div>
        </div>

        <!-- Neuigkeiten-Einträge -->
        <div class="w3-col l8 m7">
            <?php foreach ($neuigkeiten as $neuigkeit): //Schleife für jede Neuigkeit?>
                <div class='w3-card-4 w3-panel w3-responsive w3-round w3-bottombar'>

                    <!-- Überschrift -->
                    <?php if ($neuigkeit['titel']): ?>
                        <div class="w3-stretch w3-container w3-primary w3-center">
                            <h3><?= $neuigkeit['titel'] ?></h3>
                        </div>
                    <?php endif; ?>

                    <!-- Bild -->
                    <?php if ($neuigkeit['link_jpg'] != ''): ?>
                        <div class='w3-center w3-card w3-section'>
                            <a href='<?= $neuigkeit['bild_verlinken'] ?: $neuigkeit['link_jpg'] ?>'>
                                <img class='w3-image w3-hover-opacity' alt="<?= $neuigkeit['titel'] ?? "Foto"?>" src=<?= $neuigkeit['link_jpg'] ?>>
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Text -->
                    <div class="w3-section">
                        <?= nl2br($neuigkeit['inhalt']) ?>
                    </div>

                    <!-- PDF -->
                    <?php if ($neuigkeit['link_pdf'] != ''): ?>
                        <?= Html::link($neuigkeit['link_pdf'], "PDF-Anhang", true, "insert_drive_file") ?>
                    <?php endif; ?>

                    <!-- Autor + Zeitstempel -->
                    <div class='w3-text-grey w3-hide-small w3-hide-medium'>
                        <p class="w3-left">
                            <?= Html::icon("create") ?> <?= ($neuigkeit['eingetragen_von']) ?>
                        </p>
                        <p class='w3-right'>
                            <?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?>
                        </p>
                    </div>
                    <div class='w3-text-grey w3-hide-large'>
                        <p class="w3-right">
                            <?= Html::icon("create") ?> <?= ($neuigkeit['eingetragen_von']) ?>
                            <br>
                            <?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?>
                        </p>
                    </div>
                    <!-- Link zum Bearbeiten falls man im Ligacenter oder Teamcenter eingeloggt ist -->
                    <?php if (LigaLeitung::is_logged_in("ligaausschuss")){ ?>
                        <p>
                            <a href='../ligacenter/lc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>' class='no'>
                                <button class="w3-button w3-block w3-tertiary">
                                    <?= Html::icon("create") ?> Bearbeiten
                                </button>
                            </a>
                        </p>
                    <?php } elseif (
                            LigaLeitung::is_logged_in("oeffentlichkeitsausschuss")
                            && $neuigkeit["eingetragen_von"] == "Öffentlichkeitsausschuss"
                          ){ ?>
                    <p>
                        <a href='../oefficenter/oc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>' class='no'>
                            <button class="w3-button w3-block w3-tertiary">
                                <?= Html::icon("create") ?> Bearbeiten
                            </button>
                        </a>
                    </p>
                    <?php } elseif (Neuigkeit::darf_bearbeiten($neuigkeit['eingetragen_von'])){ ?>
                        <p>
                            <a href='../teamcenter/tc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>' class='no'>
                                <button class="w3-button w3-block w3-tertiary">
                                    <?= Html::icon("create") ?> Bearbeiten
                                </button>
                            </a>
                        </p>
                    <?php } //end if?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

<?php include '../../templates/footer.tmp.php';
