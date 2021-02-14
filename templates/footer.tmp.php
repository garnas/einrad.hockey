                </div>
            </div>
        </main>
        <footer class="w3-container w3-margin-top w3-center w3-cell-bottom w3-primary">
            <div class="w3-center">
                <a href="<?= Config::BASE_URL ?>/liga/kontakt.php" class="w3-button">
                    <?= Form::icon('mail') ?> Kontakt
                </a>
                <a href="<?= Config::LINK_FACE ?>" class="w3-button" target="_blank" rel="noopener noreferrer">
                    <?= Form::icon('group_add') ?> Facebook
                </a>
                <a href="<?= Config::LINK_INSTA ?>" class="w3-button" target="_blank" rel="noopener noreferrer">
                    <?= Form::icon('camera_alt') ?> Instagram
                </a>
            </div>
            <div class="w3-center">
                <a href="<?= Config::BASE_URL ?>/liga/ligaleitung.php" class="w3-button">
                    <?= Form::icon('account_box') ?> Ligaleitung
                </a>
                <a href="<?= Config::BASE_URL ?>/liga/datenschutz.php" class="w3-button">
                    <?= Form::icon('security') ?> Datenschutz
                </a>
                <a href="<?= Config::BASE_URL ?>/liga/impressum.php" class="w3-button">
                    <?= Form::icon('view_headline') ?> Impressum
                </a>
            </div>
        </footer>
    </body>
</html>

<?php
// Logs der Besucher
Form::log("log_visits.log",
    $_SERVER['REQUEST_URI']
        . " | " . round(microtime(TRUE) - $_SERVER["REQUEST_TIME_FLOAT"], 3) . " s (Load)"
        . " | " . dbi::$db->query_count . " (Querys)");