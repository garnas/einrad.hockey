<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/team_session.logic.php'; //Auth

//$turnierID= $_SESSION['turnier_ID'];//TODO
$turnierID=910;
$spielplan=new spielplan($turnierID);
if(isset($_POST["gesendet_tur"])){
    for($i=0;$i<$spielplan->getAnzahlSpiele();$i++){
        $spielplan->updateTore($i,$_POST["toreAPOST"][$i],$_POST["toreBPOST"][$i],$_POST["penAPOST"][$i],$_POST["penBPOST"][$i]);
    }
}

db::debug();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan";
include '../../templates/header.tmp.php';
?>

<div  class="w3-container w3-card-4">
    <?php
    //die Seite sieht für 4-7 Turniere und DoppelKo Turniere gleich aus
    if($spielplan->getPlaetze()<8||$spielplan->getSpielplan()=="dko"){
        $group=0;
        include '../../templates/vorTurnierTabelle.tmp.php';
        
        
        $spielplan->createTurnierTeams();
        
        echo " TurnierTeams erstellt!<br>";
        $erg=$spielplan->getTurnierTeamResults();
        
        $necessaryPenatlies=$spielplan->getNecessaryPenalties();
        
        echo '<form action ="tc_spielplan.php" method="post">';
        
        include '../../templates/spieleTabelle.tmp.php';
        
        $output=$spielplan->penaltyWarning();
        echo "<p>$output</p>";
        include '../../templates/ergebnisTabelle.tmp.php';
        
    }else{
        //8 er Gruppe
        $group=1;
        include '../../templates/vorTurnierTabelle.tmp.php';
        $group=2;
        include '../../templates/vorTurnierTabelle.tmp.php';
        if(isset($_POST["gesendet"])){
            for($i=0;$i<$spielplan->getAnzahlSpiele();$i++){
                $spielplan->updateTore($i,$_POST["toreAPOST"][$i],$_POST["toreBPOST"][$i],$_POST["penAPOST"][$i],$_POST["penBPOST"][$i]);
            }
        }

        $spielplan->createTurnierTeams(1);
        $spielplan->createTurnierTeams(2);

        $erg1=$spielplan->getTurnierTeamResults(1);

        $warning1=$spielplan->penaltyWarning();


        $erg2=$spielplan->getTurnierTeamResults(2);

        $warning2=$spielplan->penaltyWarning();
        $necessaryPenatlies=$spielplan->getNecessaryPenalties();
        echo '<form action ="tc_spielplan.php" method="post">';
        include '../../templates/spieleTabelle.tmp.php';
        echo "<p>$warning1</p>";
        $group=1;
        include '../../templates/ergebnisTabelle.tmp.php';
        echo "<p>$warning2</p>";
        $group=2;
        include '../../templates/ergebnisTabelle.tmp.php';
        //KO-Spiele
        $group=-1;
        include '../../templates/spieleTabelle.tmp.php';
        echo "</form>";
        $spielplan->createTurnierTeams(-1);
        $erg=$spielplan->get8erGruppeEndResultat();
        include '../../templates/ergebnisTabelle.tmp.php';

    }

   ?>
    <?php
    function repairTore($tor){
        if($tor<0){
            $tor=" ";
        }
        return $tor;
    }
   ?>
</div>

<?php include '../../templates/footer.tmp.php';

