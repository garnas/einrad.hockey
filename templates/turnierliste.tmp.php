<!--$turniere muss übergeben werden-->
<div class="w3-responsive w3-card-4">
    <table class="w3-table w3-bordered w3-striped" style="white-space: nowrap">
        <tr class='w3-primary'>
            <th>Datum</th>
            <th><i>Phase<i></th>
            <th>Ort <i>(Block)</i></th>
            <th><i>Ausrichter<i></th>
            <th>Links</th>
        </tr>
        <?php foreach ($turniere as $turnier){?>
            <tr style="cursor: pointer;" class="w3-hover-tertiary <?=$turnier['row_color'] ?? ''?>" onclick="window.open('<?=$turnier['link_zeile']?>', '_self')">
                <td><?=date("d.m.y", strtotime($turnier['datum']))?> | <i><?=$turnier['freivoll'] ?? ''?></i></td>
                <td style='text-align: left;' ><i class='' ><?=$turnier['phase']?></i></td>
                <td><?=$turnier['ort']?> <span class='<?=$turnier['block_color'] ?? ''?>'><i>(<?=$turnier['tblock']?>)</i></span><br><i class="w3-text-grey"><?=$turnier['tname']?></i></td>
                <td style='text-align: left;' ><i class='' ><?=$turnier['teamname']?></i></td>
                <td style="white-space:normal;">
                    <?php foreach ($turnier['links'] as $link){ ?>
                        <?=$link?><br>
                    <?php }//end foreach?>
                </td>   
            </tr>
        <?php }//end foreach?>
    </table>
</div>
