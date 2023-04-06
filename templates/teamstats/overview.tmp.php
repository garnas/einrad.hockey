<style>
    .ehl-green {background-color: hsl(154deg, 38%, 58%)}
    .ehl-yellow {background-color: hsl(38deg, 38%, 58%)}
    .ehl-red {background-color: hsl(7deg, 38%, 58%)}
</style>

<!-- Panels mit Verteilung -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- Script zum Schalten der Buttons -->
<script>
    function show_all() {
        $('#gesamt').show();
        $('#btn_gesamt').addClass('w3-primary').removeClass('w3-light-gray w3-text-gray');

        $('#schwach').hide();
        $('#btn_schwach').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#stark').hide();
        $('#btn_stark').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#infotext').text('Zusammenfassung der Spiele der aktuellen Saison. Dabei werden auch jene beachtet bei ' +
            'denen gegen ein Nichtligateam gespielt wurde.');
    }

    function show_schwach() {
        $('#gesamt').hide();
        $('#btn_gesamt').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#schwach').show();
        $('#btn_schwach').addClass('w3-primary').removeClass('w3-light-gray w3-text-gray');

        $('#stark').hide();
        $('#btn_stark').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#infotext').text('Zusammenfassung der Spiele der aktuellen Saison. Dabei werden nur jene bedachtet, bei ' +
            'denen das gegnerische Team zu diesem Zeitpunkt in der Rangtabelle besser platziert war.');
    }

    function show_stark() {
        $('#gesamt').hide();
        $('#btn_gesamt').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#schwach').hide();
        $('#btn_schwach').addClass('w3-light-gray w3-text-gray').removeClass('w3-primary');

        $('#stark').show();
        $('#btn_stark').addClass('w3-primary').removeClass('w3-light-gray w3-text-gray');

        $('#infotext').text('Zusammenfassung der Spiele der aktuellen Saison. Dabei werden nur jene bedachtet, bei ' +
            'denen das gegnerische Team zu diesem Zeitpunkt in der Rangtabelle schlechter platziert war. Dazu ' +
            'gehören auch Nichtligateams.');
    }
</script>
<div class="w3-row-padding">
    <h3 class="w3-text-secondary">Zusammenfassung</h3>
    <p class="w3-border-top w3-border-grey w3-text-grey">
        <span id="infotext"></span>
    </p>

    <!-- Buttons -->
    <div class="w3-bar w3-border">
        <button id="btn_gesamt" style="width: 33.3%" class="w3-button w3-bar-item w3-hover-primary" onclick="show_all()">
            Gegen <b>alle</b> Teams
        </button>
        <button id="btn_schwach" style="width: 33.3%" class="w3-button w3-bar-item w3-hover-primary" onclick="show_schwach()">
            Gegen <b>starke</b> Teams
        </button>
        <button id="btn_stark" style="width: 33.3%" class="w3-button w3-bar-item w3-hover-primary" onclick="show_stark()">
            Gegen <b>schwache</b> Teams
        </button>
    </div>

    <div id="gesamt">
        <?php include "gesamt_verteilung.tmp.php"; ?>
    </div>
    <div id="schwach">
        <?php include "schwach_verteilung.tmp.php"; ?>
    </div>
    <div id="stark">
        <?php include "stark_verteilung.tmp.php"; ?>
    </div>

    <script>
        $(document).ready(show_all());
    </script>
</div>

<!-- Tabelle mit Ergebnissen gegen alle anderen Teams -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Gesamtübersicht über alle Teams</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>Dargestellt werden alle bisherigen Spiele gruppiert nach dem jeweiligen Gegner.</span>
        </p>
    </div>
    <?php include "gegner.tmp.php"; ?>
</div>

<!-- Panels und Tabelle mit Lieblingsgegner -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Lieblingsgegner</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>
                Ausschlaggebend ist die Anzahl der Siege gegen ein anderes Team. Bei Gleichheit entscheidet
                die Summe der Anzahl aus Unentschieden und Niederlagen. Besteht weiterhin Gleichheit, wird die
                Tordifferenz herangezogen, wobei alle Gegner gelistet werden.
            </span>
        </p>
    </div>
    <?php if (isset($first_liebling)) include "lieblingsgegner.tmp.php"; ?>
    <?php if (!empty($liebling)) include "lieblingsgegner_table.tmp.php"; ?>
</div>

<!-- Panels und Tabelle mit Angstgegner -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Angstgegner</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>
                Ausschlaggebend ist die Anzahl der Niederlagen gegen ein anderes Team. Bei Gleichheit entscheidet
                die Summe der Anzahl aus Unentschieden und Siege. Besteht weiterhin Gleichheit, wird die
                Tordifferenz herangezogen, wobei alle Gegner gelistet werden.
            </span>
        </p>
    </div>
    <?php if (isset($first_angst)) include "angstgegner.tmp.php"; ?>
    <?php if (!empty($angst)) include "angstgegner_table.tmp.php"; ?>
</div>

<!-- Panels mit Turnierergebnissen -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Turnierergebnisse</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>
                Aufzeigt werden das beste und das schlechteste Turnier in der laufenden Saison bemessen
                an den erhaltenen Punkten.
            </span>
        </p>
    </div>
    <?php include "turnierergebnisse.tmp.php"; ?>
</div>

<!-- Panels mit Spielergebnissen -->
<div>
    <div class="w3-row-padding">
        <h3 class="w3-text-secondary">Spielergebnisse</h3>
        <p class="w3-border-top w3-border-grey w3-text-grey">
            <span>
                Darstellung des höchsten Siegs und der höchsten Niederlage über alle Spiele. Es werden zuerst die
                Tore/Gegentore beachtet. Bei Gleichheit ist die Differenz ausschlaggebend.
            </span>
        </p>
    </div>
    <?php include "spielergebnisse.tmp.php"; ?>
</div>
