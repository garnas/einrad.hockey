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

$header = "w3-display-topleft w3-margin w3-white w3-padding w3-text-primary w3-large";
$answer = "w3-display-bottomleft w3-margin w3-padding";

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////CONTENT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
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
<h1 class="w3-text-primary">Günther</h1>
<div class="w3-primary w3-display-container" style="min-height: 250px;">  
    <div class="slideshow1">
        <p class="<?=$header?>">
            Kurze Fakten
        </p>
        <table class="<?=$answer?>">
            <tr>
                <td style="vertical-align: top; width: 150px;">Spielt seit:</td>
                <td style="vertical-align: top;">1990</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Erstes Team:</td>
                <td style="vertical-align: top;">Uniwheeler (auch heute noch)</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Turniere:</td>
                <td style="vertical-align: top;">ca. 150 + Abschlussturniere</td>
            </tr>
        </table>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1">
        <p class="<?=$header?>">
            Wenn du eine Regel im Einradhockey verändert dürftest welche wäre es?
        </p>
        <p class="<?=$answer?>">
            Ausleihen müsste einfacher werden, so können Turniere einfacher durchgeführt werden.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1">
        <p class="<?=$header?>">
            Was wünschst du dir für den Sport?
        </p>
        <p class="<?=$answer?>">
            Soll weiterhin Spaß machen. Ich mag das familiäre und das wir eigentlich immer gut miteinander ausgkommen.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1">
        <p class="<?=$header?>">
            An welches Turnier kannst du dich noch besonders erinnern und warum?
        </p>
        <p class="<?=$answer?>">
            Genossen habe ich das Turnier in Peking. Da habe ich in einer Mannschaft aus Hong Kong gespielt. Das war lustig, auch wenn wir nichts gerissen haben. Deswegen spiele ich Einradhockey, um immer mal wieder mit anderen zusammen spielen zu können.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
    <div class="slideshow1">
        <p class="<?=$header?>">
            Wie bist du zum Einradfahren gekommen?
        </p>
        <p class="<?=$answer?>">
            In unserem Jugendzentrum hat mein Trainer damals Einradfahren angeboten, das war 1987. Für uns war das sehr spannend, weil es damals noch nicht populär war. Wir haben die ersten Einräder aus Teilen aus dem Sperrmüll zusammengebaut. UNd mühsam Einradfahren gelernt.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 0)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 0)">&#10095;</button>
    </div>
</div>

<!-- Antworten erster Abschnitt -->
<p class="w3-text-secondary"><b>Snackwave typewriter drinking vinegar letterpress</b></p>
<p>I'm baby iceland synth activated charcoal lo-fi copper mug williamsburg normcore hella banjo banh mi adaptogen narwhal squid typewriter. Fam distillery brunch edison bulb portland selvage sustainable four loko lyft man braid. Offal raw denim small batch wolf food truck asymmetrical lumbersexual truffaut kinfolk try-hard pork belly beard. Synth la croix put a bird on it schlitz chartreuse viral narwhal 8-bit pok pok brunch taxidermy humblebrag kale chips. Irony helvetica retro twee art party. Fanny pack XOXO cronut flannel, sriracha bicycle rights street art blog. Kickstarter hell of bespoke drinking vinegar normcore, four loko gochujang kogi hoodie wayfarers photo booth knausgaard street art.</p>

<p class="w3-text-secondary"><b>Cred 90's flexitarian</b></p>
<p>Fashion axe prism portland, meh enamel pin hoodie distillery chartreuse kinfolk health goth 90's. Gentrify narwhal man bun viral poke, swag mumblecore fashion axe messenger bag shabby chic palo santo blue bottle wayfarers squid biodiesel. Mustache taxidermy normcore brunch. 8-bit celiac viral, pok pok four dollar toast cray microdosing. Mustache keffiyeh mixtape drinking vinegar plaid butcher fixie.</p>

<p class="w3-text-secondary"><b>Snackwave poutine poke vinyl dreamcatcher occupy. Kogi XOXO hoodie, sartorial chambray PBR&B sustainable chillwave vaporware gastropub</b></p>
<p>Gluten-free brooklyn artisan shoreditch kitsch ugh four loko paleo pork belly raclette brunch. Fanny pack photo booth pabst, messenger bag next level readymade bushwick sriracha prism etsy activated charcoal. Taiyaki four loko tilde VHS. Pok pok sustainable hot chicken hashtag narwhal brooklyn iceland franzen tumeric green juice tumblr craft beer VHS. Tilde pour-over retro, gastropub master cleanse keffiyeh four loko organic keytar prism. Kogi organic meh, quinoa unicorn chia tumeric pok pok try-hard cold-pressed. Williamsburg man braid tacos schlitz sustainable 8-bit affogato.</p>

<!-- Zweiter Spielerabschnitt -->
<h1 class="w3-text-primary">Robert</h1>
<div class="w3-primary w3-display-container" style="min-height: 250px;">  
    <div class="slideshow2">
        <p class="<?=$header?>">
            Kurze Fakten
        </p>
        <table class="<?=$answer?>">
            <tr>
                <td style="vertical-align: top; width: 150px;">Spielt seit:</td>
                <td style="vertical-align: top;">1990</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Erstes Team:</td>
                <td style="vertical-align: top;">Radlos (gelb) - bis heute</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Lieblingsgegner:</td>
                <td style="vertical-align: top;">Mehrere (z.B. Wirbelsturm Bonlanden, Stolpervögel Breckenheim)</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Turniere:</td>
                <td style="vertical-align: top;">ca. 200</td>
            </tr>
        </table>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 1)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 1)">&#10095;</button>
    </div>
    <div class="slideshow2">
        <p class="<?=$header?>">
            Hättest du zur Gründung der Liga gedacht, dass sie so sein wird, wie sie heute ist?
        </p>
        <p class="<?=$answer?>">
            Nein. Ich war aber auch unvoreingenommen und ohne Vorstellungen und Wünsche, wie sie sich entwickeln würde und ließ es auf mich zukommen. Ich war aber gespannt, wie und was daraus gemacht werden würde.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 1)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 1)">&#10095;</button>
    </div>
    <div class="slideshow2">
        <p class="<?=$header?>">
            Welche Modusänderung war dringend notwendig?
        </p>
        <p class="<?=$answer?>">
            Die Teilung der Liga in verschieden starke Bereiche.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 1)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 1)">&#10095;</button>
    </div>
    <div class="slideshow2">
        <p class="<?=$header?>">
            An welches Turnier kannst du dich noch besonders erinnern und warum?
        </p>
        <p class="<?=$answer?>">
            Eher an einzelne Szenen, als an ganze Turniere. Aber zum Beispiel das UNICON Einradhockeyweltmeisterschaftsturnier in Bottrop 1998 beim Finale mit Huunderten tobenden Zuschauern und grandioser Stimmung.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 1)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 1)">&#10095;</button>
    </div>
</div>

<!-- Antworten zweiter Abschnitt -->
<p class="w3-text-secondary"><b>Snackwave typewriter drinking vinegar letterpress</b></p>
<p>I'm baby iceland synth activated charcoal lo-fi copper mug williamsburg normcore hella banjo banh mi adaptogen narwhal squid typewriter. Fam distillery brunch edison bulb portland selvage sustainable four loko lyft man braid. Offal raw denim small batch wolf food truck asymmetrical lumbersexual truffaut kinfolk try-hard pork belly beard. Synth la croix put a bird on it schlitz chartreuse viral narwhal 8-bit pok pok brunch taxidermy humblebrag kale chips. Irony helvetica retro twee art party. Fanny pack XOXO cronut flannel, sriracha bicycle rights street art blog. Kickstarter hell of bespoke drinking vinegar normcore, four loko gochujang kogi hoodie wayfarers photo booth knausgaard street art.</p>

<p class="w3-text-secondary"><b>Cred 90's flexitarian</b></p>
<p>Fashion axe prism portland, meh enamel pin hoodie distillery chartreuse kinfolk health goth 90's. Gentrify narwhal man bun viral poke, swag mumblecore fashion axe messenger bag shabby chic palo santo blue bottle wayfarers squid biodiesel. Mustache taxidermy normcore brunch. 8-bit celiac viral, pok pok four dollar toast cray microdosing. Mustache keffiyeh mixtape drinking vinegar plaid butcher fixie.</p>

<p class="w3-text-secondary"><b>Snackwave poutine poke vinyl dreamcatcher occupy. Kogi XOXO hoodie, sartorial chambray PBR&B sustainable chillwave vaporware gastropub</b></p>
<p>Gluten-free brooklyn artisan shoreditch kitsch ugh four loko paleo pork belly raclette brunch. Fanny pack photo booth pabst, messenger bag next level readymade bushwick sriracha prism etsy activated charcoal. Taiyaki four loko tilde VHS. Pok pok sustainable hot chicken hashtag narwhal brooklyn iceland franzen tumeric green juice tumblr craft beer VHS. Tilde pour-over retro, gastropub master cleanse keffiyeh four loko organic keytar prism. Kogi organic meh, quinoa unicorn chia tumeric pok pok try-hard cold-pressed. Williamsburg man braid tacos schlitz sustainable 8-bit affogato.</p>

<!-- Dritter Spielerabschnitt -->
<h1 class="w3-text-primary">Adrian</h1>
<div class="w3-primary w3-display-container" style="min-height: 250px;">  
    <div class="slideshow3">
        <p class="<?=$header?>">
            Kurze Fakten
        </p>
        <table class="<?=$answer?>">
            <tr>
                <td style="vertical-align: top; width: 150px;">Spielt seit:</td>
                <td style="vertical-align: top;">ca. 1996</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Erstes Team:</td>
                <td style="vertical-align: top;">VE Berlin (VEB = Volkseigener Betrieb aus der DDR)</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Lieblingsgegner:</td>
                <td style="vertical-align: top;">Wupper Piraten</td>
            </tr>
            <tr>
                <td style="vertical-align: top; width: 150px;">Turniere:</td>
                <td style="vertical-align: top;">ca. 60</td>
            </tr>
        </table>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 2)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 2)">&#10095;</button>
    </div>
    <div class="slideshow3">
        <p class="<?=$header?>">
            An welches Turnier kannst du dich noch besonders erinnern?
        </p>
        <p class="<?=$answer?>">
            Wuppertal (2011?) weil ich mir beim Sackhüpfen am Vorabend im Vereinsheim eine blaube Nase geholt habe.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 2)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 2)">&#10095;</button>
    </div>
    <div class="slideshow3">
        <p class="<?=$header?>">
            Wie bist du zum Einradfahren gekommen?
        </p>
        <p class="<?=$answer?>">
            Mein Sportlehrer am Gynmasium hat das Buch "Einradfahren, Vom Anfänger zum Könner" geschrieben und es mir beigebracht.
        </p>
        <button class="w3-button w3-light-grey w3-display-left w3-opacity" onclick="plusDivs(-1, 2)">&#10094;</button>
        <button class="w3-button w3-light-grey w3-display-right w3-opacity" onclick="plusDivs(1, 2)">&#10095;</button>
    </div>
</div>


<!-- Script für die Slideshow -->
<script>
var slideIndex = [1, 1, 1];
var slideId = ["slideshow1", "slideshow2", "slideshow3"];
showDivs(1,0);
showDivs(1,1);
showDivs(1,2);

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