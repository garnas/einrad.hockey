<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/team_session.logic.php'; //Auth
include '../../logic/emails.logic.php';

Form::attention("Bei einer großen Anzahl an Email-Empfängern müssen diese im BCC angeschrieben werden!");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = 'Kontaktcenter | ' . $_SESSION['teamname'];
include '../../templates/header.tmp.php';
include '../../templates/emails.tmp.php';

if (!empty($emails)){?>                	    
    <div class="w3-card-4 w3-panel">
        <h3 class="w3-text-primary">Liste der ausgewählten E-Mail-Adressen</h3>
        <p>
            <?php foreach($emails as $email){?>
                <?=$email?><br>
            <?php } //end foreach ?>
        </p>
        <h3 class="w3-text-primary">Ligaleitung</h3>
        <p> 
        <?=Config::LAMAIL?><br>
        <?=Config::TECHNIKMAIL?><br>
        <?=Config::SCHIRIMAIL?>
        <p>
    </div>
<?php } //end if?>

<?php include '../../templates/footer.tmp.php';