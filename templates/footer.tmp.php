                </div>
            </div>
        </main>
        <footer class="w3-container w3-margin-top w3-center w3-cell-bottom w3-primary">
            <div class="w3-center">
                <a href="<?= Env::BASE_URL ?>/liga/kontakt.php" class="w3-button">
                    <?= Html::icon('mail') ?> Kontakt
                </a>
                <a href="<?= Nav::LINK_FACE ?>" class="w3-button" target="_blank" rel="noopener noreferrer">
                    <?= Html::icon('group_add') ?> Facebook
                </a>
                <a href="<?= Nav::LINK_INSTA ?>" class="w3-button" target="_blank" rel="noopener noreferrer">
                    <?= Html::icon('camera_alt') ?> Instagram
                </a>
                <a href="<?= Nav::LINK_FORUM ?>" class="w3-button">
                    <?= Html::icon('chat') ?> Forum
                </a>
            </div>
            <div class="w3-center">
                <a href="<?= Env::BASE_URL ?>/liga/ligaleitung.php" class="w3-button">
                    <?= Html::icon('account_box') ?> Ligaleitung
                </a>
                <a href="<?= Nav::LINK_GIT ?>" class="w3-button" target="_blank" rel="noopener noreferrer">
                    <?= Html::icon('flutter_dash') ?> GitHub
                </a>
                <a href="<?= Env::BASE_URL ?>/liga/datenschutz.php" class="w3-button">
                    <?= Html::icon('security') ?> Datenschutz
                </a>
                <a href="<?= Env::BASE_URL ?>/liga/impressum.php" class="w3-button">
                    <?= Html::icon('view_headline') ?> Impressum
                </a>
            </div>
        </footer>
    </body>
</html>