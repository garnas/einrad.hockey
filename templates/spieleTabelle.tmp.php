
    <div class="w3-responsive">
        <table class="w3-table w3-striped w3-bordered">
            <thead>
            <tr class="w3-primary">
                <th>Zeit</th>
                <th>Schiri</th>
                <th >Mannschaft 1</th>
                <th>Mannschaft 2</th>
                <th>Tore 1</th>
                <th>Tore 2</th>
                <th>Pen. 1</th>
                <th>Pen. 2</th>
            </tr>
            </thead>

            <?php
            $number=$spielplan->getAnzahlSpiele();
            if($group==2){//für Gruppenphase 8er Gruppe
                $number=12;
            }elseif ($group==-1){
                $number=8;
            }

            for($i=0;$i<$number;$i++){
                $q=$i;
                if($group==-1){
                    $q=12+$i;
                }
                //$spiel=$spielplan->getSpiel($i);
                $spielID=$spielplan->getSpiel_ID($q);
                if($spielID>=0){
                    $spiel= new Spiel($spielID);
                    $zeit=$spielplan->getZeit($q);
                    $schiri=$spielplan->getSchiriString($q);
                    $teamA=$spielplan->getTeamAString($q);
                    $teamB=$spielplan->getTeamBString($q);
                    $toreA=repairTore($spiel->getToreA());
                    $toreB=repairTore($spiel->getToreB());
                    $penA=repairTore($spiel->getPenaltyA());
                    $penB=repairTore($spiel->getPenaltyB());
                }else{
                    $zeit=$spielplan->getZeit($q);
                    $schiri="";
                    $teamA=$spielplan->getDefaultTeams($i)[0];
                    $teamB=$spielplan->getDefaultTeams($i)[1];
                    $toreA="";
                    $toreB="";
                    $penA="";
                    $penB="";
                }

                //echo "toreA $toreA <br>";
                echo
                "<tr>
                    <td> $zeit</td>
                    <td>$schiri</td>
                    <td>$teamA</td>
                    <td>$teamB</td>
                    <td><input name='toreAPOST[$q]' value='$toreA' size='5'></td>
                    <td><input name='toreBPOST[$q]' value='$toreB' size='5'></td>
                    ";
                if($necessaryPenatlies[$q]){
                    echo
                    "
                    <td><input name='penAPOST[$q]' value='$penA' size='5'></td>
                    <td><input name='penBPOST[$q]' value='$penB' size='5'></td>
                    </tr>
                    ";
                }else{
                    echo
                    "
                    <td><input name='penAPOST[$q]' value='$penA' size='5' readonly='readonly'></td>
                    <td><input name='penBPOST[$q]' value='$penB' size='5' readonly='readonly'></td>
                    </tr>
                    ";
                }

            }

           ?>

        </table>
    </div>
    <p><input type="submit" name="gesendet_tur"></p>

<br>

