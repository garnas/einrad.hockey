<section>
    <h2 class="w3-text-secondary w3-large">Kommentare</h2>
    <table class="w3-table w3-striped w3-border-top w3-border-bottom w3-border-grey">
        <?php if (empty($comment)):?>
            <tr><td><b>Es wurden keine Kommentare abgegeben.</b></td></tr>
        <?php else:?>
            <?php foreach ($comment as $key => $weitere):?>
                <tr class="w3-border-bottom w3-border-grey"><td><?=$key?></td></tr>
            <?php endforeach;?>
        <?php endif;?>
    </table>
</section>