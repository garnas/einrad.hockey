<?php

// Logik
require_once '../../../../init.php';
$now = new DateTime();

// Layout
Html::$titel = "Adventskalender | Deutsche Einradhockeyliga";
Html::$content = "Adventskalender der Deutschen Einradhockeyliga für das Jahr 2025.";

include '../../../../templates/header.tmp.php'; ?>

<link type="text/css" rel="stylesheet" href="../style.css">
<link type="text/css" rel="stylesheet" href="../colors.css">

<div class="w3-display-container w3-round-xlarge w3-padding-16" style="margin-top: 16px;">
    <?php if ($now >= new DateTime('2025-12-07 08:00')): ?>

        <div class="w3-section" style="line-height: 1.2;">
            <h1 class="advent-text-secondary">Die wohl organisatorisch aufwendigste Meisterschaft - Throwback Deutsche Meisterschaft 2019</h1>
        </div>

        <div class="w3-section" style="">
            <p style="font-style: italic; background-color: #edddd1; padding: 8px 16px;">
                2019 fand eine ganz besondere Meisterschaft in Wuppertal statt. Alle Meisterschaften, von A bis D, wurden an 
                einem einzigen Ort ausgetragen. Ein einzigartiges Erlebnis, besonders für die Ausrichter. Aber wie war es für die 
                Wupper-Löwen, ein so großes Turnier zu organisieren? Wir haben nachgefragt und für euch einen Erfahrungsbericht aus 
                den Antworten zusammengestellt, die wir von den Wupper-Löwen zu unseren Fragen bekommen haben.
            </p>
        </div>

        <div class="w3-section">
            <img src="../../../bilder/advent/Jzvmrk7fTY/door.jpg" class="w3-image w3-round w3-right" style="width:25%; margin-left:16px;">
            <p>
                Die Planung begann etwa 3 bis 6 Monate im Voraus und war sehr intensiv. Zum Glück übernahm der Ligaausschuss die 
                Organisation der Spiele und der Meisterschaft selbst. Wir als Ausrichter bildeten ein Kernteam von Ansprechpartner*innen, 
                die die Gesamtkoordination im Blick hatten.
            </p>
            <p>
                Als wir die Nachricht vom Ligaausschuss erhielten, dass alle Meisterschaften in Wuppertal stattfinden würden, 
                war die Vorfreude unter uns allen riesig. Es sollte ein großes Treffen aller Einradhockeyspieler und -mannschaften auf 
                allen Niveaus werden. Ein Wochenende, das nicht nur die Meisterschaft feierte, sondern auch die Freude an diesem 
                besonderen Sport und der Geselligkeit teilte. Das war unsere Idee. Zudem liegt unsere Region in NRW relativ zentral 
                und ist für Mannschaften aus ganz Deutschland gut erreichbar.
            </p>
            <p>
                Trotz der großen Vorfreude stießen wir schnell auf die ersten großen Herausforderungen: Die Kalkulation der Zahl der 
                erwarteten Teilnehmer*innen in Bezug auf Verpflegung, Unterkunft, Anreise und so weiter. Außerdem war es schwer abzuschätzen, 
                wie viele Zuschauer kommen würden.
            </p>
            <p>
                Für die Verpflegung hatten wir eine besondere Idee: eine Pastaparty! Dafür verfolgten wir eine eher unkonventionelle 
                Methode: Über mehrere Wochen kauften wir montagnachmittags im Werksverkauf Würstchen und Pasta ein.
            </p>
            <p>
                Dann kam plötzlich noch eine neue Idee auf: Ähnlich wie bei den Unicon-Conventions wollten wir Meisterschafts-T-Shirts 
                gestalten. Es ging an die Arbeit:  ein Logo musste entworfen, die Mannschaften informiert, Bestellungen und 
                Finanzen kalkuliert und schließlich die Bestellung aufgegeben werden. Insgesamt bestellten wir 250 Shirts und haben 
                am Ende fast alle verkauft - nur ein paar wenige sind übrig geblieben.
            </p>
            <p>
                Doch dann gab es ein Problem, mit dem niemand gerechnet hatte: In der Halle gab es keine geeigneten Bande für 
                das Spiel, also mussten diese extra gebaut werden!
            </p>
            <p>
                Kurz vor der Meisterschaft hieß es dann: Presse einladen und fleißig Schilder schreiben.
            </p>
            <p>
                Und dann war der große Tag gekommen - für uns bedeutete das vor allem: Spülen! Um die Umwelt zu schonen, verzichteten 
                wir auf Wegwerfgeschirr, was aber leider eine Menge Spülarbeit mit sich brachte. Dabei stellten wir fest: Das ganze 
                Organisieren beeinflusste unsere eigene Spielweise beim Turnier. Aber da bei uns immer der Spaß am gemeinsamen 
                Sport im Vordergrund steht, machte es uns nichts aus, wenn wir nicht ganz so erfolgreich auf dem Spielfeld waren. 
                Hauptsache, wir durften dabei sein, das war für uns genug.
            </p>
            <p>
                Wir hatten außerdem großartige Unterstützung von der Mannschaft aus Remscheid und vielen anderen lieben Helfer*innen.
            </p>
            <p>
                Heute denken wir jedes Mal, wenn wir das T-Shirt der Deutschen Meisterschaft 2019 in Wuppertal bei einem Turnier 
                sehen, an diese stressige und herausfordernde Zeit zurück. Wir sind stolz auf uns, dass wir ein so großes Event 
                stemmen konnten. Schließlich haben wir auch viel positives Feedback für unsere Arbeit erhalten.
            </p>
        </div>
    <?php else: ?>
    
        <div class="slide-container"><img class="slide" src="../../../bilder/advent/time0800.jpg" /></div>
    
    <?php endif; ?>
</div>

<?php include '../../../../templates/footer.tmp.php'; ?>