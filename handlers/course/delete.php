<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/lemur/admin');
}

$page->layout = 'admin';
info ($_POST);

?>