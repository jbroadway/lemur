<?php

$this->require_admin ();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	$this->redirect ('/lemur/admin');
}

if (! isset ($_POST['id']) || ! is_numeric ($_POST['id'])) {
	$this->redirect ('/lemur/admin');
}

$cat = new lemur\Category ($_POST['id']);

if (! $cat->remove ()) {
	$this->add_notification ('An error occurred.');
} else {
	$this->add_notification ('Category deleted.');
}

$this->redirect ('/lemur/admin');

?>