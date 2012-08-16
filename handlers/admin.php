<?php

$page->layout = 'admin';

$this->require_admin ();

$page->title = __ ('Lemur Learning');

echo $tpl->render ('lemur/admin', array ());

?>