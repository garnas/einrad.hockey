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
<p class="w3-text-secondary"><b>Welche Modusänderung war dringend notwendig?</b></p>
<p>Da fällt mir nichts ein. Die Modusänderungen haben mir persönlich keinen Mehrwert gebracht.<br>Soweit ich das überblicken kann waren die Mannschaften in der Tabelle immer da, wo sie hingehörten. Jetzt ist es etwas schwierig in der Tabelle wieder etwas abzusteigen. Dadurch kann man an manchen Turnieren nicht Teilnehmen und dadurch auch etwas weniger spielen. </p>

<p class="w3-text-secondary"><b>Wie bist du zum Einradhockey gekommen?</b></p>
<p>Ich habe in unserem Dorf Einradfahren gelernt. Das wurde im Jugendzentrum angeboten, zusammen mit Jonglieren. Daraus ist auch ein Kinderzirkus entstanden, der heute noch existiert. Unser damaliger Trainer hatte das Einradfahren an der Uni in Bremen gelernt. Dort gab es eine lebendige Szene mit Einradfahrern und Jongleuren, wo ich dann einige Jahre gefahren bin. Zu Beginn haben wir auch immer Einradhockey mit Unihockeyschlägern gespielt, unter uns.<br>1990 sind wir zu der Europäischen Jonglierconvention in Oldenburg gefahren. Dort in der Outdoorhalle haben wir die Langenfelder kennengelernt, die richtig Einradhockey gespielt haben. Im Herbst desselben Jahres sind wir nach Langenfeld gefahren und haben dort ein Wochenende nur Hockey gespielt.<br>Mit der Zeit waren wir auf einer Europäischen Einradconvention in Brüssel, sind dann noch öfter zu den Langenfeldern gefahren und haben dort ab und an gespielt. Parallel dazu haben wir in Bremen Conventions veranstaltet mit einem großen Einradhockeyblock.<br>Über die Jahre sind immer mehr Mannschaften aufgetaucht und haben sich vernetzt. Daraus ist dann die Liga entstanden. An der wir seit der Gründung teilnehmen.</p>

<p class="w3-text-secondary"><b>Wie bist du zum Einradfahren gekommen?</b></p>
<p>In unserem Jugendzentrum hat mein Trainer damals Einradfahren angeboten, das war 1987. Für uns war das sehr spannend, weil es damals noch nicht populär war. Wir haben die ersten Einräder aus Teilen aus dem Sperrmüll zusammengebaut. Und mühsam Einradfahren gelernt. Da wir nur ein paar Räder hatten haben wir in den Trainingspausen auch Jonglieren gelernt. Mein Trainer hat das Einradfahren an der Uni Bremen gelernt, da sind wir anschließend auch hin.<br><br>Es war damals früher etwas mühsamer mit dem Lernen. Vom Wheelwalk haben wir immer nur gehört und ich konnte nach drei Jahren dann mal nach Kiel fahren, und jemanden Treffen, der mir das zeigen konnte. Wir waren noch lange nicht so gut vernetzt und es gab auch kein YouTube, wo man sich schnell mal ein paar Tutorials anschauen konnte.</p>

<p class="w3-text-secondary"><b>Hättest du zur Gründung der Liga gedacht, dass sie so sein wird, wie sie heute ist?</b></p>
<p>Nö, das konnte ich mir nicht vorstellen.<br>Als wir angefangen haben waren wir eine überschaubare Gruppe und alle in ähnlichem Alter. Dass es jetzt so viele Mannschaften mit Kindern gibt habe ich mir nicht vorstellen können. Aber der ganze Sport ist immer jünger geworden.
</p>

<p class="w3-text-secondary"><b>Was wünschst du dir für die Liga?</b></p>
<p>Sie soll ganz entspannt weiterlaufen. Das wichtigste ist, dass man spielen kann und auch immer wieder neue Mannschaften kennenlernt.</p>

<p class="w3-text-secondary"><b>Was wünschst du dir für den Sport?</b></p>
<p>Er soll weiterhin Spaß machen. Ich glaube nicht, dass wir in meinem Leben noch International werden. Ich mag das familiäre und das wir eigentlich immer gut miteinander auskommen. Das ist bei größeren Sportarten wie zum Beispiel Fußball, nicht so gegeben.</p>

<p class="w3-text-secondary"><b>Welche ist die auffälligste Veränderung seit damals?</b></p>
<p>Durch die Strukturierung, mit der versucht wurde, dass alles etwas zu professionalisieren, sind immer alle dabei zuschauen was regelkonform ist und wie sie mit diesen Regeln noch ein paar Plätze in der Tabelle gut machen können.</p>

<p class="w3-text-secondary"><b>Wenn du eine Regel im Einradhockey verändern dürftest welche wäre es?</b></p>
<p>Ausleihen müsste einfacher werden, so können Turniere einfacher durchgeführt werden. Wenn alle Mannschaften auf einem Turnier mit der Ausleihe einverstanden sind könnte das problemlos geschehen. Eigentlich kennen wir uns untereinander und können zu große Ungerechtigkeiten auf Turnieren vermeiden. </p>

<p class="w3-text-secondary"><b>Was bringt dich dazu dich regelmäßig auf das Einrad zu setzen?</b></p>
<p>Einradhockey macht am meisten Spaß von allem. Ich habe schon einiges ausprobiert, aber Einradhockey macht einfach Sauspaß.

<p class="w3-text-secondary"><b>Wenn du dir nochmal ein komplett neues Team aussuchen müsstest, bei wem würdest du spielen?</b></p>
<p>Da ich nur spielen möchte, kann ich mit jedem spielen. Ich bin aber mit meiner Mannschaft ganz zufrieden.</p>

<p class="w3-text-secondary"><b>Wie ist die Idee zur Gründung der Liga entstanden?</b></p>
<p>Mit der Zunahme an Mannschaften wollten wir versuchen regelmäßig zu spielen und auch das jeder gegen jeden spielen kann. Es wurden auch schon Turniere gespielt, aber eben unregelmäßig. Wir fanden es eine gute Idee, auch wenn wir zu Anfang zu den Außenseitern gehörten, also eine Mannschaft waren die weitere Wege zu den Turnieren fahren musste. Das hat sich zum Glück etwas geändert.</p>


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