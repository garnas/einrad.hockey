<!--$turniere muss übergeben werden-->
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-bordered w3-striped">
        <tr class='w3-primary'>
            <th>Datum</th>
            <th>Ort <i>(Block)  </i></th>
            <th class="w3-hide-small">Turniername</th>
            <th><i class='' >Phase<i></th>
            <th>Link</th>
        </tr>
        <?php foreach ($turniere as $turnier){?>
            <tr style="cursor: pointer;" class="w3-hover-tertiary <?=$turnier['row_color'] ?? ''?>" onclick="window.open('<?=$turnier['link_anmelden'] ?? $turnier['link_bearbeiten']?>', '_self')">
                <td style='vertical-align: middle;' ><?=date("d.m.y", strtotime($turnier['datum']))?> <i><?=$turnier['freivoll'] ?? ''?></i></td>
                <td style='vertical-align: middle;' ><?=$turnier['ort'] . " <span class='" . ($turnier['block_color'] ?? '') . "'><i>(" . $turnier['tblock'] . ")</i></span>"?></td>
                <td class="w3-hide-small" style='vertical-align: middle;' ><?=$turnier['tname']?></td>
                <td style='vertical-align: middle; text-align: left;' ><i class='' ><?=$turnier['phase']?></i></td>
            
                <!--Aktionen für Teams-->
                <?php if ($teamcenter) {?> 
                    <td style='vertical-align: middle; white-space: nowrap'>
                        <?php if (isset($turnier['link_anmelden'])){?>
                        <a class="no w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_anmelden']?>'><i class="material-icons">how_to_reg</i> An-/Abmeldung</a>
                        <br>
                        <a class="no w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_details']?>'><i class="material-icons">info</i> Details</a>
                        <?php }elseif (isset($turnier['link_bearbeiten'])){?>
                            <a class="no w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_bearbeiten']?>'><i class="material-icons">create</i> Turnier bearbeiten</a>
                        <?php }//end if?>
                    </td> 
                <?php }//end if?>
                
                <!--Aktionen für Ligaausschuss-->
                <?php if ($ligacenter) {?> 
                    <td style='vertical-align: middle; white-space: nowrap'>
                        <a class="no w3-large w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_bearbeiten']?>'>Turnier&nbsp;bearbeiten</a>
                        <br>
                        <a class="no w3-large w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_log']?>'>Log&nbsp;einsehen</a>
                        <br>
                        <a class="no w3-large w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_spielplan']?>'>Spielplan/Ergebnis</a>
                        <br>
                        <a class="no w3-large w3-text-blue w3-hover-text-secondary" href='<?=$turnier['link_anmelden']?>'>Teams an/abmelden</a>
                    </td> 
                <?php }//end if?>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
