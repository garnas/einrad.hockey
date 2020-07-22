<h3 class="w3-text-grey"><?=$daten['teamname']?></h3>
<h3 class="w3-text-primary">Teamfoto</h3>
<form class="w3-card w3-panel" method="post" enctype="multipart/form-data">
        <?php if (empty($daten['teamfoto'])){?>
            <p><i>Es können Bilder im <b>.jpg, .jpeg, .gif, .png</b> Format mit bis zu 11,9 Megabyte hochgeladen werden. Bilder werden webtauglich verarbeitet - exif-Daten der Bilder werden gelöscht.</i></p>
            <p>
                <input class="w3-button w3-block w3-primary" type="file" name="jpgupload" id="jpgupload">
            </p>       
            <p>
                <input type="submit" name="teamfoto" class="w3-button w3-tertiary" value="Teamfoto hochladen">
            </p>
        <?php }else{?>
            <p>
                <img src="<?=$daten['teamfoto']?>" alt="<?=$daten['teamname']?>" class="w3-card w3-image" style="max-height: 360px">
            </p>
            <p>
                <input type="submit" name="delete_teamfoto" class="w3-button w3-tertiary" value="Neues Teamfoto / Teamfoto löschen">
            </p>
        <?php }  //end if?>
        <p class="w3-text-grey">Das Teamfoto wird öffentlich <?=Form::link('../liga/teams.php#' . $daten['team_id'], 'auf der Teams-Seite')?> angezeigt.</p>
</form>

<form method='post'>
    <h3 class="w3-text-primary">Teamdetails</h3>
    <div class="w3-panel w3-card">
        <p>
            <label for='ligavertreter' class="w3-text-primary">Ligavertreter</label>
            <input class='w3-input w3-border w3-border-primary' type='text' id='ligavertreter' name='ligavertreter' required value='<?=$daten['ligavertreter']?>'>
        </p>
        <p>
            <label for='plz' class="w3-text-primary">PLZ</label>
            <input class='w3-input w3-border w3-border-primary' type='number' name='plz' name='plz' value='<?=$daten['plz']?>'>
        </p>
        <p>
            <label for='ort' class="w3-text-primary">Ort</label>
            <input class='w3-input w3-border w3-border-primary' type='text' id='ort' name='ort' value='<?=$daten['ort']?>'>
        </p>
        <p>
            <label for='verein' class="w3-text-primary">Verein</label>
            <input class='w3-input w3-border w3-border-primary' type='text'id='verein' name='verein' value='<?=$daten['verein']?>'>
        </p>
        <p>
            <label for='homepage' class="w3-text-primary">Homepage</label>
            <input class='w3-input w3-border w3-border-primary' type='url' id='homepage' name='homepage' value='<?=$daten['homepage']?>'>
        </p>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <thead>
                    <tr class="w3-primary">
                        <td class="" style="vertical-align:bottom">Email</td>
                        <td class="w3-center" style="vertical-align:bottom">Öffentlich?*</td>
                        <td class="w3-center" style="vertical-align:bottom">Infomails?**</td>
                        <td class="w3-center" style="vertical-align:bottom">Löschen?</td>
                    </tr>
                </thead>
                <?php
                $z=0;
                foreach($akt_team_kontakte->get_all_emails() as $email){
                $selected_info='';
                $selected_public='';
                if ($email['public']=='Nein'){$selected_public='selected';}
                if ($email['get_info_mail']=='Nein'){$selected_info='selected';}
                ?>
                    <tr>
                        <td style='vertical-align: middle'><?=$email['email']?></td>
                        <td class='w3-center'>
                            <select style='max-width: 100px' class='w3-select w3-border w3-border-primary' name='public<?=$email['teams_kontakt_id']?>'>
                                <option value='Ja'>Ja</option>
                                <option value='Nein' <?=$selected_public?> >Nein</option>
                            </select>
                        </td>
                        <td class='w3-center'>
                            <select style='max-width: 100px' class='w3-select w3-border w3-border-primary' name='info<?=$email['teams_kontakt_id']?>'>
                                <option value='Ja'>Ja</option>
                                <option value='Nein' <?=$selected_info?> >Nein</option>
                            </select>
                        </td>
                        <td class='w3-center'>
                            <select style='max-width: 100px' class=' w3-select w3-border w3-border-primary' name='delete<?=$email['teams_kontakt_id']?>'>
                                <option value='Ja'>Ja</option>
                                <option value='Nein' selected>Nein</option>
                            </select>
                        </td>
                    </tr>
                <?php } //Ende foreach?>
            </table>
        </div>
        <p><input class="w3-button w3-secondary" name="change" type='submit' value='Daten ändern'></p>
    </div>
</form>

<form method='post'>
    <h3 class="w3-text-primary">Weitere Email eintragen</h3>
    <div class="w3-container w3-card">
        <p>
            <label for='email' class="w3-text-primary">Email</label>
            <input class='w3-input w3-border w3-border-primary' type='email' id='email' name='email' value='Email'>
        </P>

        <p>
            <label for='public' class="w3-text-primary">Email auf der öffentlichen Webseite anzeigen?*</label>
            <select  style='' class='w3-input w3-border w3-border-primary' id='public' name='public'>
                <option value='Ja' selected>Ja</option>
                <option value='Nein'>Nein</option>
            </select>
        </p>

        <p>
            <label for='get_info_mail' class="w3-text-primary">Automatische Infomails erhalten?**</label>
            <select  style='' class='w3-input w3-border w3-border-primary' id='get_info_mail' name='get_info_mail'>
                <option value='Ja' selected>Ja</option>
                <option value='Nein'>Nein</option>
            </select>
        </p>
        <p>
            <input class='w3-button w3-secondary' name='neue_email' type='submit' value='Email eintragen'>
        </p>
    </div>
    <p class=""><b>Öffentlich*</b>: Deine Email-Adresse wird in der Teamsliste <?=Form::link('../liga/teams.php','hier')?> angezeigt. Andere Ligateams können auch nicht-öffentliche Emails einsehen und kontaktieren.</p>
    <p class=""><b>Infomails**</b>: Du bekommst automatische Infomails, z. B. wenn ein für dein Team relevantes Turnier eingestellt wird.</p>
</form>