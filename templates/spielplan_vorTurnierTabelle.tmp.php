<!-- TEAMLISTE -->
<h3 class="w3-text-secondary w3-margin-top">Teamliste</h3>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th class="w3-right-align">Team ID</th>
                <th>Teamname</th>
                <th class="w3-right-align">Teamblock</th>
                <th class="w3-right-align">Wertigkeit</th>
            </tr>

        <?php foreach ($teamliste as $index => $team){?>
            <tr>
            <td class="w3-right-align"><?= $team["team_id"]?></td>
            <td><?= $team["teamname"]?></td>
            <td class="w3-right-align"><?= $team["tblock"]?></td>
            <td class="w3-right-align"><?= $team["wertigkeit"]?></td>
            </tr>
        <?php }//end foreach?>
        </table>
    </div>