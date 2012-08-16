<?php

$page->title = __ ($appconf['Lemur']['public_name']);
$page->layout = $appconf['Lemur']['layout'];

echo $tpl->render ('lemur/index', array ());

?>