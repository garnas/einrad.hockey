<?php if (!empty($daten)){ //Wird nur angezeigt, wenn daten zum ausfüllen übertragen worden sind?> 
    <h3 class="w3-text-grey"><?=$daten['teamname']?></h3>
    <h3 class="w3-text-primary">Teamfoto</h3>
    <?php if (!empty($daten['teamfoto'])){?>
        <p>
            <img src="<?=$daten['teamfoto']?>" class="w3-card w3-image" alt="<?=$daten['teamname']?>" style="max-height: 360px;">
        </p>
    <?php }else{?>
        <p class="w3-text-grey">Es wurde noch kein Teamfoto hochgeladen.</p>
    <?php } //end if?>
        
    <h3 class="w3-text-primary">Teamdaten</h3>
    <div class="w3-responsive w3-card-4">
        <table class="w3-table w3-striped">
            <tr>
                <th class="w3-primary" style="width: 140px">Teamname</th>
                <td><b><?=$daten['teamname']?></b></td>
            </tr>
            <tr>
                <th class="w3-primary">Team ID</th>
                <td><?=$daten['team_id']?></td>
            </tr>
            <tr>
                <th class="w3-primary">Freilose</th>
                <td><?=$daten['freilose']?></td>
            </tr>
            <tr>
                <th class="w3-primary">Ligavertreter</th>
                <td><?=$daten['ligavertreter']?></td>
            </tr>
            <tr>
                <th class="w3-primary" style="width: 140px">PLZ</th>
                <td><?=$daten['plz']?></td>
            </tr>
            <tr>
                <th class="w3-primary">Ort</th>
                <td><?=$daten['ort']?></td>
            </tr>
            <tr>
                <th class="w3-primary">Verein</th>
                <td><?=$daten['verein']?></td>
            </tr>
            <tr>
                <th class="w3-primary">Homepage</th>
                <td><a href="<?=$daten['homepage']?>" class="w3-text-blue no w3-hover-text-tertiary"><?=$daten['homepage']?></a></td>
            </tr>
        </table>
    </div>
    <h3 class="w3-text-primary">Kontaktdaten</h3>
    <div class="w3-responsive w3-card-4">
        <table class="w3-table w3-striped">
            <tr>
                <th class="w3-primary">Email</th>
                <th class="w3-primary w3-center">Auf Webseite anzeigen?</th>
                <th class="w3-primary w3-center">Infomails erhalten?</th>
            </tr>
            <?php foreach($emails as $email){?>
                <tr>
                    <td><?=$email['email']?></td>
                    <td class='w3-center'><?=$email['public']?></td>
                    <td class='w3-center'><?=$email['get_info_mail']?></td>
                </tr>
            <?php }?>
        </table>
    </div>
<?php } //ende if?>
