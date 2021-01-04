<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "25 Jahre | Deutsche Einradhockeyliga";
$content = "Spielerprofile für das 25. Jubiläum der Deutschen Einradhockeyliga";
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">25 Jahre Deutsche Einradhockeyliga</h1>

<p>
I'm baby iceland hell of wolf direct trade, narwhal ennui church-key woke fingerstache distillery PBR&B aesthetic bicycle rights green juice. Single-origin coffee actually bitters man bun kickstarter DIY tumblr four dollar toast shoreditch yr trust fund la croix organic. Woke vegan tousled lyft. Snackwave drinking vinegar raw denim, gastropub health goth before they sold out beard blog artisan man bun subway tile venmo tilde literally. Asymmetrical shabby chic echo park intelligentsia food truck la croix. Freegan marfa subway tile tumeric cronut.
</p>
<p>
Vinyl sartorial flexitarian roof party aesthetic, dreamcatcher migas normcore paleo lomo helvetica cold-pressed church-key subway tile. Offal banjo fashion axe, normcore waistcoat food truck pork belly jean shorts af portland neutra tumeric echo park copper mug kale chips. Pabst asymmetrical fixie readymade. Kitsch +1 pickled swag. 8-bit thundercats vape polaroid cronut scenester lomo retro hexagon pinterest iceland keytar sriracha.
</p>
<p>
Everyday carry tilde food truck fanny pack jean shorts, blue bottle yr DIY selvage cliche whatever. IPhone organic fingerstache, VHS four loko photo booth bushwick lo-fi brunch you probably haven't heard of them chicharrones kitsch. Letterpress fixie authentic, intelligentsia ennui yuccie craft beer photo booth gluten-free street art raw denim. Shabby chic poke tbh tote bag, hell of leggings williamsburg trust fund. La croix chillwave wolf, lo-fi actually raclette vexillologist pop-up fixie lumbersexual cold-pressed.
</p>
<p>
Subway tile meditation mlkshk pork belly fam cronut. Williamsburg wolf snackwave normcore heirloom franzen iceland ugh. Crucifix taxidermy fanny pack listicle, street art whatever blue bottle vegan +1 chartreuse hell of readymade meditation. Pinterest godard freegan pug jianbing. La croix bitters offal slow-carb hammock etsy asymmetrical.
</p>
<p>
Iceland banjo dreamcatcher, snackwave marfa aesthetic vape photo booth YOLO godard ethical. Mumblecore lyft glossier mixtape, bushwick gastropub fam mustache 8-bit post-ironic single-origin coffee master cleanse cronut. Pok pok actually cloud bread migas readymade put a bird on it four loko small batch aesthetic gluten-free. Fixie artisan iceland bespoke af swag ennui beard man braid hot chicken. Fam thundercats keffiyeh roof party etsy, portland heirloom chillwave tacos fingerstache.
</p>

<!-- Erster Spielerabschnitt -->
<div class="w3-primary w3-round w3-display-container" style="min-height: 250px;">  
    <div class="slideshow1 w3-container">
        <h1 class="w3-text-primary w3-padding w3-white w3-round">Günther</h1>
        <table class="w3-table">
            <tr>
                <td style="vertical-align: top; width: 150px;">Spielt seit:</td>
                <td style="vertical-align: top;">1990</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Erstes Team:</td>
                <td style="vertical-align: top;">Uniwheeler (auch heute noch) </td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Turniere:</td>
                <td style="vertical-align: top;">ca. 150 + Abschlussturniere</td>
            </tr>
        </table>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1 w3-container">
        <p class="w3-display-topleft w3-margin w3-white w3-padding w3-round w3-text-primary w3-large">
            Wenn du eine Regel im Einradhockey verändert dürftest welche wäre es?
        </p>
        <p class="w3-display-bottomleft w3-margin w3-padding">
            Ausleihen müsste einfacher werden, so können Turniere einfacher durchgeführt werden.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1 w3-container">
        <p class="w3-display-topleft w3-margin w3-white w3-padding w3-round w3-text-primary w3-large">
            Was wünschst du dir für den Sport?
        </p>
        <p class="w3-display-bottomleft w3-margin w3-padding">
            Soll weiterhin Spaß machen. Ich mag das familiäre und das wir eigentlich immer gut miteinander ausgkommen.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1 w3-container">
        <p class="w3-display-topleft w3-margin w3-white w3-padding w3-round w3-text-primary w3-large">
            An welches Turnier kannst du dich noch besonders erinnern und warum?
        </p>
        <p class="w3-display-bottomleft w3-margin w3-padding">
            Genossen habe ich das Turnier in Peking. Da habe ich in einer Mannschaft aus Hong Kong gespielt. Das war lustig, auch wenn wir nichts gerissen haben. Deswegen spiele ich Einradhockey, um immer mal wieder mit anderen zusammen spielen zu können.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1 w3-container">
        <p class="w3-display-topleft w3-margin w3-white w3-padding w3-round w3-text-primary w3-large">
            Wie bist du zum Einradfahren gekommen?
        </p>
        <p class="w3-display-bottomleft w3-margin w3-padding">
            In unserem Jugendzentrum hat mein Trainer damals Einradfahren angeboten, das war 1987. Für uns war das sehr spannend, weil es damals noch nicht populär war. Wir haben die ersten Einräder aus Teilen aus dem Sperrmüll zusammengebaut. UNd mühsam Einradfahren gelernt.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
</div>

<!-- Antworten erster Abschnitt -->

<!-- Script für die Slideshow -->
<script>
var slideIndex = [1];
var slideId = ["slideshow1"];
showDivs(1,0);

function plusDivs(n, no) {
  showDivs(slideIndex[no] += n, no);
}

function showDivs(n, no) {
  var i;
  var x = document.getElementsByClassName(slideId[no]);
  if (n > x.length) {slideIndex[no] = 1}
  if (n < 1) {slideIndex[no] = x.length}
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";  
  }
  x[slideIndex[no]-1].style.display = "block";  
}
</script>


<?php include '../../templates/footer.tmp.php';