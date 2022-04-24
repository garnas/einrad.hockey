<?php
require '../init.php';
?>

<pre>
<?= file_get_contents(Env::BASE_PATH . '/system/logs/errors.log') ?>
</pre>


