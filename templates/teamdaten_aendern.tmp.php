<h3 class="w3-text-grey"><?= $team->getName() ?></h3>


<form class="w3-card w3-panel" method="post" enctype="multipart/form-data">
    <h2 class="w3-text-primary"><?= Html::icon("group", 16,28 ) ?> Teamfoto</h2>
    <?php if (empty($team->getDetails()->getTeamfoto())) { ?>
        <p>
            <i>Es können Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden.</i>
        </p>
        <p>
            <input required class="w3-button w3-block w3-primary" type="file" name="jpgupload" id="jpgupload">
        </p>
        <p>
            <button type="submit" name="teamfoto" class="w3-button w3-tertiary w3-block">
                <?= Html::icon("arrow_circle_up") ?> Teamfoto hochladen
            </button>
        </p>
    <?php } else { ?>
        <p>
            <img src="<?= $team->getDetails()->getTeamfoto() ?>" alt="<?= $team->getName() ?>" class="w3-card w3-image"
                 style="max-height: 360px">
        </p>
        <p>
            <input type="submit" name="delete_teamfoto" class="w3-button w3-tertiary"
                   value="Neues Teamfoto / Teamfoto löschen">
        </p>
    <?php }  //end if?>
    <p class="w3-text-grey">
        Das Teamfoto kann öffentlich <?= Html::link('../liga/teams.php#' . $team->id(), 'auf der Teams-Seite') ?>
        eingesehen werden.
    </p>
</form>
<div class="w3-card-4 w3-panel">
    <h2 id="trikotfarbe" class="w3-text-primary">
        <?= Html::icon("brush", 16,28 ) ?> Trikotfarben
    </h2>
    <div class="w3-row-padding w3-center w3-strech">
        <div class="w3-half">
            <form method="post">
                <p>
                    1. Trikotfarbe
                    <button type="submit"
                            class="w3-hover-text-secondary w3-text-primary w3-white"
                            name="no_color_1"
                            style='cursor: pointer; border: 0px;'
                    >
                        <?= Html::icon('delete') ?>
                    </button>
                </p>
            </form>
            <form method="post">
                <label for="color_1" style="cursor:pointer;">
                    <span class="w3-card-4" style="height:70px;width:70px;background-color:<?= empty($team->getDetails()->getTrikotFarbe1()) ? '#bbb' : $team->getDetails()->getTrikotFarbe1()?>;border-radius:50%;display:inline-block;">
                        <br><?= (empty($team->getDetails()->getTrikotFarbe1())) ? Html::icon('not_interested') : '' ?>
                    </span>

                </label>
                <p>
                    <input type='color'
                           name='color_1'
                           id='color_1'
                           class="w3-white"
                           value="<?= empty($team->getDetails()->getTrikotFarbe1()) ? '#bbbbbb' : $team->getDetails()->getTrikotFarbe1() ?>"
                           style="cursor: pointer;"
                           onchange="this.form.submit()"
                    >
                    <label class="w3-text-primary w3-hover-text-secondary" for="color_1" style="cursor:pointer">
                        <?= Html::icon("brush") ?> 1. Farbe wählen
                    </label>
                </p>
            </form>
        </div>
        <div class="w3-half">
            <form method="post">
                <p>
                    2. Trikotfarbe
                    <button type="submit"
                            class="w3-hover-text-secondary w3-text-primary w3-white"
                            name="no_color_2"
                            style='cursor: pointer; border: 0px;'
                    >
                        <?= Html::icon('delete') ?>
                    </button>
                </p>
            </form>
            <form method="post">
                <label for="color_2" style="cursor:pointer;">
                    <span class="w3-card-4" style="height:70px;width:70px; background-color:<?= empty($team->getDetails()->getTrikotFarbe2()) ? '#bbb' : $team->getDetails()->getTrikotFarbe2() ?>;border-radius:50%;display:inline-block;">
                        <br><?= (empty($team->getDetails()->getTrikotFarbe2())) ? Html::icon('not_interested') : '' ?>
                    </span>
                </label>
                <p>
                    <input type='color'
                           name='color_2'
                           id='color_2'
                           class="w3-white"
                           value="<?= empty($team->getDetails()->getTrikotFarbe2()) ? '#bbbbbb' : $team->getDetails()->getTrikotFarbe2() ?>"
                           style="cursor: pointer;"
                           onchange="this.form.submit()"
                    >
                    <label class="w3-text-primary w3-hover-text-secondary" for="color_2" style="cursor:pointer">
                        <?= Html::icon("brush") ?> 2. Farbe wählen
                    </label>
                </p>
            </form>
        </div>
    </div>
    <p class="w3-text-grey">
        Eure Trikotfarben werden im Spielplan angezeigt. Sie helfen anderen Teams bei der Wahl ihrer Trikots und
        Zuschauern dein Team zu identifizieren. Die Farbe eures 1. Trikots wird bevorzugt im Spielplan angegeben.
    </p>
</div>
<form method='post' class="w3-panel w3-card">
    <h2 class="w3-text-primary"><?= Html::icon("info", 16,28 ) ?> Teamdetails</h2>
    <p>
        <label for='ligavertreter' class="w3-text-primary">Ligavertreter</label>
        <input class='w3-input w3-border w3-border-primary'
               type='text'
               id='ligavertreter'
               name='ligavertreter'
               required
               value='<?= $team->getDetails()->getLigavertreter() ?>'
        >
        <span class="w3-text-grey">
            <i>Nur eine Person kann als Ligavertreter angegeben werden</i>
        </span>
    </p>
    <p>
        <input type="checkbox"
               <?= !empty($team->getDetails()->getLigavertreter()) ? 'checked' : '' ?>
               class="w3-check"
               value="zugestimmt"
               name="dsgvo"
               id="dsgvo">
        <label for="dsgvo" style="cursor: pointer">
            Der Ligavertreter hat die <?= Html::link(Nav::LINK_DSGVO, "Datenschutz-Hinweise") ?>
            gelesen und ihnen zugestimmt.
        </label>
    </p>
    <p>
        <label for='plz' class="w3-text-primary">PLZ</label>
        <input class='w3-input w3-border w3-border-primary'
               type='number'
               id='plz'
               name='plz'
               value='<?= $team->getDetails()->getPlz() ?>'
        >
    </p>
    <p>
        <label for='ort' class="w3-text-primary">Ort</label>
        <input class='w3-input w3-border w3-border-primary'
               type='text'
               id='ort'
               name='ort'
               value='<?= $team->getDetails()->getOrt()?>'
        >
    </p>
    <p>
        <label for='verein' class="w3-text-primary">Verein</label>
        <input class='w3-input w3-border w3-border-primary'
               type='text'
               id='verein'
               name='verein'
               value='<?= $team->getDetails()->getVerein() ?>'
        >
    </p>
    <p>
        <label for='homepage' class="w3-text-primary">Homepage</label>
        <input class='w3-input w3-border w3-border-primary'
               type='url'
               id='homepage'
               name='homepage'
               placeholder='Muss mit https:// oder http:// beginnen.'
               value='<?= $team->getDetails()->getHomepage() ?>'>
    </p>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped w3-centered">
            <thead>
                <tr class="w3-primary">
                    <td class="" style="vertical-align:bottom">Email</td>
                    <td class="w3-center" style="vertical-align:bottom">Öffentlich?*</td>
                    <td class="w3-center" style="vertical-align:bottom">Infomails?**</td>
                    <td class="w3-center" style="vertical-align:bottom">Löschen?</td>
                </tr>
            </thead>
            <?php foreach ($emails as $email) { ?>
                <tr>
                    <td style='vertical-align: middle'><?= $email['email'] ?></td>
                    <td>
                        <select style='max-width: 100px'
                                class='w3-select w3-border w3-centered w3-border-primary'
                                name='public<?= $email['teams_kontakt_id'] ?>'
                        >
                            <option value='Ja'>
                                Ja
                            </option>
                            <option value='Nein' <?= ($email['public'] === 'Nein') ? 'selected' : '' ?> >
                                Nein
                            </option>
                        </select>
                    </td>
                    <td>
                        <select style='max-width: 100px'
                                class='w3-select w3-border w3-border-primary'
                                name='info<?= $email['teams_kontakt_id'] ?>'
                        >
                            <option value='Ja'>
                                Ja
                            </option>
                            <option value='Nein' <?= ($email['get_info_mail'] === 'Nein') ? 'selected' : '' ?> >
                                Nein
                            </option>
                        </select>
                    </td>
                    <td>
                        <select style='max-width: 100px'
                                class=' w3-select w3-border w3-border-primary'
                                name='delete<?= $email['teams_kontakt_id'] ?>'>
                            <option value='Ja'>Ja</option>
                            <option value='Nein' selected>Nein</option>
                        </select>
                    </td>
                </tr>
            <?php } //End foreach?>
        </table>
    </div>
    <p>
        <button class="w3-button w3-tertiary w3-block"
               type='submit'
               name="teamdaten_aendern"
               >
            <?= Html::icon("save_alt") ?> Teamdaten speichern
        </button>
    </p>
</form>

<form method='post' class="w3-card-4 w3-panel">
    <h2 class="w3-text-primary"><?= Html::icon("mail", 16,28 ) ?> Email hinzufügen</h2>
        <p>
            <label for='email' class="w3-text-primary">Email</label>
            <input class='w3-input w3-border w3-border-primary'
                   type='email'
                   id='email'
                   name='email'
                   placeholder='Email-Adresse eingeben'
            >
        </p>
        <p>
            <label for='public' class="w3-text-primary">Email auf der öffentlichen Webseite anzeigen?*</label>
            <select class='w3-input w3-border w3-border-primary'
                    id='public'
                    name='public'
            >
                <option value='Ja' selected>Ja</option>
                <option value='Nein'>Nein</option>
            </select>
        </p>

        <p>
            <label for='get_info_mail' class="w3-text-primary">Automatische Infomails erhalten?**</label>
            <select class='w3-input w3-border w3-border-primary'
                    id='get_info_mail'
                    name='get_info_mail'
            >
                <option value='Ja' selected>Ja</option>
                <option value='Nein'>Nein</option>
            </select>
        </p>
        <p>
            <button class='w3-button w3-tertiary w3-block'
                    name='neue_email'
                    type='submit'
                    >
                <?= Html::icon("add") ?> Email hinzufügen
            </button>
        </p>
    <p>
        <b>Öffentlich*</b>: Deine Email-Adresse wird in der Teamsliste
        <?= Html::link('../liga/teams.php', 'hier') ?> angezeigt. Andere Ligateams können nicht-öffentliche
        Emails auf der Webseite nicht einsehen, dafür aber im Kontaktcenter anschreiben.
    </p>
    <p class="">
        <b>Infomails**</b>: Du bekommst automatische Infomails, z. B. wenn ein für dein Team relevantes Turnier
        eingestellt wird.
    </p>
</form>