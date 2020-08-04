<!--$turniere muss übergeben werden-->
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-bordered w3-striped">
        <tr class='w3-primary'>
            <th>Datum</th>
            <th><i>Phase<i></th>
            <th>Ort <i>(Block)</i></th>
            <th><i>Ausrichter<i></th>
            <th>Links</th>
        </tr>
        <?php foreach ($turniere as $turnier){?>
            <tr style="cursor: pointer;" class="w3-hover-tertiary <?=$turnier['row_color'] ?? ''?>" onclick="window.open('<?=$turnier['link_anmelden'] ?? $turnier['link_bearbeiten']?>', '_self')">
                <td style='vertical-align: middle;' ><?=date("d.m.y", strtotime($turnier['datum']))?> <i><?=$turnier['freivoll'] ?? ''?></i></td>
                <td style='vertical-align: middle; text-align: left;' ><i class='' ><?=$turnier['phase']?></i></td>
                <td style='vertical-align: middle;' ><?=$turnier['ort']?> <span class='<?=$turnier['block_color'] ?? ''?>'><i><?=$turnier['tblock']?></i></span><br><i class="w3-text-grey"><?=$turnier['tname']?></i></td>
                <td style='vertical-align: middle; text-align: left;' ><i class='' ><?=$turnier['teamname']?></i></td>
                
            
                <!--Aktionen für Teams-->
                <?php if ($teamcenter) {?> 
                    <td style='vertical-align: middle; white-space: nowrap'>
                        <?php if (isset($turnier['link_anmelden'])){?>
                        <?=Form::link($turnier['link_anmelden'],'<i class="material-icons">how_to_reg</i> An/abmelden')?>
                        <br>
                        <?=Form::link($turnier['link_details'], '<i class="material-icons">info</i> Details')?>
                        <?php }elseif (isset($turnier['link_bearbeiten'])){?>
                            <?=Form::link($turnier['link_bearbeiten'],'<i class="material-icons">create</i> Turnier bearbeiten')?>
                        <?php }//end if?>
                    </td> 
                <?php }//end if?>
                
                <!--Aktionen für Ligaausschuss-->
                <?php if ($ligacenter) {?> 
                    <td style='vertical-align: middle; white-space: nowrap'>
                        <?=Form::link($turnier['link_bearbeiten'],'<i class="material-icons">create</i> Turnier bearbeiten')?>
                        <br>
                        <?=Form::link($turnier['link_log'],'<i class="material-icons">info_outline</i> Log einsehen')?>
                        <br>
                        <?=Form::link($turnier['link_spielplan'], '<i class="material-icons">list</i> Spielplan/Ergebnis')?>
                        <br>
                        <?=Form::link($turnier['link_anmelden'],'<i class="material-icons">how_to_reg</i> Teams an/abmelden')?>
                    </td> 
                <?php }//end if?>
            </tr>
        <?php }//end foreach?>
    </table>
</div>
