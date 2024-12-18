<h2 class="w3-text-secondary w3-margin-top">Liveticker</h2>

<?php Html::include_widget_bot(); ?>
    <p>
        Der Liveticker funktioniert dieses Jahr über Discord, so kann man auch direkt Benachrichtigungen auf sein Handy
        bekommen.
    </p>
    <p>
        --> Ergebnisse die ihr unten eintragt, werden automatisch an den Channel gesendet. <--
    </p>
    <p>
        Zurzeit kann bei Guestmode erstmal jeder in den Liveticker Nachrichten senden und zum Beispiel Fragen
        stellen. Sollte wir das irgendwann abstellen müssen, dann könnt ihr
        weiterhin mit den Discord-Zugangsdaten technik@einrad.hockey (pw: einrad+live) Nachrichten versenden.
    </p>
    <p>
        Wenn ihr einen eigenen Discord-Account habt, dann könnt ihr über den Link unten auch direkt via Handy in den Liveticker schreiben,
        oder sogar Livebilder teilen.
    </p>
    <p>
        <?= Html::link(Env::LINK_DISCORD, Env::LINK_DISCORD, true, 'bookmark') ?>
    </p>
