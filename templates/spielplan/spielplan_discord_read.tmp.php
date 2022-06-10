<?php
?>
    <h2 class="w3-text-secondary w3-margin-top">Liveticker</h2>

    <?php Html::include_widget_bot(); ?>

    <p>
       Hier könnt ihr den Liveticker direkt in der Discord-App auf euer Handy bekommen:
    </p>
    <p>
        <?= Html::link(Env::LINK_DISCORD, Env::LINK_DISCORD, true, 'bookmark') ?>
    </p>
<?php