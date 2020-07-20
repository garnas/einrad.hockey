<div class="w3-responsive">
    <table class="w3-table w3-striped w3-bordered">
        <thead>
        <tr class="w3-primary">
            <th>Platzierung</th>
            <th>Mannschaft</th>
            <th >Tore</th>
            <th >Gegentore</th>
            <th >Differenz</th>
            <th >Punkte</th>
            <?php
            if($group<1){
                echo "<th >Ergebnis</th>";
            }
           ?>

        </tr>
        </thead>

        <?php
        $number=$spielplan->getPlaetze();
        if($group==1){
            $erg=$erg1;
            $number=4;
        }else if($group==2){
            $erg=$erg2;
            $number=4;
        }

        for($i=0;$i<$number;$i++){
            //$spiel=$spielplan->getSpiel($i);
            $teamname=$erg[$i]["teamname"];
            $tore=$erg[$i]["tore"];
            $gegentore=$erg[$i]["gegentore"];
            $diff=$tore-$gegentore;
            $punkte=$erg[$i]["turnierPunkte"];
            $index=$i+1;
            $ligaPunkte=$erg[$i]["ligaPunkte"];
            echo
            "<tr>
                    <td> $index</td>
                    <td>$teamname</td>
                    <td>$tore</td>
                    <td>$gegentore</td>
                    <td>$diff</td>
                    <td>$punkte</td>";
            if($group<1){
                echo "<td>$ligaPunkte</td>";
            }
            echo "
                  </tr>
                    ";
        }

       ?>

    </table>
</div>
<br>
